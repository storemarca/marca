<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إضافة السعودية
        Country::updateOrCreate(
            ['code' => 'SA'],
            [
                'name' => 'المملكة العربية السعودية',
                'currency_code' => 'SAR',
                'currency_symbol' => 'ر.س',
                'tax_rate' => 15.00,
                'is_active' => true,
            ]
        );

        // إضافة مصر
        Country::updateOrCreate(
            ['code' => 'EG'],
            [
                'name' => 'مصر',
                'currency_code' => 'EGP',
                'currency_symbol' => 'ج.م',
                'tax_rate' => 14.00,
                'is_active' => true,
            ]
        );

        // إضافة الإمارات العربية المتحدة
        Country::updateOrCreate(
            ['code' => 'AE'],
            [
                'name' => 'الإمارات العربية المتحدة',
                'currency_code' => 'AED',
                'currency_symbol' => 'د.إ',
                'tax_rate' => 5.00,
                'is_active' => true,
            ]
        );
    }
} 