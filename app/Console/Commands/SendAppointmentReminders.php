<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\AppointmentReminderSms;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Send SMS reminders for upcoming appointments';

    public function handle(): int
    {
        $leadMinutes = (int) (config('salon.reminder_lead_minutes') ?? 1440);

        $windowStart = now()->addMinutes($leadMinutes)->subMinutes(5);
        $windowEnd = now()->addMinutes($leadMinutes)->addMinutes(5);

        $appointments = Appointment::query()
            ->where('status', Appointment::STATUS_BOOKED)
            ->whereNull('reminder_sent_at')
            ->whereBetween('starts_at', [$windowStart, $windowEnd])
            ->with(['service', 'staff'])
            ->limit(200)
            ->get();

        foreach ($appointments as $appointment) {
            try {
                $appointment->notify(new AppointmentReminderSms($appointment));
                $appointment->update(['reminder_sent_at' => now()]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->info("Processed {$appointments->count()} reminder(s).");
        return self::SUCCESS;
    }
}
