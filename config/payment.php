<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Settings
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for all payment gateways
    | used in the application. Instead of using env() directly in the code,
    | we centralize all payment configurations here for better security
    | and easier management.
    |
    */

    'stripe' => [
        'key' => env('STRIPE_KEY', ''),
        'secret' => env('STRIPE_SECRET', ''),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
        'currency' => env('STRIPE_CURRENCY', 'USD'),
        'sandbox' => env('STRIPE_SANDBOX', true),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', ''),
        'secret' => env('PAYPAL_SECRET', ''),
        'currency' => env('PAYPAL_CURRENCY', 'USD'),
        'sandbox' => env('PAYPAL_SANDBOX', true),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID', ''),
    ],
    
    'mada' => [
        'api_key' => env('MADA_API_KEY', ''),
        'secret' => env('MADA_SECRET', ''),
        'currency' => env('MADA_CURRENCY', 'SAR'),
        'sandbox' => env('MADA_SANDBOX', true),
    ],
    
    'apple_pay' => [
        'merchant_id' => env('APPLE_PAY_MERCHANT_ID', ''),
        'certificate_path' => env('APPLE_PAY_CERTIFICATE_PATH', ''),
        'private_key_path' => env('APPLE_PAY_PRIVATE_KEY_PATH', ''),
        'currency' => env('APPLE_PAY_CURRENCY', 'SAR'),
        'sandbox' => env('APPLE_PAY_SANDBOX', true),
    ],
    
    'bank_transfer' => [
        'bank_details' => [
            'bank_name' => env('BANK_NAME', 'البنك الأهلي السعودي'),
            'account_name' => env('BANK_ACCOUNT_NAME', 'شركة ماركا'),
            'account_number' => env('BANK_ACCOUNT_NUMBER', 'SA0380000000608010167519'),
            'swift_code' => env('BANK_SWIFT_CODE', 'NCBKSAJE'),
        ],
    ],

    'default_currency' => env('DEFAULT_CURRENCY', 'USD'),
    
    'currencies' => [
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'decimal_places' => 2,
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'decimal_places' => 2,
        ],
        'SAR' => [
            'name' => 'Saudi Riyal',
            'symbol' => 'ر.س',
            'decimal_places' => 2,
        ],
        'AED' => [
            'name' => 'UAE Dirham',
            'symbol' => 'د.إ',
            'decimal_places' => 2,
        ],
        'EGP' => [
            'name' => 'Egyptian Pound',
            'symbol' => 'ج.م',
            'decimal_places' => 2,
        ],
    ],
    
    'payment_gateways' => [
        'stripe' => [
            'class' => \App\Services\Payments\StripePaymentGateway::class,
            'name' => 'Stripe',
        ],
        'paypal' => [
            'class' => \App\Services\Payments\PayPalPaymentGateway::class,
            'name' => 'PayPal',
        ],
        'cash' => [
            'class' => \App\Services\Payments\CashPaymentGateway::class,
            'name' => 'الدفع عند الاستلام',
        ],
        'mada' => [
            'class' => \App\Services\Payments\MadaPaymentGateway::class,
            'name' => 'مدى',
        ],
        'apple_pay' => [
            'class' => \App\Services\Payments\ApplePayPaymentGateway::class,
            'name' => 'Apple Pay',
        ],
        'bank_transfer' => [
            'class' => \App\Services\Payments\BankTransferPaymentGateway::class,
            'name' => 'تحويل بنكي',
        ],
    ],
];