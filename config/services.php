<?php

declare(strict_types=1);

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

    'yookassa' => [
        'base_url' => env('YOOKASSA_BASE_URL', 'https://api.yookassa.ru/v3'),
        'shop_id' => env('YOOKASSA_SHOP_ID'),
        'secret_key' => env('YOOKASSA_SECRET_KEY'),
        'currency' => env('YOOKASSA_CURRENCY', 'RUB'),
        'timeout' => env('YOOKASSA_TIMEOUT', 15),

        'receipts' => [
            'enabled' => env('YOOKASSA_RECEIPTS_ENABLED', true),
            'mode' => env('YOOKASSA_RECEIPTS_MODE', 'embedded'),
            'vat_code' => env('YOOKASSA_RECEIPT_VAT_CODE', 1),
            'payment_mode' => env('YOOKASSA_RECEIPT_PAYMENT_MODE', 'full_payment'),
            'payment_subject' => env('YOOKASSA_RECEIPT_PAYMENT_SUBJECT', 'commodity'),
            'tax_system_code' => env('YOOKASSA_RECEIPT_TAX_SYSTEM_CODE', 1),
            'settlement_type' => env('YOOKASSA_RECEIPT_SETTLEMENT_TYPE', 'cashless'),
        ],
    ],

];
