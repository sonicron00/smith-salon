<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultationForm extends Model
{
    protected $fillable = [
        'name',
        'fields',
        'active',
    ];

    protected $casts = [
        'fields' => 'array',
        'active' => 'boolean',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ConsultationResponse::class);
    }
}
