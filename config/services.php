<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => \App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'slotegrator' => [
        'merchant_key' => env('SLOTEGRATOR_API_KEY'),
        'merchant_id' => env('SLOTEGRATOR_API_ID'),
        'base_url' => env('SLOTEGRATOR_API_URL'),
        'callback_url' => env('SLOTEGRATOR_CALLBACK_URL'),
    ],

    'b2b_slots' => [
        'api_url' => env('B2B_API_URL'),
        'api_key' => env('B2B_API_KEY'),
        'partner_id' => env('B2B_PARTNER_ID'),
    ],
    'tbs2' => [
        'base_url' => env('TBS2_BASE_URL'),
        'hall_id' => env('TBS2_HALL_ID'),
        'hall_key' => env('TBS2_HALL_KEY'),
    ],
    'ulogin' => [
        'endpoint' => env('ULOGIN_ENDPOINT'),
    ],

    'recaptcha' => [
        'site_key' => env('NOCAPTCHA_SITEKEY'),
        'secret_key' => env('NOCAPTCHA_SECRET'),
    ] 
];
