<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    protected $returnService;
    
    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }
    
    /**
     * Display a listing of the return requests.
     */
    public function index(Request $request)
    {
        $query = ReturnRequest::with(['order', 'customer']);
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $returnRequests = $query->paginate(20);
        
        return view('admin.returns.index', compact('returnRequests'));
    }
    
    /**
     * Display the specified return request.
     */
    public function show(ReturnRequest $return)
    {
        $return->load(['order', 'customer', 'items.product', 'items.orderItem']);
        
        return view('admin.returns.show', compact('return'));
    }
    
    /**
     * Update the status of a return request.
     */
    public function updateStatus(Request $request, ReturnRequest $return)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        try {
            $this->returnService->updateReturnStatus($return, $validated['status'], $validated['admin_notes'] ?? null);
            
            return redirect()->route('admin.returns.show', $return)
                ->with('success', 'Return request status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Export return requests to CSV.
     */
    public function export(Request $request)
    {
        $query = ReturnRequest::with(['order', 'customer']);
        
        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $returnRequests = $query->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="returns-' . date('Y-m-d') . '.csv"',
        ];
        
        $columns = ['Return Number', 'Order Number', 'Customer', 'Status', 'Total Amount', 'Date', 'Reason'];
        
        $callback = function() use ($returnRequests, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($returnRequests as $return) {
                fputcsv($file, [
                    $return->return_number,
                    $return->order->order_number,
                    $return->customer->name,
                    $return->status,
                    $return->total_amount,
                    $return->created_at->format('Y-m-d H:i:s'),
                    $return->reason,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 