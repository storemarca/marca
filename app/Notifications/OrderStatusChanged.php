<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus = null)
    {
        $this->order = $order;
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
            ->subject('تحديث حالة الطلب #' . $this->order->order_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم تحديث حالة طلبك #' . $this->order->order_number . '.');
            
        switch ($this->order->status) {
            case 'processing':
                $message->line('طلبك قيد المعالجة الآن.');
                break;
            case 'shipped':
                $message->line('تم شحن طلبك وهو في الطريق إليك.')
                       ->line('رقم التتبع: ' . ($this->order->tracking_number ?? 'غير متوفر'));
                break;
            case 'delivered':
                $message->line('تم توصيل طلبك بنجاح.')
                       ->line('نأمل أن تكون راضياً عن منتجاتنا.');
                break;
            case 'cancelled':
                $message->line('تم إلغاء طلبك.')
                       ->line('إذا كان لديك أي استفسارات، يرجى التواصل مع خدمة العملاء.');
                break;
        }
        
        return $message
            ->action('عرض تفاصيل الطلب', route('user.orders.show', $this->order->id))
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
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
            'message' => $this->getStatusMessage(),
        ];
    }
    
    /**
     * Get status message based on the order status.
     */
    protected function getStatusMessage(): string
    {
        switch ($this->order->status) {
            case 'processing':
                return 'طلبك رقم #' . $this->order->order_number . ' قيد المعالجة الآن.';
            case 'shipped':
                return 'تم شحن طلبك رقم #' . $this->order->order_number . ' وهو في الطريق إليك.';
            case 'delivered':
                return 'تم توصيل طلبك رقم #' . $this->order->order_number . ' بنجاح.';
            case 'cancelled':
                return 'تم إلغاء طلبك رقم #' . $this->order->order_number . '.';
            default:
                return 'تم تحديث حالة طلبك رقم #' . $this->order->order_number . '.';
        }
    }
} 