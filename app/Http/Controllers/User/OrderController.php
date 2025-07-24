<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * عرض قائمة الطلبات للمستخدم
     */
    public function index(Request $request)
    {
        $query = Order::where('customer_id', auth()->id());
        
        // تصفية حسب الحالة
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $orders = $query->orderBy('created_at', 'desc')
                       ->withCount('items')
                       ->paginate(10);
        
        return view('user.orders.index', compact('orders'));
    }
    
    /**
     * عرض تفاصيل طلب محدد
     */
    public function show($id)
    {
        $order = Order::with([
                'items.product', 
                'shipments.shippingCompany', 
                'shippingMethod',
                'country',
                'shippingCompany'
            ])
            ->where('customer_id', auth()->id())
            ->findOrFail($id);
        
        return view('user.orders.show', compact('order'));
    }
    
    /**
     * تتبع الطلب للزوار
     */
    public function track(Request $request)
    {
        // إذا تم تقديم نموذج البحث
        if ($request->has('order_number') && $request->has('order_token')) {
            $order = Order::with([
                    'items.product', 
                    'shipments.shippingCompany', 
                    'shipments.items',
                    'country',
                    'shippingCompany'
                ])
                ->where('order_number', $request->order_number)
                ->where('token', $request->order_token)
                ->first();
            
            if (!$order) {
                return view('user.orders.track')
                    ->with('error', 'لم يتم العثور على الطلب. يرجى التحقق من رقم الطلب ورمز التتبع.');
            }
            
            return view('user.orders.track', compact('order'));
        }
        
        // عرض نموذج البحث فقط
        return view('user.orders.track', ['showSearchForm' => true]);
    }

    /**
     * تتبع الطلب للزوار
     */
    public function trackOrder(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('user.orders.track');
        }
        
        $validated = $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email',
        ]);
        
        $order = Order::with([
                'items.product', 
                'shipments.shippingCompany', 
                'country',
                'shippingCompany'
            ])
            ->where('order_number', $validated['order_number'])
            ->whereHas('customer', function($query) use ($validated) {
                $query->where('email', $validated['email']);
            })
            ->first();
        
        if (!$order) {
            return back()->with('error', 'لم يتم العثور على الطلب. يرجى التحقق من رقم الطلب والبريد الإلكتروني.');
        }
        
        return view('user.orders.show', [
            'order' => $order,
            'isGuestTracking' => true
        ]);
    }
}
