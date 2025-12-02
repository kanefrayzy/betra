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


    'streampay' => [
        'base_url' => env('STREAMPAY_BASE_URL', 'https://api.streampay.org'),
        'public_key' => env('STREAMPAY_PUBLIC_KEY'),
        'primary_key' => env('STREAMPAY_PRIMARY_KEY'),
        'store_id' => env('STREAMPAY_STORE_ID', 405),
    ],

    'betatransfer' => [
        'public_key' => env('BETATRANSFER_PUBLIC_KEY'),
        'secret_key' => env('BETATRANSFER_SECRET_KEY'),
    ],

    'westwallet' => [
        'api_url' => env('WESTWALLET_API_URL', 'https://api.westwallet.io'),
        'public_key' => env('WESTWALLET_PUBLIC_KEY'),
        'private_key' => env('WESTWALLET_PRIVATE_KEY'),
        'trusted_ips' => explode(',', env('WESTWALLET_TRUSTED_IPS', '5.188.51.47')),
        'skip_ip_check' => env('WESTWALLET_SKIP_IP_CHECK', false),
    ],
];
