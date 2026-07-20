<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Setting;
use App\Services\Messaging\TemplateRenderer;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class AppointmentBookedSms
{
    public function __construct(private Appointment $appointment) {}

    public function send(): void
    {
        $template = Setting::get('sms.confirmation_template')
            ?? "Hi {{name}}, you're booked for {{service}} with {{staff}} on {{date}} at {{time}}. Manage: {{manage_url}}";

        $body = app(TemplateRenderer::class)->render($template, $this->appointment);
        $to = $this->appointment->customer_phone;

        $sid = config('twilio-notification-channel.account_sid');
        $token = config('twilio-notification-channel.auth_token');
        $from = config('twilio-notification-channel.from')
            ?: config('twilio-notification-channel.alphanumeric_sender');

        $debugTo = config('twilio-notification-channel.debug_to');
        if ($debugTo) {
            $to = $debugTo;
        }

        if (! $from) {
            Log::error('AppointmentBookedSms: No TWILIO_FROM or TWILIO_ALPHA_SENDER configured.');
            return;
        }

        try {
            $client = new Client($sid, $token);
            $client->messages->create($to, [
                'from' => $from,
                'body' => $body,
            ]);
        } catch (\Throwable $e) {
            Log::error('AppointmentBookedSms failed: ' . $e->getMessage());
            report($e);
        }
    }
}
