<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function testShippingMethods()
    {
        $shippingMethods = ShippingMethod::all();
        
        return response()->json([
            'count' => $shippingMethods->count(),
            'methods' => $shippingMethods
        ]);
    }
}
