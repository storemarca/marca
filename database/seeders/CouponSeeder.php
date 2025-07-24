<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a fixed amount coupon
        Coupon::create([
            'code' => 'WELCOME50',
            'type' => 'fixed',
            'value' => 50,
            'min_order_amount' => 200,
            'usage_limit' => 100,
            'user_usage_limit' => 1,
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(3),
            'description' => 'كوبون ترحيبي بخصم 50 ريال للطلبات التي تزيد عن 200 ريال',
        ]);
        
        // Create a percentage coupon
        Coupon::create([
            'code' => 'PERCENT20',
            'type' => 'percentage',
            'value' => 20,
            'min_order_amount' => 300,
            'max_discount_amount' => 100,
            'usage_limit' => 50,
            'user_usage_limit' => 1,
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(2),
            'description' => 'كوبون بخصم 20% للطلبات التي تزيد عن 300 ريال (الحد الأقصى للخصم 100 ريال)',
        ]);
        
        // Create a special occasion coupon
        Coupon::create([
            'code' => 'EID2023',
            'type' => 'percentage',
            'value' => 15,
            'min_order_amount' => 0,
            'max_discount_amount' => null,
            'usage_limit' => null,
            'user_usage_limit' => 2,
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addDays(14),
            'description' => 'كوبون خاص بمناسبة العيد بخصم 15% على جميع المنتجات',
        ]);
        
        // Create a free shipping coupon
        Coupon::create([
            'code' => 'FREESHIP',
            'type' => 'fixed',
            'value' => 30,
            'min_order_amount' => 250,
            'usage_limit' => 200,
            'user_usage_limit' => 1,
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(1),
            'description' => 'كوبون شحن مجاني للطلبات التي تزيد عن 250 ريال',
        ]);
    }
} 