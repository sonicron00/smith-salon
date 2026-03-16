<?php

namespace App\Support;

use App\Mail\SalonBookingNotificationMail;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;

class SalonNotifications
{
    public static function emailSalon(Appointment $appointment, string $actionLabel): void
    {
        $recipient = config('salon.notification_email');

        if (! $recipient) {
            return;
        }

        Mail::to($recipient)->send(new SalonBookingNotificationMail($appointment->loadMissing(['service', 'staff']), $actionLabel));
    }
}
