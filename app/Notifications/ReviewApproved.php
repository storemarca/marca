<?php

namespace App\Notifications;

use App\Models\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;

    /**
     * Create a new notification instance.
     */
    public function __construct(ProductReview $review)
    {
        $this->review = $review;
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
            ->subject('تمت الموافقة على تقييمك')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نود إعلامك بأنه تمت الموافقة على تقييمك للمنتج "' . $this->review->product->name . '".')
            ->line('شكراً لمشاركة رأيك مع مجتمعنا!')
            ->action('عرض التقييم', route('user.products.show', $this->review->product->slug))
            ->line('نقدر مساهمتك في تحسين تجربة التسوق لدينا.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'product_id' => $this->review->product_id,
            'product_name' => $this->review->product->name,
            'message' => 'تمت الموافقة على تقييمك للمنتج "' . $this->review->product->name . '".',
        ];
    }
} 