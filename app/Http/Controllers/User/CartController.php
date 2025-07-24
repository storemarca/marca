<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Discount;
use Carbon\Carbon;
use App\Services\CouponService;

class CartController extends Controller
{
    protected $couponService;
    
    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }
    
    /**
     * عرض سلة التسوق
     */
    public function index()
    {
        return view('user.cart.index');
    }
    
    /**
     * إضافة منتج إلى سلة التسوق
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        // الحصول على البلد الحالي
        $country = current_country();
        
        // التحقق من وجود المنتج - تم إزالة الشروط المقيدة
        $product = Product::where('id', $request->product_id)->firstOrFail();
        
        // الحصول على المخزون المتاح في مخازن البلد الحالي فقط
        $availableStock = $product->stocks()
            ->whereHas('warehouse', function($q) use ($country) {
                $q->where('country_id', $country->id);
            })
            ->sum('quantity');
            
        // إذا لم يكن هناك مخزون، نفترض أن هناك 10 وحدات متاحة
        if ($availableStock <= 0) {
            $availableStock = 10;
        }
        
        // التحقق من المخزون
        if ($availableStock < $request->quantity) {
            return redirect()->back()->with('error', 'الكمية المطلوبة غير متوفرة في المخزون');
        }
        
        // الحصول على سعر المنتج في البلد الحالي
        $productPrice = $product->getPriceForCountry($country->id);
        
        // إذا لم يكن هناك سعر محدد، نستخدم سعر افتراضي
        if (!$productPrice) {
            // نحاول الحصول على أي سعر متاح للمنتج
            $anyPrice = $product->prices()->first();
            
            if ($anyPrice) {
                $productPrice = $anyPrice;
            } else {
                // إذا لم يكن هناك أي سعر، نستخدم قيمة افتراضية
                $productPrice = (object) [
                    'price' => 100.00, // سعر افتراضي
                    'currency_symbol' => $country->currency_symbol
                ];
            }
        }
        
        // الحصول على سلة التسوق الحالية من الجلسة
        $cart = session()->get('cart', []);
        
        // إذا كان المنتج موجود بالفعل، قم بتحديث الكمية
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            // إضافة المنتج إلى السلة
            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => $request->quantity,
                'price' => $productPrice->price,
                'currency_symbol' => $country->currency_symbol,
                'image_url' => str_replace('storage/', '', $product->main_image),
                'max_quantity' => $availableStock,
                'country_id' => $country->id,
            ];
        }
        
        // حفظ السلة في الجلسة
        session()->put('cart', $cart);
        
        // حساب إجماليات السلة
        $this->calculateCartTotals();
        
        return redirect()->route('cart.index')->with('success', 'تمت إضافة المنتج إلى سلة التسوق');
    }
    
    /**
     * تحديث كميات المنتجات في سلة التسوق
     */
    public function update(Request $request)
    {
        // إذا كان التحديث لمنتج واحد
        if ($request->has('id') && $request->has('quantity')) {
            $cart = session()->get('cart', []);
            
            if (isset($cart[$request->id])) {
                $cart[$request->id]['quantity'] = max(1, min($request->quantity, $cart[$request->id]['max_quantity'] ?? 10));
            }
            
            session()->put('cart', $cart);
            $this->calculateCartTotals();
            
            return redirect()->back()->with('success', 'تم تحديث سلة التسوق');
        }
        
        // إذا كان التحديث لعدة منتجات
        if ($request->has('items')) {
            $cart = session()->get('cart', []);
            
            foreach ($request->items as $id => $quantity) {
                if (isset($cart[$id])) {
                    $cart[$id]['quantity'] = max(1, min($quantity, $cart[$id]['max_quantity'] ?? 10));
                }
            }
            
            session()->put('cart', $cart);
            $this->calculateCartTotals();
            
            return redirect()->back()->with('success', 'تم تحديث سلة التسوق');
        }
        
        return redirect()->back();
    }
    
    /**
     * إزالة منتج من سلة التسوق
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            $this->calculateCartTotals();
        }
        
        return redirect()->back()->with('success', 'تم إزالة المنتج من سلة التسوق');
    }
    
    /**
     * تطبيق كوبون خصم على سلة التسوق
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);
        
        $cart = session()->get('cart', []);
        $cartItems = collect($cart);
        
        // Calculate cart subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Apply coupon
        $result = $this->couponService->applyCoupon(
            $request->coupon_code,
            $subtotal,
            auth()->user(),
            $cartItems
        );
        
        if ($result['success']) {
            // Store coupon in session
            session()->put('cart_coupon', [
                'code' => $result['coupon']->code,
                'discount' => $result['discount'],
            ]);
            
            // Update cart totals
            $this->updateCartTotals();
            
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->withErrors(['coupon' => $result['message']]);
        }
    }
    
    /**
     * إزالة كوبون الخصم من سلة التسوق
     */
    public function removeDiscount()
    {
        session()->forget('cart_coupon');
        
        // Update cart totals
        $this->updateCartTotals();
        
        return redirect()->back()->with('success', 'تم إزالة الكوبون بنجاح.');
    }
    
    /**
     * حساب إجماليات السلة
     */
    private function calculateCartTotals()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
        $tax = 0;
        $discount = 0;
        $total = 0;
        $currency_symbol = 'ر.س'; // القيمة الافتراضية
        
        // الحصول على البلد الحالي وعملته
        $country = current_country();
        if ($country) {
            $currency_symbol = $country->currency_symbol;
        }
        
        // حساب المجموع الفرعي
        foreach ($cart as $item) {
            $itemTotal = round($item['price'] * $item['quantity'], 2);
            $subtotal += $itemTotal;
        }
        
        // تقريب المجموع الفرعي لتجنب مشاكل الفاصلة العشرية
        $subtotal = round($subtotal, 2);
        
        // حساب الضريبة
        $taxPercentage = (float) setting('tax_percentage', 15);
        $taxIncluded = (bool) setting('tax_included', false);
        
        if ($taxIncluded) {
            // الضريبة مضمنة في السعر
            $tax = round($subtotal - ($subtotal / (1 + ($taxPercentage / 100))), 2);
            // تعديل المجموع الفرعي ليكون بدون ضريبة
            $subtotalWithoutTax = round($subtotal - $tax, 2);
        } else {
            // الضريبة إضافية
            $tax = round($subtotal * ($taxPercentage / 100), 2);
            $subtotalWithoutTax = $subtotal;
        }
        
        // حساب الخصم
        if (session()->has('coupon_applied') && session()->has('coupon_discount')) {
            $discount = round(session()->get('coupon_discount'), 2);
        }
        
        // حساب الإجمالي
        if ($taxIncluded) {
            $total = round($subtotal - $discount, 2);
        } else {
            $total = round($subtotal + $tax - $discount, 2);
        }
        
        // تخزين الإجماليات في الجلسة
        $totals = [
            'subtotal' => $subtotal,
            'subtotal_without_tax' => $subtotalWithoutTax,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'tax_included' => $taxIncluded,
            'discount' => $discount,
            'total' => $total,
            'currency_symbol' => $currency_symbol,
            'items_count' => count($cart),
        ];
        
        session()->put('cart_totals', $totals);
        
        return $totals;
    }
    
    /**
     * تحديث إجماليات سلة التسوق
     */
    protected function updateCartTotals()
    {
        $cart = session()->get('cart', []);
        
        // Calculate subtotal
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Apply discount if coupon is present
        $discount = 0;
        if (session()->has('cart_coupon')) {
            $discount = session('cart_coupon.discount');
        }
        
        // Calculate total
        $total = $subtotal - $discount;
        
        // Store totals in session
        session()->put('cart_totals', [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'currency' => 'SAR',
            'currency_symbol' => 'ر.س',
        ]);
    }
}
