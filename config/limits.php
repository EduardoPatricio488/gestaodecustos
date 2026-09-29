<?php

return [
    'api' => [
        'requests_per_minute' => (int) env('API_RATE_LIMIT', 60),
        'public_requests_per_minute' => (int) env('PUBLIC_RATE_LIMIT', 120),
    ],

    'ai' => [
        'requests_per_minute' => (int) env('AI_RATE_LIMIT', 20),
        'monthly_tokens' => (int) env('AI_MONTHLY_TOKEN_LIMIT', 100000),
        'request_timeout_seconds' => (int) env('AI_REQUEST_TIMEOUT', 30),
        'connect_timeout_seconds' => (int) env('AI_CONNECT_TIMEOUT', 5),
    ],

    'uploads' => [
        'max_mb' => (int) env('UPLOAD_MAX_MB', 10),
    ],

    'http' => [
        'connect_timeout_seconds' => (int) env('HTTP_CONNECT_TIMEOUT', 5),
        'timeout_seconds' => (int) env('HTTP_TIMEOUT', 15),
    ],
];
