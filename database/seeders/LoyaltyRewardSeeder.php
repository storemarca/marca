<?php

namespace Database\Seeders;

use App\Models\LoyaltyReward;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LoyaltyRewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // خصم ثابت
        LoyaltyReward::create([
            'name' => 'SAR 50 Discount',
            'name_ar' => 'خصم 50 ريال',
            'type' => LoyaltyReward::TYPE_DISCOUNT,
            'points_required' => 500,
            'reward_data' => [
                'discount_type' => 'fixed',
                'value' => 50,
            ],
            'description' => 'Get SAR 50 off your next order.',
            'description_ar' => 'احصل على خصم 50 ريال على طلبك التالي.',
            'is_active' => true,
            'stock' => null,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(6),
        ]);
        
        // خصم نسبة مئوية
        LoyaltyReward::create([
            'name' => '10% Discount',
            'name_ar' => 'خصم 10%',
            'type' => LoyaltyReward::TYPE_DISCOUNT,
            'points_required' => 750,
            'reward_data' => [
                'discount_type' => 'percentage',
                'value' => 10,
                'max_discount' => 100,
            ],
            'description' => 'Get 10% off your next order (maximum discount SAR 100).',
            'description_ar' => 'احصل على خصم 10% على طلبك التالي (الحد الأقصى للخصم 100 ريال).',
            'is_active' => true,
            'stock' => null,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(6),
        ]);
        
        // شحن مجاني
        LoyaltyReward::create([
            'name' => 'Free Shipping',
            'name_ar' => 'شحن مجاني',
            'type' => LoyaltyReward::TYPE_FREE_SHIPPING,
            'points_required' => 300,
            'reward_data' => [],
            'description' => 'Get free shipping on your next order.',
            'description_ar' => 'احصل على شحن مجاني على طلبك التالي.',
            'is_active' => true,
            'stock' => null,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(6),
        ]);
        
        // بطاقة هدية
        LoyaltyReward::create([
            'name' => 'SAR 100 Gift Card',
            'name_ar' => 'بطاقة هدية بقيمة 100 ريال',
            'type' => LoyaltyReward::TYPE_GIFT_CARD,
            'points_required' => 1000,
            'reward_data' => [
                'value' => 100,
            ],
            'description' => 'Get a SAR 100 gift card to use on any purchase.',
            'description_ar' => 'احصل على بطاقة هدية بقيمة 100 ريال للاستخدام في أي عملية شراء.',
            'is_active' => true,
            'stock' => 50,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(3),
        ]);
        
        // بطاقة هدية أكبر
        LoyaltyReward::create([
            'name' => 'SAR 500 Gift Card',
            'name_ar' => 'بطاقة هدية بقيمة 500 ريال',
            'type' => LoyaltyReward::TYPE_GIFT_CARD,
            'points_required' => 5000,
            'reward_data' => [
                'value' => 500,
            ],
            'description' => 'Get a SAR 500 gift card to use on any purchase.',
            'description_ar' => 'احصل على بطاقة هدية بقيمة 500 ريال للاستخدام في أي عملية شراء.',
            'is_active' => true,
            'stock' => 10,
            'starts_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMonths(3),
        ]);
    }
} 