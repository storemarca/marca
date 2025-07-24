<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Address;
use App\Models\ShippingMethod;
use App\Models\PaymentGateway;
use App\Models\Coupon;
use App\Events\OrderCompleted;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected PaymentService $paymentService;
    protected CouponService $couponService;

    public function __construct(PaymentService $paymentService, CouponService $couponService)
    {
        $this->paymentService = $paymentService;
        $this->couponService = $couponService;
    }

    public function createOrder(Customer $customer, array $orderData): Order
    {
        DB::beginTransaction();

        try {
            $order = new Order([
                'customer_id' => $customer->id,
                'order_number' => Order::generateOrderNumber(),
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'currency' => $orderData['currency'] ?? 'USD',
                'subtotal' => 0,
                'shipping_cost' => 0,
                'tax' => 0,
                'discount' => 0,
                'total_amount' => 0,
                'notes' => $orderData['notes'] ?? null,
            ]);

            if (isset($orderData['shipping_address_id'])) {
                $shippingAddress = Address::findOrFail($orderData['shipping_address_id']);
                $order->shipping_address_id = $shippingAddress->id;
                $order->country_id = $shippingAddress->country_id;
            }

            $order->billing_address_id = $orderData['billing_address_id'] ?? $order->shipping_address_id;

            if (isset($orderData['shipping_method_id'])) {
                $shippingMethod = ShippingMethod::findOrFail($orderData['shipping_method_id']);
                $order->shipping_method_id = $shippingMethod->id;
                $order->shipping_cost = $shippingMethod->calculateCost($order);
            }

            $coupon = null;
            if (!empty($orderData['coupon_code'])) {
                $coupon = Coupon::where('code', $orderData['coupon_code'])->first();
                if ($coupon && $this->couponService->isValid($coupon, $customer)) {
                    $order->coupon_code = $coupon->code;
                } else {
                    $coupon = null;
                }
            }

            $order->save();

            $subtotal = 0;
            foreach ($orderData['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = $itemData['quantity'];

                if (!$this->checkProductStock($product, $quantity)) {
                    throw new Exception("المنتج {$product->name} غير متوفر في المخزون");
                }

                $orderItem = new OrderItem([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $product->price * $quantity,
                ]);
                $orderItem->save();

                $subtotal += $orderItem->total_price;
            }

            $order->subtotal = $subtotal;

            if ($coupon) {
                $order->discount = $this->couponService->calculateDiscount($order, $coupon);
            }

            $taxRate = optional($order->country)->tax_rate ?? 0;
            $order->tax = ($order->subtotal - $order->discount) * ($taxRate / 100);

            $order->calculateTotal();
            $order->save();

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('فشل إنشاء الطلب: ' . $e->getMessage(), [
                'customer_id' => $customer->id,
                'order_data' => $orderData,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function processOrderPayment(Order $order, string $gatewayCode, array $paymentData): array
    {
        try {
            $gateway = PaymentGateway::where('code', $gatewayCode)
                ->where('is_active', true)
                ->firstOrFail();

            $transaction = $this->paymentService->processPayment($order, $gateway, $paymentData);

            if ($transaction->status === 'completed') {
                $order->status = Order::STATUS_PROCESSING;
                $order->payment_status = Order::PAYMENT_PAID;
                $order->save();

                $this->updateProductStock($order);
            }

            return [
                'success' => $transaction->status === 'completed',
                'transaction' => $transaction,
                'order' => $order,
            ];
        } catch (Exception $e) {
            Log::error('فشل معالجة الدفع: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'gateway' => $gatewayCode,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        $oldStatus = $order->status;
        $order->status = $status;
        $order->save();

        if ($status === Order::STATUS_COMPLETED && $oldStatus !== Order::STATUS_COMPLETED) {
            event(new OrderCompleted($order));
        }

        return $order;
    }

    protected function checkProductStock(Product $product, int $quantity): bool
    {
        $stock = $product->stock()->lockForUpdate()->first();
        return $stock && $stock->quantity >= $quantity;
    }

    protected function updateProductStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            $stock = $product->stock()->lockForUpdate()->first();

            if ($stock) {
                $stock->quantity -= $item->quantity;
                $stock->save();

                $product->stockMovements()->create([
                    'type' => 'order',
                    'reference_id' => $order->id,
                    'quantity' => -$item->quantity,
                    'notes' => "طلب رقم #{$order->order_number}",
                ]);
            }
        }
    }

    public function getCustomerOrders(Customer $customer, int $perPage = 10)
    {
        return Order::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getOrderDetails(Order $order): Order
    {
        return $order->load([
            'items.product',
            'transactions',
            'shipments.items',
            'shippingAddress',
            'billingAddress',
            'shippingMethod',
        ]);
    }

    public function cancelOrder(Order $order, string $reason = ''): Order
    {
        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_PROCESSING])) {
            throw new Exception('لا يمكن إلغاء هذا الطلب.');
        }

        DB::beginTransaction();

        try {
            $order->status = Order::STATUS_CANCELLED;
            $order->notes = trim(($order->notes ?? '') . "\nسبب الإلغاء: {$reason}");
            $order->save();

            foreach ($order->items as $item) {
                $product = $item->product;
                $stock = $product->stock()->lockForUpdate()->first();

                if ($stock) {
                    $stock->quantity += $item->quantity;
                    $stock->save();

                    $product->stockMovements()->create([
                        'type' => 'order_cancel',
                        'reference_id' => $order->id,
                        'quantity' => $item->quantity,
                        'notes' => "إلغاء طلب رقم #{$order->order_number}",
                    ]);
                }
            }

            if ($order->payment_status === Order::PAYMENT_PAID) {
                $lastTransaction = $order->lastTransaction;
                if ($lastTransaction) {
                    $this->paymentService->processRefund(
                        $lastTransaction,
                        $lastTransaction->amount,
                        "تم إلغاء الطلب: {$reason}"
                    );

                    $order->payment_status = Order::PAYMENT_REFUNDED;
                    $order->save();
                }
            }

            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('فشل إلغاء الطلب: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
