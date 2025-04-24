<?php

return [
    'merchant_code' => env('TRIPAY_MERCHANT_CODE'),
    'mode' => env('TRIPAY_MODE', false),
    'sandbox' => [
        'endpoint' => env('TRIPAY_SANDBOX_ENDPOINT', 'https://api.tripay.co.id/v2'),
        'api_key' => env('TRIPAY_SANDBOX_API_KEY'),
        'private_key' => env('TRIPAY_SANDBOX_PRIVATE_KEY'),
        'instruksi_pembayaran' => env('TRIPAY_SANDBOX_INSTRUCTION', 'https://tripay.co.id/api-sandbox/payment/instruction'),
    ],
    'production' => [
        'endpoint' => env('TRIPAY_PRODUCTION_ENDPOINT', 'https://api.tripay.co.id/v2'),
        'api_key' => env('TRIPAY_PRODUCTION_API_KEY'),
        'private_key' => env('TRIPAY_PRODUCTION_PRIVATE_KEY'),
        'instruksi_pembayaran' => env('TRIPAY_PRODUCTION_INSTRUCTION', 'https://tripay.co.id/api/payment/instruction'),
    ],
    'version' => env('TRIPAY_VERSION', '2021-08-18'),
    'currency' => env('TRIPAY_CURRENCY', 'IDR'),
    'currency_code' => env('TRIPAY_CURRENCY_CODE', 'IDR'),
    'currency_symbol' => env('TRIPAY_CURRENCY_SYMBOL', 'Rp'),
    'currency_rate' => env('TRIPAY_CURRENCY_RATE', 1),
    'timeout' => 30,
    'guzzle_options' => [],
];
