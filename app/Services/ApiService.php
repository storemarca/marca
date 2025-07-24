<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiService
{
    /**
     * أنواع طلبات API
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    
    /**
     * @var LogService
     */
    protected $logService;
    
    /**
     * @var CacheService
     */
    protected $cacheService;
    
    /**
     * إنشاء مثيل جديد من الخدمة
     *
     * @param LogService $logService
     * @param CacheService $cacheService
     */
    public function __construct(LogService $logService, CacheService $cacheService)
    {
        $this->logService = $logService;
        $this->cacheService = $cacheService;
    }
    
    /**
     * إرسال طلب إلى API خارجي
     *
     * @param string $method طريقة الطلب (GET, POST, PUT, PATCH, DELETE)
     * @param string $url عنوان URL للطلب
     * @param array $data البيانات المرسلة (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @param bool $throwOnError رمي استثناء عند حدوث خطأ (اختياري)
     * @return array استجابة API
     */
    public function request(
        string $method,
        string $url,
        array $data = [],
        array $headers = [],
        int $timeout = 30,
        bool $throwOnError = false
    ): array {
        $startTime = microtime(true);
        $method = strtoupper($method);
        
        // إعداد الطلب
        $request = Http::withHeaders(array_merge([
            'Accept' => 'application/json',
            'User-Agent' => 'Marca-API-Client/' . config('app.version', '1.0'),
        ], $headers))->timeout($timeout);
        
        // تنفيذ الطلب
        try {
            $response = null;
            
            switch ($method) {
                case self::METHOD_GET:
                    $response = $request->get($url, $data);
                    break;
                case self::METHOD_POST:
                    $response = $request->post($url, $data);
                    break;
                case self::METHOD_PUT:
                    $response = $request->put($url, $data);
                    break;
                case self::METHOD_PATCH:
                    $response = $request->patch($url, $data);
                    break;
                case self::METHOD_DELETE:
                    $response = $request->delete($url, $data);
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid HTTP method: {$method}");
            }
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2); // بالميلي ثانية
            
            // تحضير البيانات للتسجيل
            $logData = [
                'url' => $url,
                'method' => $method,
                'status_code' => $response->status(),
                'execution_time' => $executionTime,
            ];
            
            // تسجيل الطلب
            if ($response->successful()) {
                $this->logService->logInfo(
                    "API request successful",
                    "api.request",
                    $logData
                );
            } else {
                $logData['error'] = $response->body();
                $this->logService->logWarning(
                    "API request failed",
                    "api.request",
                    new \Exception("API Error: {$response->status()}"),
                    null
                );
                
                if ($throwOnError) {
                    $response->throw();
                }
            }
            
            // تحضير الاستجابة
            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'data' => $response->json() ?: [],
                'headers' => $response->headers(),
                'execution_time' => $executionTime,
            ];
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            // تسجيل الخطأ
            $this->logService->logError(
                "API request exception",
                "api.request",
                $e
            );
            
            if ($throwOnError) {
                throw $e;
            }
            
            return [
                'success' => false,
                'status_code' => 0,
                'data' => [],
                'error' => $e->getMessage(),
                'execution_time' => $executionTime,
            ];
        }
    }
    
    /**
     * إرسال طلب GET إلى API خارجي
     *
     * @param string $url عنوان URL للطلب
     * @param array $params المعلمات (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @param bool $throwOnError رمي استثناء عند حدوث خطأ (اختياري)
     * @return array استجابة API
     */
    public function get(
        string $url,
        array $params = [],
        array $headers = [],
        int $timeout = 30,
        bool $throwOnError = false
    ): array {
        return $this->request(self::METHOD_GET, $url, $params, $headers, $timeout, $throwOnError);
    }
    
    /**
     * إرسال طلب POST إلى API خارجي
     *
     * @param string $url عنوان URL للطلب
     * @param array $data البيانات المرسلة (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @param bool $throwOnError رمي استثناء عند حدوث خطأ (اختياري)
     * @return array استجابة API
     */
    public function post(
        string $url,
        array $data = [],
        array $headers = [],
        int $timeout = 30,
        bool $throwOnError = false
    ): array {
        return $this->request(self::METHOD_POST, $url, $data, $headers, $timeout, $throwOnError);
    }
    
    /**
     * إرسال طلب PUT إلى API خارجي
     *
     * @param string $url عنوان URL للطلب
     * @param array $data البيانات المرسلة (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @param bool $throwOnError رمي استثناء عند حدوث خطأ (اختياري)
     * @return array استجابة API
     */
    public function put(
        string $url,
        array $data = [],
        array $headers = [],
        int $timeout = 30,
        bool $throwOnError = false
    ): array {
        return $this->request(self::METHOD_PUT, $url, $data, $headers, $timeout, $throwOnError);
    }
    
    /**
     * إرسال طلب PATCH إلى API خارجي
     *
     * @param string $url عنوان URL للطلب
     * @param array $data البيانات المرسلة (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @param bool $throwOnError رمي استثناء عند حدوث خطأ (اختياري)
     * @return array استجابة API
     */
    public function patch(
        string $url,
        array $data = [],
        array $headers = [],
        int $timeout = 30,
        bool $throwOnError = false
    ): array {
        return $this->request(self::METHOD_PATCH, $url, $data, $headers, $timeout, $throwOnError);
    }
    
    /**
     * إرسال طلب DELETE إلى API خارجي
     *
     * @param string $url عنوان URL للطلب
     * @param array $data البيانات المرسلة (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @param bool $throwOnError رمي استثناء عند حدوث خطأ (اختياري)
     * @return array استجابة API
     */
    public function delete(
        string $url,
        array $data = [],
        array $headers = [],
        int $timeout = 30,
        bool $throwOnError = false
    ): array {
        return $this->request(self::METHOD_DELETE, $url, $data, $headers, $timeout, $throwOnError);
    }
    
    /**
     * إرسال طلب GET إلى API خارجي مع التخزين المؤقت
     *
     * @param string $url عنوان URL للطلب
     * @param array $params المعلمات (اختياري)
     * @param int $cacheTtl مدة صلاحية التخزين المؤقت بالثواني (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @return array استجابة API
     */
    public function getCached(
        string $url,
        array $params = [],
        int $cacheTtl = 3600,
        array $headers = [],
        int $timeout = 30
    ): array {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'api_' . md5($url . serialize($params));
        
        // محاولة الحصول على البيانات من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $cacheTtl, function () use ($url, $params, $headers, $timeout) {
            $response = $this->get($url, $params, $headers, $timeout);
            
            // تخزين البيانات فقط إذا كان الطلب ناجحًا
            if ($response['success']) {
                return $response;
            }
            
            // إذا فشل الطلب، لا تقم بتخزينه مؤقتًا
            throw new \Exception('API request failed, not caching');
        });
    }
    
    /**
     * تنفيذ طلبات متعددة بالتوازي
     *
     * @param array $requests مصفوفة من الطلبات
     * @param int $concurrency عدد الطلبات المتزامنة
     * @return array مصفوفة من الاستجابات
     */
    public function batchRequests(array $requests, int $concurrency = 5): array
    {
        $responses = [];
        $batches = array_chunk($requests, $concurrency);
        
        foreach ($batches as $batch) {
            $promises = [];
            
            foreach ($batch as $key => $request) {
                $method = $request['method'] ?? self::METHOD_GET;
                $url = $request['url'] ?? '';
                $data = $request['data'] ?? [];
                $headers = $request['headers'] ?? [];
                $timeout = $request['timeout'] ?? 30;
                
                // إنشاء وعد لكل طلب
                $promises[$key] = function () use ($method, $url, $data, $headers, $timeout) {
                    return $this->request($method, $url, $data, $headers, $timeout);
                };
            }
            
            // تنفيذ الوعود بالتوازي
            foreach ($promises as $key => $promise) {
                $responses[$key] = $promise();
            }
        }
        
        return $responses;
    }
    
    /**
     * تنفيذ طلب API مع إعادة المحاولة
     *
     * @param string $method طريقة الطلب
     * @param string $url عنوان URL للطلب
     * @param array $data البيانات المرسلة (اختياري)
     * @param int $maxRetries الحد الأقصى لعدد المحاولات
     * @param int $retryDelay التأخير بين المحاولات بالميلي ثانية
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @return array استجابة API
     */
    public function requestWithRetry(
        string $method,
        string $url,
        array $data = [],
        int $maxRetries = 3,
        int $retryDelay = 1000,
        array $headers = [],
        int $timeout = 30
    ): array {
        $attempt = 0;
        $lastResponse = null;
        
        while ($attempt < $maxRetries) {
            $response = $this->request($method, $url, $data, $headers, $timeout);
            
            // إذا نجح الطلب، أعد الاستجابة
            if ($response['success']) {
                return $response;
            }
            
            // حفظ آخر استجابة
            $lastResponse = $response;
            
            // زيادة عدد المحاولات
            $attempt++;
            
            // إذا لم تكن هذه المحاولة الأخيرة، انتظر قبل المحاولة التالية
            if ($attempt < $maxRetries) {
                usleep($retryDelay * 1000); // تحويل الميلي ثانية إلى ميكرو ثانية
            }
        }
        
        // إذا وصلنا إلى هنا، فقد فشلت جميع المحاولات
        $this->logService->logWarning(
            "API request failed after {$maxRetries} attempts",
            "api.request",
            new \Exception("Max retries exceeded for URL: {$url}")
        );
        
        return $lastResponse ?: [
            'success' => false,
            'status_code' => 0,
            'data' => [],
            'error' => "Max retries exceeded",
            'execution_time' => 0,
        ];
    }
    
    /**
     * تحميل ملف من URL
     *
     * @param string $url عنوان URL للملف
     * @param string $savePath مسار حفظ الملف
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @return bool نجاح العملية
     */
    public function downloadFile(string $url, string $savePath, array $headers = [], int $timeout = 60): bool
    {
        try {
            $response = Http::withHeaders(array_merge([
                'User-Agent' => 'Marca-API-Client/' . config('app.version', '1.0'),
            ], $headers))->timeout($timeout)->get($url);
            
            if ($response->successful()) {
                $directory = dirname($savePath);
                
                // إنشاء المجلد إذا لم يكن موجودًا
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // حفظ الملف
                file_put_contents($savePath, $response->body());
                
                // تسجيل النشاط
                $this->logService->logActivity(
                    "Downloaded file from {$url}",
                    'file',
                    null,
                    ['url' => $url, 'save_path' => $savePath, 'size' => filesize($savePath)],
                    LogService::ACTIVITY_TYPE_CREATE
                );
                
                return true;
            } else {
                // تسجيل الخطأ
                $this->logService->logWarning(
                    "Failed to download file",
                    "api.download",
                    new \Exception("HTTP Error: {$response->status()}"),
                    null
                );
                
                return false;
            }
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Exception while downloading file",
                "api.download",
                $e
            );
            
            return false;
        }
    }
    
    /**
     * تحميل ملف إلى URL
     *
     * @param string $url عنوان URL للتحميل
     * @param string $filePath مسار الملف المراد تحميله
     * @param string $fileKey اسم حقل الملف
     * @param array $data بيانات إضافية (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @return array استجابة API
     */
    public function uploadFile(
        string $url,
        string $filePath,
        string $fileKey = 'file',
        array $data = [],
        array $headers = [],
        int $timeout = 60
    ): array {
        try {
            $startTime = microtime(true);
            
            // التحقق من وجود الملف
            if (!file_exists($filePath)) {
                throw new \Exception("File not found: {$filePath}");
            }
            
            // إنشاء طلب متعدد الأجزاء
            $request = Http::withHeaders(array_merge([
                'Accept' => 'application/json',
                'User-Agent' => 'Marca-API-Client/' . config('app.version', '1.0'),
            ], $headers))->timeout($timeout);
            
            // إضافة الملف والبيانات الإضافية
            $request = $request->attach($fileKey, file_get_contents($filePath), basename($filePath));
            
            foreach ($data as $key => $value) {
                $request = $request->withData($key, $value);
            }
            
            // إرسال الطلب
            $response = $request->post($url);
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            // تحضير البيانات للتسجيل
            $logData = [
                'url' => $url,
                'file_path' => $filePath,
                'file_size' => filesize($filePath),
                'status_code' => $response->status(),
                'execution_time' => $executionTime,
            ];
            
            // تسجيل الطلب
            if ($response->successful()) {
                $this->logService->logInfo(
                    "File upload successful",
                    "api.upload",
                    $logData
                );
            } else {
                $logData['error'] = $response->body();
                $this->logService->logWarning(
                    "File upload failed",
                    "api.upload",
                    new \Exception("API Error: {$response->status()}"),
                    null
                );
            }
            
            // تحضير الاستجابة
            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'data' => $response->json() ?: [],
                'headers' => $response->headers(),
                'execution_time' => $executionTime,
            ];
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime), 2);
            
            // تسجيل الخطأ
            $this->logService->logError(
                "Exception while uploading file",
                "api.upload",
                $e
            );
            
            return [
                'success' => false,
                'status_code' => 0,
                'data' => [],
                'error' => $e->getMessage(),
                'execution_time' => $executionTime,
            ];
        }
    }
    
    /**
     * إرسال طلب API مع مصادقة OAuth2
     *
     * @param string $method طريقة الطلب
     * @param string $url عنوان URL للطلب
     * @param string $accessToken رمز الوصول
     * @param array $data البيانات المرسلة (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @param bool $throwOnError رمي استثناء عند حدوث خطأ (اختياري)
     * @return array استجابة API
     */
    public function requestWithOAuth(
        string $method,
        string $url,
        string $accessToken,
        array $data = [],
        array $headers = [],
        int $timeout = 30,
        bool $throwOnError = false
    ): array {
        // إضافة رمز الوصول إلى الرؤوس
        $headers['Authorization'] = "Bearer {$accessToken}";
        
        return $this->request($method, $url, $data, $headers, $timeout, $throwOnError);
    }
    
    /**
     * الحصول على رمز وصول OAuth2
     *
     * @param string $tokenUrl عنوان URL للحصول على الرمز
     * @param string $clientId معرف العميل
     * @param string $clientSecret سر العميل
     * @param string $grantType نوع المنحة
     * @param array $additionalParams معلمات إضافية (اختياري)
     * @return array استجابة API
     */
    public function getOAuthToken(
        string $tokenUrl,
        string $clientId,
        string $clientSecret,
        string $grantType = 'client_credentials',
        array $additionalParams = []
    ): array {
        $data = array_merge([
            'grant_type' => $grantType,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ], $additionalParams);
        
        return $this->post($tokenUrl, $data);
    }
    
    /**
     * تحديث رمز وصول OAuth2
     *
     * @param string $tokenUrl عنوان URL للحصول على الرمز
     * @param string $clientId معرف العميل
     * @param string $clientSecret سر العميل
     * @param string $refreshToken رمز التحديث
     * @param array $additionalParams معلمات إضافية (اختياري)
     * @return array استجابة API
     */
    public function refreshOAuthToken(
        string $tokenUrl,
        string $clientId,
        string $clientSecret,
        string $refreshToken,
        array $additionalParams = []
    ): array {
        $data = array_merge([
            'grant_type' => 'refresh_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
        ], $additionalParams);
        
        return $this->post($tokenUrl, $data);
    }
    
    /**
     * إرسال طلب GraphQL
     *
     * @param string $url عنوان URL للطلب
     * @param string $query استعلام GraphQL
     * @param array $variables متغيرات الاستعلام (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @param bool $throwOnError رمي استثناء عند حدوث خطأ (اختياري)
     * @return array استجابة API
     */
    public function graphql(
        string $url,
        string $query,
        array $variables = [],
        array $headers = [],
        int $timeout = 30,
        bool $throwOnError = false
    ): array {
        $data = [
            'query' => $query,
        ];
        
        if (!empty($variables)) {
            $data['variables'] = $variables;
        }
        
        // إضافة رأس Content-Type
        $headers['Content-Type'] = 'application/json';
        
        return $this->post($url, $data, $headers, $timeout, $throwOnError);
    }
    
    /**
     * إرسال طلب GraphQL مع التخزين المؤقت
     *
     * @param string $url عنوان URL للطلب
     * @param string $query استعلام GraphQL
     * @param array $variables متغيرات الاستعلام (اختياري)
     * @param int $cacheTtl مدة صلاحية التخزين المؤقت بالثواني (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param int $timeout مهلة الطلب بالثواني (اختياري)
     * @return array استجابة API
     */
    public function graphqlCached(
        string $url,
        string $query,
        array $variables = [],
        int $cacheTtl = 3600,
        array $headers = [],
        int $timeout = 30
    ): array {
        // إنشاء مفتاح التخزين المؤقت
        $cacheKey = 'graphql_' . md5($url . $query . serialize($variables));
        
        // محاولة الحصول على البيانات من التخزين المؤقت
        return $this->cacheService->remember($cacheKey, $cacheTtl, function () use ($url, $query, $variables, $headers, $timeout) {
            $response = $this->graphql($url, $query, $variables, $headers, $timeout);
            
            // تخزين البيانات فقط إذا كان الطلب ناجحًا
            if ($response['success']) {
                return $response;
            }
            
            // إذا فشل الطلب، لا تقم بتخزينه مؤقتًا
            throw new \Exception('GraphQL request failed, not caching');
        });
    }
}