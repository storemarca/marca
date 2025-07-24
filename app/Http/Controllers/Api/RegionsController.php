<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Country;
use App\Models\District;
use App\Models\Governorate;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    /**
     * الحصول على قائمة الدول
     */
    public function getCountries()
    {
        $countries = Country::where('is_active', true)->get(['id', 'name', 'name_ar', 'code', 'currency_symbol', 'currency_code']);
        
        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }
    
    /**
     * الحصول على معلومات دولة محددة
     */
    public function getCountry($id)
    {
        $country = Country::where('is_active', true)->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $country
        ]);
    }

    /**
     * الحصول على قائمة المحافظات حسب الدولة
     */
    public function getGovernorates($countryId)
    {
        $governorates = Governorate::where('country_id', $countryId)
            ->where('is_active', true)
            ->get(['id', 'name', 'name_ar', 'code', 'shipping_cost']);
        
        return response()->json([
            'success' => true,
            'data' => $governorates
        ]);
    }
    
    /**
     * الحصول على معلومات محافظة محددة
     */
    public function getGovernorate($id)
    {
        $governorate = Governorate::where('is_active', true)->with('country')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $governorate
        ]);
    }

    /**
     * الحصول على قائمة المراكز حسب المحافظة
     */
    public function getDistricts($governorateId)
    {
        $districts = District::where('governorate_id', $governorateId)
            ->where('is_active', true)
            ->get(['id', 'name', 'name_ar', 'code', 'additional_shipping_cost']);
        
        return response()->json([
            'success' => true,
            'data' => $districts
        ]);
    }
    
    /**
     * الحصول على معلومات مركز محدد
     */
    public function getDistrict($id)
    {
        $district = District::where('is_active', true)->with('governorate')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $district
        ]);
    }

    /**
     * الحصول على قائمة المناطق حسب المركز
     */
    public function getAreas($districtId)
    {
        $areas = Area::where('district_id', $districtId)
            ->where('is_active', true)
            ->get(['id', 'name', 'name_ar', 'code', 'additional_shipping_cost']);
        
        return response()->json([
            'success' => true,
            'data' => $areas
        ]);
    }
    
    /**
     * الحصول على معلومات منطقة محددة
     */
    public function getArea($id)
    {
        $area = Area::where('is_active', true)->with('district.governorate')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $area
        ]);
    }

    /**
     * حساب تكلفة الشحن بناءً على المنطقة المختارة
     */
    public function calculateShippingCost(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id'
        ]);
        
        $area = Area::with(['district.governorate.country'])->findOrFail($request->area_id);
        
        // التأكد من أن المنطقة نشطة
        if (!$area->is_active || !$area->district->is_active || !$area->district->governorate->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'المنطقة المختارة غير متاحة للشحن حاليًا'
            ], 422);
        }
        
        // حساب تكلفة الشحن الإجمالية
        $shippingCost = $area->district->governorate->shipping_cost + 
                        $area->district->additional_shipping_cost + 
                        $area->additional_shipping_cost;
        
        return response()->json([
            'success' => true,
            'data' => [
                'shipping_cost' => $shippingCost,
                'currency_symbol' => $area->district->governorate->country->currency_symbol,
                'currency_code' => $area->district->governorate->country->currency_code,
                'area' => [
                    'id' => $area->id,
                    'name' => $area->name,
                    'name_ar' => $area->name_ar,
                    'additional_cost' => $area->additional_shipping_cost
                ],
                'district' => [
                    'id' => $area->district->id,
                    'name' => $area->district->name,
                    'name_ar' => $area->district->name_ar,
                    'additional_cost' => $area->district->additional_shipping_cost
                ],
                'governorate' => [
                    'id' => $area->district->governorate->id,
                    'name' => $area->district->governorate->name,
                    'name_ar' => $area->district->governorate->name_ar,
                    'base_cost' => $area->district->governorate->shipping_cost
                ],
                'country' => [
                    'id' => $area->district->governorate->country->id,
                    'name' => $area->district->governorate->country->name,
                    'name_ar' => $area->district->governorate->country->name_ar
                ]
            ]
        ]);
    }
    
    /**
     * الحصول على المناطق الجغرافية المتسلسلة (دولة -> محافظة -> مركز -> منطقة)
     */
    public function getRegionsHierarchy()
    {
        $countries = Country::where('is_active', true)
            ->with(['governorates' => function($query) {
                $query->where('is_active', true)
                    ->with(['districts' => function($query) {
                        $query->where('is_active', true)
                            ->with(['areas' => function($query) {
                                $query->where('is_active', true);
                            }]);
                    }]);
            }])
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }
} 