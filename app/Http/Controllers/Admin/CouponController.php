<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use App\Services\CouponService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected $couponService;
    
    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }
    
    /**
     * Display a listing of the coupons.
     */
    public function index(Request $request)
    {
        $query = Coupon::query();
        
        // Apply filters
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $coupons = $query->paginate(20);
        
        return view('admin.coupons.index', compact('coupons'));
    }
    
    /**
     * Show the form for creating a new coupon.
     */
    public function create()
    {
        $categories = Category::all();
        $products = Product::all();
        $users = User::all();
        
        return view('admin.coupons.create', compact('categories', 'products', 'users'));
    }
    
    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'user_usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'description' => 'nullable|string',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        
        // Additional validation for percentage type
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['value' => 'النسبة المئوية يجب أن تكون بين 0 و 100.']);
        }
        
        // Format dates
        if (!empty($validated['starts_at'])) {
            $validated['starts_at'] = Carbon::parse($validated['starts_at']);
        }
        
        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = Carbon::parse($validated['expires_at']);
        }
        
        try {
            $coupon = $this->couponService->createCoupon($validated);
            
            return redirect()->route('admin.coupons.index')
                ->with('success', 'تم إنشاء الكوبون بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الكوبون: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified coupon.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['categories', 'products', 'users']);
        
        return view('admin.coupons.show', compact('coupon'));
    }
    
    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon)
    {
        $coupon->load(['categories', 'products', 'users']);
        
        $categories = Category::all();
        $products = Product::all();
        $users = User::all();
        
        $selectedCategoryIds = $coupon->categories->pluck('id')->toArray();
        $selectedProductIds = $coupon->products->pluck('id')->toArray();
        $selectedUserIds = $coupon->users->pluck('id')->toArray();
        
        return view('admin.coupons.edit', compact(
            'coupon',
            'categories',
            'products',
            'users',
            'selectedCategoryIds',
            'selectedProductIds',
            'selectedUserIds'
        ));
    }
    
    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'user_usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'description' => 'nullable|string',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);
        
        // Additional validation for percentage type
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['value' => 'النسبة المئوية يجب أن تكون بين 0 و 100.']);
        }
        
        // Format dates
        if (!empty($validated['starts_at'])) {
            $validated['starts_at'] = Carbon::parse($validated['starts_at']);
        }
        
        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = Carbon::parse($validated['expires_at']);
        }
        
        try {
            $coupon = $this->couponService->updateCoupon($coupon, $validated);
            
            return redirect()->route('admin.coupons.index')
                ->with('success', 'تم تحديث الكوبون بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الكوبون: ' . $e->getMessage());
        }
    }
    
    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $coupon->delete();
            
            return redirect()->route('admin.coupons.index')
                ->with('success', 'تم حذف الكوبون بنجاح.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الكوبون: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle the active status of the coupon.
     */
    public function toggleActive(Coupon $coupon)
    {
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();
        
        $status = $coupon->is_active ? 'تفعيل' : 'تعطيل';
        
        return redirect()->back()
            ->with('success', "تم {$status} الكوبون بنجاح.");
    }
    
    /**
     * Export coupons to CSV.
     */
    public function export()
    {
        $coupons = Coupon::all();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="coupons-' . date('Y-m-d') . '.csv"',
        ];
        
        $columns = [
            'ID',
            'Code',
            'Type',
            'Value',
            'Min Order Amount',
            'Max Discount Amount',
            'Usage Limit',
            'Usage Count',
            'User Usage Limit',
            'Active',
            'Starts At',
            'Expires At',
            'Description',
            'Created At'
        ];
        
        $callback = function() use ($coupons, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($coupons as $coupon) {
                fputcsv($file, [
                    $coupon->id,
                    $coupon->code,
                    $coupon->type,
                    $coupon->value,
                    $coupon->min_order_amount,
                    $coupon->max_discount_amount,
                    $coupon->usage_limit,
                    $coupon->usage_count,
                    $coupon->user_usage_limit,
                    $coupon->is_active ? 'Yes' : 'No',
                    $coupon->starts_at ? $coupon->starts_at->format('Y-m-d H:i:s') : '',
                    $coupon->expires_at ? $coupon->expires_at->format('Y-m-d H:i:s') : '',
                    $coupon->description,
                    $coupon->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 