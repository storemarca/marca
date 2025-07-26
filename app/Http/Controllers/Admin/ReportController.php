<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\ProductStock;
use App\Models\Order;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use App\Models\Country;


class ReportController extends Controller
{
    public function stockMovements(Request $request)
    {
        $query = StockMovement::with(['product', 'warehouse', 'user']);

        // فلاتر حسب المنتج، المستودع، ونوع العملية
        foreach (['product_id', 'warehouse_id', 'operation'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        // فلترة حسب نطاق التواريخ
        if ($request->filled('date_range')) {
            $this->applyDateRangeFilter($query, $request->input('date_range'));
        }

        // فلترة حسب تواريخ بداية ونهاية مخصصة
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20);
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.reports.stock-movements', compact('movements', 'products', 'warehouses'));
    }

    // دالة مساعدة لتطبيق فلتر نطاق التاريخ بناءً على القيمة المطلوبة
    protected function applyDateRangeFilter($query, $range)
    {
        switch ($range) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                      ->whereYear('created_at', Carbon::now()->subMonth()->year);
                break;
            case 'custom':
                // لا نفعل شيء هنا، لأنه سيتم تطبيق start_date و end_date بشكل منفصل
                break;
        }
    }

    public function inventory(Request $request)
    {
        $query = ProductStock::with(['product.category', 'warehouse']);

        // فلاتر البحث
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('quantity_filter')) {
            switch ($request->quantity_filter) {
                case 'low':
                    $query->where('quantity', '<=', 5)->where('quantity', '>', 0);
                    break;
                case 'out':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'available':
                    $query->where('quantity', '>', 0);
                    break;
            }
        }

        // الحصول على البيانات
        $stocks = $query->orderBy('quantity', 'asc')->paginate(20);
        
        // إحصائيات
        $totalStock = ProductStock::sum('quantity');
        $lowStockCount = ProductStock::where('quantity', '<=', 5)->where('quantity', '>', 0)->count();
        $outOfStockCount = ProductStock::where('quantity', '<=', 0)->count();
        
        // الحصول على قوائم للفلاتر
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.reports.inventory', compact(
            'stocks', 
            'products', 
            'warehouses', 
            'totalStock', 
            'lowStockCount', 
            'outOfStockCount'
        ));
    }

    /**
     * عرض تقرير المبيعات
     */
    public function sales(Request $request)
{
    $query = Order::with(['customer', 'items.product']);

    // فلترة حسب نطاق التواريخ
    if ($request->filled('date_range')) {
        $this->applyDateRangeFilter($query, $request->input('date_range'));
    }

    // فلترة حسب تواريخ بداية ونهاية مخصصة
    if ($request->filled('start_date')) {
        $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
    }
    if ($request->filled('end_date')) {
        $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
    }

    // فلترة حسب حالة الطلب
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // فلترة حسب طريقة الدفع
    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    $orders = $query->orderBy('created_at', 'desc')->paginate(20);
    
    // إحصائيات
    $totalSales = Order::where('status', '!=', 'cancelled')->sum('total_amount');
    $totalOrders = Order::where('status', '!=', 'cancelled')->count();
    $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
    
    // إحصائيات إضافية
    $salesByMonth = $this->getSalesByMonth();
    $topProducts = $this->getTopSellingProducts();

    // ✅ إضافة التاريخ الافتراضي
    $startDate = Carbon::now()->subMonths(11)->startOfMonth();
    $endDate = Carbon::now()->endOfMonth();
    $countries = Country::orderBy('name')->get();
    $salesByDate = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
    ->where('status', '!=', 'cancelled')
    ->groupBy('date')
    ->orderBy('date', 'desc')
    ->get();    
    $salesByCountry = Order::select('shipping_country', \DB::raw('SUM(total_amount) as total_sales'))
    ->where('status', '!=', 'cancelled')
    ->groupBy('shipping_country')
    ->orderByDesc('total_sales')
    ->get();
    
    return view('admin.reports.sales', compact(
        'orders',
        'totalSales',
        'totalOrders',
        'averageOrderValue',
        'salesByMonth',
        'topProducts',
        'startDate',
        'endDate',
        'countries',
        'salesByDate',
        'salesByCountry'
    ));
}
    
    /**
     * الحصول على المبيعات حسب الشهر
     */
    protected function getSalesByMonth()
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        
        $salesByMonth = Order::where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        return $salesByMonth;
    }
    
    /**
     * الحصول على المنتجات الأكثر مبيعًا
     */
    protected function getTopSellingProducts($limit = 10)
    {
        return \DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->select('products.id', 'products.name', \DB::raw('SUM(order_items.quantity) as total_quantity'), \DB::raw('SUM(order_items.subtotal) as total_sales'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * عرض تقرير التحصيلات
     */
    public function collections(Request $request)
{
    $query = Order::with('customer')->where('status', '!=', 'cancelled');

    // فلترة حسب نطاق التاريخ الجاهز
    if ($request->filled('date_range')) {
        $this->applyDateRangeFilter($query, $request->input('date_range'));
    }

    // فلترة حسب تواريخ بداية ونهاية مخصصة
    if ($request->filled('start_date')) {
        $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
    }
    if ($request->filled('end_date')) {
        $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
    }

    // فلترة حسب طريقة الدفع
    if ($request->filled('payment_method')) {
        $query->where('payment_method', $request->payment_method);
    }

    // التحصيلات
    $totalCollections = $query->sum('total_amount');

    // تحميل الطلبات مع بيانات التصفية
    $collections = $query->orderBy('created_at', 'desc')->paginate(20);

    return view('admin.reports.collections', compact('collections', 'totalCollections'));
}
    
    /**
     * عرض تقرير المشتريات
     */
    public function purchases(Request $request)
    {
        $query = PurchaseOrder::query();

        // فلترة حسب نطاق التاريخ
        if ($request->filled('date_range')) {
            $this->applyDateRangeFilter($query, $request->input('date_range'));
        }

        // فلترة حسب تواريخ بداية ونهاية مخصصة
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.reports.purchases', compact('purchaseOrders'));
    }
}
