<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Staff;
use App\Services\Booking\SlotService;
use App\Services\Messaging\TemplateRenderer;
use App\Notifications\AppointmentBookedSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function start()
    {
        $services = Service::query()->where('active', true)->orderBy('name')->get();
        return view('public.booking.start', compact('services'));
    }

    public function pickStaff(Service $service)
    {
        $staff = Staff::query()->where('active', true)->orderBy('name')->get();
        return view('public.booking.staff', compact('service', 'staff'));
    }

    public function pickSlot(Service $service, Staff $staff, Request $request, SlotService $slotService)
    {
        $date = $request->query('date') ?: now()->toDateString();
        $slots = $slotService->availableSlots($service, $staff, $date);

        return view('public.booking.slots', compact('service', 'staff', 'date', 'slots'));
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
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $staff = Staff::findOrFail($validated['staff_id']);

        [$startsAt, $endsAt] = $slotService->slotToRange($service, $validated['date'], $validated['time']);

        $appointment = DB::transaction(function () use ($service, $staff, $validated, $startsAt, $endsAt) {
            // Overlap protection (race-safe when wrapped in transaction)
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

            return Appointment::create([
                'staff_id' => $staff->id,
                'service_id' => $service->id,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'status' => Appointment::STATUS_BOOKED,
                'manage_token' => Str::random(48),
            ]);
        });

        // Queue SMS confirmation
        try {
            $appointment->notify(new AppointmentBookedSms($appointment));
            $appointment->update(['confirmation_sent_at' => now()]);
        } catch (\Throwable $e) {
            // Fail silently for MVP; you may log and show softer UX
            report($e);
        }

        return redirect()->route('booking.done', ['appointment' => $appointment->id]);
    }

    public function done(Appointment $appointment)
    {
        return view('public.booking.done', compact('appointment'));
    }
}
