<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnItem;
use App\Models\ReturnRequest;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Notifications\ReturnRequestCreated;
use App\Notifications\ReturnRequestStatusChanged;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnService
{
    /**
     * Create a new return request
     *
     * @param array $data
     * @return ReturnRequest
     */
    public function createReturnRequest(array $data): ReturnRequest
    {
        DB::beginTransaction();
        
        try {
            $order = Order::findOrFail($data['order_id']);
            
            // Check if the order is returnable
            if (!$order->isReturnable()) {
                throw new Exception('This order is not eligible for return.');
            }
            
            // Create the return request
            $returnRequest = new ReturnRequest([
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'return_number' => ReturnRequest::generateReturnNumber(),
                'status' => ReturnRequest::STATUS_PENDING,
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'return_method' => $data['return_method'] ?? ReturnRequest::METHOD_REFUND,
                'total_amount' => 0, // Will be calculated after items are added
            ]);
            
            $returnRequest->save();
            
            // Add items to the return request
            $totalAmount = 0;
            
            foreach ($data['items'] as $itemData) {
                $orderItem = OrderItem::findOrFail($itemData['order_item_id']);
                
                // Validate that the item belongs to the order
                if ($orderItem->order_id !== $order->id) {
                    throw new Exception('Invalid order item.');
                }
                
                // Validate that the return quantity is not more than the ordered quantity
                if ($itemData['quantity'] > $orderItem->quantity) {
                    throw new Exception('Return quantity cannot exceed ordered quantity.');
                }
                
                // Create return item
                $returnItem = new ReturnItem([
                    'return_id' => $returnRequest->id,
                    'order_item_id' => $orderItem->id,
                    'product_id' => $orderItem->product_id,
                    'quantity' => $itemData['quantity'],
                    'price' => $orderItem->price,
                    'subtotal' => $orderItem->price * $itemData['quantity'],
                    'condition' => $itemData['condition'],
                    'reason' => $itemData['reason'],
                    'status' => ReturnItem::STATUS_PENDING,
                ]);
                
                $returnItem->save();
                
                $totalAmount += $returnItem->subtotal;
            }
            
            // Update the total amount
            $returnRequest->total_amount = $totalAmount;
            $returnRequest->save();
            
            // Notify admin about the new return request
            $order->notify(new ReturnRequestCreated($returnRequest));
            
            DB::commit();
            
            return $returnRequest;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create return request: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update the status of a return request
     *
     * @param ReturnRequest $returnRequest
     * @param string $status
     * @param string|null $adminNotes
     * @return ReturnRequest
     */
    public function updateReturnStatus(ReturnRequest $returnRequest, string $status, ?string $adminNotes = null): ReturnRequest
    {
        DB::beginTransaction();
        
        try {
            $oldStatus = $returnRequest->status;
            $returnRequest->status = $status;
            
            if ($adminNotes) {
                $returnRequest->admin_notes = $adminNotes;
            }
            
            // Process based on the new status
            switch ($status) {
                case ReturnRequest::STATUS_APPROVED:
                    // Mark all items as approved
                    foreach ($returnRequest->items as $item) {
                        $item->status = ReturnItem::STATUS_APPROVED;
                        $item->save();
                    }
                    break;
                    
                case ReturnRequest::STATUS_REJECTED:
                    // Mark all items as rejected
                    foreach ($returnRequest->items as $item) {
                        $item->status = ReturnItem::STATUS_REJECTED;
                        $item->save();
                    }
                    break;
                    
                case ReturnRequest::STATUS_COMPLETED:
                    // Process the return completion
                    $this->processReturnCompletion($returnRequest);
                    $returnRequest->processed_at = now();
                    break;
                    
                case ReturnRequest::STATUS_CANCELLED:
                    // No special processing needed for cancellation
                    break;
            }
            
            $returnRequest->save();
            
            // Notify customer about the status change
            $returnRequest->customer->notify(new ReturnRequestStatusChanged($returnRequest, $oldStatus));
            
            DB::commit();
            
            return $returnRequest;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update return status: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Process the completion of a return
     *
     * @param ReturnRequest $returnRequest
     * @return void
     */
    protected function processReturnCompletion(ReturnRequest $returnRequest): void
    {
        // Process based on the return method
        switch ($returnRequest->return_method) {
            case ReturnRequest::METHOD_REFUND:
                // Process refund using the payment service
                // $paymentService = app(PaymentService::class);
                // $paymentService->processRefund($returnRequest->order->lastTransaction, $returnRequest->total_amount, 'Order return');
                break;
                
            case ReturnRequest::METHOD_EXCHANGE:
                // Process exchange - this would involve creating a new order or similar
                break;
                
            case ReturnRequest::METHOD_STORE_CREDIT:
                // Add store credit to the customer's account
                break;
        }
        
        // Update inventory for returned items
        foreach ($returnRequest->items as $item) {
            if ($item->condition === ReturnItem::CONDITION_NEW || $item->condition === ReturnItem::CONDITION_LIKE_NEW) {
                // Only add back to inventory if the item is in good condition
                $this->returnItemToInventory($item);
            }
        }
    }
    
    /**
     * Return an item to inventory
     *
     * @param ReturnItem $item
     * @return void
     */
    protected function returnItemToInventory(ReturnItem $item): void
    {
        // Find the product stock in the default warehouse
        $productStock = ProductStock::where('product_id', $item->product_id)
            ->where('warehouse_id', 1) // Assuming 1 is the default warehouse ID
            ->first();
        
        if (!$productStock) {
            // Create a new stock record if one doesn't exist
            $productStock = new ProductStock([
                'product_id' => $item->product_id,
                'warehouse_id' => 1,
                'quantity' => 0,
            ]);
        }
        
        // Increase the stock quantity
        $productStock->quantity += $item->quantity;
        $productStock->save();
        
        // Record the stock movement
        StockMovement::create([
            'product_id' => $item->product_id,
            'warehouse_id' => $productStock->warehouse_id,
            'quantity' => $item->quantity,
            'type' => 'return',
            'reference_id' => $item->return_id,
            'reference_type' => 'return',
            'notes' => "Return from order #{$item->returnRequest->order->order_number}",
        ]);
    }
} 