<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Country;

class CountrySwitcherController extends Controller
{
    /**
     * تغيير الدولة الحالية
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchCountry(Request $request, $id)
    {
        try {
            $country = Country::findOrFail($id);

            if (!$country->is_active) {
                Log::warning("محاولة التبديل إلى دولة غير مفعلة: {$country->name} (ID: {$country->id})");
                return redirect()->back()->with('error', 'هذه الدولة غير متاحة حالياً.');
            }

            // حفظ الدولة في الجلسة والكوكيز
            Session::put('country_id', $country->id);
            Cookie::queue('country_id', $country->id, 60 * 24 * 365); // سنة واحدة

            // إفراغ الكاش المتعلق بالدولة
            Cache::forget('current_country');

            Log::info("تم تغيير الدولة إلى: {$country->name} (ID: {$country->id})");

            return redirect()->back()->with('success', "تم تغيير الدولة إلى {$country->name} بنجاح.");
        } catch (\Exception $e) {
            Log::error("خطأ أثناء تغيير الدولة: " . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء محاولة تغيير الدولة. الرجاء المحاولة لاحقاً.');
        }
    }
}
