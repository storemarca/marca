<?php

namespace Database\Seeders;

use App\Models\Affiliate;
use App\Models\AffiliateLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class AffiliateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مسوقين بالعمولة
        $users = User::where('email', 'like', '%@example.com')->take(5)->get();
        
        foreach ($users as $index => $user) {
            // إنشاء حساب مسوق بالعمولة
            $affiliate = Affiliate::create([
                'user_id' => $user->id,
                'code' => 'AFF' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'status' => $index < 3 ? Affiliate::STATUS_APPROVED : Affiliate::STATUS_PENDING,
                'commission_rate' => rand(5, 15),
                'balance' => $index < 3 ? rand(100, 1000) : 0,
                'lifetime_earnings' => $index < 3 ? rand(1000, 5000) : 0,
                'website' => 'https://example.com/affiliate' . ($index + 1),
                'social_media' => 'instagram: affiliate' . ($index + 1),
                'marketing_methods' => 'Social media marketing, Email marketing, Content marketing',
                'payment_details' => json_encode([
                    'method' => 'bank_transfer',
                    'details' => 'Bank: Example Bank, Account: 123456789',
                ]),
                'approved_at' => $index < 3 ? now()->subDays(rand(1, 30)) : null,
            ]);
            
            // إنشاء روابط تسويقية للمسوقين المعتمدين
            if ($index < 3) {
                // رابط لمنتج
                AffiliateLink::create([
                    'affiliate_id' => $affiliate->id,
                    'name' => 'Product Link ' . ($index + 1),
                    'slug' => 'product-link-' . ($index + 1),
                    'target_type' => AffiliateLink::TARGET_TYPE_PRODUCT,
                    'target_id' => rand(1, 10),
                    'clicks' => rand(50, 200),
                    'conversions' => rand(5, 20),
                    'earnings' => rand(50, 500),
                    'is_active' => true,
                ]);
                
                // رابط لتصنيف
                AffiliateLink::create([
                    'affiliate_id' => $affiliate->id,
                    'name' => 'Category Link ' . ($index + 1),
                    'slug' => 'category-link-' . ($index + 1),
                    'target_type' => AffiliateLink::TARGET_TYPE_CATEGORY,
                    'target_id' => rand(1, 5),
                    'clicks' => rand(30, 150),
                    'conversions' => rand(3, 15),
                    'earnings' => rand(30, 300),
                    'is_active' => true,
                ]);
                
                // رابط مخصص
                AffiliateLink::create([
                    'affiliate_id' => $affiliate->id,
                    'name' => 'Custom Link ' . ($index + 1),
                    'slug' => 'custom-link-' . ($index + 1),
                    'target_type' => AffiliateLink::TARGET_TYPE_CUSTOM,
                    'custom_url' => 'https://example.com/special-offer',
                    'clicks' => rand(20, 100),
                    'conversions' => rand(2, 10),
                    'earnings' => rand(20, 200),
                    'is_active' => true,
                ]);
            }
        }
    }
} 