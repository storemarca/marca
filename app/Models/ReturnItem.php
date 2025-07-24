<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'order_item_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'condition',
        'reason',
        'status',
    ];

    protected $casts = [
        'price' => 'float',
        'subtotal' => 'float',
    ];

    // Item conditions
    const CONDITION_NEW = 'new';
    const CONDITION_LIKE_NEW = 'like_new';
    const CONDITION_USED = 'used';
    const CONDITION_DAMAGED = 'damaged';

    // Item statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RECEIVED = 'received';

    /**
     * Get the return request that owns the item.
     */
    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_id');
    }

    /**
     * Get the order item associated with the return item.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product associated with the return item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the subtotal for this item.
     */
    public function calculateSubtotal(): float
    {
        return $this->price * $this->quantity;
    }
} 