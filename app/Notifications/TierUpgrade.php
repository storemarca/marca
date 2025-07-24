<?php

namespace App\Notifications;

use App\Models\LoyaltyTier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TierUpgrade extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $oldTier;
    protected $newTier;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, ?LoyaltyTier $oldTier, LoyaltyTier $newTier)
    {
        $this->user = $user;
        $this->oldTier = $oldTier;
        $this->newTier = $newTier;
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
            ->subject('تهانينا! تمت ترقية مستوى الولاء الخاص بك')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نود إعلامك بأنه تمت ترقية مستوى الولاء الخاص بك.');
            
        if ($this->oldTier) {
            $message->line('المستوى السابق: ' . $this->oldTier->localized_name);
        }
        
        $message->line('المستوى الجديد: ' . $this->newTier->localized_name)
                ->line('المزايا الجديدة:');
                
        if ($this->newTier->discount_percentage > 0) {
            $message->line('- خصم ' . $this->newTier->discount_percentage . '% على جميع المشتريات');
        }
        
        if ($this->newTier->free_shipping) {
            $message->line('- شحن مجاني على جميع الطلبات');
        }
        
        if ($this->newTier->points_multiplier > 1) {
            $message->line('- مضاعف النقاط: ' . $this->newTier->points_multiplier . 'x');
        }
        
        if ($this->newTier->description) {
            $message->line('- ' . $this->newTier->localized_description);
        }
        
        return $message
            ->action('عرض حساب الولاء الخاص بك', route('user.loyalty.index'))
            ->line('شكراً لولائك المستمر!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'old_tier_id' => $this->oldTier ? $this->oldTier->id : null,
            'old_tier_name' => $this->oldTier ? $this->oldTier->name : null,
            'new_tier_id' => $this->newTier->id,
            'new_tier_name' => $this->newTier->name,
            'message' => 'تهانينا! تمت ترقية مستوى الولاء الخاص بك إلى ' . $this->newTier->localized_name,
        ];
    }
} 