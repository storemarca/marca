<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * عرض قائمة البلدان
     */
    public function index(Request $request)
    {
        $query = Country::query();
        
        // البحث حسب الاسم أو الرمز
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('currency_code', 'like', "%{$search}%");
            });
        }
        
        // التصفية حسب الحالة
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $countries = $query->orderBy('name')->paginate(15);
        
        return view('admin.countries.index', compact('countries'));
    }

    /**
     * عرض نموذج إنشاء بلد جديد
     */
    public function create()
    {
        return view('admin.countries.create');
    }

    /**
     * تخزين بلد جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:countries',
            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        Country::create($validated);
        
        return redirect()->route('admin.countries.index')
            ->with('success', __('country_created_successfully'));
    }

    /**
     * عرض بلد محدد
     */
    public function show(Country $country)
    {
        // عدد العملاء من هذا البلد
        $customersCount = $country->customers()->count();
        
        // عدد العناوين في هذا البلد
        $addressesCount = $country->addresses()->count();
        
        // عدد الطلبات من هذا البلد
        $ordersCount = $country->orders()->count() ?? 0;
        
        // عدد المستودعات في هذا البلد
        $warehousesCount = $country->warehouses()->count();
        
        return view('admin.countries.show', compact(
            'country',
            'customersCount',
            'addressesCount',
            'ordersCount',
            'warehousesCount'
        ));
    }

    /**
     * عرض نموذج تعديل بلد
     */
    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    /**
     * تحديث بلد محدد
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code,' . $country->id,
            'currency_code' => 'required|string|size:3',
            'currency_symbol' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        $country->update($validated);
        
        return redirect()->route('admin.countries.index')
            ->with('success', __('country_updated_successfully'));
    }

    /**
     * حذف بلد محدد
     */
    public function destroy(Country $country)
    {
        // التحقق من وجود سجلات مرتبطة
        if (
            $country->customers()->exists() ||
            $country->addresses()->exists() ||
            $country->orders()->exists() ||
            $country->warehouses()->exists() ||
            $country->suppliers()->exists()
        ) {
            return back()->with('error', __('cannot_delete_country_with_relations'));
        }
        
        try {
            $country->delete();
            return redirect()->route('admin.countries.index')
                ->with('success', __('country_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('error_deleting_country'));
        }
    }
    
    /**
     * تبديل حالة البلد (نشط/غير نشط)
     */
    public function toggleStatus(Country $country)
    {
        $country->is_active = !$country->is_active;
        $country->save();
        
        $status = $country->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', __('country_' . $status . '_successfully'));
    }
} 