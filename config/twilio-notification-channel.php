<?php

return [
    'enabled' => (bool) env('TWILIO_ENABLED', true),
    'username' => env('TWILIO_USERNAME'),
    'password' => env('TWILIO_PASSWORD'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),
    'account_sid' => env('TWILIO_ACCOUNT_SID'),

    'from' => env('TWILIO_FROM'),
    'alphanumeric_sender' => env('TWILIO_ALPHA_SENDER'),
    'shorten_urls' => (bool) env('TWILIO_SHORTEN_URLS', false),

    'sms_service_sid' => env('TWILIO_SMS_SERVICE_SID'),

    'debug_to' => env('TWILIO_DEBUG_TO'),

    'ignored_error_codes' => [
        21608,
        21211,
        21614,
        21408,
    ],
];
