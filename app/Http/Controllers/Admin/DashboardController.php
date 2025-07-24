<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Collection;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use App\Models\PaymentTransaction;
use App\Models\ReturnRequest;
use App\Models\ProductReview;

class DashboardController extends Controller
{
    /**
     * عرض لوحة التحكم
     */
    public function index(AnalyticsService $analyticsService)
    {
        // الإحصائيات العامة
        $totalOrders = \App\Models\Order::count();
        $totalCustomers = \App\Models\Customer::count();
        $totalProducts = \App\Models\Product::count();
        $totalRevenue = \App\Models\Order::where('status', '!=', 'cancelled')->sum('total_amount');
        
        // إحصائيات الفترة الحالية (هذا الشهر)
        $startOfMonth = Carbon::now()->startOfMonth();
        $ordersThisMonth = \App\Models\Order::where('created_at', '>=', $startOfMonth)->count();
        $revenueThisMonth = \App\Models\Order::where('created_at', '>=', $startOfMonth)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
        $customersThisMonth = \App\Models\Customer::where('created_at', '>=', $startOfMonth)->count();
        
        // أكثر المنتجات مبيعاً
        $bestSellingProducts = $analyticsService->getBestSellingProducts(5);
        
        // أكثر المنتجات مشاهدة
        $mostViewedProducts = $analyticsService->getMostViewedProducts(5);
        
        // إحصائيات المبيعات حسب الفترة (آخر 30 يوم)
        $salesStats = $analyticsService->getSalesStatsByPeriod('daily', 30);
        
        // إحصائيات المبيعات حسب البلد
        $salesByCountry = $analyticsService->getSalesStatsByCountry(5);
        
        // معدل التحويل
        $conversionRate = $analyticsService->getConversionRate();
        
        // متوسط قيمة الطلب
        $averageOrderValue = $analyticsService->getAverageOrderValue();
        
        // الطلبات الأخيرة
        $latestOrders = \App\Models\Order::with(['customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // العملاء الجدد
        $newCustomers = \App\Models\Customer::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // المنتجات منخفضة المخزون
        $lowStockProducts = \App\Models\ProductStock::with(['product', 'warehouse'])
            ->where('quantity', '>', 0)
            ->where('quantity', '<=', 5)
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get();
            
        // أحدث الشحنات
        $latestShipments = \App\Models\Shipment::with(['order'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // التحصيلات المعلقة
        $pendingCollections = \App\Models\Collection::with(['shipment'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Payment statistics
        $totalPayments = PaymentTransaction::where('status', 'completed')->sum('amount');
        $recentPayments = PaymentTransaction::with(['order', 'paymentGateway'])
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();
            
        $paymentMethodStats = PaymentTransaction::where('status', 'completed')
            ->join('payment_gateways', 'payment_transactions.payment_gateway_id', '=', 'payment_gateways.id')
            ->selectRaw('payment_gateways.name, COUNT(*) as count, SUM(payment_transactions.amount) as total')
            ->groupBy('payment_gateways.name')
            ->get();
        
        // Returns statistics
        $pendingReturns = ReturnRequest::where('status', ReturnRequest::STATUS_PENDING)->count();
        $recentReturns = ReturnRequest::with(['order', 'customer'])
            ->latest()
            ->take(5)
            ->get();
            
        $returnStats = ReturnRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
            
        // Reviews statistics
        $pendingReviews = ProductReview::where('is_approved', false)->count();
        $recentReviews = ProductReview::with(['product', 'customer'])
            ->latest()
            ->take(5)
            ->get();
            
        $reviewStats = ProductReview::selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->get();
        
        // تجميع الإحصائيات في مصفوفة واحدة
        $stats = [
            'total_orders' => $totalOrders,
            'total_sales' => $totalRevenue,
            'total_products' => $totalProducts,
            'pending_collections' => \App\Models\Collection::where('status', 'pending')->count(),
            'orders_this_month' => $ordersThisMonth,
            'revenue_this_month' => $revenueThisMonth,
            'customers_this_month' => $customersThisMonth,
        ];
        
        return view('admin.dashboard', compact(
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'totalRevenue',
            'ordersThisMonth',
            'revenueThisMonth',
            'customersThisMonth',
            'bestSellingProducts',
            'mostViewedProducts',
            'salesStats',
            'salesByCountry',
            'conversionRate',
            'averageOrderValue',
            'latestOrders',
            'newCustomers',
            'lowStockProducts',
            'latestShipments',
            'pendingCollections',
            'stats',
            'totalPayments',
            'recentPayments',
            'paymentMethodStats',
            'pendingReturns',
            'recentReturns',
            'returnStats',
            'pendingReviews',
            'recentReviews',
            'reviewStats'
        ));
    }
}
