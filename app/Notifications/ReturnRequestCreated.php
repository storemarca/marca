<?php

namespace App\Notifications;

use App\Models\ReturnRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnRequestCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $returnRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(ReturnRequest $returnRequest)
    {
        $this->returnRequest = $returnRequest;
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
        return (new MailMessage)
            ->subject('طلب مرتجعات جديد #' . $this->returnRequest->return_number)
            ->greeting('مرحباً')
            ->line('تم إنشاء طلب مرتجعات جديد يحتاج إلى مراجعتك.')
            ->line('تفاصيل الطلب:')
            ->line('- رقم الطلب: #' . $this->returnRequest->return_number)
            ->line('- رقم الطلب الأصلي: #' . $this->returnRequest->order->order_number)
            ->line('- العميل: ' . $this->returnRequest->customer->name)
            ->line('- المبلغ الإجمالي: ' . number_format($this->returnRequest->total_amount, 2))
            ->line('- سبب الإرجاع: ' . $this->returnRequest->reason)
            ->action('عرض طلب المرتجعات', route('admin.returns.show', $this->returnRequest->id))
            ->line('يرجى مراجعة الطلب واتخاذ الإجراء المناسب.');
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
            'order_number' => $this->returnRequest->order->order_number,
            'customer_id' => $this->returnRequest->customer_id,
            'customer_name' => $this->returnRequest->customer->name,
            'total_amount' => $this->returnRequest->total_amount,
            'message' => 'تم إنشاء طلب مرتجعات جديد #' . $this->returnRequest->return_number,
        ];
    }
} 