<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalonBookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $actionLabel,
    ) {}

    public function build(): self
    {
        return $this
            ->subject("Salon booking {$this->actionLabel}: {$this->appointment->customer_name}")
            ->view('mail.salon-booking-notification');
    }
}
