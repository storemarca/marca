<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Country;
use Illuminate\Support\Facades\Session;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * عرض صفحة المنتج
     */
    public function show($slug, AnalyticsService $analyticsService)
    {
        // Obtener el país actual
        $country = current_country();
        
        // Buscar el producto por slug y cargar relaciones necesarias
        $product = Product::where('slug', $slug)
            ->with(['category', 'prices', 'stocks.warehouse', 'countries'])
            ->firstOrFail();
        
        // Mejoras SEO
        $seoTitle = $product->name;
        $seoDescription = $product->short_description ?? substr(strip_tags($product->description), 0, 160);
        $seoKeywords = implode(', ', [
            $product->name,
            $product->category->name,
            setting('site_name'),
            'شراء ' . $product->name,
            $product->sku
        ]);
        
        // Obtener el precio del producto para el país actual
        $productPrice = $product->getPriceForCountry($country->id);
        
        // Si no hay precio específico para este país, registrar advertencia
        if (!$productPrice) {
            \Log::warning("No price found for product {$product->id} ({$product->name}) in country {$country->id} ({$country->name})");
        }
        
        // Obtener productos relacionados del mismo país
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->whereHas('prices', function($q) use ($country) {
                $q->where('country_id', $country->id);
            })
            ->with(['prices' => function($q) use ($country) {
                $q->where('country_id', $country->id);
            }, 'countries'])
            ->inRandomOrder()
            ->limit(4)
            ->get();
        
        // Registrar vista del producto para análisis
        if (auth()->check()) {
            $analyticsService->logProductView($product->id, auth()->id());
        } else {
            $analyticsService->logProductView($product->id);
        }
        
        return view('user.products.show', compact(
            'product',
            'productPrice',
            'relatedProducts',
            'seoTitle',
            'seoDescription',
            'seoKeywords',
            'country'
        ));
    }
    
    /**
     * عرض صفحة المنتج باستخدام المعرف
     */
    public function showById($id, AnalyticsService $analyticsService)
    {
        // الحصول على البلد الحالي
        $country = current_country();
        
        // البحث عن المنتج بالمعرف
        $product = Product::where('id', $id)
            ->with(['category', 'prices', 'stocks.warehouse', 'countries'])
            ->firstOrFail();
        
        // تحسينات SEO
        $seoTitle = $product->name;
        $seoDescription = $product->short_description ?? substr(strip_tags($product->description), 0, 160);
        $seoKeywords = implode(', ', [
            $product->name,
            $product->category->name,
            setting('site_name'),
            'شراء ' . $product->name,
            $product->sku
        ]);
        
        // الحصول على سعر المنتج للبلد الحالي
        $productPrice = $product->getPriceForCountry($country->id);
        
        // الحصول على منتجات مشابهة من نفس البلد
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['prices', 'countries'])
            ->inRandomOrder()
            ->limit(4)
            ->get();
        
        // تسجيل مشاهدة المنتج للتحليلات
        if (auth()->check()) {
            $analyticsService->logProductView($product->id, auth()->id());
        } else {
            $analyticsService->logProductView($product->id);
        }
        
        return view('user.products.show', compact(
            'product',
            'productPrice',
            'relatedProducts',
            'seoTitle',
            'seoDescription',
            'seoKeywords'
        ));
    }
    
    /**
     * عرض قائمة المنتجات
     */
    public function index(Request $request, AnalyticsService $analyticsService)
    {
        // Obtener el país actual
        $country = current_country();
        
        // Consulta base de productos
        $query = Product::query();
        
        // Filtrar solo productos activos
        $query->where('is_active', true);
        
        // Filtrar productos por país actual
        $query->whereHas('prices', function($q) use ($country) {
            $q->where('country_id', $country->id)
              ->where('is_active', true);
        });
        
        // Cargar los precios específicos para el país actual
        $query->with(['prices' => function($q) use ($country) {
            $q->where('country_id', $country->id);
        }]);
        
        // Búsqueda
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
            
            // Registrar consulta de búsqueda para análisis
            $resultsCount = $query->count();
            if (auth()->check()) {
                $analyticsService->logSearchQuery($searchTerm, $resultsCount, auth()->id());
            } else {
                $analyticsService->logSearchQuery($searchTerm, $resultsCount);
            }
        }
        
        // Filtrar por categorías
        if ($request->filled('categories')) {
            $categories = $request->input('categories');
            if (is_array($categories) && count($categories) > 0) {
                $query->whereIn('category_id', $categories);
            }
        } elseif ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        
        // Filtrar por rango de precio
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('prices', function($q) use ($request, $country) {
                $q->where('country_id', $country->id);
                
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->input('min_price'));
                }
                
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->input('max_price'));
                }
            });
        }
        
        // Filtrar por stock
        if ($request->filled('in_stock')) {
            $query->whereHas('stocks', function($q) {
                $q->whereRaw('quantity - reserved_quantity > 0');
            });
        }
        
        // Filtrar por productos destacados
        if ($request->filled('featured') && $request->input('featured') == 1) {
            $query->where('is_featured', true);
        }
        
        // Ordenar productos
        $sortBy = $request->input('sort_by', 'newest');
        switch ($sortBy) {
            case 'price_asc':
                $query->join('product_prices', function ($join) use ($country) {
                    $join->on('products.id', '=', 'product_prices.product_id')
                         ->where('product_prices.country_id', '=', $country->id);
                })
                ->orderBy('product_prices.price', 'asc')
                ->select('products.*');
                break;
            case 'price_desc':
                $query->join('product_prices', function ($join) use ($country) {
                    $join->on('products.id', '=', 'product_prices.product_id')
                         ->where('product_prices.country_id', '=', $country->id);
                })
                ->orderBy('product_prices.price', 'desc')
                ->select('products.*');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Obtener productos paginados
        $products = $query->paginate(12)->withQueryString();
        
        // Obtener categorías para filtros
        $categories = \App\Models\Category::orderBy('name')->get();
        
        return view('user.products.index', compact('products', 'categories', 'country'));
    }
    
    /**
     * إضافة تقييم للمنتج
     */
    public function review(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);
        
        $product = Product::findOrFail($id);
        
        $product->reviews()->create([
            'customer_id' => auth()->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->back()->with('success', 'تم إضافة تقييمك بنجاح');
    }
}
