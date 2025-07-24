<?php

namespace App\Services;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class QueueService
{
    /**
     * أنواع الوظائف
     */
    const JOB_TYPE_ORDER = 'order';
    const JOB_TYPE_NOTIFICATION = 'notification';
    const JOB_TYPE_EXPORT = 'export';
    const JOB_TYPE_IMPORT = 'import';
    const JOB_TYPE_EMAIL = 'email';
    const JOB_TYPE_WEBHOOK = 'webhook';
    const JOB_TYPE_REPORT = 'report';
    const JOB_TYPE_MAINTENANCE = 'maintenance';
    
    /**
     * حالات الوظائف
     */
    const JOB_STATUS_PENDING = 'pending';
    const JOB_STATUS_PROCESSING = 'processing';
    const JOB_STATUS_COMPLETED = 'completed';
    const JOB_STATUS_FAILED = 'failed';
    const JOB_STATUS_RETRYING = 'retrying';
    
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
     * إضافة وظيفة إلى الطابور
     *
     * @param object $job الوظيفة المراد إضافتها
     * @param string $queue اسم الطابور (اختياري)
     * @param int $delay التأخير بالثواني (اختياري)
     * @param string $jobType نوع الوظيفة (اختياري)
     * @param string $description وصف الوظيفة (اختياري)
     * @return mixed معرف الوظيفة
     */
    public function dispatch($job, string $queue = null, int $delay = 0, string $jobType = null, string $description = null)
    {
        try {
            // تحديد الطابور إذا تم توفيره
            if ($queue) {
                $job->onQueue($queue);
            }
            
            // تحديد التأخير إذا تم توفيره
            if ($delay > 0) {
                $job->delay($delay);
            }
            
            // إضافة البيانات الوصفية إلى الوظيفة إذا كانت متاحة
            if (method_exists($job, 'withMetadata') && ($jobType || $description)) {
                $metadata = [];
                
                if ($jobType) {
                    $metadata['type'] = $jobType;
                }
                
                if ($description) {
                    $metadata['description'] = $description;
                }
                
                $job->withMetadata($metadata);
            }
            
            // إرسال الوظيفة إلى الطابور
            $jobId = Queue::push($job);
            
            // تسجيل النشاط
            $this->logService->logInfo(
                "Job dispatched to queue",
                "queue.dispatch",
                [
                    'job_id' => $jobId,
                    'job_class' => get_class($job),
                    'queue' => $queue ?: 'default',
                    'delay' => $delay,
                    'type' => $jobType ?: 'unknown',
                    'description' => $description,
                ]
            );
            
            return $jobId;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to dispatch job to queue",
                "queue.dispatch",
                $e
            );
            
            throw $e;
        }
    }
    
    /**
     * إضافة وظيفة إلى الطابور بتأخير
     *
     * @param object $job الوظيفة المراد إضافتها
     * @param int $delay التأخير بالثواني
     * @param string $queue اسم الطابور (اختياري)
     * @param string $jobType نوع الوظيفة (اختياري)
     * @param string $description وصف الوظيفة (اختياري)
     * @return mixed معرف الوظيفة
     */
    public function dispatchWithDelay($job, int $delay, string $queue = null, string $jobType = null, string $description = null)
    {
        return $this->dispatch($job, $queue, $delay, $jobType, $description);
    }
    
    /**
     * جدولة وظيفة للتنفيذ في وقت محدد
     *
     * @param object $job الوظيفة المراد جدولتها
     * @param \DateTimeInterface|\DateInterval|\Carbon\Carbon|int $time وقت التنفيذ
     * @param string $queue اسم الطابور (اختياري)
     * @param string $jobType نوع الوظيفة (اختياري)
     * @param string $description وصف الوظيفة (اختياري)
     * @return mixed معرف الوظيفة
     */
    public function schedule($job, $time, string $queue = null, string $jobType = null, string $description = null)
    {
        try {
            // تحديد الطابور إذا تم توفيره
            if ($queue) {
                $job->onQueue($queue);
            }
            
            // إضافة البيانات الوصفية إلى الوظيفة إذا كانت متاحة
            if (method_exists($job, 'withMetadata') && ($jobType || $description)) {
                $metadata = [];
                
                if ($jobType) {
                    $metadata['type'] = $jobType;
                }
                
                if ($description) {
                    $metadata['description'] = $description;
                }
                
                $job->withMetadata($metadata);
            }
            
            // جدولة الوظيفة
            $jobId = Queue::later($time, $job);
            
            // تحويل وقت التنفيذ إلى تنسيق قابل للقراءة
            $readableTime = $time;
            if ($time instanceof \DateTimeInterface) {
                $readableTime = $time->format('Y-m-d H:i:s');
            } elseif (is_numeric($time)) {
                $readableTime = Carbon::now()->addSeconds($time)->format('Y-m-d H:i:s');
            }
            
            // تسجيل النشاط
            $this->logService->logInfo(
                "Job scheduled in queue",
                "queue.schedule",
                [
                    'job_id' => $jobId,
                    'job_class' => get_class($job),
                    'queue' => $queue ?: 'default',
                    'scheduled_time' => $readableTime,
                    'type' => $jobType ?: 'unknown',
                    'description' => $description,
                ]
            );
            
            return $jobId;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to schedule job in queue",
                "queue.schedule",
                $e
            );
            
            throw $e;
        }
    }
    
    /**
     * إضافة وظيفة للتنفيذ الفوري (متزامن)
     *
     * @param object $job الوظيفة المراد تنفيذها
     * @param string $jobType نوع الوظيفة (اختياري)
     * @param string $description وصف الوظيفة (اختياري)
     * @return mixed نتيجة تنفيذ الوظيفة
     */
    public function dispatchNow($job, string $jobType = null, string $description = null)
    {
        try {
            // تسجيل بداية التنفيذ
            $this->logService->logInfo(
                "Executing job synchronously",
                "queue.dispatch_now",
                [
                    'job_class' => get_class($job),
                    'type' => $jobType ?: 'unknown',
                    'description' => $description,
                ]
            );
            
            // تنفيذ الوظيفة فورًا
            $result = Queue::dispatchNow($job);
            
            // تسجيل اكتمال التنفيذ
            $this->logService->logInfo(
                "Synchronous job execution completed",
                "queue.dispatch_now",
                [
                    'job_class' => get_class($job),
                    'type' => $jobType ?: 'unknown',
                    'description' => $description,
                    'result' => $result,
                ]
            );
            
            return $result;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to execute job synchronously",
                "queue.dispatch_now",
                $e
            );
            
            throw $e;
        }
    }
    
    /**
     * إضافة مجموعة من الوظائف إلى الطابور
     *
     * @param array $jobs مصفوفة من الوظائف
     * @param string $queue اسم الطابور (اختياري)
     * @param string $jobType نوع الوظيفة (اختياري)
     * @param string $description وصف الوظيفة (اختياري)
     * @return array مصفوفة من معرفات الوظائف
     */
    public function dispatchBatch(array $jobs, string $queue = null, string $jobType = null, string $description = null): array
    {
        $jobIds = [];
        
        foreach ($jobs as $job) {
            $jobIds[] = $this->dispatch($job, $queue, 0, $jobType, $description);
        }
        
        return $jobIds;
    }
    
    /**
     * إضافة وظيفة متكررة إلى الطابور
     *
     * @param object $job الوظيفة المراد إضافتها
     * @param string $frequency تكرار الوظيفة (مثل: daily, hourly، إلخ)
     * @param string $queue اسم الطابور (اختياري)
     * @param string $jobType نوع الوظيفة (اختياري)
     * @param string $description وصف الوظيفة (اختياري)
     * @return mixed معرف الوظيفة
     */
    public function dispatchRecurring($job, string $frequency, string $queue = null, string $jobType = null, string $description = null)
    {
        // هذه الطريقة تتطلب تكامل مع مكتبة مثل Laravel Task Scheduling
        // هنا نقوم بتنفيذ الوظيفة مرة واحدة ونترك التكرار للمجدول
        
        // إضافة معلومات التكرار إلى الوصف
        $recurringDescription = $description ? "{$description} (Recurring: {$frequency})" : "Recurring job: {$frequency}";
        
        return $this->dispatch($job, $queue, 0, $jobType, $recurringDescription);
    }
    
    /**
     * الحصول على إحصائيات الطابور
     *
     * @param string|array $queues أسماء الطوابير (اختياري)
     * @return array إحصائيات الطابور
     */
    public function getQueueStats($queues = null): array
    {
        try {
            $stats = [];
            
            // تحديد الطوابير المراد فحصها
            if (is_null($queues)) {
                $queues = ['default'];
            } elseif (is_string($queues)) {
                $queues = [$queues];
            }
            
            // الحصول على إحصائيات لكل طابور
            foreach ($queues as $queue) {
                $queueStats = [
                    'name' => $queue,
                    'size' => 0,
                    'failed' => 0,
                    'recent_jobs' => [],
                ];
                
                // محاولة الحصول على حجم الطابور
                try {
                    // استخدام Redis إذا كان متاحًا
                    if (config('queue.default') === 'redis') {
                        $queueStats['size'] = Redis::command('llen', ["queues:{$queue}"]);
                    }
                    // استخدام قاعدة البيانات إذا كانت متاحة
                    elseif (config('queue.default') === 'database') {
                        $queueStats['size'] = DB::table(config('queue.connections.database.table', 'jobs'))
                            ->where('queue', $queue)
                            ->count();
                    }
                } catch (\Exception $e) {
                    // تسجيل الخطأ ولكن الاستمرار
                    Log::warning("Failed to get queue size for {$queue}: " . $e->getMessage());
                }
                
                // محاولة الحصول على عدد الوظائف الفاشلة
                try {
                    if (config('queue.failed.database')) {
                        $queueStats['failed'] = DB::table(config('queue.failed.table', 'failed_jobs'))
                            ->where('queue', $queue)
                            ->count();
                    }
                } catch (\Exception $e) {
                    // تسجيل الخطأ ولكن الاستمرار
                    Log::warning("Failed to get failed jobs count for {$queue}: " . $e->getMessage());
                }
                
                // محاولة الحصول على الوظائف الأخيرة
                try {
                    if (config('queue.default') === 'database') {
                        $queueStats['recent_jobs'] = DB::table(config('queue.connections.database.table', 'jobs'))
                            ->where('queue', $queue)
                            ->orderBy('id', 'desc')
                            ->limit(5)
                            ->get()
                            ->map(function ($job) {
                                return [
                                    'id' => $job->id,
                                    'payload' => json_decode($job->payload, true),
                                    'attempts' => $job->attempts,
                                    'created_at' => $job->created_at,
                                ];
                            })
                            ->toArray();
                    }
                } catch (\Exception $e) {
                    // تسجيل الخطأ ولكن الاستمرار
                    Log::warning("Failed to get recent jobs for {$queue}: " . $e->getMessage());
                }
                
                $stats[$queue] = $queueStats;
            }
            
            return $stats;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to get queue statistics",
                "queue.stats",
                $e
            );
            
            return [];
        }
    }
    
    /**
     * تنظيف الطابور من الوظائف القديمة
     *
     * @param string $queue اسم الطابور
     * @param int $olderThanHours عمر الوظائف بالساعات
     * @return int عدد الوظائف التي تم تنظيفها
     */
    public function cleanupOldJobs(string $queue, int $olderThanHours = 24): int
    {
        try {
            $count = 0;
            
            // تنظيف الوظائف القديمة من قاعدة البيانات
            if (config('queue.default') === 'database') {
                $cutoffDate = Carbon::now()->subHours($olderThanHours);
                
                $count = DB::table(config('queue.connections.database.table', 'jobs'))
                    ->where('queue', $queue)
                    ->where('created_at', '<', $cutoffDate)
                    ->delete();
            }
            
            // تسجيل النشاط
            $this->logService->logInfo(
                "Cleaned up old jobs from queue",
                "queue.cleanup",
                [
                    'queue' => $queue,
                    'older_than_hours' => $olderThanHours,
                    'jobs_removed' => $count,
                ]
            );
            
            return $count;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to clean up old jobs",
                "queue.cleanup",
                $e
            );
            
            return 0;
        }
    }
    
    /**
     * تنظيف الوظائف الفاشلة القديمة
     *
     * @param int $olderThanDays عمر الوظائف بالأيام
     * @return int عدد الوظائف التي تم تنظيفها
     */
    public function cleanupFailedJobs(int $olderThanDays = 7): int
    {
        try {
            $cutoffDate = Carbon::now()->subDays($olderThanDays);
            
            $count = DB::table(config('queue.failed.table', 'failed_jobs'))
                ->where('failed_at', '<', $cutoffDate)
                ->delete();
            
            // تسجيل النشاط
            $this->logService->logInfo(
                "Cleaned up old failed jobs",
                "queue.cleanup_failed",
                [
                    'older_than_days' => $olderThanDays,
                    'jobs_removed' => $count,
                ]
            );
            
            return $count;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to clean up old failed jobs",
                "queue.cleanup_failed",
                $e
            );
            
            return 0;
        }
    }
    
    /**
     * إعادة محاولة تنفيذ وظيفة فاشلة
     *
     * @param string|int $jobId معرف الوظيفة
     * @return bool نجاح العملية
     */
    public function retryFailedJob($jobId): bool
    {
        try {
            // الحصول على الوظيفة الفاشلة
            $failedJob = DB::table(config('queue.failed.table', 'failed_jobs'))
                ->where('id', $jobId)
                ->first();
            
            if (!$failedJob) {
                throw new \Exception("Failed job with ID {$jobId} not found");
            }
            
            // إعادة إضافة الوظيفة إلى الطابور
            $payload = json_decode($failedJob->payload, true);
            $queue = $failedJob->queue;
            
            // استخراج اسم الفئة من الحمولة
            $command = unserialize($payload['data']['command']);
            
            // إضافة الوظيفة إلى الطابور مرة أخرى
            $this->dispatch($command, $queue, 0, self::JOB_TYPE_RETRYING, "Retry of failed job ID: {$jobId}");
            
            // حذف الوظيفة الفاشلة من جدول الوظائف الفاشلة
            DB::table(config('queue.failed.table', 'failed_jobs'))
                ->where('id', $jobId)
                ->delete();
            
            // تسجيل النشاط
            $this->logService->logInfo(
                "Retried failed job",
                "queue.retry_failed",
                [
                    'job_id' => $jobId,
                    'queue' => $queue,
                    'job_class' => get_class($command),
                ]
            );
            
            return true;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to retry failed job",
                "queue.retry_failed",
                $e
            );
            
            return false;
        }
    }
    
    /**
     * إعادة محاولة تنفيذ جميع الوظائف الفاشلة
     *
     * @param string $queue اسم الطابور (اختياري)
     * @return int عدد الوظائف التي تمت إعادة محاولتها
     */
    public function retryAllFailedJobs(string $queue = null): int
    {
        try {
            // بناء الاستعلام
            $query = DB::table(config('queue.failed.table', 'failed_jobs'));
            
            // تصفية حسب الطابور إذا تم توفيره
            if ($queue) {
                $query->where('queue', $queue);
            }
            
            // الحصول على جميع الوظائف الفاشلة
            $failedJobs = $query->get();
            
            $count = 0;
            
            // إعادة محاولة كل وظيفة
            foreach ($failedJobs as $failedJob) {
                if ($this->retryFailedJob($failedJob->id)) {
                    $count++;
                }
            }
            
            // تسجيل النشاط
            $this->logService->logInfo(
                "Retried all failed jobs",
                "queue.retry_all_failed",
                [
                    'queue' => $queue ?: 'all',
                    'jobs_retried' => $count,
                    'total_failed_jobs' => count($failedJobs),
                ]
            );
            
            return $count;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to retry all failed jobs",
                "queue.retry_all_failed",
                $e
            );
            
            return 0;
        }
    }
    
    /**
     * الحصول على قائمة الوظائف الفاشلة
     *
     * @param string $queue اسم الطابور (اختياري)
     * @param int $limit عدد النتائج (اختياري)
     * @param int $offset الإزاحة (اختياري)
     * @return array قائمة الوظائف الفاشلة
     */
    public function getFailedJobs(string $queue = null, int $limit = 10, int $offset = 0): array
    {
        try {
            // بناء الاستعلام
            $query = DB::table(config('queue.failed.table', 'failed_jobs'))
                ->orderBy('failed_at', 'desc');
            
            // تصفية حسب الطابور إذا تم توفيره
            if ($queue) {
                $query->where('queue', $queue);
            }
            
            // تطبيق الحد والإزاحة
            $query->limit($limit)->offset($offset);
            
            // الحصول على النتائج
            $failedJobs = $query->get();
            
            // تحويل النتائج إلى مصفوفة
            $result = [];
            foreach ($failedJobs as $job) {
                $payload = json_decode($job->payload, true);
                $exception = json_decode($job->exception, true);
                
                $result[] = [
                    'id' => $job->id,
                    'queue' => $job->queue,
                    'payload' => $payload,
                    'exception' => $exception,
                    'failed_at' => $job->failed_at,
                    'job_class' => $payload['data']['commandName'] ?? 'Unknown',
                    'error_message' => $exception ?? 'Unknown error',
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to get failed jobs",
                "queue.get_failed",
                $e
            );
            
            return [];
        }
    }
    
    /**
     * الحصول على عدد الوظائف الفاشلة
     *
     * @param string $queue اسم الطابور (اختياري)
     * @return int عدد الوظائف الفاشلة
     */
    public function getFailedJobsCount(string $queue = null): int
    {
        try {
            // بناء الاستعلام
            $query = DB::table(config('queue.failed.table', 'failed_jobs'));
            
            // تصفية حسب الطابور إذا تم توفيره
            if ($queue) {
                $query->where('queue', $queue);
            }
            
            // الحصول على العدد
            return $query->count();
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to get failed jobs count",
                "queue.get_failed_count",
                $e
            );
            
            return 0;
        }
    }
    
    /**
     * الحصول على إحصائيات الوظائف الفاشلة حسب النوع
     *
     * @return array إحصائيات الوظائف الفاشلة
     */
    public function getFailedJobsStatsByType(): array
    {
        try {
            $stats = [];
            
            // الحصول على جميع الوظائف الفاشلة
            $failedJobs = DB::table(config('queue.failed.table', 'failed_jobs'))->get();
            
            // تجميع الإحصائيات حسب النوع
            foreach ($failedJobs as $job) {
                $payload = json_decode($job->payload, true);
                $commandName = $payload['data']['commandName'] ?? 'Unknown';
                
                // تحديد نوع الوظيفة بناءً على اسم الفئة
                $jobType = 'unknown';
                
                if (strpos($commandName, 'Order') !== false) {
                    $jobType = self::JOB_TYPE_ORDER;
                } elseif (strpos($commandName, 'Notification') !== false) {
                    $jobType = self::JOB_TYPE_NOTIFICATION;
                } elseif (strpos($commandName, 'Export') !== false) {
                    $jobType = self::JOB_TYPE_EXPORT;
                } elseif (strpos($commandName, 'Import') !== false) {
                    $jobType = self::JOB_TYPE_IMPORT;
                } elseif (strpos($commandName, 'Email') !== false || strpos($commandName, 'Mail') !== false) {
                    $jobType = self::JOB_TYPE_EMAIL;
                } elseif (strpos($commandName, 'Webhook') !== false) {
                    $jobType = self::JOB_TYPE_WEBHOOK;
                } elseif (strpos($commandName, 'Report') !== false) {
                    $jobType = self::JOB_TYPE_REPORT;
                } elseif (strpos($commandName, 'Maintenance') !== false) {
                    $jobType = self::JOB_TYPE_MAINTENANCE;
                }
                
                // زيادة العداد لهذا النوع
                if (!isset($stats[$jobType])) {
                    $stats[$jobType] = 0;
                }
                
                $stats[$jobType]++;
            }
            
            return $stats;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to get failed jobs stats by type",
                "queue.get_failed_stats",
                $e
            );
            
            return [];
        }
    }
    
    /**
     * حذف وظيفة فاشلة
     *
     * @param string|int $jobId معرف الوظيفة
     * @return bool نجاح العملية
     */
    public function deleteFailedJob($jobId): bool
    {
        try {
            // حذف الوظيفة الفاشلة
            $deleted = DB::table(config('queue.failed.table', 'failed_jobs'))
                ->where('id', $jobId)
                ->delete();
            
            if ($deleted) {
                // تسجيل النشاط
                $this->logService->logInfo(
                    "Deleted failed job",
                    "queue.delete_failed",
                    ['job_id' => $jobId]
                );
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to delete failed job",
                "queue.delete_failed",
                $e
            );
            
            return false;
        }
    }
    
    /**
     * حذف جميع الوظائف الفاشلة
     *
     * @param string $queue اسم الطابور (اختياري)
     * @return int عدد الوظائف التي تم حذفها
     */
    public function deleteAllFailedJobs(string $queue = null): int
    {
        try {
            // بناء الاستعلام
            $query = DB::table(config('queue.failed.table', 'failed_jobs'));
            
            // تصفية حسب الطابور إذا تم توفيره
            if ($queue) {
                $query->where('queue', $queue);
            }
            
            // حذف الوظائف
            $count = $query->delete();
            
            // تسجيل النشاط
            $this->logService->logInfo(
                "Deleted all failed jobs",
                "queue.delete_all_failed",
                [
                    'queue' => $queue ?: 'all',
                    'jobs_deleted' => $count,
                ]
            );
            
            return $count;
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to delete all failed jobs",
                "queue.delete_all_failed",
                $e
            );
            
            return 0;
        }
    }
    
    /**
     * مراقبة حالة الطابور وإرسال تنبيهات إذا كان هناك مشاكل
     *
     * @param string|array $queues أسماء الطوابير (اختياري)
     * @param int $failedThreshold عتبة الوظائف الفاشلة (اختياري)
     * @param int $sizeThreshold عتبة حجم الطابور (اختياري)
     * @return array نتائج المراقبة
     */
    public function monitorQueues($queues = null, int $failedThreshold = 10, int $sizeThreshold = 100): array
    {
        try {
            $stats = $this->getQueueStats($queues);
            $alerts = [];
            
            foreach ($stats as $queueName => $queueStats) {
                // التحقق من عدد الوظائف الفاشلة
                if ($queueStats['failed'] >= $failedThreshold) {
                    $alerts[] = [
                        'type' => 'failed_jobs',
                        'queue' => $queueName,
                        'count' => $queueStats['failed'],
                        'threshold' => $failedThreshold,
                        'message' => "Queue {$queueName} has {$queueStats['failed']} failed jobs (threshold: {$failedThreshold})",
                    ];
                }
                
                // التحقق من حجم الطابور
                if ($queueStats['size'] >= $sizeThreshold) {
                    $alerts[] = [
                        'type' => 'queue_size',
                        'queue' => $queueName,
                        'size' => $queueStats['size'],
                        'threshold' => $sizeThreshold,
                        'message' => "Queue {$queueName} has {$queueStats['size']} pending jobs (threshold: {$sizeThreshold})",
                    ];
                }
            }
            
            // إرسال تنبيهات إذا كان هناك مشاكل
            if (!empty($alerts)) {
                // تسجيل التنبيهات
                foreach ($alerts as $alert) {
                    $this->logService->logWarning(
                        $alert['message'],
                        "queue.monitor",
                        new \Exception($alert['message']),
                        null
                    );
                }
                
                // يمكن إضافة إرسال إشعارات هنا
            }
            
            return [
                'stats' => $stats,
                'alerts' => $alerts,
            ];
        } catch (\Exception $e) {
            // تسجيل الخطأ
            $this->logService->logError(
                "Failed to monitor queues",
                "queue.monitor",
                $e
            );
            
            return [
                'stats' => [],
                'alerts' => [],
                'error' => $e->getMessage(),
            ];
        }
    }
}