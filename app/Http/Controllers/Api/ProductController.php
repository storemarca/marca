<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductPrice;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }
        
        // Apply sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $allowedSortFields = ['name', 'created_at', 'price'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Load relationships
        $query->with(['category', 'prices', 'stocks']);
        
        // Paginate results
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->load(['category', 'prices', 'stocks.warehouse']);
        
        // Get related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['prices'])
            ->take(4)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'related_products' => $relatedProducts
            ]
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:100|unique:products',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'cost' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'video_url' => 'nullable|url',
            'attributes' => 'nullable|array',
        ]);
        
        $product = Product::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'cost' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'images' => 'nullable|array',
            'video_url' => 'nullable|url',
            'attributes' => 'nullable|array',
        ]);
        
        $product->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Check if product has order items
        if ($product->orderItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with associated orders'
            ], 422);
        }
        
        $product->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Update product stock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stocks' => 'required|array',
            'stocks.*.warehouse_id' => 'required|exists:warehouses,id',
            'stocks.*.quantity' => 'required|integer|min:0',
        ]);
        
        foreach ($request->stocks as $stock) {
            ProductStock::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $stock['warehouse_id'],
                ],
                [
                    'quantity' => $stock['quantity'],
                    'cost_price' => $product->cost,
                ]
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Product stock updated successfully'
        ]);
    }

    /**
     * Update product prices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function updatePrices(Request $request, Product $product)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.country_id' => 'required|exists:countries,id',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.sale_price' => 'nullable|numeric|min:0',
            'prices.*.sale_price_start_date' => 'nullable|date',
            'prices.*.sale_price_end_date' => 'nullable|date|after_or_equal:prices.*.sale_price_start_date',
        ]);
        
        foreach ($request->prices as $priceData) {
            ProductPrice::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'country_id' => $priceData['country_id'],
                ],
                [
                    'price' => $priceData['price'],
                    'sale_price' => $priceData['sale_price'] ?? null,
                    'sale_price_start_date' => $priceData['sale_price_start_date'] ?? null,
                    'sale_price_end_date' => $priceData['sale_price_end_date'] ?? null,
                    'is_active' => true,
                ]
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Product prices updated successfully'
        ]);
    }
} 