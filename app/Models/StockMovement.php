<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'old_quantity',
        'new_quantity',
        'quantity_change',
        'operation',
        'reason',
        'order_id',
        'purchase_order_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_quantity' => 'integer',
        'new_quantity' => 'integer',
        'quantity_change' => 'integer',
    ];

    /**
     * Get the product that owns the stock movement.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse that owns the stock movement.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the order associated with the stock movement.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the purchase order associated with the stock movement.
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Get the user who made the stock movement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include movements of a specific product.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $productId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to only include movements in a specific warehouse.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $warehouseId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    /**
     * Scope a query to only include movements of a specific operation type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $operation
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfOperation($query, $operation)
    {
        return $query->where('operation', $operation);
    }

    /**
     * Scope a query to only include movements related to an order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $orderId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope a query to only include movements related to a purchase order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $purchaseOrderId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPurchaseOrder($query, $purchaseOrderId)
    {
        return $query->where('purchase_order_id', $purchaseOrderId);
    }
} 