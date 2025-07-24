<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockAlert;

class ProductStock extends Model
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
        'quantity',
        'reserved_quantity',
        'low_stock_threshold',
        'reorder_point',
        'reorder_quantity',
        'last_restock_date',
        'last_restock_quantity',
        'last_count_date',
        'location',
        'bin',
        'shelf',
        'selling_price',
        'cost_price',
        'sale_price',
        'sale_start_date',
        'sale_end_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'reorder_point' => 'integer',
        'reorder_quantity' => 'integer',
        'last_restock_date' => 'datetime',
        'last_count_date' => 'datetime',
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
    ];

    /**
     * Get the product that owns the stock.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse that owns the stock.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the available quantity.
     */
    public function getAvailableQuantityAttribute()
    {
        return $this->quantity - $this->reserved_quantity;
    }

    /**
     * Check if the stock is low.
     */
    public function isLowStock()
    {
        return $this->low_stock_threshold > 0 && $this->available_quantity <= $this->low_stock_threshold;
    }

    /**
     * Check if the stock needs reordering.
     */
    public function needsReordering()
    {
        return $this->reorder_point > 0 && $this->available_quantity <= $this->reorder_point;
    }

    /**
     * Update the stock quantity.
     *
     * @param int $quantity
     * @param string $operation
     * @param string $reason
     * @param int|null $orderId
     * @param int|null $purchaseOrderId
     * @return bool
     */
    public function updateStock(int $quantity, string $operation = 'add', string $reason = '', ?int $orderId = null, ?int $purchaseOrderId = null)
    {
        $oldQuantity = $this->quantity;
        
        if ($operation === 'add') {
            $this->quantity += $quantity;
            $this->last_restock_date = now();
            $this->last_restock_quantity = $quantity;
        } elseif ($operation === 'subtract') {
            if ($this->available_quantity < $quantity) {
                return false;
            }
            $this->quantity -= $quantity;
        } elseif ($operation === 'set') {
            $this->quantity = $quantity;
        }
        
        $this->save();
        
        // Log the stock movement
        $this->logStockMovement($oldQuantity, $this->quantity, $operation, $reason, $orderId, $purchaseOrderId);
        
        // Check for low stock and send notifications if needed
        $this->checkAndNotifyLowStock();
        
        return true;
    }

    /**
     * Reserve stock for an order.
     *
     * @param int $quantity
     * @param int|null $orderId
     * @return bool
     */
    public function reserveStock(int $quantity, ?int $orderId = null)
    {
        if ($this->available_quantity < $quantity) {
            return false;
        }
        
        $this->reserved_quantity += $quantity;
        $this->save();
        
        // Log the reservation
        $this->logStockMovement(
            $this->quantity,
            $this->quantity,
            'reserve',
            'Stock reserved for order',
            $orderId
        );
        
        return true;
    }

    /**
     * Release reserved stock.
     *
     * @param int $quantity
     * @param int|null $orderId
     * @return bool
     */
    public function releaseReservedStock(int $quantity, ?int $orderId = null)
    {
        if ($this->reserved_quantity < $quantity) {
            return false;
        }
        
        $this->reserved_quantity -= $quantity;
        $this->save();
        
        // Log the release
        $this->logStockMovement(
            $this->quantity,
            $this->quantity,
            'release',
            'Reserved stock released',
            $orderId
        );
        
        return true;
    }

    /**
     * Log stock movement.
     *
     * @param int $oldQuantity
     * @param int $newQuantity
     * @param string $operation
     * @param string $reason
     * @param int|null $orderId
     * @param int|null $purchaseOrderId
     * @return void
     */
    protected function logStockMovement(int $oldQuantity, int $newQuantity, string $operation, string $reason = '', ?int $orderId = null, ?int $purchaseOrderId = null)
    {
        StockMovement::create([
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'quantity_change' => $newQuantity - $oldQuantity,
            'operation' => $operation,
            'reason' => $reason,
            'order_id' => $orderId,
            'purchase_order_id' => $purchaseOrderId,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Check for low stock and send notifications if needed.
     *
     * @return void
     */
    protected function checkAndNotifyLowStock()
    {
        if ($this->isLowStock()) {
            try {
                // Get warehouse manager and admin users
                $adminUsers = User::role(['admin', 'warehouse_manager'])->get();
                
                // Send notification to all admin users
                Notification::send($adminUsers, new LowStockAlert($this));
                
                // Log the low stock alert
                Log::channel('inventory')->info('Low stock alert sent', [
                    'product_id' => $this->product_id,
                    'product_name' => $this->product->name,
                    'warehouse_id' => $this->warehouse_id,
                    'warehouse_name' => $this->warehouse->name,
                    'current_quantity' => $this->quantity,
                    'threshold' => $this->low_stock_threshold,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send low stock notification', [
                    'error' => $e->getMessage(),
                    'product_id' => $this->product_id,
                    'warehouse_id' => $this->warehouse_id,
                ]);
            }
        }
    }
}
