<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have a
    | conventional file to locate various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'strava' => [
        'client_id' => env('STRAVA_CLIENT_ID'),
        'client_secret' => env('STRAVA_CLIENT_SECRET'),
        'redirect_uri' => env('STRAVA_REDIRECT_URI', '/fitness/strava/callback'),
    ],

    'whatsapp' => [
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'prices' => [
            'pro' => env('STRIPE_PRICE_PRO'),
            'business' => env('STRIPE_PRICE_BUSINESS'),
        ],
    ],

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        // Margem de segurança abaixo do limite de 20 pedidos/minuto dos modelos gratuitos.
        'requests_per_minute' => (int) env('OPENROUTER_REQUESTS_PER_MINUTE', 18),
        'connect_timeout' => (int) env('OPENROUTER_CONNECT_TIMEOUT', 8),
        'request_timeout' => (int) env('OPENROUTER_REQUEST_TIMEOUT', 45),
        // Modelos gratuitos com suporte a imagem. Não adicionar modelos pagos a esta lista.
        'model' => env('AI_MODEL', 'google/gemma-4-26b-a4b-it:free'),
        // O scanner de faturas fixa openrouter/free em InvoiceScanService; não usa fallback.\n    ],

    'market_data' => [
        'yahoo_api_key' => env('YAHOO_API_KEY'),
        'alpha_vantage_api_key' => env('ALPHA_VANTAGE_API_KEY'),
    ],

];
