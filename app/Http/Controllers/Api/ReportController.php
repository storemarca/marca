<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Generar reporte de ventas
     */
    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        $sales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        return response()->json(['data' => $sales]);
    }
    
    /**
     * Generar reporte de inventario
     */
    public function inventoryReport(Request $request)
    {
        $lowStock = $request->input('low_stock', 5);
        
        $inventory = ProductStock::with('product', 'warehouse')
            ->select('product_id', 'warehouse_id', 
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(reserved_quantity) as total_reserved')
            )
            ->groupBy('product_id', 'warehouse_id')
            ->having('total_quantity', '<=', $lowStock)
            ->get();
            
        return response()->json(['data' => $inventory]);
    }
} 