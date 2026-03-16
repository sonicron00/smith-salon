<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'duration_minutes',
        'buffer_minutes',
        'price_pence',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'service_staff');
    }
}
