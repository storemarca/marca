<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Services\Payments\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process a payment for an order
     *
     * @param Order $order
     * @param string $gatewayCode
     * @param array $paymentData
     * @return array
     * @throws Exception
     */
    public function processPayment(Order $order, string $gatewayCode, array $paymentData = []): array
    {
        try {
            // Get the payment gateway
            $gateway = PaymentGateway::where('code', $gatewayCode)
                ->where('is_active', true)
                ->first();
            
            if (!$gateway) {
                throw new Exception('بوابة الدفع غير متوفرة أو غير نشطة');
            }
            
            // Get the payment gateway handler
            $gatewayHandler = $this->getGatewayHandler($gateway);
            
            // Process the payment
            $result = $gatewayHandler->processPayment($order, $paymentData);
            
            // Record the transaction
            if ($result['success']) {
                $this->recordTransaction($order, $gateway, $result);
                
                // Update order status if payment is completed
                if (isset($result['status']) && $result['status'] === 'completed') {
                    $order->payment_status = Order::PAYMENT_PAID;
                    $order->paid_at = now();
                    $order->save();
                }
            }
            
            return $result;
        } catch (Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'gateway' => $gatewayCode,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process a refund for a transaction
     *
     * @param PaymentTransaction $transaction
     * @param float $amount
     * @param string $reason
     * @return array
     * @throws Exception
     */
    public function processRefund(PaymentTransaction $transaction, float $amount, string $reason = ''): array
    {
        try {
            // Get the payment gateway
            $gateway = $transaction->paymentGateway;
            
            if (!$gateway) {
                throw new Exception('بوابة الدفع غير متوفرة');
            }
            
            // Get the payment gateway handler
            $gatewayHandler = $this->getGatewayHandler($gateway);
            
            // Process the refund
            $result = $gatewayHandler->processRefund($transaction, $amount, $reason);
            
            // Record the refund transaction
            if ($result['success']) {
                $this->recordRefundTransaction($transaction, $result, $amount, $reason);
                
                // Update order payment status if full refund
                if ($amount >= $transaction->amount) {
                    $order = $transaction->order;
                    if ($order) {
                        $order->payment_status = Order::PAYMENT_REFUNDED;
                        $order->save();
                    }
                }
            }
            
            return $result;
        } catch (Exception $e) {
            Log::error('Refund processing failed', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Get available payment gateways
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableGateways()
    {
        return PaymentGateway::where('is_active', true)->get();
    }
    
    /**
     * Get the payment gateway handler
     *
     * @param PaymentGateway $gateway
     * @return PaymentGatewayInterface
     * @throws Exception
     */
    protected function getGatewayHandler(PaymentGateway $gateway): PaymentGatewayInterface
    {
        $gatewayClass = config("payment.payment_gateways.{$gateway->code}.class");
        
        if (!$gatewayClass || !class_exists($gatewayClass)) {
            throw new Exception("بوابة الدفع غير مدعومة: {$gateway->code}");
        }
        
        $gatewayHandler = app($gatewayClass);
        
        if (!($gatewayHandler instanceof PaymentGatewayInterface)) {
            throw new Exception("معالج بوابة الدفع غير صالح: {$gateway->code}");
        }
        
        return $gatewayHandler->setGateway($gateway);
    }
    
    /**
     * Record a payment transaction
     *
     * @param Order $order
     * @param PaymentGateway $gateway
     * @param array $result
     * @return PaymentTransaction
     */
    protected function recordTransaction(Order $order, PaymentGateway $gateway, array $result): PaymentTransaction
    {
        $transaction = new PaymentTransaction();
        $transaction->order_id = $order->id;
        $transaction->payment_gateway_id = $gateway->id;
        $transaction->transaction_id = $result['transaction_id'];
        $transaction->amount = $order->total_amount;
        $transaction->currency = $order->currency;
        $transaction->status = $result['status'] ?? 'pending';
        $transaction->response_data = $result['data'] ?? [];
        $transaction->save();
        
        return $transaction;
    }
    
    /**
     * Record a refund transaction
     *
     * @param PaymentTransaction $transaction
     * @param array $result
     * @param float $amount
     * @param string $reason
     * @return PaymentTransaction
     */
    protected function recordRefundTransaction(PaymentTransaction $transaction, array $result, float $amount, string $reason): PaymentTransaction
    {
        $refundTransaction = new PaymentTransaction();
        $refundTransaction->order_id = $transaction->order_id;
        $refundTransaction->payment_gateway_id = $transaction->payment_gateway_id;
        $refundTransaction->parent_transaction_id = $transaction->id;
        $refundTransaction->transaction_id = $result['refund_id'];
        $refundTransaction->amount = -$amount; // Negative amount for refunds
        $refundTransaction->currency = $transaction->currency;
        $refundTransaction->status = $result['status'] ?? 'pending';
        $refundTransaction->response_data = $result['data'] ?? [];
        $refundTransaction->notes = $reason;
        $refundTransaction->type = 'refund';
        $refundTransaction->save();
        
        return $refundTransaction;
    }
}