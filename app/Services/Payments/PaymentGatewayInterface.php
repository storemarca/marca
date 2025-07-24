<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\PaymentTransaction;

interface PaymentGatewayInterface
{
    /**
     * Process a payment
     *
     * @param Order $order
     * @param array $paymentData
     * @return array
     */
    public function processPayment(Order $order, array $paymentData): array;
    
    /**
     * Process a refund
     *
     * @param PaymentTransaction $transaction
     * @param float $amount
     * @param string $reason
     * @return array
     */
    public function processRefund(PaymentTransaction $transaction, float $amount, string $reason = ''): array;
    
    /**
     * Validate payment data
     *
     * @param array $paymentData
     * @return bool
     */
    public function validatePaymentData(array $paymentData): bool;
} 