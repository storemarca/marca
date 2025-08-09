<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * تقرير المخزون
     */
    public function inventory(Request $request)
    {
        // Query with relationships
        $query = ProductStock::with(['product.category', 'warehouse']);
        
        // Product filter
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        // Quantity filter
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
        
        // Get paginated stocks
        $stocks = $query->paginate(15);
        
        // Calculate statistics
        $totalStock = ProductStock::sum('quantity');
        $lowStockCount = ProductStock::where('quantity', '<=', 5)->where('quantity', '>', 0)->count();
        $outOfStockCount = ProductStock::where('quantity', '<=', 0)->count();
        
        // Get all products and warehouses for filters
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        
        return view('admin.reports.inventory', compact(
            'stocks',
            'totalStock',
            'lowStockCount',
            'outOfStockCount',
            'products',
            'warehouses'
        ));
    }
    
    /**
     * تقرير التحصيلات
     */
public function collections(Request $request)
{
    $query = Collection::with(['shipment.order.customer', 'collector']);

    // فلاتر التاريخ
    if ($request->filled('date_range')) {
        $this->applyDateRangeFilter($query, $request->date_range);
    }

    if ($request->filled('start_date')) {
        $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
    }

    if ($request->filled('end_date')) {
        $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
    }

    // فلتر طريقة الدفع
    if ($request->filled('payment_method')) {
        $query->whereHas('shipment.order', function ($q) use ($request) {
            $q->where('payment_method', $request->payment_method);
        });
    }

    $query->orderBy('created_at', 'desc');

    // جلب التحصيلات
    $collections = $query->get();

    // استخراج الطلبات المرتبطة
    $orders = $collections->pluck('shipment.order')->filter()->unique('id')->values();

    // إحصائيات
    $ordersCount = $orders->count();
    $totalCollections = $collections->sum('amount');

    // ترتيب الطلبات وعمل pagination يدوي
    $perPage = 10;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $pagedOrders = new LengthAwarePaginator(
        $orders->forPage($currentPage, $perPage),
        $orders->count(),
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return view('admin.reports.collections', [
        'collections' => $collections,
        'ordersCount' => $ordersCount,
        'totalCollections' => $totalCollections,
        'orders' => $pagedOrders
    ]);
}



    /**
     * تقرير الطلبات
     */
    public function orders(Request $request)
    {
        $query = Order::with(['customer', 'shipments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->start_date));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->end_date));
        }

        $orders = $query->paginate(20);

        return view('admin.reports.orders', compact('orders'));
    }

    /**
     * تقرير أداء المساعدين
     */
    public function assistants(Request $request)
    {
        $assistants = User::whereHas('roles', function ($q) {
            $q->where('name', 'assistant');
        })->withCount([
            'collections as total_collections',
            'collections as total_collected_amount' => function ($q) {
                $q->select(DB::raw('SUM(amount)'))->where('status', 'collected');
            },
            'orders as total_orders',
        ])->get();

        return view('admin.reports.assistants', compact('assistants'));
    }

    /**
     * تطبيق فلاتر التاريخ السريعة
     */
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
                $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month)->whereYear('created_at', Carbon::now()->subMonth()->year);
                break;
        }
    }
}
