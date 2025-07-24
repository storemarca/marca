<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShippingMethod;
use App\Models\ShippingCompany;
use App\Models\OrderItem;
use App\Events\ShipmentStatusChanged;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Create a new shipment for an order
     *
     * @param Order $order
     * @param array $shipmentData
     * @return Shipment
     */
    public function createShipment(Order $order, array $shipmentData): Shipment
    {
        DB::beginTransaction();
        
        try {
            // Check if order can be shipped
            if (!in_array($order->status, ['processing', 'paid'])) {
                throw new Exception('لا يمكن شحن هذا الطلب في حالته الحالية');
            }
            
            // Create the shipment
            $shipment = new Shipment([
                'order_id' => $order->id,
                'shipping_company_id' => $shipmentData['shipping_company_id'] ?? null,
                'tracking_number' => $shipmentData['tracking_number'] ?? null,
                'status' => 'pending',
                'notes' => $shipmentData['notes'] ?? null,
                'expected_delivery_date' => $shipmentData['expected_delivery_date'] ?? null,
            ]);
            
            $shipment->save();
            
            // Add shipment items
            foreach ($shipmentData['items'] as $itemData) {
                $orderItem = OrderItem::findOrFail($itemData['order_item_id']);
                
                // Check if quantity is valid
                if ($itemData['quantity'] > $this->getAvailableQuantityForShipment($orderItem)) {
                    throw new Exception("الكمية المطلوبة للشحن غير متوفرة للمنتج {$orderItem->product->name}");
                }
                
                // Create shipment item
                $shipmentItem = new ShipmentItem([
                    'shipment_id' => $shipment->id,
                    'order_item_id' => $orderItem->id,
                    'quantity' => $itemData['quantity'],
                ]);
                
                $shipmentItem->save();
            }
            
            // Update order status if all items are shipped
            if ($this->isOrderFullyShipped($order)) {
                $order->status = 'shipped';
                $order->shipped_at = now();
                $order->save();
            }
            
            DB::commit();
            return $shipment;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Shipment creation failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'shipment_data' => $shipmentData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Update shipment status
     *
     * @param Shipment $shipment
     * @param string $status
     * @param array $additionalData
     * @return Shipment
     */
    public function updateShipmentStatus(Shipment $shipment, string $status, array $additionalData = []): Shipment
    {
        $oldStatus = $shipment->status;
        $shipment->status = $status;
        
        // Update additional fields if provided
        if (isset($additionalData['tracking_number'])) {
            $shipment->tracking_number = $additionalData['tracking_number'];
        }
        
        if (isset($additionalData['notes'])) {
            $shipment->notes = $additionalData['notes'];
        }
        
        if ($status === 'delivered') {
            $shipment->delivered_at = now();
            
            // Update order status
            $order = $shipment->order;
            $order->status = 'delivered';
            $order->delivered_at = now();
            $order->save();
        }
        
        $shipment->save();
        
        // Trigger event if status changed
        if ($oldStatus !== $status) {
            event(new ShipmentStatusChanged($shipment, $oldStatus, $status));
        }
        
        return $shipment;
    }
    
    /**
     * Calculate shipping cost for an order
     *
     * @param Order $order
     * @param ShippingMethod $shippingMethod
     * @return float
     */
    public function calculateShippingCost(Order $order, ShippingMethod $shippingMethod): float
    {
        $cost = $shippingMethod->base_cost;
        
        // Calculate based on weight if applicable
        if ($shippingMethod->weight_based) {
            $totalWeight = $this->calculateOrderWeight($order);
            $cost += $totalWeight * $shippingMethod->cost_per_kg;
        }
        
        // Apply country-specific costs if applicable
        if ($order->country_id && $shippingMethod->countries()->where('country_id', $order->country_id)->exists()) {
            $countryCost = $shippingMethod->countries()
                ->where('country_id', $order->country_id)
                ->first()
                ->pivot
                ->cost;
                
            if ($countryCost > 0) {
                $cost = $countryCost;
            }
        }
        
        // Free shipping threshold
        if ($shippingMethod->free_shipping_threshold > 0 && $order->subtotal >= $shippingMethod->free_shipping_threshold) {
            $cost = 0;
        }
        
        return $cost;
    }
    
    /**
     * Calculate the total weight of an order
     *
     * @param Order $order
     * @return float
     */
    protected function calculateOrderWeight(Order $order): float
    {
        $totalWeight = 0;
        
        foreach ($order->items as $item) {
            $weight = $item->product->weight ?? 0;
            $totalWeight += $weight * $item->quantity;
        }
        
        return $totalWeight;
    }
    
    /**
     * Get available shipping methods for an order
     *
     * @param Order $order
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableShippingMethods(Order $order)
    {
        $query = ShippingMethod::where('is_active', true);
        
        // Filter by country if applicable
        if ($order->country_id) {
            $query->whereDoesntHave('countries')
                ->orWhereHas('countries', function ($q) use ($order) {
                    $q->where('country_id', $order->country_id);
                });
        }
        
        // Get methods and calculate costs
        $methods = $query->get();
        
        foreach ($methods as $method) {
            $method->calculated_cost = $this->calculateShippingCost($order, $method);
        }
        
        return $methods;
    }
    
    /**
     * Check if an order is fully shipped
     *
     * @param Order $order
     * @return bool
     */
    public function isOrderFullyShipped(Order $order): bool
    {
        foreach ($order->items as $item) {
            $shippedQuantity = $this->getShippedQuantity($item);
            
            if ($shippedQuantity < $item->quantity) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get the total shipped quantity for an order item
     *
     * @param OrderItem $orderItem
     * @return int
     */
    protected function getShippedQuantity(OrderItem $orderItem): int
    {
        return $orderItem->shipmentItems()->sum('quantity');
    }
    
    /**
     * Get the available quantity for shipment
     *
     * @param OrderItem $orderItem
     * @return int
     */
    protected function getAvailableQuantityForShipment(OrderItem $orderItem): int
    {
        $shippedQuantity = $this->getShippedQuantity($orderItem);
        return $orderItem->quantity - $shippedQuantity;
    }
    
    /**
     * Get shipping companies
     *
     * @param bool $activeOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getShippingCompanies(bool $activeOnly = true)
    {
        $query = ShippingCompany::query();
        
        if ($activeOnly) {
            $query->where('is_active', true);
        }
        
        return $query->get();
    }
    
    /**
     * Track a shipment
     *
     * @param Shipment $shipment
     * @return array
     */
    public function trackShipment(Shipment $shipment): array
    {
        // This is a placeholder for actual tracking integration
        // In a real application, this would connect to the shipping company's API
        
        if (!$shipment->tracking_number) {
            return [
                'success' => false,
                'message' => 'لا يوجد رقم تتبع لهذه الشحنة'
            ];
        }
        
        $company = $shipment->shippingCompany;
        
        if (!$company) {
            return [
                'success' => false,
                'message' => 'شركة الشحن غير محددة'
            ];
        }
        
        // Simulate tracking information
        $trackingInfo = [
            'status' => $shipment->status,
            'tracking_number' => $shipment->tracking_number,
            'shipping_company' => $company->name,
            'events' => [
                [
                    'date' => now()->subDays(3)->format('Y-m-d H:i:s'),
                    'status' => 'تم استلام الطلب',
                    'location' => 'المستودع الرئيسي'
                ]
            ]
        ];
        
        // Add more events based on status
        if (in_array($shipment->status, ['processing', 'shipped'])) {
            $trackingInfo['events'][] = [
                'date' => now()->subDays(2)->format('Y-m-d H:i:s'),
                'status' => 'قيد المعالجة',
                'location' => 'مركز الفرز'
            ];
        }
        
        if (in_array($shipment->status, ['shipped', 'out_for_delivery', 'delivered'])) {
            $trackingInfo['events'][] = [
                'date' => now()->subDays(1)->format('Y-m-d H:i:s'),
                'status' => 'تم شحن الطلب',
                'location' => 'مركز التوزيع'
            ];
        }
        
        if (in_array($shipment->status, ['out_for_delivery', 'delivered'])) {
            $trackingInfo['events'][] = [
                'date' => now()->subHours(5)->format('Y-m-d H:i:s'),
                'status' => 'قيد التوصيل',
                'location' => 'مندوب التوصيل'
            ];
        }
        
        if ($shipment->status === 'delivered') {
            $trackingInfo['events'][] = [
                'date' => $shipment->delivered_at ? $shipment->delivered_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                'status' => 'تم التسليم',
                'location' => 'العنوان المحدد'
            ];
        }
        
        return [
            'success' => true,
            'tracking_info' => $trackingInfo
        ];
    }
}