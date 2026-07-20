<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationResponse extends Model
{
    protected $fillable = [
        'appointment_id',
        'consultation_form_id',
        'answers',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(ConsultationForm::class, 'consultation_form_id');
    }
}
