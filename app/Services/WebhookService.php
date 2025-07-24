<?php

namespace App\Services;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookService
{
    /**
     * أنواع الأحداث المتاحة للويب هوك
     */
    const EVENT_ORDER_CREATED = 'order.created';
    const EVENT_ORDER_UPDATED = 'order.updated';
    const EVENT_ORDER_PAID = 'order.paid';
    const EVENT_ORDER_SHIPPED = 'order.shipped';
    const EVENT_ORDER_DELIVERED = 'order.delivered';
    const EVENT_ORDER_CANCELLED = 'order.cancelled';
    const EVENT_PRODUCT_CREATED = 'product.created';
    const EVENT_PRODUCT_UPDATED = 'product.updated';
    const EVENT_PRODUCT_DELETED = 'product.deleted';
    const EVENT_CUSTOMER_CREATED = 'customer.created';
    const EVENT_CUSTOMER_UPDATED = 'customer.updated';
    const EVENT_RETURN_CREATED = 'return.created';
    const EVENT_RETURN_UPDATED = 'return.updated';
    const EVENT_INVENTORY_LOW = 'inventory.low';
    const EVENT_PAYMENT_RECEIVED = 'payment.received';
    const EVENT_PAYMENT_FAILED = 'payment.failed';
    
    /**
     * حالات الويب هوك
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    
    /**
     * حالات إرسال الويب هوك
     */
    const DELIVERY_STATUS_PENDING = 'pending';
    const DELIVERY_STATUS_SUCCESS = 'success';
    const DELIVERY_STATUS_FAILED = 'failed';
    const DELIVERY_STATUS_RETRYING = 'retrying';
    
    /**
     * @var LogService
     */
    protected $logService;
    
    /**
     * إنشاء مثيل جديد من الخدمة
     *
     * @param LogService $logService
     */
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }
    
    /**
     * إنشاء ويب هوك جديد
     *
     * @param string $name اسم الويب هوك
     * @param string $url عنوان URL للويب هوك
     * @param array $events الأحداث التي سيتم الاشتراك فيها
     * @param string $secret المفتاح السري (اختياري)
     * @param array $headers الرؤوس المخصصة (اختياري)
     * @param string $description وصف الويب هوك (اختياري)
     * @param string $status حالة الويب هوك (اختياري)
     * @return Webhook
     */
    public function createWebhook(
        string $name,
        string $url,
        array $events,
        string $secret = null,
        array $headers = [],
        string $description = '',
        string $status = self::STATUS_ACTIVE
    ): Webhook {
        // إنشاء مفتاح سري إذا لم يتم تحديده
        if (empty($secret)) {
            $secret = Str::random(32);
        }
        
        // إنشاء الويب هوك
        $webhook = Webhook::create([
            'name' => $name,
            'url' => $url,
            'events' => json_encode($events),
            'secret' => $secret,
            'headers' => !empty($headers) ? json_encode($headers) : null,
            'description' => $description,
            'status' => $status,
        ]);
        
        // تسجيل النشاط
        $this->logService->logCreation('webhook', $webhook->id, [
            'name' => $name,
            'url' => $url,
            'events' => $events,
        ]);
        
        return $webhook;
    }
    
    /**
     * تحديث ويب هوك موجود
     *
     * @param int $id معرف الويب هوك
     * @param array $data البيانات المراد تحديثها
     * @return Webhook|null
     */
    public function updateWebhook(int $id, array $data): ?Webhook
    {
        $webhook = Webhook::find($id);
        
        if (!$webhook) {
            return null;
        }
        
        // حفظ البيانات القديمة للتسجيل
        $oldData = [
            'name' => $webhook->name,
            'url' => $webhook->url,
            'events' => json_decode($webhook->events, true),
            'headers' => $webhook->headers ? json_decode($webhook->headers, true) : [],
            'description' => $webhook->description,
            'status' => $webhook->status,
        ];
        
        // تحديث الحقول
        if (isset($data['name'])) {
            $webhook->name = $data['name'];
        }
        
        if (isset($data['url'])) {
            $webhook->url = $data['url'];
        }
        
        if (isset($data['events'])) {
            $webhook->events = json_encode($data['events']);
        }
        
        if (isset($data['secret'])) {
            $webhook->secret = $data['secret'];
        }
        
        if (isset($data['headers'])) {
            $webhook->headers = !empty($data['headers']) ? json_encode($data['headers']) : null;
        }
        
        if (isset($data['description'])) {
            $webhook->description = $data['description'];
        }
        
        if (isset($data['status'])) {
            $webhook->status = $data['status'];
        }
        
        $webhook->save();
        
        // تسجيل النشاط
        $newData = [
            'name' => $webhook->name,
            'url' => $webhook->url,
            'events' => json_decode($webhook->events, true),
            'headers' => $webhook->headers ? json_decode($webhook->headers, true) : [],
            'description' => $webhook->description,
            'status' => $webhook->status,
        ];
        
        $this->logService->logUpdate('webhook', $webhook->id, $oldData, $newData);
        
        return $webhook;
    }
    
    /**
     * حذف ويب هوك
     *
     * @param int $id معرف الويب هوك
     * @return bool
     */
    public function deleteWebhook(int $id): bool
    {
        $webhook = Webhook::find($id);
        
        if (!$webhook) {
            return false;
        }
        
        // تسجيل النشاط قبل الحذف
        $this->logService->logDeletion('webhook', $webhook->id, [
            'name' => $webhook->name,
            'url' => $webhook->url,
            'events' => json_decode($webhook->events, true),
        ]);
        
        // حذف سجلات الويب هوك المرتبطة
        WebhookLog::where('webhook_id', $id)->delete();
        
        // حذف الويب هوك
        return $webhook->delete();
    }
    
    /**
     * الحصول على ويب هوك بواسطة المعرف
     *
     * @param int $id معرف الويب هوك
     * @return Webhook|null
     */
    public function getWebhook(int $id): ?Webhook
    {
        return Webhook::find($id);
    }
    
    /**
     * الحصول على جميع الويب هوك
     *
     * @param string|null $status حالة الويب هوك (اختياري)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWebhooks(?string $status = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Webhook::query();
        
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    /**
     * تغيير حالة الويب هوك
     *
     * @param int $id معرف الويب هوك
     * @param string $status الحالة الجديدة
     * @return Webhook|null
     */
    public function toggleWebhookStatus(int $id, string $status): ?Webhook
    {
        $webhook = Webhook::find($id);
        
        if (!$webhook) {
            return null;
        }
        
        $oldStatus = $webhook->status;
        $webhook->status = $status;
        $webhook->save();
        
        // تسجيل النشاط
        $this->logService->logUpdate('webhook', $webhook->id, 
            ['status' => $oldStatus], 
            ['status' => $status]
        );
        
        return $webhook;
    }
    
    /**
     * إرسال حدث إلى جميع الويب هوك المشتركة
     *
     * @param string $event اسم الحدث
     * @param array $payload البيانات المرسلة
     * @return array نتائج الإرسال
     */
    public function dispatchEvent(string $event, array $payload): array
    {
        // الحصول على جميع الويب هوك النشطة المشتركة في هذا الحدث
        $webhooks = $this->getWebhooksForEvent($event);
        
        $results = [];
        
        foreach ($webhooks as $webhook) {
            // إرسال الحدث إلى الويب هوك
            $result = $this->sendWebhook($webhook, $event, $payload);
            $results[] = $result;
        }
        
        return $results;
    }
    
    /**
     * الحصول على الويب هوك المشتركة في حدث معين
     *
     * @param string $event اسم الحدث
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWebhooksForEvent(string $event): \Illuminate\Database\Eloquent\Collection
    {
        return Webhook::where('status', self::STATUS_ACTIVE)
            ->where(function ($query) use ($event) {
                $query->whereRaw("JSON_CONTAINS(events, '\"$event\"')")
                    ->orWhereRaw("JSON_CONTAINS(events, '\"*\"')");
            })
            ->get();
    }
    
    /**
     * إرسال حدث إلى ويب هوك محدد
     *
     * @param Webhook $webhook الويب هوك
     * @param string $event اسم الحدث
     * @param array $payload البيانات المرسلة
     * @return array نتيجة الإرسال
     */
    public function sendWebhook(Webhook $webhook, string $event, array $payload): array
    {
        // إنشاء سجل إرسال الويب هوك
        $log = WebhookLog::create([
            'webhook_id' => $webhook->id,
            'event' => $event,
            'payload' => json_encode($payload),
            'status' => self::DELIVERY_STATUS_PENDING,
        ]);
        
        // إعداد البيانات المرسلة
        $data = [
            'id' => (string) Str::uuid(),
            'event' => $event,
            'created_at' => now()->toIso8601String(),
            'data' => $payload,
        ];
        
        // إعداد الرؤوس
        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'Marca-Webhook/' . config('app.version', '1.0'),
            'X-Webhook-ID' => (string) $webhook->id,
            'X-Webhook-Event' => $event,
            'X-Webhook-Timestamp' => time(),
        ];
        
        // إضافة توقيع الأمان إذا كان هناك مفتاح سري
        if ($webhook->secret) {
            $signature = $this->generateSignature(json_encode($data), $webhook->secret);
            $headers['X-Webhook-Signature'] = $signature;
        }
        
        // إضافة الرؤوس المخصصة
        if ($webhook->headers) {
            $customHeaders = json_decode($webhook->headers, true);
            if (is_array($customHeaders)) {
                $headers = array_merge($headers, $customHeaders);
            }
        }
        
        $startTime = microtime(true);
        $success = false;
        $statusCode = null;
        $response = null;
        $error = null;
        
        try {
            // إرسال الطلب
            $httpResponse = Http::withHeaders($headers)
                ->timeout(30)
                ->post($webhook->url, $data);
            
            $statusCode = $httpResponse->status();
            $response = $httpResponse->body();
            $success = $httpResponse->successful();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2); // بالميلي ثانية
        
        // تحديث سجل الإرسال
        $log->status = $success ? self::DELIVERY_STATUS_SUCCESS : self::DELIVERY_STATUS_FAILED;
        $log->response_code = $statusCode;
        $log->response_body = $response;
        $log->execution_time = $executionTime;
        $log->error = $error;
        $log->save();
        
        // تسجيل النشاط
        if ($success) {
            $this->logService->logInfo(
                "Webhook delivered successfully",
                "webhook.delivery",
                [
                    'webhook_id' => $webhook->id,
                    'event' => $event,
                    'status_code' => $statusCode,
                    'execution_time' => $executionTime,
                ]
            );
        } else {
            $this->logService->logWarning(
                "Webhook delivery failed",
                "webhook.delivery",
                new \Exception($error ?? "HTTP Error: $statusCode")
            );
        }
        
        return [
            'webhook_id' => $webhook->id,
            'log_id' => $log->id,
            'event' => $event,
            'success' => $success,
            'status_code' => $statusCode,
            'execution_time' => $executionTime,
            'error' => $error,
        ];
    }
    
    /**
     * إعادة إرسال ويب هوك فاشل
     *
     * @param int $logId معرف سجل الويب هوك
     * @return array|null نتيجة الإرسال
     */
    public function retryWebhook(int $logId): ?array
    {
        $log = WebhookLog::find($logId);
        
        if (!$log || $log->status === self::DELIVERY_STATUS_SUCCESS) {
            return null;
        }
        
        $webhook = Webhook::find($log->webhook_id);
        
        if (!$webhook || $webhook->status !== self::STATUS_ACTIVE) {
            return null;
        }
        
        // تحديث حالة السجل
        $log->status = self::DELIVERY_STATUS_RETRYING;
        $log->save();
        
        // إعادة إرسال الويب هوك
        $payload = json_decode($log->payload, true) ?: [];
        return $this->sendWebhook($webhook, $log->event, $payload);
    }
    
    /**
     * إعادة إرسال جميع الويب هوك الفاشلة
     *
     * @param string|null $event اسم الحدث (اختياري)
     * @param int|null $webhookId معرف الويب هوك (اختياري)
     * @param int $limit الحد الأقصى لعدد المحاولات
     * @return array نتائج الإرسال
     */
    public function retryFailedWebhooks(?string $event = null, ?int $webhookId = null, int $limit = 50): array
    {
        $query = WebhookLog::where('status', self::DELIVERY_STATUS_FAILED);
        
        if ($event !== null) {
            $query->where('event', $event);
        }
        
        if ($webhookId !== null) {
            $query->where('webhook_id', $webhookId);
        }
        
        $failedLogs = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        $results = [];
        
        foreach ($failedLogs as $log) {
            $result = $this->retryWebhook($log->id);
            if ($result) {
                $results[] = $result;
            }
        }
        
        return $results;
    }
    
    /**
     * الحصول على سجلات الويب هوك
     *
     * @param int|null $webhookId معرف الويب هوك (اختياري)
     * @param string|null $event اسم الحدث (اختياري)
     * @param string|null $status حالة الإرسال (اختياري)
     * @param int $limit عدد النتائج
     * @param int $offset بداية النتائج
     * @return array سجلات الويب هوك
     */
    public function getWebhookLogs(
        ?int $webhookId = null,
        ?string $event = null,
        ?string $status = null,
        int $limit = 10,
        int $offset = 0
    ): array {
        $query = WebhookLog::with('webhook');
        
        if ($webhookId !== null) {
            $query->where('webhook_id', $webhookId);
        }
        
        if ($event !== null) {
            $query->where('event', $event);
        }
        
        if ($status !== null) {
            $query->where('status', $status);
        }
        
        $total = $query->count();
        
        $logs = $query->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();
        
        return [
            'logs' => $logs,
            'total' => $total,
        ];
    }
    
    /**
     * الحصول على إحصائيات الويب هوك
     *
     * @param int|null $webhookId معرف الويب هوك (اختياري)
     * @param string $startDate تاريخ البداية (Y-m-d)
     * @param string $endDate تاريخ النهاية (Y-m-d)
     * @return array إحصائيات الويب هوك
     */
    public function getWebhookStatistics(?int $webhookId = null, string $startDate, string $endDate): array
    {
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';
        
        $query = WebhookLog::whereBetween('created_at', [$startDateTime, $endDateTime]);
        
        if ($webhookId !== null) {
            $query->where('webhook_id', $webhookId);
        }
        
        // إجمالي عدد الإرسالات
        $totalDeliveries = $query->count();
        
        // عدد الإرسالات الناجحة
        $successfulDeliveries = (clone $query)->where('status', self::DELIVERY_STATUS_SUCCESS)->count();
        
        // عدد الإرسالات الفاشلة
        $failedDeliveries = (clone $query)->where('status', self::DELIVERY_STATUS_FAILED)->count();
        
        // متوسط وقت التنفيذ
        $avgExecutionTime = (clone $query)->where('status', self::DELIVERY_STATUS_SUCCESS)
            ->avg('execution_time') ?: 0;
        
        // عدد الإرسالات حسب الحدث
        $deliveriesByEvent = (clone $query)
            ->select('event', \DB::raw('COUNT(*) as count'))
            ->groupBy('event')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'event')
            ->toArray();
        
        // عدد الإرسالات حسب الويب هوك
        $deliveriesByWebhook = (clone $query)
            ->select('webhook_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('webhook_id')
            ->orderBy('count', 'desc')
            ->get();
        
        // تحميل بيانات الويب هوك
        $webhookIds = $deliveriesByWebhook->pluck('webhook_id')->toArray();
        $webhooks = Webhook::whereIn('id', $webhookIds)->get()->keyBy('id');
        
        $topWebhooks = $deliveriesByWebhook->map(function ($item) use ($webhooks) {
            $webhook = $webhooks->get($item->webhook_id);
            return [
                'webhook_id' => $item->webhook_id,
                'name' => $webhook ? $webhook->name : 'Unknown Webhook',
                'count' => $item->count,
            ];
        })->toArray();
        
        return [
            'total_deliveries' => $totalDeliveries,
            'successful_deliveries' => $successfulDeliveries,
            'failed_deliveries' => $failedDeliveries,
            'success_rate' => $totalDeliveries > 0 ? round(($successfulDeliveries / $totalDeliveries) * 100, 2) : 0,
            'avg_execution_time' => round($avgExecutionTime, 2),
            'deliveries_by_event' => $deliveriesByEvent,
            'top_webhooks' => $topWebhooks,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];
    }
    
    /**
     * حذف سجلات الويب هوك القديمة
     *
     * @param int $days عدد الأيام للاحتفاظ بالسجلات
     * @return int عدد السجلات المحذوفة
     */
    public function purgeOldWebhookLogs(int $days = 30): int
    {
        $cutoffDate = now()->subDays($days);
        return WebhookLog::where('created_at', '<', $cutoffDate)->delete();
    }
    
    /**
     * اختبار ويب هوك
     *
     * @param int $webhookId معرف الويب هوك
     * @param string|null $event اسم الحدث (اختياري)
     * @param array $payload البيانات المرسلة (اختياري)
     * @return array|null نتيجة الاختبار
     */
    public function testWebhook(int $webhookId, ?string $event = null, array $payload = []): ?array
    {
        $webhook = Webhook::find($webhookId);
        
        if (!$webhook) {
            return null;
        }
        
        // استخدام حدث اختبار إذا لم يتم تحديد حدث
        if ($event === null) {
            $event = 'webhook.test';
        }
        
        // استخدام بيانات اختبار إذا لم يتم تحديد بيانات
        if (empty($payload)) {
            $payload = [
                'test' => true,
                'timestamp' => now()->toIso8601String(),
                'webhook_id' => $webhook->id,
                'webhook_name' => $webhook->name,
            ];
        }
        
        // إرسال الويب هوك
        return $this->sendWebhook($webhook, $event, $payload);
    }
    
    /**
     * توليد توقيع أمان للويب هوك
     *
     * @param string $payload البيانات المرسلة
     * @param string $secret المفتاح السري
     * @return string التوقيع
     */
    protected function generateSignature(string $payload, string $secret): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }
    
    /**
     * التحقق من صحة توقيع الويب هوك
     *
     * @param string $payload البيانات المستلمة
     * @param string $signature التوقيع المستلم
     * @param string $secret المفتاح السري
     * @return bool صحة التوقيع
     */
    public function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = $this->generateSignature($payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}