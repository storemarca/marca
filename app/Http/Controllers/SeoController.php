<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class SeoController extends Controller
{
    /**
     * إنشاء خريطة الموقع XML
     */
    public function generateSitemap()
    {
        $products = Product::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();
        
        $content = view('seo.sitemap', compact('products', 'categories'))->render();
        
        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }
    
    /**
     * إنشاء ملف robots.txt
     */
    public function generateRobots()
    {
        $content = view('seo.robots')->render();
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain');
    }
    
    /**
     * إضافة البيانات الوصفية للصفحات
     */
    public function getMetaData($type, $slug = null)
    {
        $meta = [
            'title' => config('app.name'),
            'description' => 'Your default meta description',
            'keywords' => 'ecommerce, online shopping, products',
            'og_type' => 'website',
            'og_url' => url()->current(),
            'og_image' => asset('images/logo.png'),
        ];
        
        switch ($type) {
            case 'product':
                $product = Product::where('slug', $slug)->first();
                if ($product) {
                    $meta['title'] = $product->name . ' | ' . config('app.name');
                    $meta['description'] = Str::limit(strip_tags($product->description), 160);
                    $meta['keywords'] = $product->meta_keywords ?? $meta['keywords'];
                    $meta['og_type'] = 'product';
                    $meta['og_url'] = route('products.show', $product->slug);
                    $meta['og_image'] = $product->featured_image ? asset($product->featured_image) : $meta['og_image'];
                }
                break;
                
            case 'category':
                $category = Category::where('slug', $slug)->first();
                if ($category) {
                    $meta['title'] = $category->name . ' | ' . config('app.name');
                    $meta['description'] = Str::limit(strip_tags($category->description), 160);
                    $meta['keywords'] = $category->meta_keywords ?? $meta['keywords'];
                    $meta['og_url'] = route('categories.show', $category->slug);
                }
                break;
        }
        
        return $meta;
    }
} 