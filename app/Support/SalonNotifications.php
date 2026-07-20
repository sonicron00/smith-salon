<?php

namespace App\Support;

use App\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SalonNotifications
{
    /**
     * Send an SMS notification to the salon owner when a booking is modified.
     */
    public static function smsSalon(Appointment $appointment, string $action): void
    {
        $salonPhone = config('salon.notification_phone');

        if (! $salonPhone) {
            Log::warning('SalonNotifications: No salon notification phone configured.');
            return;
        }

        $appointment->loadMissing(['service', 'staff']);

        $message = match ($action) {
            'created' => sprintf(
                'New booking: %s for %s with %s on %s at %s',
                $appointment->customer_name,
                $appointment->service?->name,
                $appointment->staff?->name,
                $appointment->starts_at->format('D j M'),
                $appointment->starts_at->format('H:i'),
            ),
            'cancelled' => sprintf(
                'Cancelled: %s\'s %s appointment on %s at %s',
                $appointment->customer_name,
                $appointment->service?->name,
                $appointment->starts_at->format('D j M'),
                $appointment->starts_at->format('H:i'),
            ),
            'updated' => sprintf(
                'Rescheduled: %s moved to %s at %s (%s with %s)',
                $appointment->customer_name,
                $appointment->starts_at->format('D j M'),
                $appointment->starts_at->format('H:i'),
                $appointment->service?->name,
                $appointment->staff?->name,
            ),
            default => sprintf('Appointment update (%s): %s', $action, $appointment->customer_name),
        };

        try {
            $sid = env('TWILIO_ACCOUNT_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $from = env('TWILIO_FROM') ?: env('TWILIO_ALPHA_SENDER');

            if (! $from) {
                Log::error('SalonNotifications: No TWILIO_FROM or TWILIO_ALPHA_SENDER configured.');
                return;
            }

            $client = new Client($sid, $token);
            $client->messages->create($salonPhone, [
                'from' => $from,
                'body' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('SalonNotifications SMS failed: ' . $e->getMessage());
            report($e);
        }
    }
}
