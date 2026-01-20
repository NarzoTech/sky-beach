<?php

return [
    /*
    |--------------------------------------------------------------------------
    | bKash Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your bKash payment gateway credentials here.
    | Get your credentials from bKash Merchant Portal.
    |
    */

    'sandbox' => env('BKASH_SANDBOX', true),

    // Sandbox credentials
    'sandbox_app_key' => env('BKASH_SANDBOX_APP_KEY', ''),
    'sandbox_app_secret' => env('BKASH_SANDBOX_APP_SECRET', ''),
    'sandbox_username' => env('BKASH_SANDBOX_USERNAME', ''),
    'sandbox_password' => env('BKASH_SANDBOX_PASSWORD', ''),

    // Production credentials
    'app_key' => env('BKASH_APP_KEY', ''),
    'app_secret' => env('BKASH_APP_SECRET', ''),
    'username' => env('BKASH_USERNAME', ''),
    'password' => env('BKASH_PASSWORD', ''),

    // API URLs
    'sandbox_base_url' => 'https://tokenized.sandbox.bka.sh/v1.2.0-beta',
    'production_base_url' => 'https://tokenized.pay.bka.sh/v1.2.0-beta',

    // Currency
    'currency' => 'BDT',
];
