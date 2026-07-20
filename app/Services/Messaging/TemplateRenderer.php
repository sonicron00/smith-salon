<?php

namespace App\Services\Messaging;

use App\Models\Appointment;

class TemplateRenderer
{
    /**
     * Very simple token replacement.
     * Supported: {{name}}, {{service}}, {{staff}}, {{date}}, {{time}}, {{manage_url}}
     */
    public function render(string $template, Appointment $appointment): string
    {
        $map = [
            '{{name}}' => $appointment->customer_name,
            '{{service}}' => $appointment->service?->name ?? '',
            '{{staff}}' => $appointment->staff?->name ?? '',
            '{{date}}' => $appointment->starts_at->format('D j M Y'),
            '{{time}}' => $appointment->starts_at->format('H:i'),
            '{{manage_url}}' => $appointment->manageUrl(),
            '{{consultation_url}}' => $appointment->consultationUrl(),
        ];

        return str_replace(array_keys($map), array_values($map), $template);
    }
}
