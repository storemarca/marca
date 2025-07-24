<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, PaymentTransaction $transaction)
    {
        $this->order = $order;
        $this->transaction = $transaction;
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
            ->subject('تأكيد استلام الدفع للطلب #' . $this->order->order_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نود إعلامك بأننا استلمنا دفعتك للطلب #' . $this->order->order_number . ' بنجاح.')
            ->line('تفاصيل الدفع:')
            ->line('- المبلغ: ' . number_format($this->transaction->amount, 2) . ' ' . $this->transaction->currency)
            ->line('- طريقة الدفع: ' . $this->transaction->paymentGateway->name)
            ->line('- رقم المعاملة: ' . $this->transaction->transaction_id)
            ->line('- تاريخ الدفع: ' . $this->transaction->created_at->format('Y-m-d H:i:s'))
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
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount,
            'currency' => $this->transaction->currency,
            'payment_method' => $this->transaction->paymentGateway->name,
            'message' => 'تم استلام دفعة بقيمة ' . number_format($this->transaction->amount, 2) . ' ' . $this->transaction->currency . ' للطلب #' . $this->order->order_number,
        ];
    }
} 