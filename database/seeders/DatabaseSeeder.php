<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // استدعاء بذرة الدول أولاً
        $this->call(CountrySeeder::class);
        
        // استدعاء بذرة المستخدمين
        $this->call(UserSeeder::class);
        
        // استدعاء بذرة الأدوار والصلاحيات
        $this->call(RoleAndPermissionSeeder::class);
        
        // استدعاء بذرة المستخدمين الإداريين
        $this->call(AdminUserSeeder::class);
        
        // استدعاء بذرة المستخدمين الإداريين الجديدة
        $this->call(NewAdminSeeder::class);

        $saudiArabia = Country::where('code', 'SA')->first();
        $uae = Country::where('code', 'AE')->first();
        $egypt = Country::where('code', 'EG')->first();

        // الشحن القياسي
        $standard = ShippingMethod::updateOrCreate(
            ['code' => 'standard'],
            [
                'name' => 'الشحن القياسي',
                'base_cost' => 30.00,
                'is_active' => true,
                'description' => 'شحن قياسي خلال 3-5 أيام عمل',
                'weight_based' => false,
                'cost_per_kg' => 0,
                'free_shipping_threshold' => 0,
            ]
        );

        $this->attachPricing($standard, $saudiArabia, 25.00);
        $this->attachPricing($standard, $uae, 35.00);
        $this->attachPricing($standard, $egypt, 45.00);

        // الشحن السريع
        $express = ShippingMethod::updateOrCreate(
            ['code' => 'express'],
            [
                'name' => 'الشحن السريع',
                'base_cost' => 60.00,
                'is_active' => true,
                'description' => 'شحن سريع خلال 1-2 يوم عمل',
                'weight_based' => true,
                'cost_per_kg' => 5.00,
                'free_shipping_threshold' => 0,
            ]
        );

        $this->attachPricing($express, $saudiArabia, 50.00);
        $this->attachPricing($express, $uae, 70.00);
        $this->attachPricing($express, $egypt, 90.00);

        // الشحن المجاني
        $free = ShippingMethod::updateOrCreate(
            ['code' => 'free'],
            [
                'name' => 'شحن مجاني',
                'base_cost' => 0.00,
                'is_active' => true,
                'description' => 'شحن مجاني للطلبات أكثر من 300 ريال (5-7 أيام عمل)',
                'weight_based' => false,
                'cost_per_kg' => 0,
                'free_shipping_threshold' => 300.00,
            ]
        );

        $this->attachPricing($free, $saudiArabia, 0.00);
        $this->attachPricing($free, $uae, 0.00);
        $this->attachPricing($free, $egypt, 0.00, false); // غير متاح في مصر
    }

    /**
     * Attach country pricing to a shipping method.
     */
    private function attachPricing(ShippingMethod $method, ?Country $country, float $cost, bool $isAvailable = true): void
    {
        if ($country) {
            $method->countries()->syncWithoutDetaching([
                $country->getKey() => [
                    'cost' => $cost,
                    'is_available' => $isAvailable,
                ],
            ]);
        }
    }
}
