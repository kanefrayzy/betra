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
        'base_url' => env('B2B_SLOTS_BASE_URL', 'https://int.apichannel.cloud'),
        'operator_id' => env('B2B_SLOTS_OPERATOR_ID'),
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
    ],
    'aes' => [
        'base_url' => env('AES_BASE_URL'),

        'accounts' => [
            'RUB' => [
                'api_token' => env('AES_API_TOKEN_RUB'),
                'callback_token' => env('AES_CALLBACK_TOKEN_RUB'),
            ],
            'USD' => [
                'api_token' => env('AES_API_TOKEN_USD'),
                'callback_token' => env('AES_CALLBACK_TOKEN_USD'),
            ],
            'AZN' => [
                'api_token' => env('AES_API_TOKEN_AZN'),
                'callback_token' => env('AES_CALLBACK_TOKEN_AZN'),
            ],
        ],
    ],

    'betvio' => [
        /**
         * Base URL Betvio API
         */
        'base_url' => env('BETVIO_BASE_URL', 'https://api.betvio.com'),

        /**
         * Callback URL
         */
        'callback_url' => env('APP_URL') . '/gold_api',

        'accounts' => [
            'USD' => [
                'agent_code' => env('BETVIO_USD_AGENT_CODE'),
                'agent_token' => env('BETVIO_USD_AGENT_TOKEN'),
                'agent_secret' => env('BETVIO_USD_AGENT_SECRET'),
            ],
            'EUR' => [
                'agent_code' => env('BETVIO_EUR_AGENT_CODE'),
                'agent_token' => env('BETVIO_EUR_AGENT_TOKEN'),
                'agent_secret' => env('BETVIO_EUR_AGENT_SECRET'),
            ],
            'KZT' => [
                'agent_code' => env('BETVIO_KZT_AGENT_CODE'),
                'agent_token' => env('BETVIO_KZT_AGENT_TOKEN'),
                'agent_secret' => env('BETVIO_KZT_AGENT_SECRET'),
            ],
            'RUB' => [
                'agent_code' => env('BETVIO_RUB_AGENT_CODE'),
                'agent_token' => env('BETVIO_RUB_AGENT_TOKEN'),
                'agent_secret' => env('BETVIO_RUB_AGENT_SECRET'),
            ],
            'TRY' => [
                'agent_code' => env('BETVIO_TRY_AGENT_CODE'),
                'agent_token' => env('BETVIO_TRY_AGENT_TOKEN'),
                'agent_secret' => env('BETVIO_TRY_AGENT_SECRET'),
            ],
            'AZN' => [
                'agent_code' => env('BETVIO_AZN_AGENT_CODE'),
                'agent_token' => env('BETVIO_AZN_AGENT_TOKEN'),
                'agent_secret' => env('BETVIO_AZN_AGENT_SECRET'),
            ],
            'UZS' => [
                'agent_code' => env('BETVIO_UZS_AGENT_CODE'),
                'agent_token' => env('BETVIO_UZS_AGENT_TOKEN'),
                'agent_secret' => env('BETVIO_UZS_AGENT_SECRET'),
            ],
            'PLN' => [
                'agent_code' => env('BETVIO_PLN_AGENT_CODE'),
                'agent_token' => env('BETVIO_PLN_AGENT_TOKEN'),
                'agent_secret' => env('BETVIO_PLN_AGENT_SECRET'),
            ],            

        ]
    ],    
];
