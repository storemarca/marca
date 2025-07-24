<?php

namespace App\Notifications;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouponExpiring extends Notification implements ShouldQueue
{
    use Queueable;

    protected $coupon;
    protected $daysLeft;

    /**
     * Create a new notification instance.
     */
    public function __construct(Coupon $coupon, int $daysLeft)
    {
        $this->coupon = $coupon;
        $this->daysLeft = $daysLeft;
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
            ->subject('تذكير: كوبون الخصم الخاص بك سينتهي قريباً')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نود تذكيرك بأن كوبون الخصم الخاص بك سينتهي قريباً:')
            ->line('كود الخصم: ' . $this->coupon->code)
            ->line('متبقي: ' . $this->daysLeft . ' ' . $this->getDaysWord($this->daysLeft));
            
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
        
        $message->line('تاريخ الانتهاء: ' . $this->coupon->expires_at->format('Y-m-d'));
        
        return $message
            ->action('تسوق الآن', url('/products'))
            ->line('لا تفوت هذه الفرصة للاستفادة من الخصم!');
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
            'days_left' => $this->daysLeft,
            'expires_at' => $this->coupon->expires_at->format('Y-m-d'),
            'message' => 'تذكير: كوبون الخصم ' . $this->coupon->code . ' سينتهي خلال ' . $this->daysLeft . ' ' . $this->getDaysWord($this->daysLeft),
        ];
    }
    
    /**
     * Get the appropriate Arabic word for days based on the count.
     */
    protected function getDaysWord(int $days): string
    {
        if ($days === 0) {
            return 'اليوم';
        } elseif ($days === 1) {
            return 'يوم';
        } elseif ($days === 2) {
            return 'يومين';
        } elseif ($days >= 3 && $days <= 10) {
            return 'أيام';
        } else {
            return 'يوماً';
        }
    }
} 