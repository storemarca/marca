<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    /**
     * عرض قائمة المحافظات
     */
    public function index()
    {
        $governorates = Governorate::with('country')->paginate(15);
        return view('admin.regions.governorates.index', compact('governorates'));
    }

    /**
     * عرض نموذج إنشاء محافظة جديدة
     */
    public function create()
    {
        $countries = Country::where('is_active', true)->get();
        return view('admin.regions.governorates.create', compact('countries'));
    }

    /**
     * تخزين محافظة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'shipping_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Governorate::create($validated);

        return redirect()->route('admin.regions.governorates.index')
            ->with('success', 'تم إضافة المحافظة بنجاح');
    }

    /**
     * عرض نموذج تعديل محافظة
     */
    public function edit(Governorate $governorate)
    {
        $countries = Country::where('is_active', true)->get();
        return view('admin.regions.governorates.edit', compact('governorate', 'countries'));
    }

    /**
     * تحديث محافظة
     */
    public function update(Request $request, Governorate $governorate)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'shipping_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $governorate->update($validated);

        return redirect()->route('admin.regions.governorates.index')
            ->with('success', 'تم تحديث المحافظة بنجاح');
    }

    /**
     * حذف محافظة
     */
    public function destroy(Governorate $governorate)
    {
        // التحقق من عدم وجود مراكز مرتبطة بالمحافظة
        if ($governorate->districts()->count() > 0) {
            return redirect()->route('admin.regions.governorates.index')
                ->with('error', 'لا يمكن حذف المحافظة لأنها تحتوي على مراكز مرتبطة بها');
        }

        $governorate->delete();

        return redirect()->route('admin.regions.governorates.index')
            ->with('success', 'تم حذف المحافظة بنجاح');
    }
} 