<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Governorate;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * عرض قائمة المراكز
     */
    public function index(Request $request)
    {
        $query = District::with('governorate.country');
        
        // تصفية حسب المحافظة إذا تم تحديدها
        if ($request->has('governorate_id')) {
            $query->where('governorate_id', $request->governorate_id);
        }
        
        $districts = $query->paginate(15);
        $governorates = Governorate::where('is_active', true)->get();
        
        return view('admin.regions.districts.index', compact('districts', 'governorates'));
    }

    /**
     * عرض نموذج إنشاء مركز جديد
     */
    public function create()
    {
        $governorates = Governorate::where('is_active', true)->get();
        return view('admin.regions.districts.create', compact('governorates'));
    }

    /**
     * تخزين مركز جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'additional_shipping_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        District::create($validated);

        return redirect()->route('admin.regions.districts.index')
            ->with('success', 'تم إضافة المركز بنجاح');
    }

    /**
     * عرض نموذج تعديل مركز
     */
    public function edit(District $district)
    {
        $governorates = Governorate::where('is_active', true)->get();
        return view('admin.regions.districts.edit', compact('district', 'governorates'));
    }

    /**
     * تحديث مركز
     */
    public function update(Request $request, District $district)
    {
        $validated = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'additional_shipping_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $district->update($validated);

        return redirect()->route('admin.regions.districts.index')
            ->with('success', 'تم تحديث المركز بنجاح');
    }

    /**
     * حذف مركز
     */
    public function destroy(District $district)
    {
        // التحقق من عدم وجود مناطق مرتبطة بالمركز
        if ($district->areas()->count() > 0) {
            return redirect()->route('admin.regions.districts.index')
                ->with('error', 'لا يمكن حذف المركز لأنه يحتوي على مناطق مرتبطة به');
        }

        $district->delete();

        return redirect()->route('admin.regions.districts.index')
            ->with('success', 'تم حذف المركز بنجاح');
    }
} 