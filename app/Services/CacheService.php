<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CacheService
{
    /**
     * الوقت الافتراضي للتخزين المؤقت بالدقائق
     *
     * @var int
     */
    protected $defaultTtl = 60;
    
    /**
     * بادئة المفتاح الافتراضية
     *
     * @var string
     */
    protected $keyPrefix = 'marca_';
    
    /**
     * تخزين قيمة في الذاكرة المؤقتة
     *
     * @param string $key المفتاح
     * @param mixed $value القيمة
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return bool نتيجة العملية
     */
    public function put(string $key, $value, ?int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $prefixedKey = $this->getPrefixedKey($key);
        
        try {
            Cache::put($prefixedKey, $value, now()->addMinutes($ttl));
            return true;
        } catch (\Exception $e) {
            Log::error('Cache put error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * الحصول على قيمة من الذاكرة المؤقتة
     *
     * @param string $key المفتاح
     * @param mixed $default القيمة الافتراضية في حالة عدم وجود المفتاح
     * @return mixed القيمة المخزنة أو القيمة الافتراضية
     */
    public function get(string $key, $default = null)
    {
        $prefixedKey = $this->getPrefixedKey($key);
        
        try {
            return Cache::get($prefixedKey, $default);
        } catch (\Exception $e) {
            Log::error('Cache get error: ' . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * التحقق من وجود مفتاح في الذاكرة المؤقتة
     *
     * @param string $key المفتاح
     * @return bool نتيجة التحقق
     */
    public function has(string $key): bool
    {
        $prefixedKey = $this->getPrefixedKey($key);
        
        try {
            return Cache::has($prefixedKey);
        } catch (\Exception $e) {
            Log::error('Cache has error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * حذف مفتاح من الذاكرة المؤقتة
     *
     * @param string $key المفتاح
     * @return bool نتيجة العملية
     */
    public function forget(string $key): bool
    {
        $prefixedKey = $this->getPrefixedKey($key);
        
        try {
            return Cache::forget($prefixedKey);
        } catch (\Exception $e) {
            Log::error('Cache forget error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * الحصول على قيمة من الذاكرة المؤقتة أو تخزينها إذا لم تكن موجودة
     *
     * @param string $key المفتاح
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @param \Closure $callback دالة لإنشاء القيمة إذا لم تكن موجودة
     * @return mixed القيمة المخزنة
     */
    public function remember(string $key, ?int $ttl, \Closure $callback)
    {
        $prefixedKey = $this->getPrefixedKey($key);
        $ttl = $ttl ?? $this->defaultTtl;
        
        try {
            return Cache::remember($prefixedKey, now()->addMinutes($ttl), $callback);
        } catch (\Exception $e) {
            Log::error('Cache remember error: ' . $e->getMessage());
            return $callback();
        }
    }
    
    /**
     * حذف جميع المفاتيح من الذاكرة المؤقتة
     *
     * @return bool نتيجة العملية
     */
    public function flush(): bool
    {
        try {
            Cache::flush();
            return true;
        } catch (\Exception $e) {
            Log::error('Cache flush error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * حذف مفاتيح متعددة من الذاكرة المؤقتة باستخدام نمط
     *
     * @param string $pattern نمط المفاتيح
     * @return bool نتيجة العملية
     */
    public function forgetByPattern(string $pattern): bool
    {
        $pattern = $this->getPrefixedKey($pattern) . '*';
        
        try {
            // الحصول على مفاتيح التخزين المؤقت التي تطابق النمط
            $keys = $this->getKeysByPattern($pattern);
            
            // حذف المفاتيح
            foreach ($keys as $key) {
                Cache::forget($key);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Cache forgetByPattern error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * الحصول على مفاتيح التخزين المؤقت التي تطابق نمطًا معينًا
     *
     * @param string $pattern نمط المفاتيح
     * @return array مصفوفة المفاتيح
     */
    protected function getKeysByPattern(string $pattern): array
    {
        // ملاحظة: هذه الطريقة تعتمد على نوع التخزين المؤقت المستخدم
        // وقد تحتاج إلى تعديلها حسب الإعدادات الخاصة بك
        
        // مثال للتعامل مع Redis
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys($pattern);
            
            // تنظيف المفاتيح من بادئة Redis إذا لزم الأمر
            $prefix = config('cache.prefix');
            return array_map(function ($key) use ($prefix) {
                return str_replace($prefix . ':', '', $key);
            }, $keys);
        }
        
        // للأنواع الأخرى من التخزين المؤقت، قد لا يكون هناك طريقة مباشرة للبحث عن المفاتيح
        // في هذه الحالة، يمكن تنفيذ استراتيجية بديلة أو إرجاع مصفوفة فارغة
        return [];
    }
    
    /**
     * تخزين نموذج في الذاكرة المؤقتة
     *
     * @param \Illuminate\Database\Eloquent\Model $model النموذج
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return bool نتيجة العملية
     */
    public function putModel(Model $model, ?int $ttl = null): bool
    {
        $modelClass = get_class($model);
        $modelId = $model->getKey();
        $key = strtolower(class_basename($modelClass)) . '_' . $modelId;
        
        return $this->put($key, $model, $ttl);
    }
    
    /**
     * الحصول على نموذج من الذاكرة المؤقتة
     *
     * @param string $modelClass اسم فئة النموذج
     * @param mixed $modelId معرف النموذج
     * @return \Illuminate\Database\Eloquent\Model|null النموذج أو null
     */
    public function getModel(string $modelClass, $modelId): ?Model
    {
        $key = strtolower(class_basename($modelClass)) . '_' . $modelId;
        
        return $this->get($key);
    }
    
    /**
     * حذف نموذج من الذاكرة المؤقتة
     *
     * @param string $modelClass اسم فئة النموذج
     * @param mixed $modelId معرف النموذج
     * @return bool نتيجة العملية
     */
    public function forgetModel(string $modelClass, $modelId): bool
    {
        $key = strtolower(class_basename($modelClass)) . '_' . $modelId;
        
        return $this->forget($key);
    }
    
    /**
     * تخزين مجموعة من النماذج في الذاكرة المؤقتة
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection مجموعة النماذج
     * @param string $key المفتاح
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return bool نتيجة العملية
     */
    public function putCollection(Collection $collection, string $key, ?int $ttl = null): bool
    {
        return $this->put($key, $collection, $ttl);
    }
    
    /**
     * تخزين نتائج استعلام في الذاكرة المؤقتة
     *
     * @param string $key المفتاح
     * @param \Closure $queryCallback دالة الاستعلام
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return mixed نتائج الاستعلام
     */
    public function rememberQuery(string $key, \Closure $queryCallback, ?int $ttl = null)
    {
        return $this->remember($key, $ttl, $queryCallback);
    }
    
    /**
     * تخزين نتائج استعلام مع مراعاة المعلمات
     *
     * @param string $baseKey المفتاح الأساسي
     * @param array $params معلمات الاستعلام
     * @param \Closure $queryCallback دالة الاستعلام
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return mixed نتائج الاستعلام
     */
    public function rememberQueryWithParams(string $baseKey, array $params, \Closure $queryCallback, ?int $ttl = null)
    {
        // إنشاء مفتاح فريد بناءً على المعلمات
        $paramKey = md5(json_encode($params));
        $key = $baseKey . '_' . $paramKey;
        
        return $this->remember($key, $ttl, $queryCallback);
    }
    
    /**
     * حذف جميع المفاتيح المرتبطة باستعلام معين
     *
     * @param string $baseKey المفتاح الأساسي
     * @return bool نتيجة العملية
     */
    public function forgetQueryCache(string $baseKey): bool
    {
        return $this->forgetByPattern($baseKey);
    }
    
    /**
     * تعيين وقت التخزين المؤقت الافتراضي
     *
     * @param int $minutes الوقت بالدقائق
     * @return $this
     */
    public function setDefaultTtl(int $minutes): self
    {
        $this->defaultTtl = $minutes;
        return $this;
    }
    
    /**
     * تعيين بادئة المفتاح
     *
     * @param string $prefix البادئة
     * @return $this
     */
    public function setKeyPrefix(string $prefix): self
    {
        $this->keyPrefix = $prefix;
        return $this;
    }
    
    /**
     * الحصول على المفتاح مع البادئة
     *
     * @param string $key المفتاح
     * @return string المفتاح مع البادئة
     */
    protected function getPrefixedKey(string $key): string
    {
        return $this->keyPrefix . $key;
    }
    
    /**
     * تخزين بيانات الصفحة في الذاكرة المؤقتة
     *
     * @param string $route اسم المسار
     * @param array $params معلمات المسار
     * @param mixed $data بيانات الصفحة
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return bool نتيجة العملية
     */
    public function putPageData(string $route, array $params, $data, ?int $ttl = null): bool
    {
        $key = 'page_' . $route . '_' . md5(json_encode($params));
        return $this->put($key, $data, $ttl);
    }
    
    /**
     * الحصول على بيانات الصفحة من الذاكرة المؤقتة
     *
     * @param string $route اسم المسار
     * @param array $params معلمات المسار
     * @return mixed بيانات الصفحة أو null
     */
    public function getPageData(string $route, array $params)
    {
        $key = 'page_' . $route . '_' . md5(json_encode($params));
        return $this->get($key);
    }
    
    /**
     * حذف بيانات الصفحة من الذاكرة المؤقتة
     *
     * @param string $route اسم المسار
     * @param array $params معلمات المسار
     * @return bool نتيجة العملية
     */
    public function forgetPageData(string $route, array $params = []): bool
    {
        if (empty($params)) {
            // حذف جميع بيانات الصفحة لهذا المسار
            return $this->forgetByPattern('page_' . $route);
        }
        
        $key = 'page_' . $route . '_' . md5(json_encode($params));
        return $this->forget($key);
    }
    
    /**
     * تخزين إعدادات المستخدم في الذاكرة المؤقتة
     *
     * @param int $userId معرف المستخدم
     * @param array $settings الإعدادات
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return bool نتيجة العملية
     */
    public function putUserSettings(int $userId, array $settings, ?int $ttl = null): bool
    {
        $key = 'user_settings_' . $userId;
        return $this->put($key, $settings, $ttl);
    }
    
    /**
     * الحصول على إعدادات المستخدم من الذاكرة المؤقتة
     *
     * @param int $userId معرف المستخدم
     * @return array|null الإعدادات أو null
     */
    public function getUserSettings(int $userId): ?array
    {
        $key = 'user_settings_' . $userId;
        return $this->get($key);
    }
    
    /**
     * حذف إعدادات المستخدم من الذاكرة المؤقتة
     *
     * @param int $userId معرف المستخدم
     * @return bool نتيجة العملية
     */
    public function forgetUserSettings(int $userId): bool
    {
        $key = 'user_settings_' . $userId;
        return $this->forget($key);
    }
    
    /**
     * تخزين بيانات القائمة في الذاكرة المؤقتة
     *
     * @param string $menuType نوع القائمة
     * @param array $menuData بيانات القائمة
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return bool نتيجة العملية
     */
    public function putMenuData(string $menuType, array $menuData, ?int $ttl = null): bool
    {
        $key = 'menu_' . $menuType;
        return $this->put($key, $menuData, $ttl);
    }
    
    /**
     * الحصول على بيانات القائمة من الذاكرة المؤقتة
     *
     * @param string $menuType نوع القائمة
     * @return array|null بيانات القائمة أو null
     */
    public function getMenuData(string $menuType): ?array
    {
        $key = 'menu_' . $menuType;
        return $this->get($key);
    }
    
    /**
     * حذف بيانات القائمة من الذاكرة المؤقتة
     *
     * @param string $menuType نوع القائمة
     * @return bool نتيجة العملية
     */
    public function forgetMenuData(string $menuType = ''): bool
    {
        if (empty($menuType)) {
            // حذف جميع بيانات القوائم
            return $this->forgetByPattern('menu');
        }
        
        $key = 'menu_' . $menuType;
        return $this->forget($key);
    }
    
    /**
     * تخزين بيانات الإحصائيات في الذاكرة المؤقتة
     *
     * @param string $statType نوع الإحصائيات
     * @param array $params معلمات الإحصائيات
     * @param mixed $data بيانات الإحصائيات
     * @param int|null $ttl مدة الصلاحية بالدقائق
     * @return bool نتيجة العملية
     */
    public function putStatsData(string $statType, array $params, $data, ?int $ttl = null): bool
    {
        $key = 'stats_' . $statType . '_' . md5(json_encode($params));
        return $this->put($key, $data, $ttl);
    }
    
    /**
     * الحصول على بيانات الإحصائيات من الذاكرة المؤقتة
     *
     * @param string $statType نوع الإحصائيات
     * @param array $params معلمات الإحصائيات
     * @return mixed بيانات الإحصائيات أو null
     */
    public function getStatsData(string $statType, array $params)
    {
        $key = 'stats_' . $statType . '_' . md5(json_encode($params));
        return $this->get($key);
    }
    
    /**
     * حذف بيانات الإحصائيات من الذاكرة المؤقتة
     *
     * @param string $statType نوع الإحصائيات
     * @param array $params معلمات الإحصائيات
     * @return bool نتيجة العملية
     */
    public function forgetStatsData(string $statType, array $params = []): bool
    {
        if (empty($params)) {
            // حذف جميع بيانات الإحصائيات لهذا النوع
            return $this->forgetByPattern('stats_' . $statType);
        }
        
        $key = 'stats_' . $statType . '_' . md5(json_encode($params));
        return $this->forget($key);
    }
}