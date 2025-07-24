<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\CommissionTransaction;
use App\Models\WithdrawalRequest;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AffiliateController extends Controller
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Display a listing of the affiliates.
     */
    public function index(Request $request)
    {
        $query = Affiliate::with('user');
        
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->orWhere('code', 'like', '%' . $search . '%');
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $affiliates = $query->paginate(20);
        
        // Get statistics
        $totalAffiliates = Affiliate::count();
        $pendingAffiliates = Affiliate::pending()->count();
        $activeAffiliates = Affiliate::approved()->count();
        $totalEarnings = $this->affiliateService->getTotalEarnings();
        $pendingWithdrawals = $this->affiliateService->getTotalPendingWithdrawals();
        
        return view('admin.affiliates.index', compact(
            'affiliates',
            'totalAffiliates',
            'pendingAffiliates',
            'activeAffiliates',
            'totalEarnings',
            'pendingWithdrawals'
        ));
    }

    /**
     * Display the specified affiliate.
     */
    public function show(Affiliate $affiliate)
    {
        $affiliate->load('user');
        
        // Get recent transactions
        $transactions = $affiliate->commissionTransactions()
            ->latest()
            ->take(10)
            ->get();
        
        // Get pending withdrawal requests
        $pendingWithdrawals = $affiliate->withdrawalRequests()
            ->pending()
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
        
        return view('admin.affiliates.show', compact(
            'affiliate',
            'transactions',
            'pendingWithdrawals',
            'links',
            'referrals'
        ));
    }

    /**
     * Approve an affiliate.
     */
    public function approve(Affiliate $affiliate)
    {
        $affiliate->approve();
        
        return redirect()->route('admin.affiliates.show', $affiliate)
            ->with('success', 'تم الموافقة على المسوق بالعمولة بنجاح.');
    }

    /**
     * Reject an affiliate.
     */
    public function reject(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $affiliate->reject($validated['rejection_reason']);
        
        return redirect()->route('admin.affiliates.index')
            ->with('success', 'تم رفض المسوق بالعمولة بنجاح.');
    }

    /**
     * Suspend an affiliate.
     */
    public function suspend(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $affiliate->suspend($validated['rejection_reason']);
        
        return redirect()->route('admin.affiliates.index')
            ->with('success', 'تم تعليق المسوق بالعمولة بنجاح.');
    }

    /**
     * Update the commission rate for an affiliate.
     */
    public function updateCommissionRate(Request $request, Affiliate $affiliate)
    {
        $validated = $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);
        
        $affiliate->commission_rate = $validated['commission_rate'];
        $affiliate->save();
        
        return redirect()->route('admin.affiliates.show', $affiliate)
            ->with('success', 'تم تحديث نسبة العمولة بنجاح.');
    }

    /**
     * Display a listing of the withdrawal requests.
     */
    public function withdrawalRequests(Request $request)
    {
        $query = WithdrawalRequest::with('affiliate.user');
        
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('affiliate.user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $withdrawalRequests = $query->paginate(20);
        
        // إحصائيات إضافية
        $totalRequests = WithdrawalRequest::count();
        $pendingRequests = WithdrawalRequest::where('status', 'pending')->count();
        $paidAmount = WithdrawalRequest::where('status', 'paid')->sum('amount');
        $pendingAmount = WithdrawalRequest::where('status', 'pending')->sum('amount');
        
        // الحصول على قائمة المسوقين بالعمولة للفلتر
        $affiliates = Affiliate::with('user')->orderBy('id')->get();
        
        return view('admin.affiliates.withdrawal-requests', compact(
            'withdrawalRequests',
            'totalRequests',
            'pendingRequests',
            'paidAmount',
            'pendingAmount',
            'affiliates'
        ));
    }

    /**
     * Export withdrawal requests to CSV.
     */
    public function exportWithdrawalRequests(Request $request)
    {
        $query = WithdrawalRequest::with(['affiliate.user']);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('affiliate_id')) {
            $query->where('affiliate_id', $request->affiliate_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $withdrawalRequests = $query->get();
        
        // تحضير بيانات CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="withdrawal_requests_' . date('Y-m-d') . '.csv"',
        ];
        
        $columns = [
            'المعرف',
            'المسوق',
            'البريد الإلكتروني',
            'المبلغ',
            'طريقة الدفع',
            'الحالة',
            'تاريخ الطلب',
            'تاريخ الدفع',
            'ملاحظات'
        ];
        
        $callback = function() use ($withdrawalRequests, $columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add headers
            fputcsv($file, $columns);
            
            // Add data rows
            foreach ($withdrawalRequests as $request) {
                $row = [
                    $request->id,
                    $request->affiliate->user->name,
                    $request->affiliate->user->email,
                    $request->amount,
                    $request->payment_method,
                    $request->status,
                    $request->created_at->format('Y-m-d H:i:s'),
                    $request->processed_at ? $request->processed_at->format('Y-m-d H:i:s') : '-',
                    $request->notes
                ];
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }

    /**
     * Approve a withdrawal request.
     */
    public function approveWithdrawal(WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->approve();
        
        return redirect()->route('admin.affiliates.withdrawal-requests')
            ->with('success', 'تم الموافقة على طلب السحب بنجاح.');
    }

    /**
     * Reject a withdrawal request.
     */
    public function rejectWithdrawal(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $withdrawalRequest->reject($validated['rejection_reason']);
        
        return redirect()->route('admin.affiliates.withdrawal-requests')
            ->with('success', 'تم رفض طلب السحب بنجاح وتمت إعادة المبلغ إلى رصيد المسوق.');
    }

    /**
     * Mark a withdrawal request as paid.
     */
    public function markWithdrawalAsPaid(Request $request, WithdrawalRequest $withdrawalRequest)
    {
        $validated = $request->validate([
            'transaction_reference' => 'required|string|max:255',
        ]);
        
        $withdrawalRequest->markAsPaid($validated['transaction_reference']);
        
        return redirect()->route('admin.affiliates.withdrawal-requests')
            ->with('success', 'تم تحديث طلب السحب كمدفوع بنجاح.');
    }

    /**
     * Display the affiliate dashboard.
     */
    public function dashboard()
    {
        // Get statistics
        $totalAffiliates = Affiliate::count();
        $pendingAffiliates = Affiliate::pending()->count();
        $activeAffiliates = Affiliate::approved()->count();
        $totalEarnings = $this->affiliateService->getTotalEarnings();
        $pendingWithdrawals = $this->affiliateService->getTotalPendingWithdrawals();
        $paidWithdrawals = $this->affiliateService->getTotalPaidWithdrawals();
        $conversionRate = $this->affiliateService->getOverallConversionRate();
        
        // Get top affiliates
        $topAffiliates = $this->affiliateService->getTopAffiliates(5);
        
        // Get top affiliate links
        $topLinks = $this->affiliateService->getTopAffiliateLinks(5);
        
        // Get recent transactions
        $recentTransactions = CommissionTransaction::with('affiliate.user')
            ->latest()
            ->take(10)
            ->get();
        
        // Get recent withdrawal requests
        $recentWithdrawals = WithdrawalRequest::with('affiliate.user')
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.affiliates.dashboard', compact(
            'totalAffiliates',
            'pendingAffiliates',
            'activeAffiliates',
            'totalEarnings',
            'pendingWithdrawals',
            'paidWithdrawals',
            'conversionRate',
            'topAffiliates',
            'topLinks',
            'recentTransactions',
            'recentWithdrawals'
        ));
    }
} 