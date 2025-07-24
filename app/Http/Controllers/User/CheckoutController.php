<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Country;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $paymentService;
    
    public function __construct(CartService $cartService, PaymentService $paymentService)
    {
        $this->cartService = $cartService;
        $this->paymentService = $paymentService;
    }
    
    /**
     * Display the checkout page.
     */
    public function index()
    {
        // Get cart contents
        $cart = $this->cartService->getCart();
        
        if (count($cart['items']) === 0) {
            return redirect()->route('cart.index')->with('error', 'عربة التسوق فارغة');
        }
        
        // Get user's addresses if logged in
        $addresses = [];
        $customer = null;
        
        if (Auth::check()) {
            $customer = Auth::user()->customer;
            if ($customer) {
                $addresses = $customer->addresses;
            }
        }
        
        // Get available countries
        $countries = Country::where('is_active', true)->get();
        
        // Get available shipping methods
        $shippingMethods = ShippingMethod::where('is_active', true)->get();
        
        // Get available payment gateways
        $paymentGateways = $this->paymentService->getAvailableGateways();
        
        return view('user.checkout.index', compact(
            'cart',
            'addresses',
            'countries',
            'shippingMethods',
            'paymentGateways',
            'customer'
        ));
    }
    
    /**
     * Process the checkout.
     */
    public function process(Request $request)
    {
        // Validate request
        $request->validate([
            'shipping_address_id' => 'nullable|exists:addresses,id',
            'shipping_name' => 'required_without:shipping_address_id|string|max:255',
            'shipping_phone' => 'required_without:shipping_address_id|string|max:20',
            'shipping_address_line1' => 'required_without:shipping_address_id|string|max:255',
            'shipping_city' => 'required_without:shipping_address_id|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_country_id' => 'required_without:shipping_address_id|exists:countries,id',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'payment_gateway' => 'required|string',
            'save_address' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Get cart contents
            $cart = $this->cartService->getCart();
            
            if (count($cart['items']) === 0) {
                return redirect()->route('cart.index')->with('error', 'عربة التسوق فارغة');
            }
            
            // Get or create customer
            $customer = $this->getOrCreateCustomer($request);
            
            // Get or create shipping address
            $shippingAddress = $this->getOrCreateAddress($request, $customer);
            
            // Get shipping method
            $shippingMethod = ShippingMethod::findOrFail($request->shipping_method_id);
            
            // Get country
            $country = Country::findOrFail($request->shipping_country_id ?? $shippingAddress->country_id);
            
            // Create order
            $order = $this->createOrder($request, $customer, $shippingAddress, $shippingMethod, $country, $cart);
            
            // Create order items
            $this->createOrderItems($order, $cart['items']);
            
            // Process payment
            $paymentGateway = $request->payment_gateway;
            $paymentData = $request->input('payment_data', []);
            
            $paymentResult = $this->paymentService->processPayment($order, $paymentGateway, $paymentData);
            
            // Clear cart after successful order creation
            $this->cartService->clearCart();
            
            DB::commit();
            
            // Check if we need to redirect to a payment page
            if (isset($paymentResult['redirect_url'])) {
                return redirect()->away($paymentResult['redirect_url']);
            }
            
            // Redirect to success page
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('order_token', $order->token);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the checkout success page.
     */
    public function success(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Security check - only allow access if user owns the order or has the correct token
        if (Auth::check()) {
            $customer = Auth::user()->customer;
            if (!$customer || $order->customer_id !== $customer->id) {
                if (!$request->has('order_token') || $request->order_token !== $order->token) {
                    abort(403);
                }
            }
        } else {
            if (!$request->has('order_token') || $request->order_token !== $order->token) {
                abort(403);
            }
        }
        
        return view('user.checkout.success', compact('order'));
    }
    
    /**
     * Get or create a customer based on the current user.
     */
    protected function getOrCreateCustomer(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Get existing customer or create a new one
            $customer = $user->customer;
            
            if (!$customer) {
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $request->shipping_phone ?? null,
                ]);
            }
            
            return $customer;
        }
        
        // For guest checkout, create a temporary customer
        return Customer::create([
            'name' => $request->shipping_name,
            'email' => $request->email ?? null,
            'phone' => $request->shipping_phone,
            'is_guest' => true,
        ]);
    }
    
    /**
     * Get an existing address or create a new one.
     */
    protected function getOrCreateAddress(Request $request, Customer $customer)
    {
        // If shipping address ID is provided, use that address
        if ($request->filled('shipping_address_id')) {
            return Address::findOrFail($request->shipping_address_id);
        }
        
        // Create a new address if requested
        if ($request->filled('save_address') && $request->save_address) {
            return Address::create([
                'customer_id' => $customer->id,
                'user_id' => Auth::id(),
                'name' => $request->shipping_name,
                'phone' => $request->shipping_phone,
                'address_line1' => $request->shipping_address_line1,
                'address_line2' => $request->shipping_address_line2,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'postal_code' => $request->shipping_postal_code,
                'country_id' => $request->shipping_country_id,
                'is_default' => false,
            ]);
        }
        
        // Return null if no address is to be saved
        return null;
    }
    
    /**
     * Create a new order.
     */
    protected function createOrder(Request $request, Customer $customer, ?Address $shippingAddress, ShippingMethod $shippingMethod, Country $country, array $cart)
    {
        // Generate a unique order number
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));
        
        // Create the order
        $order = new Order();
        $order->customer_id = $customer->id;
        $order->user_id = Auth::id();
        $order->country_id = $country->id;
        $order->order_number = $orderNumber;
        $order->token = Str::random(32);
        $order->status = Order::STATUS_PENDING;
        $order->payment_status = Order::PAYMENT_PENDING;
        $order->payment_method = $request->payment_gateway;
        $order->shipping_method_id = $shippingMethod->id;
        
        // Set shipping address details
        if ($shippingAddress) {
            $order->shipping_address_id = $shippingAddress->id;
            $order->shipping_name = $shippingAddress->name;
            $order->shipping_phone = $shippingAddress->phone;
            $order->shipping_address_line1 = $shippingAddress->address_line1;
            $order->shipping_address_line2 = $shippingAddress->address_line2;
            $order->shipping_city = $shippingAddress->city;
            $order->shipping_state = $shippingAddress->state;
            $order->shipping_postal_code = $shippingAddress->postal_code;
            $order->shipping_country = $country->name;
        } else {
            $order->shipping_name = $request->shipping_name;
            $order->shipping_phone = $request->shipping_phone;
            $order->shipping_address_line1 = $request->shipping_address_line1;
            $order->shipping_address_line2 = $request->shipping_address_line2;
            $order->shipping_city = $request->shipping_city;
            $order->shipping_state = $request->shipping_state;
            $order->shipping_postal_code = $request->shipping_postal_code;
            $order->shipping_country = $country->name;
        }
        
        // Set order amounts
        $order->subtotal = $cart['subtotal'];
        $order->shipping_amount = $shippingMethod->cost;
        $order->tax_amount = $cart['tax'] ?? 0;
        $order->discount_amount = $cart['discount'] ?? 0;
        $order->total_amount = $cart['subtotal'] + $shippingMethod->cost + ($cart['tax'] ?? 0) - ($cart['discount'] ?? 0);
        $order->currency = $country->currency;
        
        // Set additional details
        $order->notes = $request->notes;
        $order->customer_email = $customer->email;
        
        $order->save();
        
        return $order;
    }
    
    /**
     * Create order items from cart items.
     */
    protected function createOrderItems(Order $order, array $cartItems)
    {
        foreach ($cartItems as $item) {
            $product = Product::findOrFail($item['id']);
            
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->name = $product->name;
            $orderItem->sku = $product->sku;
            $orderItem->price = $item['price'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->subtotal = $item['price'] * $item['quantity'];
            $orderItem->options = $item['options'] ?? null;
            $orderItem->save();
            
            // Update product stock
            $product->decrementStock($item['quantity']);
        }
    }
}
