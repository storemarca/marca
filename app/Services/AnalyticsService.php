<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use App\Models\ProductView;
use App\Models\SearchQuery;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * تسجيل مشاهدة منتج
     *
     * @param int $productId
     * @param int|null $userId
     * @param string|null $sessionId
     * @return void
     */
    public function logProductView($productId, $userId = null, $sessionId = null)
    {
        ProductView::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'session_id' => $sessionId ?? session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer' => request()->headers->get('referer'),
        ]);
    }

    /**
     * تسجيل استعلام بحث
     *
     * @param string $query
     * @param int $resultsCount
     * @param int|null $userId
     * @param string|null $sessionId
     * @return void
     */
    public function logSearchQuery($query, $resultsCount, $userId = null, $sessionId = null)
    {
        SearchQuery::create([
            'query' => $query,
            'results_count' => $resultsCount,
            'user_id' => $userId,
            'session_id' => $sessionId ?? session()->getId(),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * الحصول على أكثر المنتجات مشاهدة
     *
     * @param int $limit
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMostViewedProducts($limit = 10, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return ProductView::select('product_id', DB::raw('count(*) as views_count'))
            ->with('product')
            ->where('created_at', '>=', $startDate)
            ->groupBy('product_id')
            ->orderByDesc('views_count')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على أكثر المنتجات مبيعاً
     *
     * @param int $limit
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBestSellingProducts($limit = 10, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.images',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id', 'products.name', 'products.slug', 'products.images')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على أكثر عمليات البحث شيوعاً
     *
     * @param int $limit
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularSearchQueries($limit = 10, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return SearchQuery::select('query', DB::raw('count(*) as search_count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('query')
            ->orderByDesc('search_count')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على إحصائيات المبيعات حسب الفترة
     *
     * @param string $period
     * @param int $limit
     * @return array
     */
    public function getSalesStatsByPeriod($period = 'daily', $limit = 30)
    {
        $now = Carbon::now();
        $format = '%Y-%m-%d';
        $startDate = null;
        
        switch ($period) {
            case 'daily':
                $startDate = $now->copy()->subDays($limit);
                $format = '%Y-%m-%d';
                break;
            case 'weekly':
                $startDate = $now->copy()->subWeeks($limit);
                $format = '%Y-%u';
                break;
            case 'monthly':
                $startDate = $now->copy()->subMonths($limit);
                $format = '%Y-%m';
                break;
            case 'yearly':
                $startDate = $now->copy()->subYears($limit);
                $format = '%Y';
                break;
        }
        
        $stats = DB::table('orders')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        return $stats;
    }

    /**
     * الحصول على إحصائيات المبيعات حسب البلد
     *
     * @param int $limit
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSalesStatsByCountry($limit = 10, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return DB::table('orders')
            ->join('countries', 'orders.shipping_country', '=', 'countries.code')
            ->select(
                'countries.id',
                'countries.name',
                'countries.code',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(orders.total_amount) as total_sales')
            )
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('countries.id', 'countries.name', 'countries.code')
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على معدل التحويل
     *
     * @param int $days
     * @return array
     */
    public function getConversionRate($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $totalVisitors = ProductView::select('session_id')
            ->where('created_at', '>=', $startDate)
            ->distinct()
            ->count();
        
        $totalOrders = Order::where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->count();
        
        $conversionRate = $totalVisitors > 0 ? ($totalOrders / $totalVisitors) * 100 : 0;
        
        return [
            'total_visitors' => $totalVisitors,
            'total_orders' => $totalOrders,
            'conversion_rate' => round($conversionRate, 2),
        ];
    }

    /**
     * الحصول على متوسط قيمة الطلب
     *
     * @param int $days
     * @return float
     */
    public function getAverageOrderValue($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $result = Order::where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('AVG(total_amount) as avg_order_value')
            ->first();
        
        return $result ? round($result->avg_order_value, 2) : 0;
    }

    /**
     * الحصول على معدل التخلي عن سلة التسوق
     *
     * @param int $days
     * @return array
     */
    public function getCartAbandonmentRate($days = 30)
    {
        // تنفيذ هذه الوظيفة يتطلب تتبع سلات التسوق المهجورة
        // هذا مثال بسيط يفترض وجود جدول للسلات المهجورة
        
        return [
            'abandoned_carts' => 0,
            'completed_orders' => 0,
            'abandonment_rate' => 0,
        ];
    }

    /**
     * الحصول على تقرير نمو العملاء
     *
     * @param int $months
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomerGrowthReport($months = 12)
    {
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        
        return Customer::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as new_customers')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
} 