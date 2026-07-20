<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Services\Booking\SlotService;
use App\Notifications\AppointmentBookedSms;
use App\Support\SalonNotifications;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function start()
    {
        $services = Service::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('public.booking.start', compact('services'));
    }

    public function pickStaff(Service $service)
    {
        $staff = $service->staff()
            ->where('active', true)
            ->orderBy('staff.sort_order')
            ->orderBy('staff.name')
            ->get();

        return view('public.booking.staff', compact('service', 'staff'));
    }

    public function pickSlot(Service $service, Staff $staff, Request $request, SlotService $slotService)
    {
        if (! $service->staff()->whereKey($staff->id)->exists()) {
            abort(404);
        }

        $now = CarbonImmutable::now();
        $earliest = $now->addHours(24)->startOfDay();
        $latest = $now->addWeeks(6)->endOfDay();

        // Default to the first bookable day (tomorrow at earliest)
        $date = $request->query('date') ?: $earliest->toDateString();

        // Clamp date to bookable window
        $parsedDate = CarbonImmutable::parse($date);
        if ($parsedDate->lt($earliest)) {
            $date = $earliest->toDateString();
        } elseif ($parsedDate->gt($latest)) {
            $date = $latest->toDateString();
        }

        $month = $this->normaliseMonth($request->query('month'), $date);

        // Clamp month navigation to bookable window
        $monthStart = CarbonImmutable::parse($month . '-01')->startOfMonth();
        $earliestMonth = $earliest->format('Y-m');
        $latestMonth = $latest->format('Y-m');

        if ($month < $earliestMonth) {
            $month = $earliestMonth;
            $monthStart = CarbonImmutable::parse($month . '-01')->startOfMonth();
        } elseif ($month > $latestMonth) {
            $month = $latestMonth;
            $monthStart = CarbonImmutable::parse($month . '-01')->startOfMonth();
        }

        $slots = $slotService->availableSlots($service, $staff, $date);

        $slotGroups = [
            'AM' => array_values(array_filter($slots, fn (string $time) => (int) substr($time, 0, 2) < 12)),
            'PM' => array_values(array_filter($slots, fn (string $time) => (int) substr($time, 0, 2) >= 12)),
        ];

        $availableDates = $slotService->availableDatesForMonth($service, $staff, $month);
        $calendarWeeks = $this->buildCalendarWeeks($month, $availableDates, $date);

        $previousMonth = $monthStart->subMonth()->format('Y-m');
        $nextMonth = $monthStart->addMonth()->format('Y-m');

        $calendar = [
            'month' => $month,
            'label' => $monthStart->format('F Y'),
            'previous_month' => $previousMonth >= $earliestMonth ? $previousMonth : null,
            'next_month' => $nextMonth <= $latestMonth ? $nextMonth : null,
            'weeks' => $calendarWeeks,
        ];

        $nextAvailableSlot = empty($slots)
            ? $slotService->nextAvailableSlot($service, $staff, $date)
            : null;

        return view('public.booking.slots', compact(
            'service',
            'staff',
            'date',
            'slots',
            'slotGroups',
            'calendar',
            'nextAvailableSlot',
        ));
    }

    public function confirm(Request $request, SlotService $slotService)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'staff_id' => ['required', 'exists:staff,id'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:32'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $staff = Staff::findOrFail($validated['staff_id']);

        if (! $service->staff()->whereKey($staff->id)->exists()) {
            abort(404);
        }

        $availableSlots = $slotService->availableSlots($service, $staff, $validated['date']);

        if (! in_array($validated['time'], $availableSlots, true)) {
            return back()
                ->withErrors(['time' => 'That slot is no longer available. Please choose another time.'])
                ->withInput();
        }

        [$startsAt, $endsAt] = $slotService->slotToRange($service, $validated['date'], $validated['time']);

        $appointment = DB::transaction(function () use ($service, $staff, $validated, $startsAt, $endsAt) {
            $conflict = Appointment::query()
                ->where('staff_id', $staff->id)
                ->where('status', Appointment::STATUS_BOOKED)
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                abort(409, 'That time has just been booked. Please choose another slot.');
            }

            $customer = \App\Models\Customer::findOrCreateFromBooking(
                $validated['customer_name'],
                $validated['customer_phone'],
                $validated['customer_email'] ?? null,
            );

            return Appointment::create([
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'customer_id' => $customer->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_message' => $validated['customer_message'] ?? null,
                'status' => Appointment::STATUS_BOOKED,
                'manage_token' => Str::random(48),
            ]);
        });

        try {
            $appointment->notify(new AppointmentBookedSms($appointment));
            $appointment->update(['confirmation_sent_at' => now()]);
        } catch (\Throwable $e) {
            report($e);
        }

        SalonNotifications::smsSalon($appointment, 'created');

        return redirect()->route('booking.done', ['appointment' => $appointment->id]);
    }

    public function done(Appointment $appointment)
    {
        return view('public.booking.done', compact('appointment'));
    }

    private function normaliseMonth(?string $month, string $fallbackDate): string
    {
        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return CarbonImmutable::parse($month . '-01')->format('Y-m');
        }

        return CarbonImmutable::parse($fallbackDate)->format('Y-m');
    }

    private function buildCalendarWeeks(string $month, array $availableDates, string $selectedDate): array
    {
        $monthStart = CarbonImmutable::parse($month . '-01')->startOfMonth();
        $monthEnd = $monthStart->endOfMonth();

        $calendarStart = $monthStart->startOfWeek(CarbonInterface::MONDAY);
        $calendarEnd = $monthEnd->endOfWeek(CarbonInterface::SUNDAY);

        $weeks = [];
        $week = [];
        $cursor = $calendarStart;

        while ($cursor->lte($calendarEnd)) {
            $date = $cursor->toDateString();

            $week[] = [
                'date' => $date,
                'day' => $cursor->format('j'),
                'is_current_month' => $cursor->isSameMonth($monthStart),
                'is_selected' => $date === $selectedDate,
                'is_available' => in_array($date, $availableDates, true),
            ];

            if ($cursor->dayOfWeekIso === 7) {
                $weeks[] = $week;
                $week = [];
            }

            $cursor = $cursor->addDay();
        }

        return $weeks;
    }
}