<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'sku',
        'price',
        'quantity',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'options',
        'status',
        'warehouse_id',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'options' => 'array',
    ];
    
    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get the product for this order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the warehouse for this order item.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
    
    /**
     * Get the shipment items for this order item.
     */
    public function shipmentItems(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }
    
    /**
     * Get the return items for this order item.
     */
    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }
    
    /**
     * Calculate the total price for this item.
     */
    public function calculateTotal(): float
    {
        return $this->subtotal - $this->discount_amount + $this->tax_amount;
    }
    
    /**
     * Get the shipped quantity for this item.
     */
    public function getShippedQuantityAttribute(): int
    {
        return $this->shipmentItems()->sum('quantity');
    }
    
    /**
     * Get the returned quantity for this item.
     */
    public function getReturnedQuantityAttribute(): int
    {
        return $this->returnItems()->sum('quantity');
    }
    
    /**
     * Check if the item is fully shipped.
     */
    public function isFullyShipped(): bool
    {
        return $this->shipped_quantity >= $this->quantity;
    }
    
    /**
     * Check if the item is partially shipped.
     */
    public function isPartiallyShipped(): bool
    {
        return $this->shipped_quantity > 0 && $this->shipped_quantity < $this->quantity;
    }
    
    /**
     * Check if the item is fully returned.
     */
    public function isFullyReturned(): bool
    {
        return $this->returned_quantity >= $this->quantity;
    }
    
    /**
     * Check if the item is partially returned.
     */
    public function isPartiallyReturned(): bool
    {
        return $this->returned_quantity > 0 && $this->returned_quantity < $this->quantity;
    }
}
