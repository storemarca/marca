<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\ProductStock;
use App\Models\ProductPrice;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * عرض قائمة المنتجات
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // البحث حسب الاسم أو الرمز
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // التصفية حسب القسم
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // التصفية حسب الحالة
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // التصفية حسب البلد
        if ($request->has('country_id') && $request->country_id) {
            $query->whereHas('prices', function($q) use ($request) {
                $q->where('country_id', $request->country_id);
            });
        }
        
        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::all();
        $countries = Country::all();
        
        return view('admin.products.index', compact('products', 'categories', 'countries'));
    }

    /**
     * عرض نموذج إنشاء منتج جديد
     */
    public function create()
    {
        $categories = Category::all();
        $countries = Country::all();
        $warehouses = Warehouse::all();
        
        return view('admin.products.create', compact('categories', 'countries', 'warehouses'));
    }

    /**
     * تخزين منتج جديد
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:products',
                'description' => 'required|string',
                'short_description' => 'nullable|string|max:500',
                'sku' => 'nullable|string|max:100|unique:products',
                'barcode' => 'nullable|string|max:100',
                'category_id' => 'required|exists:categories,id',
                'cost' => 'required|numeric|min:0',
                'weight' => 'required|numeric|min:0',
                'width' => 'nullable|numeric|min:0',
                'height' => 'nullable|numeric|min:0',
                'length' => 'nullable|numeric|min:0',
                'pieces_count' => 'nullable|integer|min:1',
                'is_active' => 'boolean',
                'is_featured' => 'boolean',
                'images.*' => 'nullable|image|max:2048',
                'video_url' => 'nullable|url',
                'countries' => 'required|array|min:1',
                'countries.*' => 'exists:countries,id',
                'price' => 'required|array|min:1',
                'price.*' => 'required|numeric|min:0',
                'sale_price' => 'nullable|array',
                'sale_price.*' => 'nullable|numeric|min:0',
                'sale_price_start_date' => 'nullable|array',
                'sale_price_start_date.*' => 'nullable|date',
                'sale_price_end_date' => 'nullable|array',
                'sale_price_end_date.*' => 'nullable|date|after_or_equal:sale_price_start_date.*',
                'stocks' => 'nullable|array',
                'stocks.*.warehouse_id' => 'required|exists:warehouses,id',
                'stocks.*.quantity' => 'required|integer|min:0',
                'colors' => 'nullable|array',
                'colors.*.name' => 'nullable|string|max:50',
                'colors.*.code' => 'nullable|string|max:20',
                'custom_colors' => 'nullable|array',
                'custom_colors.*.name' => 'nullable|string|max:50',
                'custom_colors.*.code' => 'nullable|string|max:20',
                'common_colors' => 'nullable|array',
                'common_colors.*' => 'nullable|string',
                'sizes' => 'nullable|array',
                'sizes.*.name' => 'nullable|string|max:50',
                'custom_sizes' => 'nullable|array',
                'custom_sizes.*.name' => 'nullable|string|max:50',
                'common_sizes' => 'nullable|array',
                'common_sizes.*' => 'nullable|string',
                'videos' => 'nullable|array',
                'videos.*.title' => 'nullable|string|max:100',
                'videos.*.url' => 'nullable|url',
            ], [
                'name.required' => 'اسم المنتج مطلوب',
                'sku.unique' => 'رمز المنتج (SKU) مستخدم بالفعل',
                'category_id.required' => 'يجب اختيار فئة للمنتج',
                'cost.required' => 'تكلفة المنتج مطلوبة',
                'weight.required' => 'وزن المنتج مطلوب',
                'countries.required' => 'يجب اختيار بلد واحد على الأقل',
                'countries.min' => 'يجب اختيار بلد واحد على الأقل',
                'price.*.required' => 'سعر المنتج مطلوب',
                'images.*.image' => 'يجب أن يكون الملف صورة',
                'images.*.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت',
            ]);
            
            // إنشاء slug إذا لم يتم توفيره
            if (empty($validated['slug'])) {
                $baseSlug = Str::slug($validated['name']);
                $slug = $baseSlug;
                $counter = 1;
                
                // التحقق من وجود slug مشابه وإضافة رقم إذا كان موجوداً
                while (Product::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $validated['slug'] = $slug;
            }
            
            // توليد SKU تلقائيًا إذا لم يتم توفيره
            if (empty($validated['sku'])) {
                $validated['sku'] = Product::generateSKUFromCategory($validated['category_id']);
            }
            
            // معالجة الصور إذا تم تحميلها
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $images[] = $path;
                }
            }
            $validated['images'] = $images;
            
            // تعيين قيمة افتراضية لـ is_active و is_featured
            $validated['is_active'] = $request->has('is_active') ? true : false;
            $validated['is_featured'] = $request->has('is_featured') ? true : false;
            
            // معالجة الألوان
            $colors = [];
            
            // معالجة الألوان الشائعة المحددة
            if ($request->has('common_colors')) {
                foreach ($request->common_colors as $colorValue) {
                    list($code, $hexCode, $name) = explode('|', $colorValue);
                    $colors[] = [
                        'name' => $name,
                        'code' => $hexCode
                    ];
                }
            }
            
            // إضافة الألوان المخصصة
            if ($request->has('custom_colors')) {
                foreach ($request->custom_colors as $color) {
                    if (!empty($color['name']) && !empty($color['code'])) {
                        $colors[] = [
                            'name' => $color['name'],
                            'code' => $color['code']
                        ];
                    }
                }
            }
            
            $validated['colors'] = $colors;
            
            // معالجة المقاسات
            $sizes = [];
            
            // معالجة المقاسات الشائعة المحددة
            if ($request->has('common_sizes')) {
                foreach ($request->common_sizes as $sizeValue) {
                    $sizes[] = [
                        'name' => $sizeValue
                    ];
                }
            }
            
            // إضافة المقاسات المخصصة
            if ($request->has('custom_sizes')) {
                foreach ($request->custom_sizes as $size) {
                    if (!empty($size['name'])) {
                        $sizes[] = [
                            'name' => $size['name']
                        ];
                    }
                }
            }
            
            $validated['sizes'] = $sizes;
            
            DB::beginTransaction();
            
            try {
                // إنشاء المنتج
                // إزالة الحقول التي لا يجب أن تكون في المصفوفة $validated
                if (isset($validated['countries'])) {
                    unset($validated['countries']);
                }
                if (isset($validated['price'])) {
                    unset($validated['price']);
                }
                if (isset($validated['sale_price'])) {
                    unset($validated['sale_price']);
                }
                if (isset($validated['stocks'])) {
                    unset($validated['stocks']);
                }
                if (isset($validated['common_colors'])) {
                    unset($validated['common_colors']);
                }
                if (isset($validated['custom_colors'])) {
                    unset($validated['custom_colors']);
                }
                if (isset($validated['common_sizes'])) {
                    unset($validated['common_sizes']);
                }
                if (isset($validated['custom_sizes'])) {
                    unset($validated['custom_sizes']);
                }
                
                // نضيف حقل cost إلى المنتج
                $validated['cost'] = $request->input('cost');
                
                $product = Product::create($validated);
                
                // إضافة أسعار المنتج حسب البلد
                if ($request->has('countries') && $request->has('price')) {
                    $priceData = $request->input('price');
                    $salePriceData = $request->input('sale_price', []);
                    $salePriceStartDateData = $request->input('sale_price_start_date', []);
                    $salePriceEndDateData = $request->input('sale_price_end_date', []);
                    
                    foreach ($request->countries as $countryId) {
                        ProductPrice::create([
                            'product_id' => $product->id,
                            'country_id' => $countryId,
                            'price' => $priceData[$countryId] ?? 0,
                            'sale_price' => $salePriceData[$countryId] ?? null,
                            'sale_price_start_date' => $salePriceStartDateData[$countryId] ?? null,
                            'sale_price_end_date' => $salePriceEndDateData[$countryId] ?? null,
                            'is_active' => true,
                        ]);
                    }
                }
                
                // إنشاء مخزون المنتج في المستودعات
                if ($request->has('stocks')) {
                    $priceData = $request->input('price');
                    $defaultPrice = !empty($priceData) ? reset($priceData) : 0;
                    
                    foreach ($request->stocks as $stock) {
                        if (isset($stock['warehouse_id']) && isset($stock['quantity']) && $stock['quantity'] > 0) {
                            // إنشاء مخزون جديد
                            $productStock = ProductStock::create([
                                'product_id' => $product->id,
                                'warehouse_id' => $stock['warehouse_id'],
                                'quantity' => $stock['quantity'],
                                'reserved_quantity' => 0,
                                'selling_price' => $defaultPrice, // إضافة سعر البيع الافتراضي
                                'cost_price' => $request->input('cost'), // إضافة تكلفة المنتج
                            ]);
                            
                            // تسجيل حركة المخزون
                            \App\Models\StockMovement::create([
                                'product_id' => $product->id,
                                'warehouse_id' => $stock['warehouse_id'],
                                'old_quantity' => 0,
                                'new_quantity' => $stock['quantity'],
                                'quantity_change' => $stock['quantity'],
                                'operation' => 'add',
                                'reason' => 'إضافة منتج جديد',
                                'user_id' => Auth::check() ? Auth::user()->getKey() : 1, // 1 = system user
                            ]);
                        }
                    }
                }
                
                DB::commit();
                
                return redirect()->route('admin.products.index')
                    ->with('success', 'تم إنشاء المنتج "' . $product->name . '" بنجاح');
            } catch (\Exception $e) {
                DB::rollBack();
                
                // حذف الصور المحملة في حالة فشل العملية
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
                
                return back()->withInput()
                    ->withErrors(['error' => 'حدث خطأ أثناء إنشاء المنتج: ' . $e->getMessage()]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->withInput()
                ->withErrors(['error' => 'حدث خطأ غير متوقع: ' . $e->getMessage()]);
        }
    }

    /**
     * عرض منتج محدد
     */
    public function show(Product $product)
    {
        $product->load('category', 'stocks.warehouse', 'prices.country');
        return view('admin.products.show', compact('product'));
    }

    /**
     * عرض نموذج تعديل منتج
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $countries = Country::all();
        $warehouses = Warehouse::all();
        $product->load('stocks.warehouse', 'countries');
        
        // تحضير بيانات المخزون للعرض
        $productStocks = [];
        foreach ($product->stocks as $stock) {
            $productStocks[$stock->warehouse_id] = $stock->quantity;
        }
        
        // تحضير بيانات الأسعار للعرض
        $productPrices = [];
        $productSalePrices = [];
        $productSalePriceStartDates = [];
        $productSalePriceEndDates = [];
        $productCountries = [];
        
        foreach ($product->countries as $country) {
            $productPrices[$country->id] = $country->pivot->price;
            $productSalePrices[$country->id] = $country->pivot->sale_price;
            $productSalePriceStartDates[$country->id] = $country->pivot->sale_price_start_date;
            $productSalePriceEndDates[$country->id] = $country->pivot->sale_price_end_date;
            $productCountries[] = $country->id;
        }
        
        return view('admin.products.edit', compact(
            'product', 
            'categories', 
            'countries', 
            'warehouses', 
            'productStocks',
            'productPrices',
            'productSalePrices',
            'productSalePriceStartDates',
            'productSalePriceEndDates',
            'productCountries'
        ));
    }

    /**
     * تحديث منتج محدد
     * يدعم الوصول عن طريق الـ ID أو الـ slug
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
            'pieces_count' => 'nullable|integer|min:1',
            'is_active.*' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'countries' => 'required|array|min:1',
            'countries.*' => 'exists:countries,id',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric|min:0',
            'sale_price' => 'nullable|array',
            'sale_price.*' => 'nullable|numeric|min:0',
            'sale_price_start_date' => 'nullable|array',
            'sale_price_start_date.*' => 'nullable|date',
            'sale_price_end_date' => 'nullable|array',
            'sale_price_end_date.*' => 'nullable|date|after_or_equal:sale_price_start_date.*',
            'stocks' => 'nullable|array',
            'stocks.*.warehouse_id' => 'required|exists:warehouses,id',
            'stocks.*.quantity' => 'required|integer|min:0',
            'remove_images' => 'array',
            'remove_images.*' => 'nullable|string',
            'colors' => 'nullable|array',
            'colors.*.name' => 'nullable|string|max:50',
            'colors.*.code' => 'nullable|string|max:20',
            'custom_colors' => 'nullable|array',
            'custom_colors.*.name' => 'nullable|string|max:50',
            'custom_colors.*.code' => 'nullable|string|max:20',
            'common_colors' => 'nullable|array',
            'common_colors.*' => 'nullable|string',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'nullable|string|max:50',
            'custom_sizes' => 'nullable|array',
            'custom_sizes.*.name' => 'nullable|string|max:50',
            'common_sizes' => 'nullable|array',
            'common_sizes.*' => 'nullable|string',
            'videos' => 'nullable|array',
            'videos.*.title' => 'nullable|string|max:100',
            'videos.*.url' => 'nullable|url',
        ]);
        
        // إنشاء slug إذا لم يتم توفيره
        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            
            // التحقق من وجود slug مشابه وإضافة رقم إذا كان موجوداً
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            $validated['slug'] = $slug;
        }
        
        // معالجة الصور
        $images = $product->images ?? [];
        
        // حذف الصور المحددة
        if ($request->has('remove_images')) {
            foreach ($request->remove_images as $image) {
                $key = array_search($image, $images);
                if ($key !== false) {
                    // حذف الصورة من التخزين
                    Storage::disk('public')->delete($image);
                    // حذف الصورة من المصفوفة
                    unset($images[$key]);
                }
            }
            // إعادة ترتيب المصفوفة
            $images = array_values($images);
        }
        
        // إضافة صور جديدة
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
        }
        
        $validated['images'] = $images;
        
        // تعيين قيمة افتراضية لـ is_active و is_featured
        $validated['is_active'] = $request->input('is_active', []);
        foreach ($request->countries as $country) {
            $validated['is_active'][$country->id] = isset($validated['is_active'][$country->id]) ? 1 : 0;
        }
        $validated['is_featured'] = $request->has('is_featured') ? true : false;
        
        // معالجة الألوان
        $colors = [];
        
        // معالجة الألوان الشائعة المحددة
        if ($request->has('common_colors')) {
            foreach ($request->common_colors as $colorValue) {
                list($code, $hexCode, $name) = explode('|', $colorValue);
                $colors[] = [
                    'name' => $name,
                    'code' => $hexCode
                ];
            }
        }
        
        // إضافة الألوان المخصصة
        if ($request->has('custom_colors')) {
            foreach ($request->custom_colors as $color) {
                if (!empty($color['name']) && !empty($color['code'])) {
                    $colors[] = [
                        'name' => $color['name'],
                        'code' => $color['code']
                    ];
                }
            }
        }
        
        $validated['colors'] = $colors;
        
        // معالجة المقاسات
        $sizes = [];

        // معالجة الفيديوهات
        $videos = [];
        if ($request->has('videos')) {
            foreach ($request->videos as $video) {
                if (!empty($video['title']) && !empty($video['url'])) {
                    $videos[] = [
                        'title' => $video['title'],
                        'url' => $video['url']
                    ];
                }
            }
        }
        $validated['videos'] = $videos;
        
        // معالجة المقاسات الشائعة المحددة
        if ($request->has('common_sizes')) {
            foreach ($request->common_sizes as $sizeValue) {
                $sizes[] = [
                    'name' => $sizeValue
                ];
            }
        }
        
        // إضافة المقاسات المخصصة
        if ($request->has('custom_sizes')) {
            foreach ($request->custom_sizes as $size) {
                if (!empty($size['name'])) {
                    $sizes[] = [
                        'name' => $size['name']
                    ];
                }
            }
        }
        
        $validated['sizes'] = $sizes;
        
        // إزالة الحقول التي لا يجب أن تكون في المصفوفة $validated قبل تحديث المنتج
        if (isset($validated['countries'])) {
            unset($validated['countries']);
        }
        if (isset($validated['price'])) {
            unset($validated['price']);
        }
        if (isset($validated['sale_price'])) {
            unset($validated['sale_price']);
        }
        if (isset($validated['sale_price_start_date'])) {
            unset($validated['sale_price_start_date']);
        }
        if (isset($validated['sale_price_end_date'])) {
            unset($validated['sale_price_end_date']);
        }
        if (isset($validated['stocks'])) {
            unset($validated['stocks']);
        }
        if (isset($validated['common_colors'])) {
            unset($validated['common_colors']);
        }
        if (isset($validated['custom_colors'])) {
            unset($validated['custom_colors']);
        }
        if (isset($validated['common_sizes'])) {
            unset($validated['common_sizes']);
        }
        if (isset($validated['custom_sizes'])) {
            unset($validated['custom_sizes']);
        }
        
        DB::beginTransaction();
        
        try {
            // تحديث المنتج
            $product->update($validated);
            
            // تحديث أسعار المنتج حسب البلد
            if ($request->has('countries') && $request->has('price')) {
                // حذف الأسعار القديمة التي لم تعد موجودة
                $product->prices()->whereNotIn('country_id', $request->countries)->delete();
                
                // إضافة أو تحديث الأسعار الجديدة
                $priceData = $request->input('price');
                $salePriceData = $request->input('sale_price', []);
                $salePriceStartDateData = $request->input('sale_price_start_date', []);
                $salePriceEndDateData = $request->input('sale_price_end_date', []);
                
                foreach ($request->countries as $countryId) {
                    ProductPrice::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'country_id' => $countryId,
                        ],
                        [
                            'price' => $priceData[$countryId] ?? 0,
                            'sale_price' => $salePriceData[$countryId] ?? null,
                            'sale_price_start_date' => $salePriceStartDateData[$countryId] ?? null,
                            'sale_price_end_date' => $salePriceEndDateData[$countryId] ?? null,
                            'is_active' => true,
                        ]
                    );
                }
            }
            
            // تحديث مخزون المنتج في المستودعات
            if ($request->has('stocks')) {
                foreach ($request->stocks as $stock) {
                    if (isset($stock['warehouse_id']) && isset($stock['quantity'])) {
                        ProductStock::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'warehouse_id' => $stock['warehouse_id'],
                            ],
                            [
                                'quantity' => $stock['quantity'],
                                'cost_price' => $request->input('cost'), // إضافة تكلفة المنتج
                            ]
                        );
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'تم تحديث المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'حدث خطأ أثناء تحديث المنتج: ' . $e->getMessage()]);
        }
    }

    /**
     * حذف منتج محدد
     */
    public function destroy(Product $product)
    {
        try {
            // حذف صور المنتج
            if (!empty($product->images) && is_array($product->images)) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            // حذف المنتج
            $product->delete();
            
            return redirect()->route('admin.products.index')
                ->with('success', 'تم حذف المنتج بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')
                ->with('error', 'حدث خطأ أثناء حذف المنتج: ' . $e->getMessage());
        }
    }

    /**
     * عرض صفحة إدارة مخزون المنتج
     */
    public function stock(Product $product)
    {
        $product->load('stocks.warehouse');
        $warehouses = Warehouse::all();
        
        return view('admin.products.stock', compact('product', 'warehouses'));
    }

    /**
     * تحديث مخزون المنتج
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stocks' => 'required|array',
            'stocks.*.warehouse_id' => 'required|exists:warehouses,id',
            'stocks.*.quantity' => 'required|integer|min:0',
        ], [
            'stocks.required' => 'بيانات المخزون مطلوبة',
            'stocks.*.warehouse_id.required' => 'المستودع مطلوب',
            'stocks.*.warehouse_id.exists' => 'المستودع غير موجود',
            'stocks.*.quantity.required' => 'الكمية مطلوبة',
            'stocks.*.quantity.integer' => 'الكمية يجب أن تكون رقمًا صحيحًا',
            'stocks.*.quantity.min' => 'الكمية يجب أن تكون 0 أو أكثر',
        ]);
        
        try {
            DB::beginTransaction();
            
            // حذف المخزون الحالي
            // ProductStock::where('product_id', $product->id)->delete();
            
            // إضافة المخزون الجديد
            foreach ($request->stocks as $stockData) {
                $warehouseId = $stockData['warehouse_id'];
                $quantity = $stockData['quantity'];
                
                // البحث عن المخزون الحالي للمنتج في هذا المستودع
                $stock = ProductStock::where('product_id', $product->id)
                    ->where('warehouse_id', $warehouseId)
                    ->first();
                
                if ($stock) {
                    // إذا كان هناك تغيير في الكمية، نسجل حركة مخزون
                    if ($stock->quantity != $quantity) {
                        $oldQuantity = $stock->quantity;
                        $difference = $quantity - $oldQuantity;
                        
                        // تحديث الكمية
                        $stock->quantity = $quantity;
                        $stock->save();
                        
                        // تسجيل حركة المخزون
                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouseId,
                            'old_quantity' => $oldQuantity,
                            'new_quantity' => $quantity,
                            'quantity_change' => $difference,
                            'operation' => $difference > 0 ? 'add' : 'subtract',
                            'reason' => 'تعديل يدوي للمخزون',
                            'user_id' => Auth::check() ? Auth::user()->getKey() : 1, // 1 = system user
                        ]);
                    }
                } else {
                    // إنشاء مخزون جديد
                    $stock = ProductStock::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouseId,
                        'quantity' => $quantity,
                        'reserved_quantity' => 0,
                    ]);
                    
                    // تسجيل حركة المخزون
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouseId,
                        'old_quantity' => 0,
                        'new_quantity' => $quantity,
                        'quantity_change' => $quantity,
                        'operation' => 'add',
                        'reason' => 'إضافة مخزون جديد',
                        'user_id' => Auth::check() ? Auth::user()->getKey() : 1, // 1 = system user
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.show', $product->id)
                ->with('success', 'تم تحديث مخزون المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المخزون: ' . $e->getMessage());
        }
    }

    /**
     * عرض صفحة إدارة أسعار المنتج
     */
    public function prices(Product $product)
    {
        $product->load('prices.country');
        $countries = Country::all();
        
        return view('admin.products.prices', compact('product', 'countries'));
    }
    
    /**
     * تحديث أسعار المنتج
     */
    public function updatePrices(Request $request, Product $product)
    {
        $request->validate([
            'prices' => 'required|array',
            'prices.*.country_id' => 'required|exists:countries,id',
            'prices.*.price' => 'required|numeric|min:0',
            'prices.*.sale_price' => 'nullable|numeric|min:0',
            'prices.*.is_active' => 'boolean',
        ], [
            'prices.required' => 'بيانات الأسعار مطلوبة',
            'prices.*.country_id.required' => 'البلد مطلوب',
            'prices.*.country_id.exists' => 'البلد غير موجود',
            'prices.*.price.required' => 'السعر مطلوب',
            'prices.*.price.numeric' => 'السعر يجب أن يكون رقمًا',
            'prices.*.price.min' => 'السعر يجب أن يكون 0 أو أكثر',
        ]);
        
        try {
            DB::beginTransaction();
            
            // تحديث الأسعار
            foreach ($request->prices as $priceData) {
                $countryId = $priceData['country_id'];
                $price = $priceData['price'];
                $salePrice = $priceData['sale_price'] ?? null;
                $isActive = isset($priceData['is_active']) ? true : false;
                
                // البحث عن السعر الحالي للمنتج في هذا البلد
                $productPrice = ProductPrice::where('product_id', $product->id)
                    ->where('country_id', $countryId)
                    ->first();
                
                if ($productPrice) {
                    // تحديث السعر الحالي
                    $productPrice->price = $price;
                    $productPrice->sale_price = $salePrice;
                    $productPrice->is_active = $isActive;
                    $productPrice->save();
                } else {
                    // إنشاء سعر جديد
                    ProductPrice::create([
                        'product_id' => $product->id,
                        'country_id' => $countryId,
                        'price' => $price,
                        'sale_price' => $salePrice,
                        'is_active' => $isActive,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.products.show', $product->id)
                ->with('success', 'تم تحديث أسعار المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الأسعار: ' . $e->getMessage());
        }
    }
    
    /**
     * توليد SKU تلقائيًا للمنتج
     */
    public function generateSku(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
        ]);
        
        $categoryId = $request->input('category_id');
        $sku = Product::generateSKUFromCategory($categoryId);
        
        return response()->json([
            'success' => true,
            'sku' => $sku
        ]);
    }
}
