<?php

declare(strict_types=1);

return [
    'sb' => [
        'merchant_id' => env('MIDTRANS_SB_MERCHANT_ID'),
        'client_key' => env('MIDTRANS_SB_CLIENT_KEY'),
        'server_key' => env('MIDTRANS_SB_SERVER_KEY'),
        'snap_frontend_url' => 'https://app.sandbox.midtrans.com/snap/snap.js',
    ],
    'production' => [
        'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'snap_url' => 'https://app.midtrans.com/snap/snap.js',
    ],
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', false),
];
