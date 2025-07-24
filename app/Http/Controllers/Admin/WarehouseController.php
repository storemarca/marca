<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Country;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the warehouses.
     */
    public function index(Request $request)
    {
        $query = Warehouse::with('country');
        
        // Filter by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by country
        if ($request->has('country_id') && $request->country_id) {
            $query->where('country_id', $request->country_id);
        }
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $warehouses = $query->latest()->paginate(15);
        $countries = Country::all();
        
        return view('admin.warehouses.index', compact('warehouses', 'countries'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        $countries = Country::all();
        return view('admin.warehouses.create', compact('countries'));
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);
        
        $warehouse = Warehouse::create($validated);
        
        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', __('warehouse_created_successfully'));
    }

    /**
     * Display the specified warehouse.
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load('country');
        
        // Get stock information
        $stocks = ProductStock::with('product')
            ->where('warehouse_id', $warehouse->id)
            ->get();
            
        // Calculate total inventory value
        $totalValue = 0;
        foreach ($stocks as $stock) {
            $totalValue += $stock->quantity * $stock->product->cost;
        }
        
        return view('admin.warehouses.show', compact('warehouse', 'stocks', 'totalValue'));
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        $countries = Country::all();
        return view('admin.warehouses.edit', compact('warehouse', 'countries'));
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);
        
        $warehouse->update($validated);
        
        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', __('warehouse_updated_successfully'));
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        // Check if warehouse has any product stocks
        $hasStocks = ProductStock::where('warehouse_id', $warehouse->id)->exists();
        
        if ($hasStocks) {
            return back()->with('error', __('cannot_delete_warehouse_with_stock'));
        }
        
        try {
            $warehouse->delete();
            return redirect()
                ->route('admin.warehouses.index')
                ->with('success', __('warehouse_deleted_successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('error_deleting_warehouse'));
        }
    }
    
    /**
     * Toggle warehouse active status.
     */
    public function toggleStatus(Warehouse $warehouse)
    {
        $warehouse->is_active = !$warehouse->is_active;
        $warehouse->save();
        
        $status = $warehouse->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', __('warehouse_' . $status . '_successfully'));
    }
} 