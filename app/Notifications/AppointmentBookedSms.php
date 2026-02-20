<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Setting;
use App\Services\Messaging\TemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioMessage;

class AppointmentBookedSms extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Appointment $appointment) {}

    public function via(object $notifiable): array
    {
        return ['twilio'];
    }

    public function toTwilio(object $notifiable): TwilioMessage
    {
        $template = Setting::get('sms.confirmation_template')
            ?? "Hi {{name}}, you're booked for {{service}} with {{staff}} on {{date}} at {{time}}. Manage: {{manage_url}}";

        $body = app(TemplateRenderer::class)->render($template, $this->appointment);

        return (new TwilioMessage())->content($body);
    }
}
