<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Support\Str;

class LogService
{
    /**
     * أنواع الأنشطة المتاحة
     */
    const ACTIVITY_TYPE_CREATE = 'create';
    const ACTIVITY_TYPE_UPDATE = 'update';
    const ACTIVITY_TYPE_DELETE = 'delete';
    const ACTIVITY_TYPE_LOGIN = 'login';
    const ACTIVITY_TYPE_LOGOUT = 'logout';
    const ACTIVITY_TYPE_VIEW = 'view';
    const ACTIVITY_TYPE_EXPORT = 'export';
    const ACTIVITY_TYPE_IMPORT = 'import';
    const ACTIVITY_TYPE_PAYMENT = 'payment';
    const ACTIVITY_TYPE_SHIPPING = 'shipping';
    const ACTIVITY_TYPE_RETURN = 'return';
    const ACTIVITY_TYPE_OTHER = 'other';
    
    /**
     * مستويات الأخطاء المتاحة
     */
    const ERROR_LEVEL_INFO = 'info';
    const ERROR_LEVEL_WARNING = 'warning';
    const ERROR_LEVEL_ERROR = 'error';
    const ERROR_LEVEL_CRITICAL = 'critical';
    
    /**
     * تسجيل نشاط في النظام
     *
     * @param string $action الإجراء المتخذ
     * @param string $entityType نوع الكيان
     * @param int|null $entityId معرف الكيان (اختياري)
     * @param array $data البيانات المرتبطة بالنشاط (اختياري)
     * @param string $type نوع النشاط
     * @param int|null $userId معرف المستخدم (اختياري، سيتم استخدام المستخدم الحالي إذا لم يتم تحديده)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logActivity(
        string $action,
        string $entityType,
        ?int $entityId = null,
        array $data = [],
        string $type = self::ACTIVITY_TYPE_OTHER,
        ?int $userId = null
    ): ActivityLog {
        // الحصول على معرف المستخدم الحالي إذا لم يتم تحديده
        if ($userId === null && Auth::check()) {
            $userId = Auth::id();
        }
        
        // إنشاء سجل النشاط
        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'type' => $type,
            'data' => !empty($data) ? json_encode($data) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    /**
     * تسجيل نشاط إنشاء كيان
     *
     * @param string $entityType نوع الكيان
     * @param int $entityId معرف الكيان
     * @param array $data البيانات المرتبطة بالنشاط (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logCreation(string $entityType, int $entityId, array $data = [], ?int $userId = null): ActivityLog
    {
        return $this->logActivity(
            "Created {$entityType} #{$entityId}",
            $entityType,
            $entityId,
            $data,
            self::ACTIVITY_TYPE_CREATE,
            $userId
        );
    }
    
    /**
     * تسجيل نشاط تحديث كيان
     *
     * @param string $entityType نوع الكيان
     * @param int $entityId معرف الكيان
     * @param array $oldData البيانات القديمة
     * @param array $newData البيانات الجديدة
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logUpdate(
        string $entityType,
        int $entityId,
        array $oldData,
        array $newData,
        ?int $userId = null
    ): ActivityLog {
        // تحديد التغييرات بين البيانات القديمة والجديدة
        $changes = $this->getDataChanges($oldData, $newData);
        
        return $this->logActivity(
            "Updated {$entityType} #{$entityId}",
            $entityType,
            $entityId,
            ['changes' => $changes],
            self::ACTIVITY_TYPE_UPDATE,
            $userId
        );
    }
    
    /**
     * تسجيل نشاط حذف كيان
     *
     * @param string $entityType نوع الكيان
     * @param int $entityId معرف الكيان
     * @param array $data البيانات المرتبطة بالنشاط (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logDeletion(string $entityType, int $entityId, array $data = [], ?int $userId = null): ActivityLog
    {
        return $this->logActivity(
            "Deleted {$entityType} #{$entityId}",
            $entityType,
            $entityId,
            $data,
            self::ACTIVITY_TYPE_DELETE,
            $userId
        );
    }
    
    /**
     * تسجيل نشاط تسجيل الدخول
     *
     * @param \App\Models\User $user المستخدم
     * @param bool $success نجاح العملية
     * @param string|null $reason سبب الفشل (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logLogin(User $user, bool $success, ?string $reason = null): ActivityLog
    {
        $action = $success ? "User logged in successfully" : "Failed login attempt";
        $data = ['success' => $success];
        
        if (!$success && $reason) {
            $data['reason'] = $reason;
        }
        
        return $this->logActivity(
            $action,
            'user',
            $user->id,
            $data,
            self::ACTIVITY_TYPE_LOGIN,
            $user->id
        );
    }
    
    /**
     * تسجيل نشاط تسجيل الخروج
     *
     * @param \App\Models\User $user المستخدم
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logLogout(User $user): ActivityLog
    {
        return $this->logActivity(
            "User logged out",
            'user',
            $user->id,
            [],
            self::ACTIVITY_TYPE_LOGOUT,
            $user->id
        );
    }
    
    /**
     * تسجيل نشاط عرض كيان
     *
     * @param string $entityType نوع الكيان
     * @param int $entityId معرف الكيان
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logView(string $entityType, int $entityId, ?int $userId = null): ActivityLog
    {
        return $this->logActivity(
            "Viewed {$entityType} #{$entityId}",
            $entityType,
            $entityId,
            [],
            self::ACTIVITY_TYPE_VIEW,
            $userId
        );
    }
    
    /**
     * تسجيل نشاط تصدير بيانات
     *
     * @param string $entityType نوع الكيان
     * @param string $format صيغة التصدير
     * @param array $filters المرشحات المستخدمة (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logExport(
        string $entityType,
        string $format,
        array $filters = [],
        ?int $userId = null
    ): ActivityLog {
        return $this->logActivity(
            "Exported {$entityType} data as {$format}",
            $entityType,
            null,
            ['format' => $format, 'filters' => $filters],
            self::ACTIVITY_TYPE_EXPORT,
            $userId
        );
    }
    
    /**
     * تسجيل نشاط استيراد بيانات
     *
     * @param string $entityType نوع الكيان
     * @param string $filename اسم الملف
     * @param int $rowCount عدد الصفوف المستوردة
     * @param int $successCount عدد الصفوف الناجحة
     * @param int $errorCount عدد الصفوف الفاشلة
     * @param array $errors الأخطاء (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logImport(
        string $entityType,
        string $filename,
        int $rowCount,
        int $successCount,
        int $errorCount,
        array $errors = [],
        ?int $userId = null
    ): ActivityLog {
        return $this->logActivity(
            "Imported {$entityType} data from {$filename}",
            $entityType,
            null,
            [
                'filename' => $filename,
                'row_count' => $rowCount,
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors,
            ],
            self::ACTIVITY_TYPE_IMPORT,
            $userId
        );
    }
    
    /**
     * تسجيل نشاط دفع
     *
     * @param string $paymentMethod طريقة الدفع
     * @param float $amount المبلغ
     * @param string $currency العملة
     * @param string $status الحالة
     * @param string $entityType نوع الكيان (مثل 'order')
     * @param int $entityId معرف الكيان
     * @param array $additionalData بيانات إضافية (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logPayment(
        string $paymentMethod,
        float $amount,
        string $currency,
        string $status,
        string $entityType,
        int $entityId,
        array $additionalData = [],
        ?int $userId = null
    ): ActivityLog {
        $data = [
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
        ];
        
        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }
        
        return $this->logActivity(
            "{$status} payment of {$amount} {$currency} via {$paymentMethod} for {$entityType} #{$entityId}",
            $entityType,
            $entityId,
            $data,
            self::ACTIVITY_TYPE_PAYMENT,
            $userId
        );
    }
    
    /**
     * تسجيل نشاط شحن
     *
     * @param string $shippingMethod طريقة الشحن
     * @param string $trackingNumber رقم التتبع
     * @param string $status الحالة
     * @param string $entityType نوع الكيان (مثل 'order')
     * @param int $entityId معرف الكيان
     * @param array $additionalData بيانات إضافية (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logShipping(
        string $shippingMethod,
        string $trackingNumber,
        string $status,
        string $entityType,
        int $entityId,
        array $additionalData = [],
        ?int $userId = null
    ): ActivityLog {
        $data = [
            'shipping_method' => $shippingMethod,
            'tracking_number' => $trackingNumber,
            'status' => $status,
        ];
        
        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }
        
        return $this->logActivity(
            "{$status} shipment with tracking number {$trackingNumber} via {$shippingMethod} for {$entityType} #{$entityId}",
            $entityType,
            $entityId,
            $data,
            self::ACTIVITY_TYPE_SHIPPING,
            $userId
        );
    }
    
    /**
     * تسجيل نشاط إرجاع
     *
     * @param string $returnMethod طريقة الإرجاع
     * @param float $amount المبلغ
     * @param string $currency العملة
     * @param string $status الحالة
     * @param string $entityType نوع الكيان (مثل 'order')
     * @param int $entityId معرف الكيان
     * @param array $additionalData بيانات إضافية (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ActivityLog سجل النشاط
     */
    public function logReturn(
        string $returnMethod,
        float $amount,
        string $currency,
        string $status,
        string $entityType,
        int $entityId,
        array $additionalData = [],
        ?int $userId = null
    ): ActivityLog {
        $data = [
            'return_method' => $returnMethod,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
        ];
        
        if (!empty($additionalData)) {
            $data = array_merge($data, $additionalData);
        }
        
        return $this->logActivity(
            "{$status} return of {$amount} {$currency} via {$returnMethod} for {$entityType} #{$entityId}",
            $entityType,
            $entityId,
            $data,
            self::ACTIVITY_TYPE_RETURN,
            $userId
        );
    }
    
    /**
     * تسجيل خطأ في النظام
     *
     * @param string $message رسالة الخطأ
     * @param string $context سياق الخطأ
     * @param \Exception|null $exception الاستثناء (اختياري)
     * @param string $level مستوى الخطأ
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ErrorLog سجل الخطأ
     */
    public function logError(
        string $message,
        string $context,
        ?\Exception $exception = null,
        string $level = self::ERROR_LEVEL_ERROR,
        ?int $userId = null
    ): ErrorLog {
        // الحصول على معرف المستخدم الحالي إذا لم يتم تحديده
        if ($userId === null && Auth::check()) {
            $userId = Auth::id();
        }
        
        // إعداد بيانات الخطأ
        $data = [
            'message' => $message,
            'context' => $context,
        ];
        
        // إضافة معلومات الاستثناء إذا كان متاحًا
        if ($exception) {
            $data['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }
        
        // تسجيل الخطأ في سجل لارافيل
        $this->logToLaravel($message, $level, $data);
        
        // إنشاء سجل الخطأ
        return ErrorLog::create([
            'user_id' => $userId,
            'message' => Str::limit($message, 255),
            'context' => $context,
            'level' => $level,
            'data' => json_encode($data),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    /**
     * تسجيل خطأ معلوماتي
     *
     * @param string $message رسالة الخطأ
     * @param string $context سياق الخطأ
     * @param array $data بيانات إضافية (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ErrorLog سجل الخطأ
     */
    public function logInfo(string $message, string $context, array $data = [], ?int $userId = null): ErrorLog
    {
        // تحويل البيانات الإضافية إلى استثناء وهمي
        $exception = !empty($data) ? $this->createDummyException($data) : null;
        
        return $this->logError($message, $context, $exception, self::ERROR_LEVEL_INFO, $userId);
    }
    
    /**
     * تسجيل خطأ تحذيري
     *
     * @param string $message رسالة الخطأ
     * @param string $context سياق الخطأ
     * @param \Exception|null $exception الاستثناء (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ErrorLog سجل الخطأ
     */
    public function logWarning(
        string $message,
        string $context,
        ?\Exception $exception = null,
        ?int $userId = null
    ): ErrorLog {
        return $this->logError($message, $context, $exception, self::ERROR_LEVEL_WARNING, $userId);
    }
    
    /**
     * تسجيل خطأ حرج
     *
     * @param string $message رسالة الخطأ
     * @param string $context سياق الخطأ
     * @param \Exception|null $exception الاستثناء (اختياري)
     * @param int|null $userId معرف المستخدم (اختياري)
     * @return \App\Models\ErrorLog سجل الخطأ
     */
    public function logCritical(
        string $message,
        string $context,
        ?\Exception $exception = null,
        ?int $userId = null
    ): ErrorLog {
        return $this->logError($message, $context, $exception, self::ERROR_LEVEL_CRITICAL, $userId);
    }
    
    /**
     * الحصول على أنشطة المستخدم
     *
     * @param int $userId معرف المستخدم
     * @param int $limit عدد النتائج
     * @param int $offset بداية النتائج
     * @return array أنشطة المستخدم
     */
    public function getUserActivities(int $userId, int $limit = 10, int $offset = 0): array
    {
        $activities = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
        
        $total = ActivityLog::where('user_id', $userId)->count();
        
        return [
            'activities' => $activities,
            'total' => $total,
        ];
    }
    
    /**
     * الحصول على أنشطة الكيان
     *
     * @param string $entityType نوع الكيان
     * @param int $entityId معرف الكيان
     * @param int $limit عدد النتائج
     * @param int $offset بداية النتائج
     * @return array أنشطة الكيان
     */
    public function getEntityActivities(
        string $entityType,
        int $entityId,
        int $limit = 10,
        int $offset = 0
    ): array {
        $activities = ActivityLog::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
        
        $total = ActivityLog::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->count();
        
        return [
            'activities' => $activities,
            'total' => $total,
        ];
    }
    
    /**
     * الحصول على أحدث الأنشطة في النظام
     *
     * @param int $limit عدد النتائج
     * @param array $types أنواع الأنشطة (اختياري)
     * @return \Illuminate\Database\Eloquent\Collection مجموعة من الأنشطة
     */
    public function getLatestActivities(int $limit = 10, array $types = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');
        
        if (!empty($types)) {
            $query->whereIn('type', $types);
        }
        
        return $query->take($limit)->get();
    }
    
    /**
     * الحصول على أحدث الأخطاء في النظام
     *
     * @param int $limit عدد النتائج
     * @param array $levels مستويات الأخطاء (اختياري)
     * @return \Illuminate\Database\Eloquent\Collection مجموعة من الأخطاء
     */
    public function getLatestErrors(int $limit = 10, array $levels = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = ErrorLog::with('user')
            ->orderBy('created_at', 'desc');
        
        if (!empty($levels)) {
            $query->whereIn('level', $levels);
        }
        
        return $query->take($limit)->get();
    }
    
    /**
     * البحث في سجلات الأنشطة
     *
     * @param array $filters المرشحات
     * @param int $limit عدد النتائج
     * @param int $offset بداية النتائج
     * @return array نتائج البحث
     */
    public function searchActivities(array $filters, int $limit = 10, int $offset = 0): array
    {
        $query = ActivityLog::query();
        
        // تطبيق المرشحات
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (!empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }
        
        if (!empty($filters['entity_id'])) {
            $query->where('entity_id', $filters['entity_id']);
        }
        
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (!empty($filters['action'])) {
            $query->where('action', 'like', "%{$filters['action']}%");
        }
        
        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }
        
        // الحصول على إجمالي عدد النتائج
        $total = $query->count();
        
        // الحصول على النتائج
        $activities = $query->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
        
        return [
            'activities' => $activities,
            'total' => $total,
        ];
    }
    
    /**
     * البحث في سجلات الأخطاء
     *
     * @param array $filters المرشحات
     * @param int $limit عدد النتائج
     * @param int $offset بداية النتائج
     * @return array نتائج البحث
     */
    public function searchErrors(array $filters, int $limit = 10, int $offset = 0): array
    {
        $query = ErrorLog::query();
        
        // تطبيق المرشحات
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }
        
        if (!empty($filters['context'])) {
            $query->where('context', 'like', "%{$filters['context']}%");
        }
        
        if (!empty($filters['message'])) {
            $query->where('message', 'like', "%{$filters['message']}%");
        }
        
        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }
        
        // الحصول على إجمالي عدد النتائج
        $total = $query->count();
        
        // الحصول على النتائج
        $errors = $query->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
        
        return [
            'errors' => $errors,
            'total' => $total,
        ];
    }
    
    /**
     * حذف سجلات الأنشطة القديمة
     *
     * @param int $days عدد الأيام للاحتفاظ بالسجلات
     * @return int عدد السجلات المحذوفة
     */
    public function purgeOldActivities(int $days = 90): int
    {
        $cutoffDate = now()->subDays($days);
        return ActivityLog::where('created_at', '<', $cutoffDate)->delete();
    }
    
    /**
     * حذف سجلات الأخطاء القديمة
     *
     * @param int $days عدد الأيام للاحتفاظ بالسجلات
     * @param array $excludeLevels مستويات الأخطاء المستثناة من الحذف
     * @return int عدد السجلات المحذوفة
     */
    public function purgeOldErrors(int $days = 30, array $excludeLevels = [self::ERROR_LEVEL_CRITICAL]): int
    {
        $cutoffDate = now()->subDays($days);
        $query = ErrorLog::where('created_at', '<', $cutoffDate);
        
        if (!empty($excludeLevels)) {
            $query->whereNotIn('level', $excludeLevels);
        }
        
        return $query->delete();
    }
    
    /**
     * الحصول على إحصائيات الأنشطة
     *
     * @param string $startDate تاريخ البداية (Y-m-d)
     * @param string $endDate تاريخ النهاية (Y-m-d)
     * @return array إحصائيات الأنشطة
     */
    public function getActivityStatistics(string $startDate, string $endDate): array
    {
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';
        
        // إجمالي عدد الأنشطة
        $totalActivities = ActivityLog::whereBetween('created_at', [$startDateTime, $endDateTime])->count();
        
        // عدد الأنشطة حسب النوع
        $activitiesByType = ActivityLog::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select('type', \DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'type')
            ->toArray();
        
        // عدد الأنشطة حسب الكيان
        $activitiesByEntity = ActivityLog::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select('entity_type', \DB::raw('COUNT(*) as count'))
            ->groupBy('entity_type')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'entity_type')
            ->toArray();
        
        // أكثر المستخدمين نشاطًا
        $mostActiveUsers = ActivityLog::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select('user_id', \DB::raw('COUNT(*) as count'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        // تحميل بيانات المستخدمين
        $userIds = $mostActiveUsers->pluck('user_id')->toArray();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');
        
        $topUsers = $mostActiveUsers->map(function ($item) use ($users) {
            $user = $users->get($item->user_id);
            return [
                'user_id' => $item->user_id,
                'name' => $user ? $user->name : 'Unknown User',
                'count' => $item->count,
            ];
        })->toArray();
        
        return [
            'total_activities' => $totalActivities,
            'activities_by_type' => $activitiesByType,
            'activities_by_entity' => $activitiesByEntity,
            'most_active_users' => $topUsers,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];
    }
    
    /**
     * الحصول على إحصائيات الأخطاء
     *
     * @param string $startDate تاريخ البداية (Y-m-d)
     * @param string $endDate تاريخ النهاية (Y-m-d)
     * @return array إحصائيات الأخطاء
     */
    public function getErrorStatistics(string $startDate, string $endDate): array
    {
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';
        
        // إجمالي عدد الأخطاء
        $totalErrors = ErrorLog::whereBetween('created_at', [$startDateTime, $endDateTime])->count();
        
        // عدد الأخطاء حسب المستوى
        $errorsByLevel = ErrorLog::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select('level', \DB::raw('COUNT(*) as count'))
            ->groupBy('level')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'level')
            ->toArray();
        
        // عدد الأخطاء حسب السياق
        $errorsByContext = ErrorLog::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select('context', \DB::raw('COUNT(*) as count'))
            ->groupBy('context')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->pluck('count', 'context')
            ->toArray();
        
        // أكثر رسائل الأخطاء تكرارًا
        $mostCommonErrors = ErrorLog::whereBetween('created_at', [$startDateTime, $endDateTime])
            ->select('message', \DB::raw('COUNT(*) as count'))
            ->groupBy('message')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'message' => $item->message,
                    'count' => $item->count,
                ];
            })
            ->toArray();
        
        return [
            'total_errors' => $totalErrors,
            'errors_by_level' => $errorsByLevel,
            'errors_by_context' => $errorsByContext,
            'most_common_errors' => $mostCommonErrors,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];
    }
    
    /**
     * تحديد التغييرات بين البيانات القديمة والجديدة
     *
     * @param array $oldData البيانات القديمة
     * @param array $newData البيانات الجديدة
     * @return array التغييرات
     */
    protected function getDataChanges(array $oldData, array $newData): array
    {
        $changes = [];
        
        // البحث عن الحقول المتغيرة
        foreach ($newData as $key => $value) {
            // تجاهل الحقول غير الموجودة في البيانات القديمة
            if (!array_key_exists($key, $oldData)) {
                continue;
            }
            
            // تجاهل الحقول التي لم تتغير
            if ($oldData[$key] === $value) {
                continue;
            }
            
            // إضافة التغيير إلى المصفوفة
            $changes[$key] = [
                'old' => $oldData[$key],
                'new' => $value,
            ];
        }
        
        return $changes;
    }
    
    /**
     * تسجيل رسالة في سجل لارافيل
     *
     * @param string $message الرسالة
     * @param string $level المستوى
     * @param array $context السياق
     * @return void
     */
    protected function logToLaravel(string $message, string $level, array $context = []): void
    {
        switch ($level) {
            case self::ERROR_LEVEL_INFO:
                LaravelLog::info($message, $context);
                break;
            case self::ERROR_LEVEL_WARNING:
                LaravelLog::warning($message, $context);
                break;
            case self::ERROR_LEVEL_ERROR:
                LaravelLog::error($message, $context);
                break;
            case self::ERROR_LEVEL_CRITICAL:
                LaravelLog::critical($message, $context);
                break;
            default:
                LaravelLog::error($message, $context);
        }
    }
    
    /**
     * إنشاء استثناء وهمي من البيانات
     *
     * @param array $data البيانات
     * @return \Exception الاستثناء
     */
    protected function createDummyException(array $data): \Exception
    {
        $exception = new \Exception('Dummy exception for additional data');
        
        // إضافة البيانات كخاصية إلى الاستثناء
        $reflection = new \ReflectionClass($exception);
        $property = $reflection->getProperty('message');
        $property->setAccessible(true);
        $property->setValue($exception, json_encode($data));
        
        return $exception;
    }
}