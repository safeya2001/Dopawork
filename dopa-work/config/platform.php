<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dopa Work Platform Configuration
    |--------------------------------------------------------------------------
    */

    'name' => env('APP_NAME', 'Dopa Work'),
    'name_ar' => env('PLATFORM_NAME_AR', 'دوبا وورك'),

    'currency' => env('PLATFORM_CURRENCY', 'JOD'),
    'currency_symbol' => 'د.أ',
    'currency_decimals' => 3,

    'fee_percentage' => env('PLATFORM_FEE_PERCENTAGE', 15),

    'escrow_release_days' => env('ESCROW_RELEASE_DAYS', 7),

    'contact' => [
        'email' => env('PLATFORM_EMAIL', 'support@dopawork.jo'),
        'phone' => env('PLATFORM_PHONE', '+962791234567'),
        'country' => 'JO',
        'timezone' => 'Asia/Amman',
    ],

    'payment_methods' => [
        'stripe' => env('STRIPE_KEY') ? true : false,
        'cliq' => true,
        'bank_transfer' => true,
        'wallet' => true,
    ],

    'cliq' => [
        'alias' => env('CLIQ_ALIAS', 'dopawork'),
        'bank_account' => env('CLIQ_BANK_ACCOUNT'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'identity' => [
        'required' => true,
        'allowed_types' => ['national_id', 'freelancer_permit', 'passport', 'residency_permit'],
        'review_sla_hours' => 48,
    ],

    'service' => [
        'min_price' => 5.000, // JOD
        'max_price' => 50000.000, // JOD
        'max_delivery_days' => 90,
        'max_gallery_images' => 8,
    ],
];
