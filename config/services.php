<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'microsoft' => [
        'client_id'     => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect'      => env('MICROSOFT_REDIRECT_URI')
    ],

    'vonage' => [

        'api_key'    => env('VONAGE_API_KEY', ''),
        'api_secret' => env('VONAGE_API_SECRET', '')
    ],
    'twilio' => [
        'account_sid' => env('TWILIO_SID', ''),
        'auth_token'  => env('TWILIO_AUTH_TOKEN', ''),
        'from_number' => env('TWILIO_PHONE_NUMBER', ''),
        'verify'      => true,
    ],
    'oursms' => [
        'username' => env('OURSMS_USERNAME', ''),
        'token'    => env('OURSMS_TOKEN', ''),
        'source'   => env('OURSMS_SOURCE', ''),
        'url'      => env('OURSMS_URL', 'https://api.oursms.com/api-a/msgs'),
    ],
    'msegat' => [
        'username'   => env('MSEGAT_USERNAME', ''),
        'apiKey'     => env('MSEGAT_API_KEY', ''),
        'userSender' => env('MSEGAT_USER_SENDER', ''),
        'url'        => env('MSEGAT_URL', 'https://www.msegat.com/gw/sendsms.php'),
    ],


    'hyperpay' => [
        'mode'                   => env('HYPERPAY_MODE', 'test'),
        'hyperpay_base_url'      => env('HYPERPAY_BASE_URL', "https://eu-test.oppwa.com"),
        'hyperpay_checkouts_url' => env('HYPERPAY_CHECKOUTS_URL', env('HYPERPAY_BASE_URL') . "/v1/checkouts"),
        'hyperpay_token'         => env('HYPERPAY_TOKEN'),
        'hyperpay_credit_id'     => env('HYPERPAY_CREDIT_ID'),
        'hyperpay_mada_id'       => env('HYPERPAY_MADA_ID'),
        'hyperpay_apple_id'      => env('HYPERPAY_APPLE_ID'),
        'hyperpay_currncy'       => env('HYPERPAY_CURRENCY', "SAR"),
        'hyper_url'              => env('HYPER_URL', 'https://eu-test.oppwa.com/v1'),
        'callback_url'           => env('HYPERPAY_CALLBACK_URL', 'successCallback'),

    ],

    'myfatoorah' => [
        'mode'        => env('MYFATOORAH_MODE', 'test'),
        'base_url'    => env('MYFATOORAH_BASE_URL', 'https://apitest.myfatoorah.com/v2/'),
        'api_key'     => env('MYFATOORAH_API_KEY', ''),
        'api_secret'  => env('MYFATOORAH_API_SECRET', ''),
        'success_url' => env('MYFATOORAH_SUCCESS_URL', ''),
        'error_url'   => env('MYFATOORAH_ERROR_URL', ''),
        'currency'    => env('MYFATOORAH_CURRENCY', ''),
    ],

    'urway' => [
        'base_url'     => env('URWAY_BASE_URL', 'https://payments-dev.urway-tech.com'),
        'username'     => env('URWAY_USER_NAME', ''),
        'terminal_id'  => env('URWAY_TERMINAL_ID', ''),
        'password'     => env('URWAY_PASSWORD', ''),
        'merchant_key' => env('URWAY_MERCHANT_SECRET_KEY', ''),
        'currency'     => env('URWAY_CURRENCY', ''),
        'callback_url' => env('URWAY_CALLBACK_URL', ''),
    ],

    'egrates' => [
        'token'      => env('EGRATES_TOKEN'),
        'base_url'   => env('EGRATES_BASE_URL', 'https://egrates.com/api/v1'),
        'currencies' => explode(',', env('EGRATES_CURRENCIES', 'USD,EUR,GBP,AED,SAR,JPY,CHF,CAD,AUD,CNY,KWD,BHD,OMR,QAR')),
    ],
    'exchangerates' => [
        'currencies' =>  ['USD', 'EUR', 'GBP', 'AED','SAR','JPY','CHF','CAD','AUD','CNY','KWD','BHD','OMR','QAR'],
        'token'      => env('EXCHANGERATES_TOKEN','HOj28lSGsuAhJnBYj9alfubaVHnBce0S'),
    ],

    'firebase' => [
        'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH', storage_path('firebase-service-account.json')),
        'project_id'           => env('FIREBASE_PROJECT_ID'),
        'messaging_sender_id'  => env('FIREBASE_MESSAGING_SENDER_ID'),
        'logging'              => [
            'enabled'      => env('FIREBASE_LOGGING_ENABLED', true),
            'log_success'  => env('FIREBASE_LOG_SUCCESS', true),
            'log_failures' => env('FIREBASE_LOG_FAILURES', true),
            'log_tokens'   => env('FIREBASE_LOG_TOKENS', false), // Set to true to log FCM tokens (be careful with privacy)
            'log_level'    => env('FIREBASE_LOG_LEVEL', 'info'), // debug, info, warning, error
        ],
    ],

    'goldpricez' => [
        'api_key'  => env('GOLDPRICEZ_API_KEY', '157a30cc5b78fbb2583023d79e81e07a157a30cc'),
        'base_url' => env('GOLDPRICEZ_BASE_URL', 'http://goldpricez.com/api/rates'),
    ],
];
