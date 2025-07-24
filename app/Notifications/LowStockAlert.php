<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $stock;
    protected $threshold;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product, ProductStock $stock, int $threshold)
    {
        $this->product = $product;
        $this->stock = $stock;
        $this->threshold = $threshold;
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
        $urgency = $this->getUrgencyLevel();
        $subject = $this->getSubjectByUrgency($urgency);
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('مرحباً')
            ->line('نود إعلامك بأن مخزون المنتج التالي منخفض ويحتاج إلى تجديد:')
            ->line('- المنتج: ' . $this->product->name)
            ->line('- SKU: ' . $this->product->sku)
            ->line('- المخزون الحالي: ' . $this->stock->quantity)
            ->line('- المستودع: ' . $this->stock->warehouse->name);
            
        if ($urgency === 'critical') {
            $message->line('⚠️ هذا المنتج في حالة نفاد وشيك ويحتاج إلى اهتمام فوري!');
        } elseif ($urgency === 'warning') {
            $message->line('⚠️ يرجى التخطيط لإعادة طلب هذا المنتج قريباً.');
        }
        
        $message->action('عرض المنتج', route('admin.products.show', $this->product->id))
                ->line('يمكنك إنشاء طلب شراء جديد من خلال نظام إدارة المخزون.');
        
        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $urgency = $this->getUrgencyLevel();
        
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'warehouse_id' => $this->stock->warehouse_id,
            'warehouse_name' => $this->stock->warehouse->name,
            'current_stock' => $this->stock->quantity,
            'threshold' => $this->threshold,
            'urgency' => $urgency,
            'message' => $this->getMessageByUrgency($urgency),
        ];
    }
    
    /**
     * Get the urgency level based on stock quantity.
     */
    protected function getUrgencyLevel(): string
    {
        if ($this->stock->quantity <= 0) {
            return 'critical';
        } elseif ($this->stock->quantity <= $this->threshold / 2) {
            return 'warning';
        } else {
            return 'notice';
        }
    }
    
    /**
     * Get subject line based on urgency.
     */
    protected function getSubjectByUrgency(string $urgency): string
    {
        switch ($urgency) {
            case 'critical':
                return '⚠️ تنبيه عاجل: نفاد مخزون المنتج "' . $this->product->name . '"';
            case 'warning':
                return '⚠️ تنبيه: انخفاض مخزون المنتج "' . $this->product->name . '"';
            default:
                return 'تنبيه: مخزون منخفض للمنتج "' . $this->product->name . '"';
        }
    }
    
    /**
     * Get message based on urgency.
     */
    protected function getMessageByUrgency(string $urgency): string
    {
        switch ($urgency) {
            case 'critical':
                return 'نفاد مخزون المنتج "' . $this->product->name . '" (الكمية: ' . $this->stock->quantity . ')';
            case 'warning':
                return 'انخفاض مخزون المنتج "' . $this->product->name . '" (الكمية: ' . $this->stock->quantity . ')';
            default:
                return 'مخزون منخفض للمنتج "' . $this->product->name . '" (الكمية: ' . $this->stock->quantity . ')';
        }
    }
} 