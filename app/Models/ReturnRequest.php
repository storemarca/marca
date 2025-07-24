<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    use HasFactory;
    
    protected $table = 'returns';

    protected $fillable = [
        'order_id',
        'customer_id',
        'return_number',
        'status',
        'reason',
        'notes',
        'admin_notes',
        'total_amount',
        'return_method',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'total_amount' => 'float',
    ];

    // Return statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Return methods
    const METHOD_REFUND = 'refund';
    const METHOD_EXCHANGE = 'exchange';
    const METHOD_STORE_CREDIT = 'store_credit';

    /**
     * Get the order associated with the return.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer associated with the return.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the items for this return.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    /**
     * Generate a unique return number.
     */
    public static function generateReturnNumber(): string
    {
        $prefix = 'RET';
        $date = now()->format('ymd');
        $lastReturn = self::latest()->first();
        
        if ($lastReturn) {
            $lastNumber = (int) substr($lastReturn->return_number, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . $date . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate the total amount for this return.
     */
    public function calculateTotalAmount(): float
    {
        return $this->items->sum('subtotal');
    }

    /**
     * Scope a query to only include returns with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include returns for a specific customer.
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
} 