<?php

namespace Database\Seeders;

use App\Models\LoyaltyTier;
use Illuminate\Database\Seeder;

class LoyaltyTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // المستوى البرونزي
        LoyaltyTier::create([
            'name' => 'Bronze',
            'name_ar' => 'برونزي',
            'required_points' => 0,
            'discount_percentage' => 0,
            'free_shipping' => false,
            'points_multiplier' => 1,
            'description' => 'Entry level tier with basic benefits.',
            'description_ar' => 'المستوى الأساسي مع المزايا الأساسية.',
            'is_active' => true,
        ]);
        
        // المستوى الفضي
        LoyaltyTier::create([
            'name' => 'Silver',
            'name_ar' => 'فضي',
            'required_points' => 1000,
            'discount_percentage' => 2,
            'free_shipping' => false,
            'points_multiplier' => 1.2,
            'description' => 'Silver tier with 2% discount and 1.2x points multiplier.',
            'description_ar' => 'المستوى الفضي مع خصم 2٪ ومضاعف نقاط 1.2x.',
            'is_active' => true,
        ]);
        
        // المستوى الذهبي
        LoyaltyTier::create([
            'name' => 'Gold',
            'name_ar' => 'ذهبي',
            'required_points' => 5000,
            'discount_percentage' => 5,
            'free_shipping' => false,
            'points_multiplier' => 1.5,
            'description' => 'Gold tier with 5% discount and 1.5x points multiplier.',
            'description_ar' => 'المستوى الذهبي مع خصم 5٪ ومضاعف نقاط 1.5x.',
            'is_active' => true,
        ]);
        
        // المستوى البلاتيني
        LoyaltyTier::create([
            'name' => 'Platinum',
            'name_ar' => 'بلاتيني',
            'required_points' => 10000,
            'discount_percentage' => 7,
            'free_shipping' => true,
            'points_multiplier' => 2,
            'description' => 'Platinum tier with 7% discount, free shipping, and 2x points multiplier.',
            'description_ar' => 'المستوى البلاتيني مع خصم 7٪ وشحن مجاني ومضاعف نقاط 2x.',
            'is_active' => true,
        ]);
        
        // المستوى الماسي
        LoyaltyTier::create([
            'name' => 'Diamond',
            'name_ar' => 'ماسي',
            'required_points' => 25000,
            'discount_percentage' => 10,
            'free_shipping' => true,
            'points_multiplier' => 3,
            'description' => 'Diamond tier with 10% discount, free shipping, and 3x points multiplier.',
            'description_ar' => 'المستوى الماسي مع خصم 10٪ وشحن مجاني ومضاعف نقاط 3x.',
            'is_active' => true,
        ]);
    }
} 