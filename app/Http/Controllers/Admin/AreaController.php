<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\District;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * عرض قائمة المناطق
     */
    public function index(Request $request)
    {
        $query = Area::with('district.governorate.country');
        
        // تصفية حسب المركز إذا تم تحديده
        if ($request->has('district_id')) {
            $query->where('district_id', $request->district_id);
        }
        
        // تصفية حسب المحافظة إذا تم تحديدها
        if ($request->has('governorate_id')) {
            $query->whereHas('district', function($q) use ($request) {
                $q->where('governorate_id', $request->governorate_id);
            });
        }
        
        $areas = $query->paginate(15);
        $districts = District::with('governorate')->where('is_active', true)->get();
        
        return view('admin.regions.areas.index', compact('areas', 'districts'));
    }

    /**
     * عرض نموذج إنشاء منطقة جديدة
     */
    public function create()
    {
        $districts = District::with('governorate')->where('is_active', true)->get();
        return view('admin.regions.areas.create', compact('districts'));
    }

    /**
     * تخزين منطقة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'additional_shipping_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Area::create($validated);

        return redirect()->route('admin.regions.areas.index')
            ->with('success', 'تم إضافة المنطقة بنجاح');
    }

    /**
     * عرض نموذج تعديل منطقة
     */
    public function edit(Area $area)
    {
        $districts = District::with('governorate')->where('is_active', true)->get();
        return view('admin.regions.areas.edit', compact('area', 'districts'));
    }

    /**
     * تحديث منطقة
     */
    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50',
            'additional_shipping_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $area->update($validated);

        return redirect()->route('admin.regions.areas.index')
            ->with('success', 'تم تحديث المنطقة بنجاح');
    }

    /**
     * حذف منطقة
     */
    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('admin.regions.areas.index')
            ->with('success', 'تم حذف المنطقة بنجاح');
    }
} 