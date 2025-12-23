<?php

return [
    'currency' => 'IRR',
    'callback-url' => env('PAYMENT_CALLBACK_URL', env('APP_URL').'/payment/callback'),
    'merchant-id' => env('ZARINPAL_MERCHANT_ID', 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'),
    'default-gateway' => env('PAYMENT_GATEWAY', 'zarinpal'),
    'gates' => [
        'zarinpal' => [
            'merchant-id' => env('ZARINPAL_MERCHANT_ID', 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'),
            'callback-url' => env('PAYMENT_CALLBACK_URL', env('APP_URL').'/payment/callback'),
        ],
    ],
];

