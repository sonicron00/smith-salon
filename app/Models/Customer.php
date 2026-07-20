<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'notes',
    ];

    protected $casts = [
        'notes' => 'array',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Find or create a customer record from booking details.
     */
    public static function findOrCreateFromBooking(string $name, string $phone, ?string $email = null): self
    {
        $customer = self::where('phone', $phone)->first();

        if ($customer) {
            // Update name/email if provided (keep records fresh)
            $customer->update(array_filter([
                'name' => $name,
                'email' => $email,
            ]));

            return $customer;
        }

        return self::create([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ]);
    }
}
