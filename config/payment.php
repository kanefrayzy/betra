<?php

return [
    'paykassa' => [
        'url' => env('PAYKASSA_URL', 'https://paykassa.app/sci/0.4/index.php'),
        'merchant_id' => env('PAYKASSA_MERCHANT_ID'),
        'merchant_key' => env('PAYKASSA_MERCHANT_KEY'),
    ],
    'freekassa' => [
        'url' => env('FREEKASSA_URL'),
        'merchant_id' => env('FREEKASSA_MERCHANT_ID'),
        'secret_key' => env('FREEKASSA_SECRET_KEY'),
    ],

    'payteez' => [
        'shop_id' => env('PAYTEEZ_SHOP_ID'),
        'secret_key' => env('PAYTEEZ_SECRET_KEY'),
    ],

    'streampay' => [
        'base_url' => env('STREAMPAY_BASE_URL', 'https://api.streampay.org'),
        'public_key' => env('STREAMPAY_PUBLIC_KEY'),
        'primary_key' => env('STREAMPAY_PRIMARY_KEY'),
        'store_id' => env('STREAMPAY_STORE_ID', 405),
    ],

    'betatransfer' => [
        'public_key' => env('BETATRANSFER_PUBLIC_KEY'),
        'secret_key' => env('BETATRANSFER_SECRET_KEY'),
    ]
];
