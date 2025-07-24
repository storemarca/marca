<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Stripe Payment Gateway
        PaymentGateway::create([
            'name' => 'Stripe',
            'code' => 'stripe',
            'description' => 'Pay securely using your credit card via Stripe.',
            'logo' => 'payment/stripe.svg',
            'is_active' => true,
            'is_default' => true,
            'fee_percentage' => 2.9,
            'fee_fixed' => 0.30,
            'config' => [
                'api_key' => env('STRIPE_SECRET_KEY', ''),
                'publishable_key' => env('STRIPE_PUBLISHABLE_KEY', ''),
                'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
            ],
        ]);

        // PayPal Payment Gateway
        PaymentGateway::create([
            'name' => 'PayPal',
            'code' => 'paypal',
            'description' => 'ادفع بأمان باستخدام حساب PayPal الخاص بك.',
            'logo' => 'payment/paypal.svg',
            'is_active' => true,
            'is_default' => false,
            'fee_percentage' => 3.5,
            'fee_fixed' => 0.30,
            'config' => [
                'client_id' => env('PAYPAL_CLIENT_ID', ''),
                'secret' => env('PAYPAL_SECRET', ''),
                'mode' => env('PAYPAL_MODE', 'sandbox'),
            ],
        ]);

        // Cash on Delivery
        PaymentGateway::create([
            'name' => 'الدفع عند الاستلام',
            'code' => 'cash',
            'description' => 'ادفع نقدًا عند استلام طلبك.',
            'logo' => 'payment/cod.svg',
            'is_active' => true,
            'is_default' => false,
            'fee_percentage' => 0,
            'fee_fixed' => 0,
            'config' => [],
        ]);
        
        // Mada Payment Gateway
        PaymentGateway::create([
            'name' => 'مدى',
            'code' => 'mada',
            'description' => 'ادفع بسهولة باستخدام بطاقة مدى الخاصة بك.',
            'logo' => 'payment/mada.svg',
            'is_active' => true,
            'is_default' => false,
            'fee_percentage' => 1.5,
            'fee_fixed' => 0.0,
            'config' => [
                'api_key' => env('MADA_API_KEY', ''),
                'secret' => env('MADA_SECRET', ''),
                'sandbox' => env('MADA_SANDBOX', true),
            ],
        ]);
        
        // Apple Pay
        PaymentGateway::create([
            'name' => 'Apple Pay',
            'code' => 'apple_pay',
            'description' => 'ادفع بسرعة وأمان باستخدام Apple Pay.',
            'logo' => 'payment/apple-pay.svg',
            'is_active' => true,
            'is_default' => false,
            'fee_percentage' => 2.0,
            'fee_fixed' => 0.0,
            'config' => [
                'merchant_id' => env('APPLE_PAY_MERCHANT_ID', ''),
                'certificate_path' => env('APPLE_PAY_CERTIFICATE_PATH', ''),
                'private_key_path' => env('APPLE_PAY_PRIVATE_KEY_PATH', ''),
                'sandbox' => env('APPLE_PAY_SANDBOX', true),
            ],
        ]);
        
        // Bank Transfer
        PaymentGateway::create([
            'name' => 'تحويل بنكي',
            'code' => 'bank_transfer',
            'description' => 'ادفع عن طريق التحويل البنكي المباشر.',
            'logo' => 'payment/bank-transfer.svg',
            'is_active' => true,
            'is_default' => false,
            'fee_percentage' => 0,
            'fee_fixed' => 0,
            'config' => [
                'bank_details' => [
                    'bank_name' => 'البنك الأهلي السعودي',
                    'account_name' => 'شركة ماركا',
                    'account_number' => 'SA0380000000608010167519',
                    'swift_code' => 'NCBKSAJE',
                ],
            ],
        ]);
    }
} 