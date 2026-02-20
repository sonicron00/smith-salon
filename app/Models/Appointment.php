<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Appointment extends Model
{
    use Notifiable;

    public const STATUS_BOOKED = 'booked';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'staff_id',
        'service_id',
        'starts_at',
        'ends_at',
        'customer_name',
        'customer_phone',
        'customer_email',
        'status',
        'manage_token',
        'confirmation_sent_at',
        'reminder_sent_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'confirmation_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function routeNotificationForTwilio(): string
    {
        return $this->customer_phone;
    }

    public function manageUrl(): string
    {
        return route('appointment.manage', ['token' => $this->manage_token]);
    }

    public function canModify(int $cutoffHours): bool
    {
        return now()->diffInHours($this->starts_at, false) >= $cutoffHours
            && $this->status === self::STATUS_BOOKED;
    }

    public function cancel(?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }
}
