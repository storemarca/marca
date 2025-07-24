<?php

namespace App\Notifications;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * الشحنة المرتبطة بالإشعار
     *
     * @var \App\Models\Shipment
     */
    protected $shipment;

    /**
     * الحالة الجديدة للشحنة
     *
     * @var string
     */
    protected $newStatus;

    /**
     * إنشاء مثيل جديد للإشعار
     *
     * @param  \App\Models\Shipment  $shipment
     * @param  string  $newStatus
     * @return void
     */
    public function __construct(Shipment $shipment, string $newStatus)
    {
        $this->shipment = $shipment;
        $this->newStatus = $newStatus;
    }

    /**
     * الحصول على قنوات تسليم الإشعار.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * الحصول على رسالة البريد الإلكتروني للإشعار.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $statusTranslations = [
            'processing' => 'قيد التجهيز',
            'shipped' => 'تم الشحن',
            'in_transit' => 'في الطريق',
            'out_for_delivery' => 'خارج للتوصيل',
            'delivered' => 'تم التسليم',
            'failed_delivery' => 'فشل التسليم',
            'returned' => 'تم الإرجاع',
        ];

        $translatedStatus = $statusTranslations[$this->newStatus] ?? $this->newStatus;
        $order = $this->shipment->order;
        $trackingUrl = $this->shipment->tracking_url;

        $mailMessage = (new MailMessage)
            ->subject('تحديث حالة الشحنة للطلب #' . $order->order_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نود إعلامك بتحديث حالة شحنة طلبك.')
            ->line('الطلب رقم: ' . $order->order_number)
            ->line('رقم التتبع: ' . $this->shipment->tracking_number)
            ->line('الحالة الجديدة: ' . $translatedStatus);

        if ($trackingUrl) {
            $mailMessage->action('تتبع الشحنة', $trackingUrl);
        }

        return $mailMessage->line('شكراً لتسوقك معنا!');
    }

    /**
     * الحصول على بيانات الإشعار للتخزين في قاعدة البيانات.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'shipment_id' => $this->shipment->id,
            'order_id' => $this->shipment->order_id,
            'order_number' => $this->shipment->order->order_number,
            'tracking_number' => $this->shipment->tracking_number,
            'status' => $this->newStatus,
            'message' => 'تم تحديث حالة شحنة الطلب #' . $this->shipment->order->order_number . ' إلى ' . $this->newStatus,
        ];
    }

    /**
     * الحصول على مصفوفة تمثيل الإشعار.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'shipment_id' => $this->shipment->id,
            'order_id' => $this->shipment->order_id,
            'tracking_number' => $this->shipment->tracking_number,
            'status' => $this->newStatus,
        ];
    }
} 