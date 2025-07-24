<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\PaymentTransaction;
use App\Models\ReturnRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Get sales report data
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $groupBy day|week|month|year
     * @return Collection
     */
    public function getSalesReport(string $startDate, string $endDate, string $groupBy = 'day'): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        $dateFormat = $this->getDateFormat($groupBy);
        
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('AVG(total) as average_order_value')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    /**
     * Get product sales report
     *
     * @param string $startDate
     * @param string $endDate
     * @param int|null $categoryId
     * @return Collection
     */
    public function getProductSalesReport(string $startDate, string $endDate, ?int $categoryId = null): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        $query = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', Order::STATUS_CANCELLED);
        
        if ($categoryId) {
            $query->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                ->where('product_categories.category_id', $categoryId);
        }
        
        return $query->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('quantity_sold')
            ->get();
    }
    
    /**
     * Get category sales report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getCategorySalesReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->select(
                'categories.id as category_id',
                'categories.name as category_name',
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as total_sales')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();
    }
    
    /**
     * Get customer sales report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getCustomerSalesReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return Order::join('customers', 'orders.customer_id', '=', 'customers.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->select(
                'customers.id as customer_id',
                'customers.name as customer_name',
                'customers.email as customer_email',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw('AVG(orders.total) as average_order_value')
            )
            ->groupBy('customers.id', 'customers.name', 'customers.email')
            ->orderByDesc('total_spent')
            ->get();
    }
    
    /**
     * Get payment method report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getPaymentMethodReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return PaymentTransaction::join('payment_gateways', 'payment_transactions.payment_gateway_id', '=', 'payment_gateways.id')
            ->whereBetween('payment_transactions.created_at', [$startDate, $endDate])
            ->where('payment_transactions.status', 'completed')
            ->select(
                'payment_gateways.name as payment_method',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(payment_transactions.amount) as total_amount'),
                DB::raw('AVG(payment_transactions.amount) as average_amount')
            )
            ->groupBy('payment_gateways.name')
            ->orderByDesc('total_amount')
            ->get();
    }
    
    /**
     * Get inventory report
     *
     * @param int|null $warehouseId
     * @param int|null $categoryId
     * @return Collection
     */
    public function getInventoryReport(?int $warehouseId = null, ?int $categoryId = null): Collection
    {
        $query = ProductStock::join('products', 'product_stocks.product_id', '=', 'products.id')
            ->join('warehouses', 'product_stocks.warehouse_id', '=', 'warehouses.id');
        
        if ($warehouseId) {
            $query->where('product_stocks.warehouse_id', $warehouseId);
        }
        
        if ($categoryId) {
            $query->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                ->where('product_categories.category_id', $categoryId);
        }
        
        return $query->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                'warehouses.name as warehouse_name',
                'product_stocks.quantity',
                'product_stocks.reserved_quantity',
                DB::raw('(product_stocks.quantity - product_stocks.reserved_quantity) as available_quantity'),
                'product_stocks.low_stock_threshold',
                'product_stocks.reorder_point',
                'product_stocks.last_restock_date'
            )
            ->orderBy('products.name')
            ->get();
    }
    
    /**
     * Get low stock report
     *
     * @param int|null $warehouseId
     * @return Collection
     */
    public function getLowStockReport(?int $warehouseId = null): Collection
    {
        $query = ProductStock::join('products', 'product_stocks.product_id', '=', 'products.id')
            ->join('warehouses', 'product_stocks.warehouse_id', '=', 'warehouses.id')
            ->whereRaw('product_stocks.quantity <= product_stocks.low_stock_threshold')
            ->where('product_stocks.low_stock_threshold', '>', 0);
        
        if ($warehouseId) {
            $query->where('product_stocks.warehouse_id', $warehouseId);
        }
        
        return $query->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                'warehouses.name as warehouse_name',
                'product_stocks.quantity',
                'product_stocks.reserved_quantity',
                DB::raw('(product_stocks.quantity - product_stocks.reserved_quantity) as available_quantity'),
                'product_stocks.low_stock_threshold',
                'product_stocks.reorder_point'
            )
            ->orderBy('available_quantity')
            ->get();
    }
    
    /**
     * Get return report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getReturnReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return ReturnRequest::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('status'),
                DB::raw('COUNT(*) as return_count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->groupBy('status')
            ->get();
    }
    
    /**
     * Get product return report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getProductReturnReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return DB::table('return_items')
            ->join('returns', 'return_items.return_id', '=', 'returns.id')
            ->join('products', 'return_items.product_id', '=', 'products.id')
            ->whereBetween('returns.created_at', [$startDate, $endDate])
            ->select(
                'products.id as product_id',
                'products.name as product_name',
                'products.sku as product_sku',
                DB::raw('COUNT(*) as return_count'),
                DB::raw('SUM(return_items.quantity) as quantity_returned'),
                DB::raw('SUM(return_items.subtotal) as total_amount')
            )
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('quantity_returned')
            ->get();
    }
    
    /**
     * Get return reason report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getReturnReasonReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return DB::table('return_items')
            ->join('returns', 'return_items.return_id', '=', 'returns.id')
            ->whereBetween('returns.created_at', [$startDate, $endDate])
            ->select(
                'return_items.reason',
                DB::raw('COUNT(*) as return_count'),
                DB::raw('SUM(return_items.quantity) as quantity_returned'),
                DB::raw('SUM(return_items.subtotal) as total_amount')
            )
            ->groupBy('return_items.reason')
            ->orderByDesc('return_count')
            ->get();
    }
    
    /**
     * Get sales by country report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getSalesByCountryReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return Order::join('addresses', 'orders.shipping_address_id', '=', 'addresses.id')
            ->join('countries', 'addresses.country_id', '=', 'countries.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->select(
                'countries.id as country_id',
                'countries.name as country_name',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total) as total_sales')
            )
            ->groupBy('countries.id', 'countries.name')
            ->orderByDesc('total_sales')
            ->get();
    }
    
    /**
     * Get coupon usage report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getCouponUsageReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return Order::join('coupons', 'orders.coupon_id', '=', 'coupons.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->select(
                'coupons.id as coupon_id',
                'coupons.code as coupon_code',
                'coupons.type as coupon_type',
                'coupons.value as coupon_value',
                DB::raw('COUNT(orders.id) as usage_count'),
                DB::raw('SUM(orders.discount) as total_discount'),
                DB::raw('SUM(orders.total) as total_sales')
            )
            ->groupBy('coupons.id', 'coupons.code', 'coupons.type', 'coupons.value')
            ->orderByDesc('usage_count')
            ->get();
    }
    
    /**
     * Get user activity report
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getUserActivityReport(string $startDate, string $endDate): Collection
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        return User::leftJoin('activity_log', 'users.id', '=', 'activity_log.causer_id')
            ->whereBetween('activity_log.created_at', [$startDate, $endDate])
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                DB::raw('COUNT(activity_log.id) as activity_count')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('activity_count')
            ->get();
    }
    
    /**
     * Get date format for SQL based on group by parameter
     *
     * @param string $groupBy
     * @return string
     */
    protected function getDateFormat(string $groupBy): string
    {
        switch ($groupBy) {
            case 'day':
                return '%Y-%m-%d';
            case 'week':
                return '%x-W%v';
            case 'month':
                return '%Y-%m';
            case 'year':
                return '%Y';
            default:
                return '%Y-%m-%d';
        }
    }
    
    /**
     * Get dashboard statistics
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();
        
        return [
            'sales' => [
                'today' => $this->getSalesTotal($today, $today->copy()->endOfDay()),
                'yesterday' => $this->getSalesTotal($yesterday, $yesterday->copy()->endOfDay()),
                'this_month' => $this->getSalesTotal($thisMonth, Carbon::now()),
                'last_month' => $this->getSalesTotal($lastMonth, $lastMonth->copy()->endOfMonth()),
                'this_year' => $this->getSalesTotal($thisYear, Carbon::now()),
            ],
            'orders' => [
                'today' => $this->getOrderCount($today, $today->copy()->endOfDay()),
                'pending' => $this->getOrderCountByStatus(Order::STATUS_PENDING),
                'processing' => $this->getOrderCountByStatus(Order::STATUS_PROCESSING),
                'shipped' => $this->getOrderCountByStatus(Order::STATUS_SHIPPED),
                'delivered' => $this->getOrderCountByStatus(Order::STATUS_DELIVERED),
            ],
            'customers' => [
                'total' => Customer::count(),
                'new_today' => Customer::whereDate('created_at', $today)->count(),
            ],
            'products' => [
                'total' => Product::count(),
                'low_stock' => ProductStock::whereRaw('quantity <= low_stock_threshold')->where('low_stock_threshold', '>', 0)->count(),
                'out_of_stock' => ProductStock::where('quantity', 0)->count(),
            ],
            'returns' => [
                'pending' => ReturnRequest::where('status', ReturnRequest::STATUS_PENDING)->count(),
                'approved' => ReturnRequest::where('status', ReturnRequest::STATUS_APPROVED)->count(),
            ],
        ];
    }
    
    /**
     * Get total sales for a date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    protected function getSalesTotal(Carbon $startDate, Carbon $endDate): float
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('total') ?? 0;
    }
    
    /**
     * Get order count for a date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    protected function getOrderCount(Carbon $startDate, Carbon $endDate): int
    {
        return Order::whereBetween('created_at', [$startDate, $endDate])->count();
    }
    
    /**
     * Get order count by status
     *
     * @param string $status
     * @return int
     */
    protected function getOrderCountByStatus(string $status): int
    {
        return Order::where('status', $status)->count();
    }
}