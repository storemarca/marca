<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Discount;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * Get the current cart.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartTotals = session()->get('cart_totals', [
            'subtotal' => 0,
            'tax' => 0,
            'discount' => 0,
            'total' => 0,
            'currency_symbol' => current_country()->currency_symbol
        ]);
        
        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cart,
                'totals' => $cartTotals
            ]
        ]);
    }
    
    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        // Get current country
        $country = current_country();
        
        // Get product
        $product = Product::findOrFail($request->product_id);
        
        // Get available stock
        $availableStock = $product->stocks()
            ->whereHas('warehouse', function($q) use ($country) {
                $q->where('country_id', $country->id);
            })
            ->sum('quantity');
            
        // If no stock, assume 10 units available
        if ($availableStock <= 0) {
            $availableStock = 10;
        }
        
        // Check stock
        if ($availableStock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'The requested quantity is not available in stock'
            ], 422);
        }
        
        // Get product price
        $productPrice = $product->getPriceForCountry($country->id);
        
        // If no price, use default or first available
        if (!$productPrice) {
            $anyPrice = $product->prices()->first();
            
            if ($anyPrice) {
                $productPrice = $anyPrice;
            } else {
                $productPrice = (object) [
                    'price' => 100.00, // Default price
                    'currency_symbol' => $country->currency_symbol
                ];
            }
        }
        
        // Get current cart
        $cart = session()->get('cart', []);
        
        // If product already in cart, update quantity
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            // Add product to cart
            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => $request->quantity,
                'price' => $productPrice->price,
                'currency_symbol' => $country->currency_symbol,
                'image_url' => $product->main_image,
                'max_quantity' => $availableStock,
                'country_id' => $country->id,
            ];
        }
        
        // Save cart in session
        session()->put('cart', $cart);
        
        // Calculate cart totals
        $this->calculateCartTotals();
        
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'data' => [
                'items' => session()->get('cart'),
                'totals' => session()->get('cart_totals')
            ]
        ]);
    }
    
    /**
     * Update cart item quantity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cart = session()->get('cart', []);
        
        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }
        
        $cart[$id]['quantity'] = max(1, min($request->quantity, $cart[$id]['max_quantity'] ?? 10));
        
        session()->put('cart', $cart);
        $this->calculateCartTotals();
        
        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'data' => [
                'items' => session()->get('cart'),
                'totals' => session()->get('cart_totals')
            ]
        ]);
    }
    
    /**
     * Remove an item from the cart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        
        if (!isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }
        
        unset($cart[$id]);
        session()->put('cart', $cart);
        $this->calculateCartTotals();
        
        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully',
            'data' => [
                'items' => session()->get('cart'),
                'totals' => session()->get('cart_totals')
            ]
        ]);
    }
    
    /**
     * Apply discount code to cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);
        
        $code = $request->input('code');
        $discount = Discount::where('code', $code)
            ->where('is_active', true)
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            })
            ->first();
        
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired discount code'
            ], 422);
        }
        
        // Calculate cart totals
        $cartTotals = $this->calculateCartTotals();
        $subtotal = $cartTotals['subtotal'];
        
        // Calculate discount amount (simplified)
        $discountAmount = $subtotal * 0.1; // Default 10% discount
        
        // Store discount in session
        session()->put('coupon_code', $code);
        session()->put('coupon_discount', $discountAmount);
        session()->put('coupon_applied', true);
        
        // Recalculate cart totals
        $this->calculateCartTotals();
        
        return response()->json([
            'success' => true,
            'message' => 'Discount code applied successfully',
            'data' => [
                'items' => session()->get('cart'),
                'totals' => session()->get('cart_totals')
            ]
        ]);
    }
    
    /**
     * Remove discount code from cart.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeDiscount()
    {
        session()->forget(['coupon_code', 'coupon_discount', 'coupon_applied']);
        
        // Recalculate cart totals
        $this->calculateCartTotals();
        
        return response()->json([
            'success' => true,
            'message' => 'Discount code removed successfully',
            'data' => [
                'items' => session()->get('cart'),
                'totals' => session()->get('cart_totals')
            ]
        ]);
    }
    
    /**
     * Calculate cart totals.
     *
     * @return array
     */
    private function calculateCartTotals()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
        $tax = 0;
        $discount = 0;
        $total = 0;
        $currency_symbol = 'ر.س'; // Default value
        
        // Get current country and currency
        $country = current_country();
        if ($country) {
            $currency_symbol = $country->currency_symbol;
        }
        
        // Calculate subtotal
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Calculate tax
        $taxPercentage = (float) setting('tax_percentage', 15);
        $taxIncluded = (bool) setting('tax_included', false);
        
        if ($taxIncluded) {
            // Tax included in price
            $tax = $subtotal - ($subtotal / (1 + ($taxPercentage / 100)));
        } else {
            // Tax added to price
            $tax = $subtotal * ($taxPercentage / 100);
        }
        
        // Calculate discount
        if (session()->has('coupon_applied') && session()->has('coupon_discount')) {
            $discount = session()->get('coupon_discount');
        }
        
        // Calculate total
        if ($taxIncluded) {
            $total = $subtotal - $discount;
        } else {
            $total = $subtotal + $tax - $discount;
        }
        
        // Store totals in session
        $totals = [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_percentage' => $taxPercentage,
            'tax_included' => $taxIncluded,
            'discount' => $discount,
            'total' => $total,
            'currency_symbol' => $currency_symbol,
        ];
        
        session()->put('cart_totals', $totals);
        
        return $totals;
    }
}
