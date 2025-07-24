<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Country;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية للمستخدم
     */
    public function index()
    {
        // الحصول على البلد الحالي
        $country = current_country();
        
        // التحقق من وجود بلد نشط
        if (!$country) {
            // محاولة الحصول على أي بلد نشط من قاعدة البيانات
            $country = Country::where('is_active', true)->first();
            
            // إذا لم يوجد أي بلد نشط، عرض رسالة خطأ
            if (!$country) {
                return view('user.errors.no-countries');
            }
            
            // تعيين البلد في الجلسة
            Session::put('country_id', $country->getKey());
        }
        
        // استرجاع الأقسام النشطة
        $categories = Category::where('is_active', true)
                             ->orderBy('name')
                             ->get();
                             
        // استرجاع المنتجات المميزة
        $featuredProductsQuery = Product::where('is_featured', true)
                                       ->where('is_active', true);
                                       
        // تصفية حسب البلد الحالي فقط
        $featuredProductsQuery->whereHas('prices', function($query) use ($country) {
            $query->where('country_id', $country->getKey())
                  ->where('is_active', true);
        });
        
        $featuredProducts = $featuredProductsQuery->with(['category', 'prices' => function($query) use ($country) {
            $query->where('country_id', $country->getKey());
        }])->take(12)->get();
        
        // استرجاع المنتجات الجديدة
        $newProductsQuery = Product::where('is_active', true)
                                  ->orderBy('created_at', 'desc');
                                  
        // تصفية حسب البلد الحالي فقط
        $newProductsQuery->whereHas('prices', function($query) use ($country) {
            $query->where('country_id', $country->getKey())
                  ->where('is_active', true);
        });
        
        $newProducts = $newProductsQuery->with(['category', 'prices' => function($query) use ($country) {
            $query->where('country_id', $country->getKey());
        }])->take(12)->get();
                             
        return view('user.home', compact('featuredProducts', 'newProducts', 'categories'));
    }
}
