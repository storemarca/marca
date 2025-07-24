<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\PaymentTransaction;

class CashPaymentGateway extends AbstractPaymentGateway
{
    /**
     * Process a cash on delivery payment
     *
     * @param Order $order
     * @param array $paymentData
     * @return array
     */
    public function processPayment(Order $order, array $paymentData): array
    {
        $this->log('Processing Cash on Delivery payment', [
            'order_id' => $order->id,
            'amount' => $order->total
        ]);
        
        // For cash on delivery, we just mark the transaction as pending
        // Payment will be collected when the order is delivered
        return [
            'success' => true,
            'transaction_id' => 'COD-' . $order->id . '-' . time(),
            'status' => 'pending',
            'message' => 'Payment will be collected upon delivery',
            'data' => [
                'payment_method' => 'cash_on_delivery',
                'amount' => $order->total,
                'currency' => $order->currency ?? 'USD'
            ]
        ];
    }
    
    /**
     * Process a refund for cash on delivery
     *
     * @param PaymentTransaction $transaction
     * @param float $amount
     * @param string $reason
     * @return array
     */
    public function processRefund(PaymentTransaction $transaction, float $amount, string $reason = ''): array
    {
        $this->log('Processing Cash refund', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $amount
        ]);
        
        // For cash refunds, we just record that a refund should be given to the customer
        return [
            'success' => true,
            'refund_id' => 'REFUND-' . $transaction->id . '-' . time(),
            'status' => 'completed',
            'message' => 'Cash refund has been recorded',
            'data' => [
                'payment_method' => 'cash',
                'amount' => $amount,
                'currency' => $transaction->currency,
                'reason' => $reason
            ]
        ];
    }
    
    /**
     * Validate payment data for cash on delivery
     *
     * @param array $paymentData
     * @return bool
     */
    public function validatePaymentData(array $paymentData): bool
    {
        // No specific validation needed for cash payments
        return true;
    }
} 