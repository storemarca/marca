<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get the current cart contents
     *
     * @return array
     */
    public function getCart(): array
    {
        $items = Session::get('cart', []);
        $subtotal = 0;
        $tax = 0;
        $discount = Session::get('discount', 0);
        
        foreach ($items as &$item) {
            $item['subtotal'] = $item['price'] * $item['quantity'];
            $subtotal += $item['subtotal'];
        }
        
        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $subtotal + $tax - $discount
        ];
    }
    
    /**
     * Add a product to the cart
     *
     * @param int $productId
     * @param int $quantity
     * @param array $options
     * @return array
     */
    public function addToCart(int $productId, int $quantity = 1, array $options = []): array
    {
        $product = Product::findOrFail($productId);
        
        $cart = Session::get('cart', []);
        
        // Generate a unique key for the product with options
        $cartItemKey = $this->generateCartItemKey($productId, $options);
        
        if (isset($cart[$cartItemKey])) {
            // Update existing cart item
            $cart[$cartItemKey]['quantity'] += $quantity;
        } else {
            // Add new cart item
            $cart[$cartItemKey] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
                'image' => $product->featured_image,
                'options' => $options,
            ];
        }
        
        Session::put('cart', $cart);
        
        return $this->getCart();
    }
    
    /**
     * Update cart item quantity
     *
     * @param string $cartItemKey
     * @param int $quantity
     * @return array
     */
    public function updateCartItemQuantity(string $cartItemKey, int $quantity): array
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$cartItemKey])) {
            if ($quantity > 0) {
                $cart[$cartItemKey]['quantity'] = $quantity;
            } else {
                unset($cart[$cartItemKey]);
            }
            
            Session::put('cart', $cart);
        }
        
        return $this->getCart();
    }
    
    /**
     * Remove an item from the cart
     *
     * @param string $cartItemKey
     * @return array
     */
    public function removeFromCart(string $cartItemKey): array
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$cartItemKey])) {
            unset($cart[$cartItemKey]);
            Session::put('cart', $cart);
        }
        
        return $this->getCart();
    }
    
    /**
     * Clear the cart
     *
     * @return void
     */
    public function clearCart(): void
    {
        Session::forget('cart');
        Session::forget('discount');
    }
    
    /**
     * Apply discount to the cart
     *
     * @param float $amount
     * @return array
     */
    public function applyDiscount(float $amount): array
    {
        Session::put('discount', $amount);
        return $this->getCart();
    }
    
    /**
     * Generate a unique key for a cart item
     *
     * @param int $productId
     * @param array $options
     * @return string
     */
    protected function generateCartItemKey(int $productId, array $options = []): string
    {
        $optionsKey = empty($options) ? '' : '_' . md5(json_encode($options));
        return $productId . $optionsKey;
    }
} 