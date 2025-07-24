<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyReward;
use App\Models\LoyaltyTier;
use App\Models\RewardRedemption;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoyaltyController extends Controller
{
    protected $loyaltyService;
    
    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }
    
    /**
     * Display the user's loyalty dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $loyaltyPoints = $user->loyaltyPoints;
        $currentTier = $loyaltyPoints ? $loyaltyPoints->currentTier : null;
        $nextTier = $currentTier ? $currentTier->getNextTier() : LoyaltyTier::active()->orderBy('required_points')->first();
        
        // Get recent transactions
        $transactions = $user->loyaltyTransactions()
            ->latest()
            ->take(10)
            ->get();
        
        // Get all tiers for display
        $tiers = LoyaltyTier::active()->orderBy('required_points')->get();
        
        // Get available rewards
        $rewards = LoyaltyReward::active()
            ->orderBy('points_required')
            ->get();
        
        // Get recent redemptions
        $redemptions = $user->rewardRedemptions()
            ->with('reward')
            ->latest()
            ->take(5)
            ->get();
        
        return view('user.loyalty.index', compact(
            'user',
            'loyaltyPoints',
            'currentTier',
            'nextTier',
            'transactions',
            'tiers',
            'rewards',
            'redemptions'
        ));
    }
    
    /**
     * Display the user's transaction history.
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->loyaltyTransactions();
        
        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $transactions = $query->paginate(20);
        
        return view('user.loyalty.transactions', compact('transactions'));
    }
    
    /**
     * Display the rewards catalog.
     */
    public function rewards(Request $request)
    {
        $user = Auth::user();
        $pointsBalance = $user->points_balance;
        
        $query = LoyaltyReward::active();
        
        // Apply filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('affordable') && $request->affordable === 'true') {
            $query->where('points_required', '<=', $pointsBalance);
        }
        
        // Sort
        $sortField = $request->sort ?? 'points_required';
        $sortDirection = $request->direction ?? 'asc';
        $query->orderBy($sortField, $sortDirection);
        
        $rewards = $query->paginate(12);
        
        return view('user.loyalty.rewards', compact('rewards', 'pointsBalance'));
    }
    
    /**
     * Display a specific reward.
     */
    public function showReward(LoyaltyReward $reward)
    {
        $user = Auth::user();
        $pointsBalance = $user->points_balance;
        $canRedeem = $reward->isAvailable() && $pointsBalance >= $reward->points_required;
        
        return view('user.loyalty.reward_detail', compact('reward', 'pointsBalance', 'canRedeem'));
    }
    
    /**
     * Redeem a reward.
     */
    public function redeemReward(Request $request, LoyaltyReward $reward)
    {
        $user = Auth::user();
        
        try {
            // Check if the reward is available
            if (!$reward->isAvailable()) {
                return redirect()->back()->with('error', 'المكافأة غير متاحة حالياً.');
            }
            
            // Check if the user has enough points
            if ($user->points_balance < $reward->points_required) {
                return redirect()->back()->with('error', 'نقاط غير كافية لاستبدال هذه المكافأة.');
            }
            
            // Redeem the reward
            $redemption = $this->loyaltyService->redeemReward($user, $reward);
            
            return redirect()->route('user.loyalty.redemptions')
                ->with('success', 'تم استبدال المكافأة بنجاح. كود الاستبدال الخاص بك هو: ' . $redemption->code);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء استبدال المكافأة: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the user's redemption history.
     */
    public function redemptions(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->rewardRedemptions()->with('reward');
        
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $redemptions = $query->paginate(20);
        
        return view('user.loyalty.redemptions', compact('redemptions'));
    }
    
    /**
     * Display a specific redemption.
     */
    public function showRedemption(RewardRedemption $redemption)
    {
        $user = Auth::user();
        
        // Ensure the redemption belongs to the user
        if ($redemption->user_id !== $user->id) {
            abort(403);
        }
        
        return view('user.loyalty.redemption_detail', compact('redemption'));
    }
    
    /**
     * Cancel a pending redemption.
     */
    public function cancelRedemption(RewardRedemption $redemption)
    {
        $user = Auth::user();
        
        // Ensure the redemption belongs to the user
        if ($redemption->user_id !== $user->id) {
            abort(403);
        }
        
        // Ensure the redemption is in pending status
        if ($redemption->status !== RewardRedemption::STATUS_PENDING) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء هذا الاستبدال.');
        }
        
        // Cancel the redemption
        $redemption->markAsCancelled();
        
        return redirect()->route('user.loyalty.redemptions')
            ->with('success', 'تم إلغاء الاستبدال بنجاح وتمت إعادة النقاط إلى رصيدك.');
    }
} 