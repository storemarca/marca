<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Country;
use App\Models\District;
use App\Models\Governorate;
use Illuminate\Database\Seeder;

class EgyptRegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التأكد من وجود مصر في جدول الدول
        $egypt = Country::where('code', 'EG')->first();
        
        if (!$egypt) {
            $egypt = Country::create([
                'name' => 'Egypt',
                'name_ar' => 'مصر',
                'code' => 'EG',
                'currency' => 'EGP',
                'currency_code' => 'EGP',
                'currency_symbol' => 'ج.م',
                'is_active' => true,
            ]);
        }
        
        // إضافة محافظات مصر
        $governorates = [
            [
                'name' => 'Cairo',
                'name_ar' => 'القاهرة',
                'code' => 'CAI',
                'shipping_cost' => 50.00,
                'districts' => [
                    [
                        'name' => 'Nasr City',
                        'name_ar' => 'مدينة نصر',
                        'code' => 'NSR',
                        'additional_shipping_cost' => 0,
                        'areas' => [
                            ['name' => 'Al Hay Al Asher', 'name_ar' => 'الحي العاشر', 'additional_shipping_cost' => 0],
                            ['name' => 'Al Hay Al Thamen', 'name_ar' => 'الحي الثامن', 'additional_shipping_cost' => 0],
                        ]
                    ],
                    [
                        'name' => 'Maadi',
                        'name_ar' => 'المعادي',
                        'code' => 'MAD',
                        'additional_shipping_cost' => 5.00,
                        'areas' => [
                            ['name' => 'Zahraa El Maadi', 'name_ar' => 'زهراء المعادي', 'additional_shipping_cost' => 0],
                            ['name' => 'Maadi Degla', 'name_ar' => 'المعادي دجلة', 'additional_shipping_cost' => 5.00],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Alexandria',
                'name_ar' => 'الإسكندرية',
                'code' => 'ALX',
                'shipping_cost' => 65.00,
                'districts' => [
                    [
                        'name' => 'Montaza',
                        'name_ar' => 'المنتزه',
                        'code' => 'MNT',
                        'additional_shipping_cost' => 0,
                        'areas' => [
                            ['name' => 'Miami', 'name_ar' => 'ميامي', 'additional_shipping_cost' => 0],
                            ['name' => 'Mandara', 'name_ar' => 'المندرة', 'additional_shipping_cost' => 0],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Menoufia',
                'name_ar' => 'المنوفية',
                'code' => 'MNF',
                'shipping_cost' => 70.00,
                'districts' => [
                    [
                        'name' => 'Shebin El Kom',
                        'name_ar' => 'شبين الكوم',
                        'code' => 'SBK',
                        'additional_shipping_cost' => 0,
                        'areas' => [
                            ['name' => 'Al Masalha', 'name_ar' => 'المصلحة', 'additional_shipping_cost' => 5.00],
                            ['name' => 'Downtown', 'name_ar' => 'وسط المدينة', 'additional_shipping_cost' => 0],
                        ]
                    ],
                    [
                        'name' => 'Ashmoun',
                        'name_ar' => 'أشمون',
                        'code' => 'ASH',
                        'additional_shipping_cost' => 10.00,
                        'areas' => [
                            ['name' => 'Ashmoun Center', 'name_ar' => 'مركز أشمون', 'additional_shipping_cost' => 0],
                            ['name' => 'Samadon', 'name_ar' => 'سمادون', 'additional_shipping_cost' => 5.00],
                        ]
                    ],
                ]
            ],
        ];
        
        // إضافة المحافظات والمراكز والقرى/المناطق
        foreach ($governorates as $governorateData) {
            $districts = $governorateData['districts'] ?? [];
            unset($governorateData['districts']);
            
            $governorate = Governorate::create(array_merge($governorateData, ['country_id' => $egypt->id]));
            
            foreach ($districts as $districtData) {
                $areas = $districtData['areas'] ?? [];
                unset($districtData['areas']);
                
                $district = District::create(array_merge($districtData, ['governorate_id' => $governorate->id]));
                
                foreach ($areas as $areaData) {
                    Area::create(array_merge($areaData, ['district_id' => $district->id]));
                }
            }
        }
    }
} 