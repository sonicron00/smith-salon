<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Setting;
use App\Services\Booking\SlotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\SalonNotifications;

class ManageAppointmentController extends Controller
{
    public function show(string $token, Request $request, SlotService $slotService)
    {
        $appointment = Appointment::query()->where('manage_token', $token)->firstOrFail();

        $cancellationPolicyHtml = Setting::get('policy.cancellation_html');
        $cutoffHours = (int) (config('salon.cancellation_cutoff_hours') ?? 24);

        $canModify = $appointment->canModify($cutoffHours);

        // slots for reschedule UI (same staff/service/day by default)
        $date = $request->query('date') ?: $appointment->starts_at->toDateString();
        $slots = $slotService->availableSlots($appointment->service, $appointment->staff, $date, ignoreAppointmentId: $appointment->id);

        return view('public.manage.show', compact('appointment', 'cancellationPolicyHtml', 'cutoffHours', 'canModify', 'date', 'slots'));
    }

    public function cancel(string $token, Request $request)
    {
        $appointment = Appointment::query()->where('manage_token', $token)->firstOrFail();

        $cutoffHours = (int) (config('salon.cancellation_cutoff_hours') ?? 24);
        if (! $appointment->canModify($cutoffHours)) {
            return back()->withErrors(['cancel' => 'Cancellation is not allowed within the cutoff window.']);
        }

        $appointment->cancel(reason: $request->input('reason'));
        SalonNotifications::emailSalon($appointment, 'cancelled');

        return back()->with('status', 'Your appointment has been cancelled.');
    }

    public function reschedule(string $token, Request $request, SlotService $slotService)
    {
        $appointment = Appointment::query()->where('manage_token', $token)->firstOrFail();

        $cutoffHours = (int) (config('salon.cancellation_cutoff_hours') ?? 24);
        if (! $appointment->canModify($cutoffHours)) {
            return back()->withErrors(['reschedule' => 'Rescheduling is not allowed within the cutoff window.']);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        [$startsAt, $endsAt] = $slotService->slotToRange($appointment->service, $validated['date'], $validated['time']);

        DB::transaction(function () use ($appointment, $startsAt, $endsAt) {
            $conflict = Appointment::query()
                ->where('staff_id', $appointment->staff_id)
                ->where('status', Appointment::STATUS_BOOKED)
                ->where('id', '!=', $appointment->id)
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                abort(409, 'That time has just been booked. Please choose another slot.');
            }

            $appointment->update([
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);
        });

        SalonNotifications::emailSalon($appointment->fresh(['service', 'staff']), 'updated');

        return back()->with('status', 'Your appointment has been rescheduled.');
    }
}
