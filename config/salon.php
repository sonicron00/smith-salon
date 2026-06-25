<?php

return [
    'cancellation_cutoff_hours' => (int) env('CANCELLATION_CUTOFF_HOURS', 24),
    'reminder_lead_minutes' => (int) env('REMINDER_LEAD_MINUTES', 1440),
    'notification_email' => env('SALON_NOTIFICATION_EMAIL'),

    // Confirm this address with the client if needed.
    'address' => env('SALON_ADDRESS', '15 Trinity Square, South Woodham Ferrers, Chelmsford CM3 5JX'),

    'opening_hours' => [
        ['day' => 'Tuesday', 'hours' => '10:00 – 17:00'],
        ['day' => 'Thursday', 'hours' => '12:00 – 20:00'],
        ['day' => 'Friday', 'hours' => '10:00 – 17:00'],
    ],
];