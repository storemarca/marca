<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Support\Facades\Http;

class StripePaymentGateway extends AbstractPaymentGateway
{
    /**
     * Process a payment via Stripe
     *
     * @param Order $order
     * @param array $paymentData
     * @return array
     * @throws Exception
     */
    public function processPayment(Order $order, array $paymentData): array
    {
        if (!$this->validatePaymentData($paymentData)) {
            throw new Exception('Invalid payment data for Stripe');
        }
        
        $this->log('Processing Stripe payment', [
            'order_id' => $order->id,
            'amount' => $order->total
        ]);
        
        try {
            // Get Stripe configuration from config file
            $stripeApiKey = $this->getConfig('api_key', Config::get('payment.stripe.secret'));
            
            $response = Http::withToken($stripeApiKey)
                ->post('https://api.stripe.com/v1/payment_intents', [
                    'amount' => (int)($this->formatAmount($order->total) * 100), // Stripe uses cents
                    'currency' => strtolower($order->currency ?? $this->currency),
                    'payment_method' => $paymentData['payment_method_id'],
                    'confirm' => true,
                    'return_url' => route('checkout.success', ['order' => $order->id]),
                    'description' => "Order #{$order->id}",
                    'metadata' => [
                        'order_id' => $order->id
                    ]
                ]);
            
            if ($response->successful()) {
                $paymentIntent = $response->json();
                
                return [
                    'success' => true,
                    'transaction_id' => $paymentIntent['id'],
                    'status' => $paymentIntent['status'],
                    'data' => $paymentIntent
                ];
            }
            
            throw new Exception($response->json()['error']['message'] ?? 'Stripe payment failed');
        } catch (Exception $e) {
            $this->log('Stripe payment failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Process a refund via Stripe
     *
     * @param PaymentTransaction $transaction
     * @param float $amount
     * @param string $reason
     * @return array
     */
    public function processRefund(PaymentTransaction $transaction, float $amount, string $reason = ''): array
    {
        $this->log('Processing Stripe refund', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $amount
        ]);
        
        try {
            // Get Stripe configuration from config file
            $stripeApiKey = $this->getConfig('api_key', Config::get('payment.stripe.secret'));
            
            $response = Http::withToken($stripeApiKey)
                ->post('https://api.stripe.com/v1/refunds', [
                    'payment_intent' => $transaction->transaction_id,
                    'amount' => (int)($this->formatAmount($amount) * 100), // Stripe uses cents
                    'reason' => $reason ?: 'requested_by_customer'
                ]);
            
            if ($response->successful()) {
                $refund = $response->json();
                
                return [
                    'success' => true,
                    'refund_id' => $refund['id'],
                    'status' => $refund['status'],
                    'data' => $refund
                ];
            }
            
            throw new Exception($response->json()['error']['message'] ?? 'Stripe refund failed');
        } catch (Exception $e) {
            $this->log('Stripe refund failed', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Validate payment data for Stripe
     *
     * @param array $paymentData
     * @return bool
     */
    public function validatePaymentData(array $paymentData): bool
    {
        return isset($paymentData['payment_method_id']);
    }
}