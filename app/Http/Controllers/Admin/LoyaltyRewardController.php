<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyReward;
use App\Models\Product;
use App\Models\RewardRedemption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LoyaltyRewardController extends Controller
{
    /**
     * Display a listing of the loyalty rewards.
     */
    public function index(Request $request)
    {
        $query = LoyaltyReward::query();
        
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
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('name_ar', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('description_ar', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $rewards = $query->paginate(20);
        
        // Get redemption counts
        foreach ($rewards as $reward) {
            $reward->redemption_count = RewardRedemption::where('reward_id', $reward->id)->count();
        }
        
        return view('admin.loyalty.rewards.index', compact('rewards'));
    }

    /**
     * Show the form for creating a new loyalty reward.
     */
    public function create()
    {
        $products = Product::all();
        $rewardTypes = [
            LoyaltyReward::TYPE_DISCOUNT => 'خصم',
            LoyaltyReward::TYPE_FREE_PRODUCT => 'منتج مجاني',
            LoyaltyReward::TYPE_GIFT_CARD => 'بطاقة هدية',
            LoyaltyReward::TYPE_FREE_SHIPPING => 'شحن مجاني',
        ];
        
        return view('admin.loyalty.rewards.create', compact('products', 'rewardTypes'));
    }

    /**
     * Store a newly created loyalty reward in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type' => 'required|in:discount,free_product,gift_card,free_shipping',
            'points_required' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
            'is_active' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            
            // Discount type fields
            'discount_type' => 'required_if:type,discount|in:fixed,percentage',
            'discount_value' => 'required_if:type,discount|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            
            // Free product fields
            'product_id' => 'required_if:type,free_product|exists:products,id',
            
            // Gift card fields
            'gift_card_value' => 'required_if:type,gift_card|numeric|min:0',
        ]);
        
        // Prepare reward data based on type
        $rewardData = [];
        
        switch ($validated['type']) {
            case LoyaltyReward::TYPE_DISCOUNT:
                $rewardData = [
                    'discount_type' => $validated['discount_type'],
                    'value' => $validated['discount_value'],
                ];
                
                if (isset($validated['max_discount']) && $validated['max_discount'] > 0) {
                    $rewardData['max_discount'] = $validated['max_discount'];
                }
                break;
                
            case LoyaltyReward::TYPE_FREE_PRODUCT:
                $rewardData = [
                    'product_id' => $validated['product_id'],
                ];
                break;
                
            case LoyaltyReward::TYPE_GIFT_CARD:
                $rewardData = [
                    'value' => $validated['gift_card_value'],
                ];
                break;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('loyalty/rewards', 'public');
            $validated['image'] = $path;
        }
        
        // Format dates
        if (!empty($validated['starts_at'])) {
            $validated['starts_at'] = Carbon::parse($validated['starts_at']);
        }
        
        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = Carbon::parse($validated['expires_at']);
        }
        
        // Create reward
        LoyaltyReward::create([
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'],
            'type' => $validated['type'],
            'points_required' => $validated['points_required'],
            'reward_data' => $rewardData,
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'image' => $validated['image'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'stock' => $validated['stock'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);
        
        return redirect()->route('admin.loyalty.rewards.index')
            ->with('success', 'تم إنشاء مكافأة الولاء بنجاح.');
    }

    /**
     * Display the specified loyalty reward.
     */
    public function show(LoyaltyReward $reward)
    {
        $redemptions = RewardRedemption::where('reward_id', $reward->id)
            ->with('user')
            ->latest()
            ->paginate(20);
        
        return view('admin.loyalty.rewards.show', compact('reward', 'redemptions'));
    }

    /**
     * Show the form for editing the specified loyalty reward.
     */
    public function edit(LoyaltyReward $reward)
    {
        $products = Product::all();
        $rewardTypes = [
            LoyaltyReward::TYPE_DISCOUNT => 'خصم',
            LoyaltyReward::TYPE_FREE_PRODUCT => 'منتج مجاني',
            LoyaltyReward::TYPE_GIFT_CARD => 'بطاقة هدية',
            LoyaltyReward::TYPE_FREE_SHIPPING => 'شحن مجاني',
        ];
        
        return view('admin.loyalty.rewards.edit', compact('reward', 'products', 'rewardTypes'));
    }

    /**
     * Update the specified loyalty reward in storage.
     */
    public function update(Request $request, LoyaltyReward $reward)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type' => 'required|in:discount,free_product,gift_card,free_shipping',
            'points_required' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // Max 2MB
            'is_active' => 'boolean',
            'stock' => 'nullable|integer|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            
            // Discount type fields
            'discount_type' => 'required_if:type,discount|in:fixed,percentage',
            'discount_value' => 'required_if:type,discount|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            
            // Free product fields
            'product_id' => 'required_if:type,free_product|exists:products,id',
            
            // Gift card fields
            'gift_card_value' => 'required_if:type,gift_card|numeric|min:0',
        ]);
        
        // Prepare reward data based on type
        $rewardData = [];
        
        switch ($validated['type']) {
            case LoyaltyReward::TYPE_DISCOUNT:
                $rewardData = [
                    'discount_type' => $validated['discount_type'],
                    'value' => $validated['discount_value'],
                ];
                
                if (isset($validated['max_discount']) && $validated['max_discount'] > 0) {
                    $rewardData['max_discount'] = $validated['max_discount'];
                }
                break;
                
            case LoyaltyReward::TYPE_FREE_PRODUCT:
                $rewardData = [
                    'product_id' => $validated['product_id'],
                ];
                break;
                
            case LoyaltyReward::TYPE_GIFT_CARD:
                $rewardData = [
                    'value' => $validated['gift_card_value'],
                ];
                break;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($reward->image) {
                Storage::disk('public')->delete($reward->image);
            }
            
            $path = $request->file('image')->store('loyalty/rewards', 'public');
            $validated['image'] = $path;
        }
        
        // Format dates
        if (!empty($validated['starts_at'])) {
            $validated['starts_at'] = Carbon::parse($validated['starts_at']);
        }
        
        if (!empty($validated['expires_at'])) {
            $validated['expires_at'] = Carbon::parse($validated['expires_at']);
        }
        
        // Update reward
        $reward->update([
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'],
            'type' => $validated['type'],
            'points_required' => $validated['points_required'],
            'reward_data' => $rewardData,
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'image' => $validated['image'] ?? $reward->image,
            'is_active' => $validated['is_active'] ?? true,
            'stock' => $validated['stock'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);
        
        return redirect()->route('admin.loyalty.rewards.index')
            ->with('success', 'تم تحديث مكافأة الولاء بنجاح.');
    }

    /**
     * Remove the specified loyalty reward from storage.
     */
    public function destroy(LoyaltyReward $reward)
    {
        // Check if there are redemptions for this reward
        $redemptionsCount = RewardRedemption::where('reward_id', $reward->id)->count();
        
        if ($redemptionsCount > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف المكافأة لأنها تم استبدالها ' . $redemptionsCount . ' مرة.');
        }
        
        // Delete image if exists
        if ($reward->image) {
            Storage::disk('public')->delete($reward->image);
        }
        
        $reward->delete();
        
        return redirect()->route('admin.loyalty.rewards.index')
            ->with('success', 'تم حذف مكافأة الولاء بنجاح.');
    }

    /**
     * Toggle the active status of the loyalty reward.
     */
    public function toggleActive(LoyaltyReward $reward)
    {
        $reward->is_active = !$reward->is_active;
        $reward->save();
        
        $status = $reward->is_active ? 'تفعيل' : 'تعطيل';
        
        return redirect()->back()
            ->with('success', "تم {$status} مكافأة الولاء بنجاح.");
    }

    /**
     * Update the stock of the loyalty reward.
     */
    public function updateStock(Request $request, LoyaltyReward $reward)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);
        
        $reward->stock = $validated['stock'];
        $reward->save();
        
        return redirect()->back()
            ->with('success', 'تم تحديث مخزون المكافأة بنجاح.');
    }
} 