<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\PurchaseOrder;
use App\Models\PurchaseItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Notifications\LowStockAlert;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;

class InventoryService
{
    /**
     * Update product stock quantity
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @param string $operation add|subtract|set
     * @param string $reason
     * @param int|null $orderId
     * @param int|null $purchaseOrderId
     * @return bool
     */
    public function updateStock(
        int $productId, 
        int $warehouseId, 
        int $quantity, 
        string $operation = 'add', 
        string $reason = '', 
        ?int $orderId = null, 
        ?int $purchaseOrderId = null
    ): bool {
        $productStock = ProductStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        if (!$productStock) {
            // Create a new stock record if one doesn't exist
            $productStock = new ProductStock([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => 0,
                'reserved_quantity' => 0,
            ]);
            $productStock->save();
        }
        
        return $productStock->updateStock($quantity, $operation, $reason, $orderId, $purchaseOrderId);
    }
    
    /**
     * Reserve stock for an order
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @param int|null $orderId
     * @return bool
     */
    public function reserveStock(int $productId, int $warehouseId, int $quantity, ?int $orderId = null): bool
    {
        $productStock = ProductStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        if (!$productStock || $productStock->available_quantity < $quantity) {
            return false;
        }
        
        return $productStock->reserveStock($quantity, $orderId);
    }
    
    /**
     * Release reserved stock
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @param int|null $orderId
     * @return bool
     */
    public function releaseReservedStock(int $productId, int $warehouseId, int $quantity, ?int $orderId = null): bool
    {
        $productStock = ProductStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        if (!$productStock || $productStock->reserved_quantity < $quantity) {
            return false;
        }
        
        return $productStock->releaseReservedStock($quantity, $orderId);
    }
    
    /**
     * Check if a product has sufficient stock in a warehouse
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $quantity
     * @return bool
     */
    public function hasAvailableStock(int $productId, int $warehouseId, int $quantity): bool
    {
        $productStock = ProductStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        return $productStock && $productStock->available_quantity >= $quantity;
    }
    
    /**
     * Get available stock for a product across all warehouses
     *
     * @param int $productId
     * @return int
     */
    public function getTotalAvailableStock(int $productId): int
    {
        return ProductStock::where('product_id', $productId)
            ->sum(DB::raw('quantity - reserved_quantity'));
    }
    
    /**
     * Process stock for an order (subtract from inventory)
     *
     * @param Order $order
     * @return bool
     */
    public function processOrderStock(Order $order): bool
    {
        DB::beginTransaction();
        
        try {
            foreach ($order->items as $item) {
                // Find the warehouse with available stock
                $productStock = ProductStock::where('product_id', $item->product_id)
                    ->where('quantity', '>=', $item->quantity)
                    ->orderBy('warehouse_id') // Prioritize warehouses by ID
                    ->first();
                
                if (!$productStock) {
                    throw new Exception("Insufficient stock for product ID: {$item->product_id}");
                }
                
                // Subtract from inventory
                $result = $productStock->updateStock(
                    $item->quantity, 
                    'subtract', 
                    'Order fulfillment', 
                    $order->id
                );
                
                if (!$result) {
                    throw new Exception("Failed to update stock for product ID: {$item->product_id}");
                }
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process order stock: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Restore stock for a cancelled order
     *
     * @param Order $order
     * @return bool
     */
    public function restoreOrderStock(Order $order): bool
    {
        DB::beginTransaction();
        
        try {
            foreach ($order->items as $item) {
                // Find the warehouse where the stock was originally taken from
                $stockMovement = StockMovement::where('order_id', $order->id)
                    ->where('product_id', $item->product_id)
                    ->where('operation', 'subtract')
                    ->first();
                
                if (!$stockMovement) {
                    // If no record found, use the default warehouse
                    $warehouseId = 1; // Default warehouse ID
                } else {
                    $warehouseId = $stockMovement->warehouse_id;
                }
                
                // Add back to inventory
                $this->updateStock(
                    $item->product_id,
                    $warehouseId,
                    $item->quantity,
                    'add',
                    'Order cancellation',
                    $order->id
                );
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore order stock: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process stock for a purchase order (add to inventory)
     *
     * @param PurchaseOrder $purchaseOrder
     * @return bool
     */
    public function processPurchaseOrderStock(PurchaseOrder $purchaseOrder): bool
    {
        DB::beginTransaction();
        
        try {
            foreach ($purchaseOrder->items as $item) {
                $this->updateStock(
                    $item->product_id,
                    $purchaseOrder->warehouse_id,
                    $item->quantity,
                    'add',
                    'Purchase order receipt',
                    null,
                    $purchaseOrder->id
                );
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process purchase order stock: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Transfer stock between warehouses
     *
     * @param int $productId
     * @param int $fromWarehouseId
     * @param int $toWarehouseId
     * @param int $quantity
     * @return bool
     */
    public function transferStock(int $productId, int $fromWarehouseId, int $toWarehouseId, int $quantity): bool
    {
        if ($fromWarehouseId === $toWarehouseId) {
            return false;
        }
        
        DB::beginTransaction();
        
        try {
            // Subtract from source warehouse
            $sourceResult = $this->updateStock(
                $productId,
                $fromWarehouseId,
                $quantity,
                'subtract',
                'Stock transfer to warehouse #' . $toWarehouseId
            );
            
            if (!$sourceResult) {
                throw new Exception("Failed to subtract stock from source warehouse");
            }
            
            // Add to destination warehouse
            $destinationResult = $this->updateStock(
                $productId,
                $toWarehouseId,
                $quantity,
                'add',
                'Stock transfer from warehouse #' . $fromWarehouseId
            );
            
            if (!$destinationResult) {
                throw new Exception("Failed to add stock to destination warehouse");
            }
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to transfer stock: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get low stock products
     *
     * @return Collection
     */
    public function getLowStockProducts(): Collection
    {
        return ProductStock::with(['product', 'warehouse'])
            ->whereRaw('quantity <= low_stock_threshold')
            ->where('low_stock_threshold', '>', 0)
            ->get();
    }
    
    /**
     * Get products that need reordering
     *
     * @return Collection
     */
    public function getProductsNeedingReorder(): Collection
    {
        return ProductStock::with(['product', 'warehouse'])
            ->whereRaw('quantity <= reorder_point')
            ->where('reorder_point', '>', 0)
            ->get();
    }
    
    /**
     * Update low stock threshold for a product
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $threshold
     * @return bool
     */
    public function updateLowStockThreshold(int $productId, int $warehouseId, int $threshold): bool
    {
        $productStock = ProductStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        if (!$productStock) {
            return false;
        }
        
        $productStock->low_stock_threshold = $threshold;
        return $productStock->save();
    }
    
    /**
     * Update reorder point and quantity for a product
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $reorderPoint
     * @param int $reorderQuantity
     * @return bool
     */
    public function updateReorderSettings(int $productId, int $warehouseId, int $reorderPoint, int $reorderQuantity): bool
    {
        $productStock = ProductStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        if (!$productStock) {
            return false;
        }
        
        $productStock->reorder_point = $reorderPoint;
        $productStock->reorder_quantity = $reorderQuantity;
        return $productStock->save();
    }
    
    /**
     * Perform stock adjustment (inventory count)
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $actualQuantity
     * @param string $reason
     * @return bool
     */
    public function adjustStock(int $productId, int $warehouseId, int $actualQuantity, string $reason): bool
    {
        $productStock = ProductStock::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        
        if (!$productStock) {
            return false;
        }
        
        $result = $productStock->updateStock($actualQuantity, 'set', $reason);
        
        if ($result) {
            $productStock->last_count_date = now();
            $productStock->save();
        }
        
        return $result;
    }
    
    /**
     * Get stock movement history for a product
     *
     * @param int $productId
     * @param int|null $warehouseId
     * @param string|null $operation
     * @param int $limit
     * @return Collection
     */
    public function getStockMovementHistory(int $productId, ?int $warehouseId = null, ?string $operation = null, int $limit = 50): Collection
    {
        $query = StockMovement::where('product_id', $productId);
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        if ($operation) {
            $query->where('operation', $operation);
        }
        
        return $query->with(['warehouse', 'order', 'purchaseOrder', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}