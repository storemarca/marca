<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShippingCompany;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\OrderStatusChanged;

class OrderController extends Controller
{
    /**
     * عرض قائمة الطلبات
     */
    public function index(Request $request)
    {
        $query = Order::query()->with(['customer', 'shipment']);
        
        // البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }
        
        // التصفية حسب الحالة
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // التصفية حسب حالة الدفع
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }
        
        // التصفية حسب البلد
        if ($request->has('country_id') && $request->country_id) {
            $query->where('shipping_country_id', $request->country_id);
        }
        
        // التصفية حسب التاريخ
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->latest()->paginate(15);
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * عرض تفاصيل طلب محدد
     */
    public function show(string $id)
    {
        $order = Order::with([
            'items.product', 
            'customer', 
            'shipment.shippingCompany',
            'shippingCompany'
        ])->findOrFail($id);
        
        $shippingCompanies = ShippingCompany::all();
        
        return view('admin.orders.show', compact('order', 'shippingCompanies'));
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string'
        ]);
        
        // تحديث حالة الطلب
        $oldStatus = $order->status;
        $order->status = $validated['status'];
        if ($request->has('notes')) {
            $order->notes = $validated['notes'];
        }
        $order->save();
        
        // إرسال إشعار للعميل إذا تغيرت الحالة
        if ($oldStatus !== $validated['status']) {
            $customer = $order->customer;
            if ($customer && $customer->user) {
                $customer->user->notify(new OrderStatusChanged($order, $validated['status']));
            }
        }
        
        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'تم تحديث حالة الطلب بنجاح');
    }
    
    /**
     * تحديث حالة الدفع
     */
    public function updatePaymentStatus(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_notes' => 'nullable|string'
        ]);
        
        // تحديث حالة الدفع
        $order->payment_status = $validated['payment_status'];
        if ($request->has('payment_notes')) {
            $order->payment_notes = $validated['payment_notes'];
        }
        $order->save();
        
        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'تم تحديث حالة الدفع بنجاح');
    }
    
    /**
     * إنشاء شحنة للطلب
     */
    public function createShipment(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        
        // التحقق من عدم وجود شحنة سابقة
        if ($order->shipment) {
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'يوجد بالفعل شحنة مرتبطة بهذا الطلب');
        }
        
        $validated = $request->validate([
            'shipping_company_id' => 'required|exists:shipping_companies,id',
            'tracking_number' => 'required|string|max:100|unique:shipments',
            'expected_delivery_date' => 'required|date|after:today',
            'notes' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            // إنشاء الشحنة
            $shipment = new Shipment();
            $shipment->order_id = $order->id;
            $shipment->shipping_company_id = $validated['shipping_company_id'];
            $shipment->tracking_number = $validated['tracking_number'];
            $shipment->status = 'processing';
            $shipment->expected_delivery_date = $validated['expected_delivery_date'];
            $shipment->notes = $validated['notes'] ?? null;
            $shipment->save();
            
            // تحديث حالة الطلب وإضافة معرف شركة الشحن
            $order->status = 'shipped';
            $order->shipping_company_id = $validated['shipping_company_id'];
            $order->tracking_number = $validated['tracking_number'];
            $order->save();
            
            DB::commit();
            
            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'تم إنشاء الشحنة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الشحنة: ' . $e->getMessage());
        }
    }
    
    /**
     * إلغاء طلب
     */
    public function cancelOrder(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        
        // لا يمكن إلغاء طلب تم شحنه أو تسليمه
        if (in_array($order->status, ['shipped', 'delivered'])) {
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'لا يمكن إلغاء طلب تم شحنه أو تسليمه');
        }
        
        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);
        
        // تحديث حالة الطلب
        $order->status = 'cancelled';
        $order->cancellation_reason = $validated['cancellation_reason'];
        $order->cancelled_at = now();
        $order->save();
        
        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'تم إلغاء الطلب بنجاح');
    }
    
    /**
     * عرض وطباعة فاتورة الطلب
     */
    public function invoice(string $id)
    {
        $order = Order::with(['items.product', 'customer', 'shippingCompany'])->findOrFail($id);
        
        return view('admin.orders.invoice', compact('order'));
    }
    
    /**
     * تحميل فاتورة الطلب كملف PDF
     */
    public function downloadInvoice(string $id)
    {
        $order = Order::with(['items.product', 'customer', 'shippingCompany'])->findOrFail($id);
        
        $pdf = PDF::loadView('admin.orders.invoice_pdf', compact('order'));
        
        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
