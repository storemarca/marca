<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShippingMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shippingMethods = ShippingMethod::all();
        return view('admin.shipping-methods.index', compact('shippingMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::where('is_active', true)->get();
        return view('admin.shipping-methods.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shipping_methods,code',
            'base_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'weight_based' => 'boolean',
            'cost_per_kg' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'countries' => 'array',
            'countries.*.country_id' => 'required|exists:countries,id',
            'countries.*.cost' => 'required|numeric|min:0',
            'countries.*.is_available' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Create the shipping method
            $shippingMethod = ShippingMethod::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'base_cost' => $validated['base_cost'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'weight_based' => $validated['weight_based'] ?? false,
                'cost_per_kg' => $validated['cost_per_kg'] ?? 0,
                'free_shipping_threshold' => $validated['free_shipping_threshold'] ?? 0,
            ]);

            // Attach countries with their specific costs
            if (!empty($validated['countries'])) {
                foreach ($validated['countries'] as $countryData) {
                    $shippingMethod->countries()->attach($countryData['country_id'], [
                        'cost' => $countryData['cost'],
                        'is_available' => $countryData['is_available'] ?? true,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.shipping-methods.index')
                ->with('success', 'تم إنشاء طريقة الشحن بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء طريقة الشحن: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShippingMethod $shippingMethod)
    {
        $shippingMethod->load('countries');
        return view('admin.shipping-methods.show', compact('shippingMethod'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingMethod $shippingMethod)
    {
        $shippingMethod->load('countries');
        $countries = Country::where('is_active', true)->get();
        return view('admin.shipping-methods.edit', compact('shippingMethod', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:shipping_methods,code,' . $shippingMethod->id,
            'base_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'weight_based' => 'boolean',
            'cost_per_kg' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'countries' => 'array',
            'countries.*.country_id' => 'required|exists:countries,id',
            'countries.*.cost' => 'required|numeric|min:0',
            'countries.*.is_available' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Update the shipping method
            $shippingMethod->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'base_cost' => $validated['base_cost'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'weight_based' => $validated['weight_based'] ?? false,
                'cost_per_kg' => $validated['cost_per_kg'] ?? 0,
                'free_shipping_threshold' => $validated['free_shipping_threshold'] ?? 0,
            ]);

            // Sync countries with their specific costs
            $countryData = [];
            if (!empty($validated['countries'])) {
                foreach ($validated['countries'] as $country) {
                    $countryData[$country['country_id']] = [
                        'cost' => $country['cost'],
                        'is_available' => $country['is_available'] ?? true,
                    ];
                }
            }
            $shippingMethod->countries()->sync($countryData);

            DB::commit();

            return redirect()->route('admin.shipping-methods.index')
                ->with('success', 'تم تحديث طريقة الشحن بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث طريقة الشحن: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingMethod $shippingMethod)
    {
        try {
            $shippingMethod->delete();
            return redirect()->route('admin.shipping-methods.index')
                ->with('success', 'تم حذف طريقة الشحن بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف طريقة الشحن: ' . $e->getMessage());
        }
    }
}
