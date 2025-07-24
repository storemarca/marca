<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateLink;
use App\Models\Category;
use App\Models\Product;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AffiliateController extends Controller
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Display the affiliate dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            return redirect()->route('affiliate.apply');
        }
        
        // Get recent transactions
        $transactions = $affiliate->commissionTransactions()
            ->latest()
            ->take(10)
            ->get();
        
        // Get affiliate links
        $links = $affiliate->affiliateLinks()
            ->orderByDesc('clicks')
            ->get();
        
        // Get referrals
        $referrals = $affiliate->referrals()
            ->with('referredUser')
            ->latest()
            ->take(10)
            ->get();
        
        // Get pending withdrawal requests
        $pendingWithdrawals = $affiliate->withdrawalRequests()
            ->where('status', '!=', 'paid')
            ->get();
        
        return view('user.affiliate.index', compact(
            'affiliate',
            'transactions',
            'links',
            'referrals',
            'pendingWithdrawals'
        ));
    }

    /**
     * Show the application form.
     */
    public function apply()
    {
        $user = Auth::user();
        
        // Check if the user already has an affiliate account
        if ($user->affiliate) {
            return redirect()->route('affiliate.index');
        }
        
        return view('user.affiliate.apply');
    }

    /**
     * Process the application form.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if the user already has an affiliate account
        if ($user->affiliate) {
            return redirect()->route('affiliate.index');
        }
        
        $validated = $request->validate([
            'website' => 'nullable|url|max:255',
            'social_media' => 'nullable|string|max:255',
            'marketing_methods' => 'required|string|max:1000',
            'payment_method' => 'required|string|max:255',
            'payment_details' => 'required|string|max:1000',
        ]);
        
        // Create the affiliate
        $affiliate = $this->affiliateService->createAffiliate($user, [
            'website' => $validated['website'],
            'social_media' => $validated['social_media'],
            'marketing_methods' => $validated['marketing_methods'],
            'payment_details' => json_encode([
                'method' => $validated['payment_method'],
                'details' => $validated['payment_details'],
            ]),
        ]);
        
        return redirect()->route('affiliate.pending')
            ->with('success', 'تم تقديم طلب الانضمام لبرنامج المسوقين بالعمولة بنجاح. سيتم مراجعة طلبك قريباً.');
    }

    /**
     * Show the pending application page.
     */
    public function pending()
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        // If the user doesn't have an affiliate account or it's not pending, redirect
        if (!$affiliate) {
            return redirect()->route('affiliate.apply');
        }
        
        if (!$affiliate->isPending()) {
            return redirect()->route('affiliate.index');
        }
        
        return view('user.affiliate.pending', compact('affiliate'));
    }

    /**
     * Show the rejected application page.
     */
    public function rejected()
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        // If the user doesn't have an affiliate account or it's not rejected, redirect
        if (!$affiliate) {
            return redirect()->route('affiliate.apply');
        }
        
        if (!$affiliate->isRejected()) {
            return redirect()->route('affiliate.index');
        }
        
        return view('user.affiliate.rejected', compact('affiliate'));
    }

    /**
     * Show the suspended account page.
     */
    public function suspended()
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        // If the user doesn't have an affiliate account or it's not suspended, redirect
        if (!$affiliate) {
            return redirect()->route('affiliate.apply');
        }
        
        if (!$affiliate->isSuspended()) {
            return redirect()->route('affiliate.index');
        }
        
        return view('user.affiliate.suspended', compact('affiliate'));
    }

    /**
     * Display the affiliate links.
     */
    public function links()
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate || !$affiliate->isApproved()) {
            return redirect()->route('affiliate.index');
        }
        
        $links = $affiliate->affiliateLinks()
            ->orderByDesc('clicks')
            ->paginate(20);
        
        // Get products and categories for creating new links
        $products = Product::active()->get();
        $categories = Category::all();
        
        return view('user.affiliate.links', compact('links', 'products', 'categories'));
    }

    /**
     * Create a new affiliate link.
     */
    public function createLink(Request $request)
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate || !$affiliate->isApproved()) {
            return redirect()->route('affiliate.index');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_type' => 'required|in:product,category,page,custom',
            'target_id' => 'required_unless:target_type,custom|nullable|integer',
            'custom_url' => 'required_if:target_type,custom|nullable|url|max:255',
        ]);
        
        // Create the affiliate link
        $link = $this->affiliateService->createAffiliateLink($affiliate, $validated);
        
        return redirect()->route('affiliate.links')
            ->with('success', 'تم إنشاء الرابط التسويقي بنجاح.');
    }

    /**
     * Display the affiliate transactions.
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            return redirect()->route('affiliate.index');
        }
        
        $query = $affiliate->commissionTransactions();
        
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
        
        return view('user.affiliate.transactions', compact('transactions', 'affiliate'));
    }

    /**
     * Display the affiliate referrals.
     */
    public function referrals(Request $request)
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            return redirect()->route('affiliate.index');
        }
        
        $query = $affiliate->referrals()->with('referredUser');
        
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
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
        
        $referrals = $query->paginate(20);
        
        return view('user.affiliate.referrals', compact('referrals', 'affiliate'));
    }

    /**
     * Display the withdrawal requests.
     */
    public function withdrawals(Request $request)
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate) {
            return redirect()->route('affiliate.index');
        }
        
        $query = $affiliate->withdrawalRequests();
        
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
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
        
        $withdrawals = $query->paginate(20);
        
        return view('user.affiliate.withdrawals', compact('withdrawals', 'affiliate'));
    }

    /**
     * Show the withdrawal request form.
     */
    public function showWithdrawalForm()
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate || !$affiliate->isApproved()) {
            return redirect()->route('affiliate.index');
        }
        
        // Check if the affiliate has enough balance
        $minimumWithdrawal = 100; // Minimum withdrawal amount
        
        if ($affiliate->balance < $minimumWithdrawal) {
            return redirect()->route('affiliate.index')
                ->with('error', 'يجب أن يكون لديك رصيد ' . $minimumWithdrawal . ' ريال على الأقل لطلب السحب.');
        }
        
        // Get payment methods
        $paymentMethods = [
            'bank_transfer' => 'تحويل بنكي',
            'paypal' => 'PayPal',
            'western_union' => 'Western Union',
        ];
        
        return view('user.affiliate.withdrawal-form', compact('affiliate', 'paymentMethods', 'minimumWithdrawal'));
    }

    /**
     * Process the withdrawal request.
     */
    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate || !$affiliate->isApproved()) {
            return redirect()->route('affiliate.index');
        }
        
        $minimumWithdrawal = 100; // Minimum withdrawal amount
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:' . $minimumWithdrawal . '|max:' . $affiliate->balance,
            'payment_method' => 'required|string|max:255',
            'payment_details' => 'required|string|max:1000',
        ]);
        
        try {
            // Create the withdrawal request
            $withdrawal = $this->affiliateService->requestWithdrawal(
                $affiliate,
                $validated['amount'],
                $validated['payment_method'],
                $validated['payment_details']
            );
            
            return redirect()->route('affiliate.withdrawals')
                ->with('success', 'تم تقديم طلب السحب بنجاح. سيتم مراجعته قريباً.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء معالجة طلب السحب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get the affiliate marketing materials.
     */
    public function marketingMaterials()
    {
        $user = Auth::user();
        $affiliate = $user->affiliate;
        
        if (!$affiliate || !$affiliate->isApproved()) {
            return redirect()->route('affiliate.index');
        }
        
        // Get banners and other marketing materials
        $banners = [
            [
                'id' => 1,
                'name' => 'Banner 300x250',
                'image' => 'images/affiliate/banners/300x250.jpg',
                'width' => 300,
                'height' => 250,
            ],
            [
                'id' => 2,
                'name' => 'Banner 728x90',
                'image' => 'images/affiliate/banners/728x90.jpg',
                'width' => 728,
                'height' => 90,
            ],
            [
                'id' => 3,
                'name' => 'Banner 160x600',
                'image' => 'images/affiliate/banners/160x600.jpg',
                'width' => 160,
                'height' => 600,
            ],
        ];
        
        return view('user.affiliate.marketing-materials', compact('affiliate', 'banners'));
    }
} 