<?php

namespace App\Notifications;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $returnRequest;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(ReturnRequest $returnRequest, string $oldStatus = null)
    {
        $this->returnRequest = $returnRequest;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('تحديث حالة طلب المرتجعات #' . $this->returnRequest->return_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم تحديث حالة طلب المرتجعات #' . $this->returnRequest->return_number . '.');
            
        switch ($this->returnRequest->status) {
            case ReturnRequest::STATUS_APPROVED:
                $message->line('تمت الموافقة على طلب المرتجعات الخاص بك.')
                       ->line('يرجى اتباع التعليمات المرسلة إليك لإرجاع المنتجات.');
                break;
            case ReturnRequest::STATUS_REJECTED:
                $message->line('نأسف لإبلاغك بأنه تم رفض طلب المرتجعات الخاص بك.')
                       ->line('سبب الرفض: ' . ($this->returnRequest->admin_notes ?? 'لم يتم تحديد سبب.'));
                break;
            case ReturnRequest::STATUS_COMPLETED:
                $message->line('تم إكمال طلب المرتجعات الخاص بك بنجاح.')
                       ->line('تم معالجة المبلغ المسترد: ' . number_format($this->returnRequest->total_amount, 2));
                break;
            case ReturnRequest::STATUS_CANCELLED:
                $message->line('تم إلغاء طلب المرتجعات الخاص بك.');
                break;
        }
        
        return $message
            ->action('عرض تفاصيل الطلب', route('user.returns.show', $this->returnRequest->id))
            ->line('شكراً لاختيارك التسوق معنا.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'return_id' => $this->returnRequest->id,
            'return_number' => $this->returnRequest->return_number,
            'order_id' => $this->returnRequest->order_id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->returnRequest->status,
            'message' => $this->getStatusMessage(),
        ];
    }
    
    /**
     * Get status message based on the return request status.
     */
    protected function getStatusMessage(): string
    {
        switch ($this->returnRequest->status) {
            case ReturnRequest::STATUS_APPROVED:
                return 'تمت الموافقة على طلب المرتجعات #' . $this->returnRequest->return_number . '.';
            case ReturnRequest::STATUS_REJECTED:
                return 'تم رفض طلب المرتجعات #' . $this->returnRequest->return_number . '.';
            case ReturnRequest::STATUS_COMPLETED:
                return 'تم إكمال طلب المرتجعات #' . $this->returnRequest->return_number . ' بنجاح.';
            case ReturnRequest::STATUS_CANCELLED:
                return 'تم إلغاء طلب المرتجعات #' . $this->returnRequest->return_number . '.';
            default:
                return 'تم تحديث حالة طلب المرتجعات #' . $this->returnRequest->return_number . '.';
        }
    }
} 