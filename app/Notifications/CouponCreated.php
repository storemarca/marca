<?php

namespace App\Notifications;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouponCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $coupon;

    /**
     * Create a new notification instance.
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
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
            ->subject('كوبون خصم جديد: ' . $this->coupon->code)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نود إعلامك بكوبون خصم جديد متاح لك:')
            ->line('كود الخصم: ' . $this->coupon->code);
            
        if ($this->coupon->type === 'fixed') {
            $message->line('قيمة الخصم: ' . $this->coupon->value . ' ر.س');
        } else {
            $message->line('قيمة الخصم: ' . $this->coupon->value . '%');
            
            if ($this->coupon->max_discount_amount) {
                $message->line('الحد الأقصى للخصم: ' . $this->coupon->max_discount_amount . ' ر.س');
            }
        }
        
        if ($this->coupon->min_order_amount > 0) {
            $message->line('الحد الأدنى للطلب: ' . $this->coupon->min_order_amount . ' ر.س');
        }
        
        if ($this->coupon->expires_at) {
            $message->line('صالح حتى: ' . $this->coupon->expires_at->format('Y-m-d'));
        }
        
        if ($this->coupon->description) {
            $message->line('وصف الكوبون: ' . $this->coupon->description);
        }
        
        return $message
            ->action('تسوق الآن', url('/products'))
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
            'coupon_id' => $this->coupon->id,
            'coupon_code' => $this->coupon->code,
            'coupon_type' => $this->coupon->type,
            'coupon_value' => $this->coupon->value,
            'expires_at' => $this->coupon->expires_at ? $this->coupon->expires_at->format('Y-m-d') : null,
            'message' => 'كوبون خصم جديد متاح لك: ' . $this->coupon->code,
        ];
    }
} 