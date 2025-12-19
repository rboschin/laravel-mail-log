<?php

return [
    'enabled' => env('MAIL_LOG_ENABLED', false),
    'to' => env('MAIL_LOG_TO', null),
    'from' => env('MAIL_LOG_FROM', null),
    'levels' => explode(',', env('MAIL_LOG_LEVELS', 'error,critical,alert,emergency')),
    'environments' => array_filter(array_map('trim', explode(',', env('MAIL_LOG_ENVIRONMENTS', '')))),
    'queue' => (bool) env('MAIL_LOG_QUEUE', true),
    // Include full JSON payload in the email body when verbose. Backwards-compatible with MAIL_LOG_ATTACH_JSON.
    'is_verbose' => (bool) (env('MAIL_LOG_IS_VERBOSE', null) ?? env('MAIL_LOG_ATTACH_JSON', true)),
    'include_stack' => (bool) env('MAIL_LOG_INCLUDE_STACK', true),
    'auto_register' => (bool) env('MAIL_LOG_AUTO_REGISTER', true),
    'throttle' => [
        'window_seconds' => (int) env('MAIL_LOG_THROTTLE_WINDOW', 300),
        'max_per_window' => (int) env('MAIL_LOG_THROTTLE_MAX', 3),
    ],
];
