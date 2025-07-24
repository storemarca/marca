<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Affiliate;
use Illuminate\Support\Str;

class UserAffiliateController extends Controller
{
    /**
     * Apply to become an affiliate
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apply(Request $request)
    {
        $user = $request->user();
        
        // Check if user already has an affiliate account
        if ($user->affiliate) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an affiliate account'
            ], 422);
        }
        
        // Validate request
        $request->validate([
            'website' => 'nullable|url|max:255',
            'social_media' => 'nullable|string|max:255',
            'reason' => 'required|string|max:1000',
        ]);
        
        // Create affiliate
        $affiliate = new Affiliate();
        $affiliate->user_id = $user->id;
        $affiliate->code = Str::random(8);
        $affiliate->website = $request->website;
        $affiliate->social_media = $request->social_media;
        $affiliate->reason = $request->reason;
        $affiliate->status = 'pending';
        $affiliate->commission_rate = 5; // Default 5%
        $affiliate->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Your affiliate application has been submitted successfully',
            'data' => $affiliate
        ]);
    }
    
    /**
     * Get affiliate dashboard data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have an affiliate account'
            ], 404);
        }
        
        // Load relationships
        $affiliate->load([
            'links',
            'transactions',
            'withdrawalRequests',
        ]);
        
        // Calculate statistics
        $totalClicks = $affiliate->links->sum('clicks');
        $totalConversions = $affiliate->links->sum('conversions');
        $totalEarnings = $affiliate->transactions->sum('amount');
        $pendingEarnings = $affiliate->transactions->where('status', 'pending')->sum('amount');
        $withdrawnEarnings = $affiliate->withdrawalRequests->where('status', 'approved')->sum('amount');
        $availableBalance = $totalEarnings - $withdrawnEarnings;
        
        // Get recent transactions
        $recentTransactions = $affiliate->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get recent links
        $recentLinks = $affiliate->links()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'affiliate' => $affiliate,
                'statistics' => [
                    'total_clicks' => $totalClicks,
                    'total_conversions' => $totalConversions,
                    'conversion_rate' => $totalClicks > 0 ? ($totalConversions / $totalClicks) * 100 : 0,
                    'total_earnings' => $totalEarnings,
                    'pending_earnings' => $pendingEarnings,
                    'withdrawn_earnings' => $withdrawnEarnings,
                    'available_balance' => $availableBalance,
                ],
                'recent_transactions' => $recentTransactions,
                'recent_links' => $recentLinks,
            ]
        ]);
    }
    
    /**
     * Create a new affiliate link
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createLink(Request $request)
    {
        $user = $request->user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have an affiliate account'
            ], 404);
        }
        
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'target_url' => 'required|url|max:255',
            'campaign' => 'nullable|string|max:255',
        ]);
        
        // Create link
        $link = $affiliate->links()->create([
            'name' => $request->name,
            'code' => Str::random(8),
            'target_url' => $request->target_url,
            'campaign' => $request->campaign,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Affiliate link created successfully',
            'data' => $link
        ]);
    }
    
    /**
     * Request a withdrawal
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function requestWithdrawal(Request $request)
    {
        $user = $request->user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have an affiliate account'
            ], 404);
        }
        
        // Validate request
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'payment_method' => 'required|string|in:paypal,bank_transfer',
            'payment_details' => 'required|string|max:255',
        ]);
        
        // Calculate available balance
        $totalEarnings = $affiliate->transactions->sum('amount');
        $withdrawnEarnings = $affiliate->withdrawalRequests->where('status', 'approved')->sum('amount');
        $availableBalance = $totalEarnings - $withdrawnEarnings;
        
        // Check if enough balance
        if ($request->amount > $availableBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance for withdrawal'
            ], 422);
        }
        
        // Create withdrawal request
        $withdrawal = $affiliate->withdrawalRequests()->create([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details,
            'status' => 'pending',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully',
            'data' => $withdrawal
        ]);
    }
} 