<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get countries for country-specific pricing
        $saudiArabia = Country::where('code', 'SA')->first();
        $uae = Country::where('code', 'AE')->first();
        $egypt = Country::where('code', 'EG')->first();
        
        // Standard Shipping
        $standard = ShippingMethod::create([
            'name' => 'الشحن القياسي',
            'code' => 'standard',
            'base_cost' => 30.00,
            'is_active' => true,
            'description' => 'شحن قياسي خلال 3-5 أيام عمل',
            'weight_based' => false,
            'cost_per_kg' => 0,
            'free_shipping_threshold' => 0,
        ]);
        
        // Add country-specific pricing for standard shipping
        if ($saudiArabia) {
            $standard->countries()->attach($saudiArabia->id, [
                'cost' => 25.00,
                'is_available' => true,
            ]);
        }
        
        if ($uae) {
            $standard->countries()->attach($uae->id, [
                'cost' => 35.00,
                'is_available' => true,
            ]);
        }
        
        if ($egypt) {
            $standard->countries()->attach($egypt->id, [
                'cost' => 45.00,
                'is_available' => true,
            ]);
        }
        
        // Express Shipping
        $express = ShippingMethod::create([
            'name' => 'الشحن السريع',
            'code' => 'express',
            'base_cost' => 60.00,
            'is_active' => true,
            'description' => 'شحن سريع خلال 1-2 يوم عمل',
            'weight_based' => true,
            'cost_per_kg' => 5.00,
            'free_shipping_threshold' => 0,
        ]);
        
        // Add country-specific pricing for express shipping
        if ($saudiArabia) {
            $express->countries()->attach($saudiArabia->id, [
                'cost' => 50.00,
                'is_available' => true,
            ]);
        }
        
        if ($uae) {
            $express->countries()->attach($uae->id, [
                'cost' => 70.00,
                'is_available' => true,
            ]);
        }
        
        if ($egypt) {
            $express->countries()->attach($egypt->id, [
                'cost' => 90.00,
                'is_available' => true,
            ]);
        }
        
        // Free Shipping
        $free = ShippingMethod::create([
            'name' => 'شحن مجاني',
            'code' => 'free',
            'base_cost' => 0.00,
            'is_active' => true,
            'description' => 'شحن مجاني للطلبات أكثر من 300 ريال (5-7 أيام عمل)',
            'weight_based' => false,
            'cost_per_kg' => 0,
            'free_shipping_threshold' => 300.00,
        ]);
        
        // Add country-specific availability for free shipping
        if ($saudiArabia) {
            $free->countries()->attach($saudiArabia->id, [
                'cost' => 0.00,
                'is_available' => true,
            ]);
        }
        
        if ($uae) {
            $free->countries()->attach($uae->id, [
                'cost' => 0.00,
                'is_available' => true,
            ]);
        }
        
        // Free shipping not available in Egypt
        if ($egypt) {
            $free->countries()->attach($egypt->id, [
                'cost' => 0.00,
                'is_available' => false,
            ]);
        }
    }
} 