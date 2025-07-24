<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Obtener todos los pedidos del usuario autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('user_id', $user->id)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json(['data' => $orders]);
    }

    /**
     * Obtener un pedido específico
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)
            ->with(['items.product', 'shipments'])
            ->findOrFail($id);
            
        return response()->json(['data' => $order]);
    }
    
    /**
     * Rastrear un pedido por su número de seguimiento
     */
    public function track(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string'
        ]);
        
        $order = Order::where('tracking_number', $request->tracking_number)->first();
        
        if (!$order) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }
        
        return response()->json(['data' => $order->load('shipments')]);
    }
} 