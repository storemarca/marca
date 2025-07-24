<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Customer; // Added this import

class CheckoutController extends Controller
{
    /**
     * Iniciar el proceso de checkout
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();
        $addresses = Address::where('user_id', $user->id)->get();
        
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'El carrito está vacío'], 400);
        }
        
        return response()->json([
            'data' => [
                'cart' => $cart,
                'addresses' => $addresses
            ]
        ]);
    }
    
    /**
     * Procesar el pago y crear la orden
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|string',
            'shipping_method' => 'required|exists:shipping_methods,id',
            'shipping_coordinates' => 'nullable|string|max:255',
        ]);
        
        // Get user if authenticated, otherwise handle as guest
        $user = $request->user();
        $customer = null;
        
        if ($user) {
            // Get or create customer for authenticated user
            $customer = Customer::where('user_id', $user->id)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'is_active' => true,
                    'first_name' => explode(' ', $user->name)[0] ?? '',
                    'last_name' => count(explode(' ', $user->name)) > 1 ? implode(' ', array_slice(explode(' ', $user->name), 1)) : '',
                ]);
            }
        } else {
            // Handle guest checkout
            // Validate additional fields for guest
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
            ]);
            
            // Create guest customer
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_active' => true,
                'first_name' => explode(' ', $request->name)[0] ?? '',
                'last_name' => count(explode(' ', $request->name)) > 1 ? implode(' ', array_slice(explode(' ', $request->name), 1)) : '',
            ]);
        }
        
        // Here you would add the logic to create the order using the customer
        // Similar to the User\CheckoutController::createOrder method
        // Make sure to set shipping_company_id to null initially
        
        return response()->json([
            'message' => 'Orden creada con éxito',
            'data' => [
                'order_id' => 1, // Este sería el ID de la orden creada
            ]
        ]);
    }
} 