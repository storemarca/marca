<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener categorías
        $smartphones = Category::where('name', 'Smartphones')->first();
        $laptops = Category::where('name', 'Laptops')->first();
        $audio = Category::where('name', 'Audio')->first();
        $mensClothing = Category::where('name', 'Men\'s Clothing')->first();
        $womensClothing = Category::where('name', 'Women\'s Clothing')->first();
        
        // Obtener países
        $egypt = Country::where('code', 'EG')->first();
        if (!$egypt) {
            $egypt = Country::create([
                'name' => 'Egypt',
                'name_ar' => 'مصر',
                'code' => 'EG',
                'is_active' => true,
                'currency_symbol' => 'ج.م',
                'currency_code' => 'EGP',
            ]);
        }

        // Crear productos de ejemplo
        $products = [
            [
                'name' => 'iPhone 13 Pro',
                'name_ar' => 'آيفون 13 برو',
                'description' => 'The latest iPhone with advanced features',
                'description_ar' => 'أحدث آيفون مع ميزات متقدمة',
                'category_id' => $smartphones->id,
                'base_price' => 999.99,
                'sale_price' => 949.99,
                'sku' => 'IP-13PRO-001',
                'stock_quantity' => 50,
                'is_featured' => true,
                'weight' => 0.2,
                'country_prices' => [
                    [
                        'country_id' => $egypt->id,
                        'price' => 39999.99,
                        'sale_price' => 37999.99,
                    ]
                ]
            ],
            [
                'name' => 'Samsung Galaxy S22',
                'name_ar' => 'سامسونج جالاكسي S22',
                'description' => 'Powerful Android smartphone with great camera',
                'description_ar' => 'هاتف أندرويد قوي مع كاميرا رائعة',
                'category_id' => $smartphones->id,
                'base_price' => 899.99,
                'sale_price' => 849.99,
                'sku' => 'SG-S22-001',
                'stock_quantity' => 75,
                'is_featured' => true,
                'weight' => 0.18,
                'country_prices' => [
                    [
                        'country_id' => $egypt->id,
                        'price' => 35999.99,
                        'sale_price' => 33999.99,
                    ]
                ]
            ],
            [
                'name' => 'MacBook Pro 16"',
                'name_ar' => 'ماك بوك برو 16 بوصة',
                'description' => 'Powerful laptop for professionals',
                'description_ar' => 'كمبيوتر محمول قوي للمحترفين',
                'category_id' => $laptops->id,
                'base_price' => 2399.99,
                'sku' => 'MB-PRO16-001',
                'stock_quantity' => 25,
                'is_featured' => true,
                'weight' => 2.1,
                'country_prices' => [
                    [
                        'country_id' => $egypt->id,
                        'price' => 95999.99,
                    ]
                ]
            ],
            [
                'name' => 'Dell XPS 15',
                'name_ar' => 'ديل XPS 15',
                'description' => 'Premium Windows laptop with InfinityEdge display',
                'description_ar' => 'كمبيوتر محمول ويندوز ممتاز مع شاشة InfinityEdge',
                'category_id' => $laptops->id,
                'base_price' => 1799.99,
                'sale_price' => 1699.99,
                'sku' => 'DL-XPS15-001',
                'stock_quantity' => 30,
                'is_featured' => false,
                'weight' => 1.8,
                'country_prices' => [
                    [
                        'country_id' => $egypt->id,
                        'price' => 71999.99,
                        'sale_price' => 67999.99,
                    ]
                ]
            ],
            [
                'name' => 'Sony WH-1000XM4',
                'name_ar' => 'سوني WH-1000XM4',
                'description' => 'Premium noise-cancelling headphones',
                'description_ar' => 'سماعات رأس فاخرة مع خاصية إلغاء الضوضاء',
                'category_id' => $audio->id,
                'base_price' => 349.99,
                'sku' => 'SN-WH1000XM4-001',
                'stock_quantity' => 100,
                'is_featured' => true,
                'weight' => 0.25,
                'country_prices' => [
                    [
                        'country_id' => $egypt->id,
                        'price' => 13999.99,
                    ]
                ]
            ],
            [
                'name' => 'Men\'s Casual Shirt',
                'name_ar' => 'قميص رجالي كاجوال',
                'description' => 'Comfortable cotton casual shirt',
                'description_ar' => 'قميص كاجوال قطني مريح',
                'category_id' => $mensClothing->id,
                'base_price' => 49.99,
                'sale_price' => 39.99,
                'sku' => 'MC-SHIRT-001',
                'stock_quantity' => 200,
                'is_featured' => false,
                'weight' => 0.3,
                'country_prices' => [
                    [
                        'country_id' => $egypt->id,
                        'price' => 1999.99,
                        'sale_price' => 1599.99,
                    ]
                ]
            ],
            [
                'name' => 'Women\'s Summer Dress',
                'name_ar' => 'فستان صيفي نسائي',
                'description' => 'Light and comfortable summer dress',
                'description_ar' => 'فستان صيفي خفيف ومريح',
                'category_id' => $womensClothing->id,
                'base_price' => 59.99,
                'sku' => 'WC-DRESS-001',
                'stock_quantity' => 150,
                'is_featured' => true,
                'weight' => 0.25,
                'country_prices' => [
                    [
                        'country_id' => $egypt->id,
                        'price' => 2399.99,
                    ]
                ]
            ],
        ];

        foreach ($products as $productData) {
            $countryPrices = $productData['country_prices'] ?? [];
            unset($productData['country_prices']);
            
            // Generar slug
            $productData['slug'] = Str::slug($productData['name']);
            
            // Crear producto
            $product = Product::create($productData);
            
            // Crear precios por país
            foreach ($countryPrices as $priceData) {
                ProductPrice::create([
                    'product_id' => $product->id,
                    'country_id' => $priceData['country_id'],
                    'price' => $priceData['price'],
                    'sale_price' => $priceData['sale_price'] ?? null,
                    'is_active' => true,
                ]);
            }
        }
    }
} 