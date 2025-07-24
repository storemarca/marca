<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Category;
use App\Models\ReturnRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SearchService
{
    /**
     * خدمة التخزين المؤقت
     *
     * @var \App\Services\CacheService
     */
    protected $cacheService;
    
    /**
     * مدة التخزين المؤقت بالدقائق
     *
     * @var int
     */
    protected $cacheTtl = 30;
    
    /**
     * إنشاء مثيل جديد من الخدمة
     *
     * @param \App\Services\CacheService $cacheService
     * @return void
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * البحث في المنتجات
     *
     * @param string $query نص البحث
     * @param array $filters مرشحات البحث
     * @param int $perPage عدد النتائج في الصفحة
     * @return \Illuminate\Pagination\LengthAwarePaginator نتائج البحث
     */
    public function searchProducts(string $query, array $filters = [], int $perPage = 15)
    {
        // تنظيف نص البحث
        $query = $this->cleanSearchQuery($query);
        
        // إذا كان نص البحث فارغًا، إرجاع نتائج فارغة
        if (empty($query)) {
            return Product::where('id', 0)->paginate($perPage);
        }
        
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'search_products_' . md5($query . json_encode($filters) . $perPage);
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () use ($query, $filters, $perPage) {
            // إنشاء استعلام البحث
            $searchQuery = Product::query();
            
            // البحث في الحقول المختلفة
            $searchQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            });
            
            // تطبيق المرشحات
            if (!empty($filters['category_id'])) {
                $searchQuery->where('category_id', $filters['category_id']);
            }
            
            if (!empty($filters['status'])) {
                $searchQuery->where('status', $filters['status']);
            }
            
            if (isset($filters['min_price'])) {
                $searchQuery->where('price', '>=', $filters['min_price']);
            }
            
            if (isset($filters['max_price'])) {
                $searchQuery->where('price', '<=', $filters['max_price']);
            }
            
            // تحميل العلاقات
            $searchQuery->with(['category', 'stocks']);
            
            // ترتيب النتائج
            if (!empty($filters['sort_by'])) {
                $direction = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'asc';
                $searchQuery->orderBy($filters['sort_by'], $direction);
            } else {
                // الترتيب الافتراضي حسب الصلة
                $searchQuery->orderByRaw("CASE 
                    WHEN name LIKE '{$query}%' THEN 1
                    WHEN sku LIKE '{$query}%' THEN 2
                    ELSE 3
                END");
            }
            
            // تنفيذ الاستعلام وإرجاع النتائج
            return $searchQuery->paginate($perPage);
        });
    }
    
    /**
     * البحث في الطلبات
     *
     * @param string $query نص البحث
     * @param array $filters مرشحات البحث
     * @param int $perPage عدد النتائج في الصفحة
     * @return \Illuminate\Pagination\LengthAwarePaginator نتائج البحث
     */
    public function searchOrders(string $query, array $filters = [], int $perPage = 15)
    {
        // تنظيف نص البحث
        $query = $this->cleanSearchQuery($query);
        
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'search_orders_' . md5($query . json_encode($filters) . $perPage);
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () use ($query, $filters, $perPage) {
            // إنشاء استعلام البحث
            $searchQuery = Order::query();
            
            // البحث في الحقول المختلفة
            if (!empty($query)) {
                $searchQuery->where(function ($q) use ($query) {
                    $q->where('order_number', 'like', "%{$query}%")
                      ->orWhere('customer_email', 'like', "%{$query}%")
                      ->orWhere('customer_name', 'like', "%{$query}%")
                      ->orWhere('id', 'like', "%{$query}%");
                });
            }
            
            // تطبيق المرشحات
            if (!empty($filters['status'])) {
                $searchQuery->where('status', $filters['status']);
            }
            
            if (!empty($filters['customer_id'])) {
                $searchQuery->where('customer_id', $filters['customer_id']);
            }
            
            if (!empty($filters['start_date'])) {
                $searchQuery->where('created_at', '>=', $filters['start_date']);
            }
            
            if (!empty($filters['end_date'])) {
                $searchQuery->where('created_at', '<=', $filters['end_date']);
            }
            
            if (isset($filters['min_total'])) {
                $searchQuery->where('total', '>=', $filters['min_total']);
            }
            
            if (isset($filters['max_total'])) {
                $searchQuery->where('total', '<=', $filters['max_total']);
            }
            
            // تحميل العلاقات
            $searchQuery->with(['customer', 'items.product']);
            
            // ترتيب النتائج
            if (!empty($filters['sort_by'])) {
                $direction = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'desc';
                $searchQuery->orderBy($filters['sort_by'], $direction);
            } else {
                // الترتيب الافتراضي حسب تاريخ الإنشاء
                $searchQuery->orderBy('created_at', 'desc');
            }
            
            // تنفيذ الاستعلام وإرجاع النتائج
            return $searchQuery->paginate($perPage);
        });
    }
    
    /**
     * البحث في العملاء
     *
     * @param string $query نص البحث
     * @param array $filters مرشحات البحث
     * @param int $perPage عدد النتائج في الصفحة
     * @return \Illuminate\Pagination\LengthAwarePaginator نتائج البحث
     */
    public function searchCustomers(string $query, array $filters = [], int $perPage = 15)
    {
        // تنظيف نص البحث
        $query = $this->cleanSearchQuery($query);
        
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'search_customers_' . md5($query . json_encode($filters) . $perPage);
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () use ($query, $filters, $perPage) {
            // إنشاء استعلام البحث
            $searchQuery = Customer::query();
            
            // البحث في الحقول المختلفة
            if (!empty($query)) {
                $searchQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('phone', 'like', "%{$query}%");
                });
            }
            
            // تطبيق المرشحات
            if (!empty($filters['country'])) {
                $searchQuery->where('country', $filters['country']);
            }
            
            if (!empty($filters['city'])) {
                $searchQuery->where('city', $filters['city']);
            }
            
            if (!empty($filters['registration_date_start'])) {
                $searchQuery->where('created_at', '>=', $filters['registration_date_start']);
            }
            
            if (!empty($filters['registration_date_end'])) {
                $searchQuery->where('created_at', '<=', $filters['registration_date_end']);
            }
            
            // تحميل العلاقات
            $searchQuery->withCount('orders');
            
            // ترتيب النتائج
            if (!empty($filters['sort_by'])) {
                $direction = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'asc';
                $searchQuery->orderBy($filters['sort_by'], $direction);
            } else {
                // الترتيب الافتراضي حسب الاسم
                $searchQuery->orderBy('name', 'asc');
            }
            
            // تنفيذ الاستعلام وإرجاع النتائج
            return $searchQuery->paginate($perPage);
        });
    }
    
    /**
     * البحث في طلبات الإرجاع
     *
     * @param string $query نص البحث
     * @param array $filters مرشحات البحث
     * @param int $perPage عدد النتائج في الصفحة
     * @return \Illuminate\Pagination\LengthAwarePaginator نتائج البحث
     */
    public function searchReturnRequests(string $query, array $filters = [], int $perPage = 15)
    {
        // تنظيف نص البحث
        $query = $this->cleanSearchQuery($query);
        
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'search_returns_' . md5($query . json_encode($filters) . $perPage);
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () use ($query, $filters, $perPage) {
            // إنشاء استعلام البحث
            $searchQuery = ReturnRequest::query();
            
            // البحث في الحقول المختلفة
            if (!empty($query)) {
                $searchQuery->where(function ($q) use ($query) {
                    $q->where('return_number', 'like', "%{$query}%")
                      ->orWhereHas('order', function ($orderQuery) use ($query) {
                          $orderQuery->where('order_number', 'like', "%{$query}%");
                      })
                      ->orWhereHas('customer', function ($customerQuery) use ($query) {
                          $customerQuery->where('name', 'like', "%{$query}%")
                                        ->orWhere('email', 'like', "%{$query}%");
                      });
                });
            }
            
            // تطبيق المرشحات
            if (!empty($filters['status'])) {
                $searchQuery->where('status', $filters['status']);
            }
            
            if (!empty($filters['customer_id'])) {
                $searchQuery->where('customer_id', $filters['customer_id']);
            }
            
            if (!empty($filters['start_date'])) {
                $searchQuery->where('created_at', '>=', $filters['start_date']);
            }
            
            if (!empty($filters['end_date'])) {
                $searchQuery->where('created_at', '<=', $filters['end_date']);
            }
            
            if (!empty($filters['return_method'])) {
                $searchQuery->where('return_method', $filters['return_method']);
            }
            
            // تحميل العلاقات
            $searchQuery->with(['order', 'customer', 'items.product']);
            
            // ترتيب النتائج
            if (!empty($filters['sort_by'])) {
                $direction = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'desc';
                $searchQuery->orderBy($filters['sort_by'], $direction);
            } else {
                // الترتيب الافتراضي حسب تاريخ الإنشاء
                $searchQuery->orderBy('created_at', 'desc');
            }
            
            // تنفيذ الاستعلام وإرجاع النتائج
            return $searchQuery->paginate($perPage);
        });
    }
    
    /**
     * البحث العام في جميع الكيانات
     *
     * @param string $query نص البحث
     * @param array $entities الكيانات المراد البحث فيها
     * @param int $limit عدد النتائج لكل كيان
     * @return array نتائج البحث
     */
    public function globalSearch(string $query, array $entities = ['products', 'orders', 'customers'], int $limit = 5): array
    {
        // تنظيف نص البحث
        $query = $this->cleanSearchQuery($query);
        
        // إذا كان نص البحث فارغًا، إرجاع نتائج فارغة
        if (empty($query)) {
            return [
                'products' => [],
                'orders' => [],
                'customers' => [],
                'returns' => [],
            ];
        }
        
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'global_search_' . md5($query . json_encode($entities) . $limit);
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () use ($query, $entities, $limit) {
            $results = [];
            
            // البحث في المنتجات
            if (in_array('products', $entities)) {
                $products = Product::where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get();
                
                $results['products'] = $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'title' => $product->name,
                        'subtitle' => 'SKU: ' . $product->sku,
                        'url' => '/admin/products/' . $product->id,
                        'type' => 'product',
                    ];
                });
            }
            
            // البحث في الطلبات
            if (in_array('orders', $entities)) {
                $orders = Order::where('order_number', 'like', "%{$query}%")
                    ->orWhere('customer_name', 'like', "%{$query}%")
                    ->orWhere('customer_email', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get();
                
                $results['orders'] = $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'title' => 'Order #' . $order->order_number,
                        'subtitle' => $order->customer_name . ' - ' . $order->created_at->format('Y-m-d'),
                        'url' => '/admin/orders/' . $order->id,
                        'type' => 'order',
                    ];
                });
            }
            
            // البحث في العملاء
            if (in_array('customers', $entities)) {
                $customers = Customer::where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get();
                
                $results['customers'] = $customers->map(function ($customer) {
                    return [
                        'id' => $customer->id,
                        'title' => $customer->name,
                        'subtitle' => $customer->email,
                        'url' => '/admin/customers/' . $customer->id,
                        'type' => 'customer',
                    ];
                });
            }
            
            // البحث في طلبات الإرجاع
            if (in_array('returns', $entities)) {
                $returns = ReturnRequest::where('return_number', 'like', "%{$query}%")
                    ->limit($limit)
                    ->get();
                
                $results['returns'] = $returns->map(function ($return) {
                    return [
                        'id' => $return->id,
                        'title' => 'Return #' . $return->return_number,
                        'subtitle' => 'Status: ' . $return->status,
                        'url' => '/admin/returns/' . $return->id,
                        'type' => 'return',
                    ];
                });
            }
            
            return $results;
        });
    }
    
    /**
     * البحث في الفئات
     *
     * @param string $query نص البحث
     * @param array $filters مرشحات البحث
     * @param int $perPage عدد النتائج في الصفحة
     * @return \Illuminate\Pagination\LengthAwarePaginator نتائج البحث
     */
    public function searchCategories(string $query, array $filters = [], int $perPage = 15)
    {
        // تنظيف نص البحث
        $query = $this->cleanSearchQuery($query);
        
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'search_categories_' . md5($query . json_encode($filters) . $perPage);
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () use ($query, $filters, $perPage) {
            // إنشاء استعلام البحث
            $searchQuery = Category::query();
            
            // البحث في الحقول المختلفة
            if (!empty($query)) {
                $searchQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('slug', 'like', "%{$query}%");
                });
            }
            
            // تطبيق المرشحات
            if (!empty($filters['status'])) {
                $searchQuery->where('status', $filters['status']);
            }
            
            if (isset($filters['parent_id'])) {
                $searchQuery->where('parent_id', $filters['parent_id']);
            }
            
            // تحميل العلاقات
            $searchQuery->withCount('products');
            
            // ترتيب النتائج
            if (!empty($filters['sort_by'])) {
                $direction = !empty($filters['sort_direction']) ? $filters['sort_direction'] : 'asc';
                $searchQuery->orderBy($filters['sort_by'], $direction);
            } else {
                // الترتيب الافتراضي حسب الاسم
                $searchQuery->orderBy('name', 'asc');
            }
            
            // تنفيذ الاستعلام وإرجاع النتائج
            return $searchQuery->paginate($perPage);
        });
    }
    
    /**
     * البحث في المنتجات باستخدام البحث المتقدم
     *
     * @param array $searchParams معلمات البحث
     * @param int $perPage عدد النتائج في الصفحة
     * @return \Illuminate\Pagination\LengthAwarePaginator نتائج البحث
     */
    public function advancedProductSearch(array $searchParams, int $perPage = 15)
    {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'advanced_product_search_' . md5(json_encode($searchParams) . $perPage);
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $this->cacheTtl, function () use ($searchParams, $perPage) {
            // إنشاء استعلام البحث
            $searchQuery = Product::query();
            
            // تطبيق معلمات البحث
            if (!empty($searchParams['keyword'])) {
                $keyword = $this->cleanSearchQuery($searchParams['keyword']);
                $searchQuery->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%")
                      ->orWhere('sku', 'like', "%{$keyword}%");
                });
            }
            
            if (!empty($searchParams['category_ids']) && is_array($searchParams['category_ids'])) {
                $searchQuery->whereIn('category_id', $searchParams['category_ids']);
            }
            
            if (!empty($searchParams['status'])) {
                $searchQuery->where('status', $searchParams['status']);
            }
            
            if (isset($searchParams['min_price'])) {
                $searchQuery->where('price', '>=', $searchParams['min_price']);
            }
            
            if (isset($searchParams['max_price'])) {
                $searchQuery->where('price', '<=', $searchParams['max_price']);
            }
            
            if (isset($searchParams['in_stock']) && $searchParams['in_stock']) {
                $searchQuery->whereHas('stocks', function ($q) {
                    $q->whereRaw('quantity - reserved_quantity > 0');
                });
            }
            
            if (!empty($searchParams['created_after'])) {
                $searchQuery->where('created_at', '>=', $searchParams['created_after']);
            }
            
            if (!empty($searchParams['created_before'])) {
                $searchQuery->where('created_at', '<=', $searchParams['created_before']);
            }
            
            // تحميل العلاقات
            $searchQuery->with(['category', 'stocks']);
            
            // ترتيب النتائج
            if (!empty($searchParams['sort_by'])) {
                $direction = !empty($searchParams['sort_direction']) ? $searchParams['sort_direction'] : 'asc';
                $searchQuery->orderBy($searchParams['sort_by'], $direction);
            } else {
                // الترتيب الافتراضي حسب الاسم
                $searchQuery->orderBy('name', 'asc');
            }
            
            // تنفيذ الاستعلام وإرجاع النتائج
            return $searchQuery->paginate($perPage);
        });
    }
    
    /**
     * تسجيل استعلام البحث
     *
     * @param string $query نص البحث
     * @param string $entityType نوع الكيان
     * @param int|null $userId معرف المستخدم
     * @param int|null $resultsCount عدد النتائج
     * @return void
     */
    public function logSearchQuery(string $query, string $entityType, ?int $userId = null, ?int $resultsCount = null): void
    {
        try {
            // تنظيف نص البحث
            $query = $this->cleanSearchQuery($query);
            
            // إذا كان نص البحث فارغًا، لا تقم بالتسجيل
            if (empty($query)) {
                return;
            }
            
            // تسجيل استعلام البحث في قاعدة البيانات
            DB::table('search_logs')->insert([
                'query' => $query,
                'entity_type' => $entityType,
                'user_id' => $userId,
                'results_count' => $resultsCount,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging search query: ' . $e->getMessage());
        }
    }
    
    /**
     * الحصول على أكثر استعلامات البحث شيوعًا
     *
     * @param string $entityType نوع الكيان
     * @param int $limit عدد النتائج
     * @param int $days عدد الأيام للبحث فيها
     * @return array استعلامات البحث الشائعة
     */
    public function getPopularSearchQueries(string $entityType, int $limit = 10, int $days = 30): array
    {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'popular_searches_' . $entityType . '_' . $limit . '_' . $days;
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, 60, function () use ($entityType, $limit, $days) {
            try {
                $startDate = now()->subDays($days);
                
                $queries = DB::table('search_logs')
                    ->select('query', DB::raw('COUNT(*) as count'))
                    ->where('entity_type', $entityType)
                    ->where('created_at', '>=', $startDate)
                    ->groupBy('query')
                    ->orderBy('count', 'desc')
                    ->limit($limit)
                    ->get()
                    ->toArray();
                
                return array_map(function ($item) {
                    return [
                        'query' => $item->query,
                        'count' => $item->count,
                    ];
                }, $queries);
            } catch (\Exception $e) {
                Log::error('Error getting popular search queries: ' . $e->getMessage());
                return [];
            }
        });
    }
    
    /**
     * الحصول على اقتراحات البحث
     *
     * @param string $query نص البحث
     * @param string $entityType نوع الكيان
     * @param int $limit عدد النتائج
     * @return array اقتراحات البحث
     */
    public function getSearchSuggestions(string $query, string $entityType = 'products', int $limit = 5): array
    {
        // تنظيف نص البحث
        $query = $this->cleanSearchQuery($query);
        
        // إذا كان نص البحث فارغًا، إرجاع نتائج فارغة
        if (empty($query)) {
            return [];
        }
        
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'search_suggestions_' . $entityType . '_' . md5($query) . '_' . $limit;
        
        // محاولة الحصول على النتائج من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, 30, function () use ($query, $entityType, $limit) {
            try {
                switch ($entityType) {
                    case 'products':
                        $suggestions = Product::where('name', 'like', "{$query}%")
                            ->orWhere('sku', 'like', "{$query}%")
                            ->limit($limit)
                            ->pluck('name')
                            ->toArray();
                        break;
                        
                    case 'customers':
                        $suggestions = Customer::where('name', 'like', "{$query}%")
                            ->orWhere('email', 'like', "{$query}%")
                            ->limit($limit)
                            ->pluck('name')
                            ->toArray();
                        break;
                        
                    case 'orders':
                        $suggestions = Order::where('order_number', 'like', "{$query}%")
                            ->orWhere('customer_name', 'like', "{$query}%")
                            ->limit($limit)
                            ->pluck('order_number')
                            ->toArray();
                        break;
                        
                    default:
                        $suggestions = [];
                }
                
                // الحصول على استعلامات البحث الشائعة التي تبدأ بنفس النص
                $popularQueries = DB::table('search_logs')
                    ->select('query', DB::raw('COUNT(*) as count'))
                    ->where('entity_type', $entityType)
                    ->where('query', 'like', "{$query}%")
                    ->groupBy('query')
                    ->orderBy('count', 'desc')
                    ->limit($limit)
                    ->pluck('query')
                    ->toArray();
                
                // دمج النتائج وإزالة التكرارات
                $allSuggestions = array_merge($suggestions, $popularQueries);
                $uniqueSuggestions = array_unique($allSuggestions);
                
                // اقتصار النتائج على العدد المطلوب
                return array_slice($uniqueSuggestions, 0, $limit);
            } catch (\Exception $e) {
                Log::error('Error getting search suggestions: ' . $e->getMessage());
                return [];
            }
        });
    }
    
    /**
     * تنظيف استعلام البحث من الأحرف الخاصة
     */
    protected function cleanSearchQuery(string $query): string
    {
        // إزالة الأحرف الخاصة والرموز غير المرغوب فيها
        $query = preg_replace('/[\\\/%^\[\]{}|@~#$&*()=+\'":;,<>?]/', ' ', $query);
        
        // إزالة المسافات الزائدة
        $query = preg_replace('/\s+/', ' ', $query);
        
        // تقليم المسافات من البداية والنهاية
        $query = trim($query);
        
        return $query;
    }
    
    /**
     * تعيين مدة التخزين المؤقت
     *
     * @param int $minutes المدة بالدقائق
     * @return $this
     */
    public function setCacheTtl(int $minutes): self
    {
        $this->cacheTtl = $minutes;
        return $this;
    }
}