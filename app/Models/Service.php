<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration_minutes',
        'buffer_minutes',
        'price_pence',
        'sort_order',
        'active',
        'is_addon',
        'consultation_form_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'is_addon' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'service_staff');
    }

    public function consultationForm(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ConsultationForm::class);
    }

    /**
     * Add-on services available to be added to this service when booking.
     */
    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_addon', 'service_id', 'addon_id');
    }

    public function scopeBookable($query)
    {
        return $query->where('is_addon', false);
    }

    public function scopeAddons($query)
    {
        return $query->where('is_addon', true);
    }
}