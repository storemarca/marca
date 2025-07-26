<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    // 🟢 حالة الطلب
   public const STATUS_NEW = 'new';
public const STATUS_OPENED = 'opened';
public const STATUS_INCOMPLETE = 'incomplete';
public const STATUS_FAILED = 'failed';
public const STATUS_PENDING = 'pending';
public const STATUS_PROCESSING = 'processing';
public const STATUS_SHIPPED = 'shipped';
public const STATUS_DELIVERED = 'delivered';
public const STATUS_CANCELLED = 'cancelled';
public const STATUS_COMPLETED = 'completed';
public const STATUS_RETURNED = 'returned';
public const STATUS_RETURN_REQUESTED = 'return_requested';
public const STATUS_RETURN_APPROVED = 'return_approved';
public const STATUS_RETURN_REJECTED = 'return_rejected';
public const STATUS_RETURN_SHIPPED = 'return_shipped';
public const STATUS_RETURN_DELIVERED = 'return_delivered';
public const STATUS_RETURN_COMPLETED = 'return_completed';
    // 🟢 حالة الدفع
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
       'order_number',
    'user_id',
    'customer_id',
    'country_id',
    'status',
    'payment_status',
    'payment_method',
    'shipping_method_id',
    'shipping_company_id',
    'shipping_address_id',
    'billing_address_id',
    'shipping_cost',
    'subtotal',
    'tax',
    'total_amount',
    'notes',
    'payment_notes',
    'admin_notes',
    'coupon_code',
    'discount_amount',
    'tracking_number',
    'shipped_at',
    'delivered_at',
    'paid_at',
    'opened_at',
    'completed_at',
    'token',
    'shipping_amount',
    'tax_amount',
    'currency',
    'shipping_name',
    'shipping_phone',
    'shipping_email',
    'shipping_address_line1',
    'shipping_address_line2',
    'shipping_city',
    'shipping_state',
    'shipping_postal_code',
    'shipping_country',
    'shipping_coordinates',
    'customer_email',
    'cancellation_reason',
    'cancelled_at',
    'is_guest_order',
    ];

    protected $casts = [
    'subtotal' => 'decimal:2',
    'tax_amount' => 'decimal:2',
    'shipping_amount' => 'decimal:2',          
    'discount_amount' => 'decimal:2',
    'total_amount' => 'decimal:2',
    'paid_at' => 'datetime',
    'opened_at' => 'datetime',
    'completed_at' => 'datetime',
    'shipped_at' => 'datetime',
    'delivered_at' => 'datetime',
    'cancelled_at' => 'datetime',
    'is_guest_order' => 'boolean',
    ];

    // === Accessors ===
// === دوال إدارة حالة الطلب ===

/**
 * فتح الطلب بواسطة مساعد
 */
public function markAsOpened($userId)
{
    $this->update([
        'status' => self::STATUS_OPENED,
        'user_id' => $userId,
        'opened_at' => now()
    ]);
}

/**
 * إكمال الطلب
 */
public function markAsCompleted()
{
    $this->update([
        'status' => self::STATUS_COMPLETED,
        'completed_at' => now()
    ]);
}

/**
 * تحديد الطلب كغير مكتمل
 */
public function markAsIncomplete()
{
    $this->update(['status' => self::STATUS_INCOMPLETE]);
}

// === Scopes للبحث ===

public function scopeByStatus($query, $status)
{
    return $query->where('status', $status);
}

public function scopeByAssistant($query, $userId)
{
    return $query->where('user_id', $userId);
}

public function scopeNew($query)
{
    return $query->where('status', self::STATUS_NEW);
}

public function scopeIncomplete($query)
{
    return $query->where('status', self::STATUS_INCOMPLETE);
}

public function scopeOpened($query)
{
    return $query->where('status', self::STATUS_OPENED);
}

public function scopeCompletedOrders($query)
{
    return $query->where('status', self::STATUS_COMPLETED);
}

public function scopeGuestOrders($query)
{
    return $query->where('is_guest_order', true);
}

    /**
     * Get the shipping cost.
     */
    public function getShippingCostAttribute()
    {
        return $this->shipping_amount;
    }

    /**
     * Get the discount amount.
     */
    public function getDiscountAttribute()
    {
        return $this->discount_amount;
    }

    /**
     * Get the tax amount.
     */
    public function getTaxAttribute()
    {
        return $this->tax_amount;
    }



    /**
     * Get the formatted shipping address.
     */
    public function getFormattedShippingAddressAttribute()
    {
        $address = $this->shipping_address_line1;
        
        if (!empty($this->shipping_address_line2)) {
            $address .= ', ' . $this->shipping_address_line2;
        }
        
        $address .= ', ' . $this->shipping_city;
        $address .= ', ' . $this->shipping_state;
        $address .= ', ' . $this->shipping_postal_code;
        $address .= ', ' . $this->shipping_country;
        
        return $address;
    }



    /**
     * Get the currency symbol from the country.
     */
    public function getCurrencySymbolAttribute()
    {
        return optional($this->country)->currency_symbol;
    }

    /**
     * Get the status text in Arabic based on order status.
     */
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
        'new' => 'جديد',
        'opened' => 'تم فتحه',
        'incomplete' => 'غير مكتمل',
        'failed' => 'فشل',
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التسليم',
        'cancelled' => 'ملغي',
        'completed' => 'مكتمل',
        default => 'غير معروف',
        };
    }

    /**
     * Get the status color class based on order status.
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
           'new' => 'bg-blue-100 text-blue-800',
        'opened' => 'bg-orange-100 text-orange-800',
        'incomplete' => 'bg-gray-100 text-gray-800',
        'failed' => 'bg-red-100 text-red-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'processing' => 'bg-blue-100 text-blue-800',
        'shipped' => 'bg-purple-100 text-purple-800',
        'delivered' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'completed' => 'bg-green-100 text-green-800',
        default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the payment method text in Arabic.
     */
    public function getPaymentMethodTextAttribute()
    {
        return match ($this->payment_method) {
            'cod' => 'الدفع عند الاستلام',
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'credit_card' => 'بطاقة ائتمان',
            'bank_transfer' => 'تحويل بنكي',
            default => $this->payment_method,
        };
    }

    /**
     * Get the shipping email.
     */
    public function getShippingEmailAttribute()
    {
        return $this->attributes['shipping_email'] ?? $this->customer_email;
    }
    
    /**
     * Get the shipping address as a formatted string.
     */
    public function getShippingAddressAttribute()
    {
        $address = $this->shipping_address_line1;
        
        if (!empty($this->shipping_address_line2)) {
            $address .= ', ' . $this->shipping_address_line2;
        }
        
        return $address;
    }
    
    /**
     * Get the shipping zip/postal code.
     */
    public function getShippingZipAttribute()
    {
        return $this->shipping_postal_code;
    }
    
    /**
     * Get the shipping method name.
     */
    public function getShippingMethodAttribute()
    {
        return $this->shippingMethod()->first() ? $this->shippingMethod()->first()->name : null;
    }
    
    /**
     * Get the grand total.
     */
    public function getGrandTotalAttribute()
    {
        return $this->total_amount;
    }

    // === العلاقات ===

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user that owns the order through the customer.
     */
    public function user()
    {
        return $this->customer ? $this->customer->user() : null;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the shipments for the order.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
    
    /**
     * Get the first shipment for the order.
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class)->latest();
    }

    /**
     * Get the latest shipment status.
     */
    public function getLatestShipmentStatusAttribute()
    {
        $shipment = $this->shipments()->latest()->first();
        return $shipment ? $shipment->status : null;
    }

    /**
     * Get the formatted shipment status in Arabic.
     */
    public function getShipmentStatusTextAttribute()
    {
        $status = $this->latest_shipment_status;
        
        return match ($status) {
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'in_transit' => 'في الطريق',
            'out_for_delivery' => 'خارج للتسليم',
            'delivered' => 'تم التسليم',
            'failed' => 'فشل التسليم',
            'returned' => 'مرتجع',
            null => 'غير مشحون',
            default => $status,
        };
    }

    /**
     * Get the color class for shipment status badge.
     */
    public function getShipmentStatusColorAttribute()
    {
        $status = $this->latest_shipment_status;
        
        return match ($status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'in_transit' => 'bg-yellow-100 text-yellow-800',
            'out_for_delivery' => 'bg-purple-100 text-purple-800',
            'delivered' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'returned' => 'bg-orange-100 text-orange-800',
            null => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the shipping progress percentage.
     */
    public function getShippingProgressAttribute()
    {
        $status = $this->latest_shipment_status;
        
        return match ($status) {
            'pending' => 10,
            'processing' => 25,
            'shipped' => 50,
            'in_transit' => 65,
            'out_for_delivery' => 85,
            'delivered' => 100,
            'failed' => 0,
            'returned' => 0,
            null => 0,
            default => 0,
        };
    }

    /**
     * Get all tracking numbers for the order's shipments.
     */
    public function getTrackingNumbersAttribute()
    {
        return $this->shipments()->whereNotNull('tracking_number')->pluck('tracking_number')->toArray();
    }

    /**
     * Check if the order has any shipments.
     */
    public function hasShipments()
    {
        return $this->shipments()->exists();
    }

    /**
     * Check if all items in the order have been shipped.
     */
    public function isFullyShipped(): bool
    {
        return $this->items->every(fn($item) =>
            $item->shipmentItems()->sum('quantity') >= $item->quantity
        );
    }

    public function isPartiallyShipped(): bool
    {
        $totalShipped = $this->items->sum(fn($item) => $item->shipmentItems()->sum('quantity'));
        return $totalShipped > 0 && !$this->isFullyShipped();
    }

    public function isReturnable(): bool
    {
        if ($this->status !== self::STATUS_DELIVERED || !$this->delivered_at) {
            return false;
        }

        return now()->diffInDays(Carbon::parse($this->delivered_at)) <= 14;
    }

    public function wasReferredByAffiliate(): bool
    {
        return $this->affiliateLinkStat()->exists();
    }

    public function calculateTotal(): float
    {
        $this->total_amount = ($this->subtotal ?? 0)
                            + ($this->shipping_cost ?? 0)
                            + ($this->tax ?? 0)
                            - ($this->discount_amount ?? 0);

        return $this->total_amount;
    }

    public static function generateOrderNumber(): string
    {
        $prefix = now()->format('Ymd');
        $lastOrder = self::where('order_number', 'like', "$prefix%")
            ->orderByDesc('order_number')
            ->first();

        $nextNumber = $lastOrder
            ? (int)substr($lastOrder->order_number, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }
    
    /**
     * Get the shipping company for the order.
     */
    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class);
    }
    
    /**
     * Get the shipping company manually to avoid relationship issues.
     */
    public function getShippingCompanyAttribute()
    {
        if ($this->shipping_company_id) {
            return ShippingCompany::find($this->shipping_company_id);
        }
        return null;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getLastTransactionAttribute()
    {
        return $this->transactions()->latest()->first();
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function commissionTransactions()
    {
        return $this->hasMany(CommissionTransaction::class);
    }

    public function affiliateLinkStat()
    {
        return $this->hasOne(AffiliateLinkStat::class);
    }

    // === عمليات لوجيك ===
}
