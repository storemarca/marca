<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Services\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    protected $returnService;
    
    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the user's return requests.
     */
    public function index()
    {
        $returnRequests = ReturnRequest::forCustomer(Auth::id())
            ->with(['order', 'items'])
            ->latest()
            ->paginate(10);
            
        return view('user.returns.index', compact('returnRequests'));
    }
    
    /**
     * Show the form for creating a new return request.
     */
    public function create(Request $request)
    {
        $order = Order::where('customer_id', Auth::id())
            ->where('id', $request->order_id)
            ->with(['items.product', 'items.returnItems'])
            ->firstOrFail();
            
        // Check if the order is returnable
        if (!$order->isReturnable()) {
            return redirect()->route('user.orders.show', $order)
                ->with('error', 'This order is not eligible for return.');
        }
        
        return view('user.returns.create', compact('order'));
    }
    
    /**
     * Store a newly created return request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'return_method' => 'required|in:refund,exchange,store_credit',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.condition' => 'required|in:new,like_new,used,damaged',
            'items.*.reason' => 'required|string|max:500',
        ]);
        
        // Verify that the order belongs to the authenticated user
        $order = Order::where('customer_id', Auth::id())
            ->where('id', $validated['order_id'])
            ->firstOrFail();
        
        try {
            $returnRequest = $this->returnService->createReturnRequest($validated);
            
            return redirect()->route('user.returns.show', $returnRequest)
                ->with('success', 'Return request submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Display the specified return request.
     */
    public function show(ReturnRequest $return)
    {
        // Ensure the return request belongs to the authenticated user
        if ($return->customer_id !== Auth::id()) {
            abort(403);
        }
        
        $return->load(['order', 'items.product', 'items.orderItem']);
        
        return view('user.returns.show', compact('return'));
    }
    
    /**
     * Cancel a return request.
     */
    public function cancel(ReturnRequest $return)
    {
        // Ensure the return request belongs to the authenticated user
        if ($return->customer_id !== Auth::id()) {
            abort(403);
        }
        
        // Only pending returns can be cancelled
        if ($return->status !== ReturnRequest::STATUS_PENDING) {
            return redirect()->route('user.returns.show', $return)
                ->with('error', 'Only pending returns can be cancelled.');
        }
        
        try {
            $this->returnService->updateReturnStatus($return, ReturnRequest::STATUS_CANCELLED);
            
            return redirect()->route('user.returns.show', $return)
                ->with('success', 'Return request cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
} 