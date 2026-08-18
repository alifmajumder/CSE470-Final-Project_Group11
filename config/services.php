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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    // --------------------------------------------------------------------------
    // Feature 2: SMS Gateway Configuration
    // --------------------------------------------------------------------------
    // driver options: ssl | gp | twilio | log
    //   ssl    — SSL Wireless (Bangladesh, recommended for production)
    //   gp     — Grameenphone BSMS
    //   twilio — Twilio (international / fallback)
    //   log    — Writes to Laravel log only (local dev / testing)
    // --------------------------------------------------------------------------
    'sms' => [
        'driver'   => env('SMS_DRIVER', 'log'),
        'helpline' => env('SMS_HELPLINE', '16XXX'),

        // Webhook security: set a shared secret and pass it as X-Webhook-Token
        'webhook_secret' => env('WEBHOOK_SECRET'),

        // SSL Wireless (https://www.sslwireless.com/sms-api)
        'ssl_api_token' => env('SSL_SMS_API_TOKEN'),
        'ssl_sid'       => env('SSL_SMS_SID'),

        // Grameenphone BSMS
        'gp_api_url'  => env('GP_SMS_API_URL', 'https://bsms.grameenphone.com/api/v2/sms/send'),
        'gp_username' => env('GP_SMS_USERNAME'),
        'gp_password' => env('GP_SMS_PASSWORD'),

        // Twilio
        'twilio_sid'   => env('TWILIO_ACCOUNT_SID'),
        'twilio_token' => env('TWILIO_AUTH_TOKEN'),

        // Sender / virtual DID (used by all drivers)
        'from_number' => env('SMS_FROM_NUMBER'),
    ],

];
