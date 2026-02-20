<?php

return [
    'cancellation_cutoff_hours' => (int) env('CANCELLATION_CUTOFF_HOURS', 24),
    'reminder_lead_minutes' => (int) env('REMINDER_LEAD_MINUTES', 1440),
];
