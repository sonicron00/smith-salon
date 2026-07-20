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
        'sort_order',
        'active',
        'consultation_form_id',
    ];

    protected $casts = [
        'active' => 'boolean',
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
}