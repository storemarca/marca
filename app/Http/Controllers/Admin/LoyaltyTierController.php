<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTier;
use App\Models\UserLoyaltyPoints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LoyaltyTierController extends Controller
{
    /**
     * Display a listing of the loyalty tiers.
     */
    public function index()
    {
        $tiers = LoyaltyTier::orderBy('required_points')->get();
        
        // Get count of users in each tier
        foreach ($tiers as $tier) {
            $tier->user_count = UserLoyaltyPoints::where('current_tier_id', $tier->id)->count();
        }
        
        return view('admin.loyalty.tiers.index', compact('tiers'));
    }

    /**
     * Show the form for creating a new loyalty tier.
     */
    public function create()
    {
        return view('admin.loyalty.tiers.create');
    }

    /**
     * Store a newly created loyalty tier in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'required_points' => 'required|integer|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'free_shipping' => 'boolean',
            'points_multiplier' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'badge_image' => 'nullable|image|max:2048', // Max 2MB
            'is_active' => 'boolean',
        ]);
        
        // Handle badge image upload
        if ($request->hasFile('badge_image')) {
            $path = $request->file('badge_image')->store('loyalty/badges', 'public');
            $validated['badge_image'] = $path;
        }
        
        LoyaltyTier::create($validated);
        
        return redirect()->route('admin.loyalty.tiers.index')
            ->with('success', 'تم إنشاء مستوى الولاء بنجاح.');
    }

    /**
     * Display the specified loyalty tier.
     */
    public function show(LoyaltyTier $tier)
    {
        // Get users in this tier
        $users = UserLoyaltyPoints::where('current_tier_id', $tier->id)
            ->with('user')
            ->paginate(20);
        
        return view('admin.loyalty.tiers.show', compact('tier', 'users'));
    }

    /**
     * Show the form for editing the specified loyalty tier.
     */
    public function edit(LoyaltyTier $tier)
    {
        return view('admin.loyalty.tiers.edit', compact('tier'));
    }

    /**
     * Update the specified loyalty tier in storage.
     */
    public function update(Request $request, LoyaltyTier $tier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'required_points' => 'required|integer|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'free_shipping' => 'boolean',
            'points_multiplier' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'badge_image' => 'nullable|image|max:2048', // Max 2MB
            'is_active' => 'boolean',
        ]);
        
        // Handle badge image upload
        if ($request->hasFile('badge_image')) {
            // Delete old image if exists
            if ($tier->badge_image) {
                Storage::disk('public')->delete($tier->badge_image);
            }
            
            $path = $request->file('badge_image')->store('loyalty/badges', 'public');
            $validated['badge_image'] = $path;
        }
        
        $tier->update($validated);
        
        return redirect()->route('admin.loyalty.tiers.index')
            ->with('success', 'تم تحديث مستوى الولاء بنجاح.');
    }

    /**
     * Remove the specified loyalty tier from storage.
     */
    public function destroy(LoyaltyTier $tier)
    {
        // Check if there are users in this tier
        $usersCount = UserLoyaltyPoints::where('current_tier_id', $tier->id)->count();
        
        if ($usersCount > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف مستوى الولاء لأنه يحتوي على ' . $usersCount . ' مستخدم.');
        }
        
        // Delete badge image if exists
        if ($tier->badge_image) {
            Storage::disk('public')->delete($tier->badge_image);
        }
        
        $tier->delete();
        
        return redirect()->route('admin.loyalty.tiers.index')
            ->with('success', 'تم حذف مستوى الولاء بنجاح.');
    }

    /**
     * Toggle the active status of the loyalty tier.
     */
    public function toggleActive(LoyaltyTier $tier)
    {
        $tier->is_active = !$tier->is_active;
        $tier->save();
        
        $status = $tier->is_active ? 'تفعيل' : 'تعطيل';
        
        return redirect()->back()
            ->with('success', "تم {$status} مستوى الولاء بنجاح.");
    }
} 