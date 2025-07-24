<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Shipment extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'warehouse_id',
        'shipping_company_id',
        'tracking_number',
        'tracking_url',
        'status',
        'shipping_cost',
        'cod_amount',
        'is_cod',
        'shipped_at',
        'delivered_at',
        'notes',
        'expected_delivery_date',
        'last_tracking_update',
        'last_status_update',
        'tracking_history',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'cod_amount' => 'decimal:2',
        'is_cod' => 'boolean',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'expected_delivery_date' => 'datetime',
        'last_tracking_update' => 'datetime',
        'last_status_update' => 'datetime',
        'tracking_history' => 'array',
    ];
    
    /**
     * The status options for shipments.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED = 'failed';
    public const STATUS_RETURNED = 'returned';
    
    /**
     * Get all available shipment statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_PROCESSING => 'قيد المعالجة',
            self::STATUS_SHIPPED => 'تم الشحن',
            self::STATUS_IN_TRANSIT => 'في الطريق',
            self::STATUS_OUT_FOR_DELIVERY => 'خارج للتسليم',
            self::STATUS_DELIVERED => 'تم التسليم',
            self::STATUS_FAILED => 'فشل التسليم',
            self::STATUS_RETURNED => 'مرتجع',
        ];
    }
    
    /**
     * Get the status text in Arabic.
     */
    public function getStatusTextAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
    
    /**
     * Get the status color class for badges.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-gray-100 text-gray-800',
            self::STATUS_PROCESSING => 'bg-blue-100 text-blue-800',
            self::STATUS_SHIPPED => 'bg-indigo-100 text-indigo-800',
            self::STATUS_IN_TRANSIT => 'bg-yellow-100 text-yellow-800',
            self::STATUS_OUT_FOR_DELIVERY => 'bg-purple-100 text-purple-800',
            self::STATUS_DELIVERED => 'bg-green-100 text-green-800',
            self::STATUS_FAILED => 'bg-red-100 text-red-800',
            self::STATUS_RETURNED => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
    
    /**
     * Get the status icon class.
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'fas fa-clock',
            self::STATUS_PROCESSING => 'fas fa-cog',
            self::STATUS_SHIPPED => 'fas fa-truck-loading',
            self::STATUS_IN_TRANSIT => 'fas fa-shipping-fast',
            self::STATUS_OUT_FOR_DELIVERY => 'fas fa-truck',
            self::STATUS_DELIVERED => 'fas fa-check-circle',
            self::STATUS_FAILED => 'fas fa-times-circle',
            self::STATUS_RETURNED => 'fas fa-undo',
            default => 'fas fa-question-circle',
        };
    }
    
    /**
     * Get the progress percentage based on status.
     */
    public function getProgressPercentageAttribute(): int
    {
        return match ($this->status) {
            self::STATUS_PENDING => 10,
            self::STATUS_PROCESSING => 25,
            self::STATUS_SHIPPED => 50,
            self::STATUS_IN_TRANSIT => 65,
            self::STATUS_OUT_FOR_DELIVERY => 85,
            self::STATUS_DELIVERED => 100,
            self::STATUS_FAILED => 0,
            self::STATUS_RETURNED => 0,
            default => 0,
        };
    }
    
    /**
     * Add a tracking history entry.
     */
    public function addTrackingHistory(string $status, string $description = null, array $details = []): self
    {
        $history = $this->tracking_history ?? [];
        
        $history[] = [
            'status' => $status,
            'description' => $description,
            'details' => $details,
            'timestamp' => now()->toIso8601String(),
        ];
        
        $this->tracking_history = $history;
        $this->last_tracking_update = now();
        $this->save();
        
        return $this;
    }
    
    /**
     * Update shipment status with tracking history.
     */
    public function updateStatus(string $status, string $description = null, array $details = []): self
    {
        $oldStatus = $this->status;
        
        if ($oldStatus !== $status) {
            $this->status = $status;
            $this->last_status_update = now();
            
            // Set shipped_at if status is shipped
            if ($status === self::STATUS_SHIPPED && !$this->shipped_at) {
                $this->shipped_at = now();
            }
            
            // Set delivered_at if status is delivered
            if ($status === self::STATUS_DELIVERED && !$this->delivered_at) {
                $this->delivered_at = now();
            }
            
            $this->save();
            
            // Add to tracking history
            $this->addTrackingHistory(
                $status,
                $description ?? "تغيير الحالة من {$oldStatus} إلى {$status}",
                $details
            );
            
            // Update order status if needed
            $this->updateOrderStatus($status);
        }
        
        return $this;
    }
    
    /**
     * Update the related order status based on shipment status.
     */
    protected function updateOrderStatus(string $status): void
    {
        $order = $this->order;
        
        if (!$order) {
            return;
        }
        
        // Map shipment status to order status
        $orderStatus = match ($status) {
            self::STATUS_SHIPPED, self::STATUS_IN_TRANSIT, self::STATUS_OUT_FOR_DELIVERY => 'shipped',
            self::STATUS_DELIVERED => 'delivered',
            self::STATUS_RETURNED => 'returned',
            self::STATUS_FAILED => 'processing', // Revert to processing if delivery fails
            default => null,
        };
        
        if ($orderStatus && $order->status !== $orderStatus) {
            $order->status = $orderStatus;
            
            if ($orderStatus === 'delivered') {
                $order->delivered_at = now();
            }
            
            $order->save();
        }
    }
    
    /**
     * Get the order that owns the shipment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the warehouse that the shipment originated from.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    /**
     * Get the shipping company handling the shipment.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }
    
    /**
     * Get the items for the shipment.
     */
    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }
    
    /**
     * Get the collection for the shipment.
     */
    public function collection()
    {
        return $this->hasOne(Collection::class);
    }
    
    /**
     * Check if the shipment has a collection.
     */
    public function hasCollection()
    {
        return $this->collection()->exists();
    }
    
    /**
     * Check if the shipment is delivered.
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }
    
    /**
     * Check if the shipment has a tracking URL.
     */
    public function hasTrackingUrl()
    {
        return !empty($this->tracking_url) || ($this->tracking_number && $this->shippingCompany && $this->shippingCompany->tracking_url_template);
    }
    
    /**
     * Get the tracking URL for the shipment.
     */
    public function getTrackingUrlAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        
        if ($this->tracking_number && $this->shippingCompany && $this->shippingCompany->tracking_url_template) {
            return str_replace('{tracking_number}', $this->tracking_number, $this->shippingCompany->tracking_url_template);
        }
        
        return null;
    }
    
    /**
     * Check if the shipment was created via API.
     */
    public function wasCreatedViaApi()
    {
        return $this->shippingCompany && $this->shippingCompany->has_api_integration && !empty($this->tracking_number);
    }
    
    /**
     * Check if the tracking information can be updated via API.
     */
    public function canUpdateTrackingViaApi()
    {
        return $this->shippingCompany && 
               $this->shippingCompany->has_api_integration && 
               !empty($this->tracking_number) &&
               in_array($this->status, ['processing', 'shipped', 'in_transit', 'out_for_delivery']);
    }
    
    /**
     * Refresh tracking information from the shipping company API.
     */
    public function refreshTrackingInfo()
    {
        if (!$this->canUpdateTrackingViaApi()) {
            return [
                'success' => false,
                'message' => 'No se puede actualizar el seguimiento vía API',
            ];
        }
        
        return $this->shippingCompany->trackShipmentViaApi($this->tracking_number);
    }
    
    /**
     * Get all pending items for this shipment.
     */
    public function getPendingItemsCount()
    {
        $totalOrderedQuantity = $this->order->items()->sum('quantity');
        $totalShippedQuantity = $this->items()->sum('quantity');
        
        return max(0, $totalOrderedQuantity - $totalShippedQuantity);
    }
    
    /**
     * Generate a tracking URL for manual shipments.
     */
    public function generateTrackingUrl()
    {
        if (!$this->shippingCompany || !$this->tracking_number) {
            return null;
        }
        
        if ($this->shippingCompany->tracking_url_template) {
            return str_replace('{tracking_number}', $this->tracking_number, $this->shippingCompany->tracking_url_template);
        }
        
        // URLs predeterminadas para empresas conocidas
        $knownCompanies = [
            'aramex' => 'https://www.aramex.com/track/shipments?ShipmentNumber={tracking_number}',
            'zajil' => 'https://zajil-express.com/ar/track?shipment={tracking_number}',
            'smsa' => 'https://www.smsaexpress.com/trackingdetails?tracknumbers={tracking_number}',
            'dhl' => 'https://www.dhl.com/en/express/tracking.html?AWB={tracking_number}',
            'fedex' => 'https://www.fedex.com/fedextrack/?trknbr={tracking_number}',
            'ups' => 'https://www.ups.com/track?tracknum={tracking_number}',
            'egypt_post' => 'https://www.egyptpost.org/entt/track?trackNumber={tracking_number}',
        ];
        
        $code = strtolower($this->shippingCompany->code);
        if (isset($knownCompanies[$code])) {
            return str_replace('{tracking_number}', $this->tracking_number, $knownCompanies[$code]);
        }
        
        return null;
    }
}
