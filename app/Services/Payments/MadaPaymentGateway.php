<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class MadaPaymentGateway extends AbstractPaymentGateway
{
    /**
     * Process a payment via Mada
     *
     * @param Order $order
     * @param array $paymentData
     * @return array
     * @throws Exception
     */
    public function processPayment(Order $order, array $paymentData): array
    {
        if (!$this->validatePaymentData($paymentData)) {
            throw new Exception('بيانات الدفع غير صالحة لبطاقة مدى');
        }
        
        $this->log('معالجة دفع مدى', [
            'order_id' => $order->id,
            'amount' => $order->total
        ]);
        
        try {
            // Get Mada configuration
            $madaApiKey = $this->getConfig('api_key');
            $madaSecret = $this->getConfig('secret');
            $isSandbox = $this->getConfig('sandbox', true);
            
            $baseUrl = $isSandbox ? 'https://api.sandbox.mada.com' : 'https://api.mada.com';
            
            $response = Http::withToken($madaApiKey)
                ->post($baseUrl . '/v1/payments', [
                    'amount' => $this->formatAmount($order->total),
                    'currency' => strtoupper($order->currency ?? $this->currency),
                    'payment_method' => $paymentData['payment_method_id'],
                    'description' => "طلب رقم #{$order->id}",
                    'customer_info' => [
                        'name' => $order->shipping_name,
                        'email' => $order->customer_email,
                        'phone' => $order->shipping_phone
                    ],
                    'callback_url' => route('checkout.success', ['order' => $order->id]),
                    'metadata' => [
                        'order_id' => $order->id
                    ]
                ]);
            
            if ($response->successful()) {
                $paymentData = $response->json();
                
                return [
                    'success' => true,
                    'transaction_id' => $paymentData['id'],
                    'status' => $paymentData['status'],
                    'redirect_url' => $paymentData['redirect_url'] ?? null,
                    'data' => $paymentData
                ];
            }
            
            throw new Exception($response->json()['message'] ?? 'فشل الدفع عبر مدى');
        } catch (Exception $e) {
            $this->log('فشل الدفع عبر مدى', [
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
     * Process a refund via Mada
     *
     * @param PaymentTransaction $transaction
     * @param float $amount
     * @param string $reason
     * @return array
     */
    public function processRefund(PaymentTransaction $transaction, float $amount, string $reason = ''): array
    {
        $this->log('معالجة استرداد مدى', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $amount
        ]);
        
        try {
            // Get Mada configuration
            $madaApiKey = $this->getConfig('api_key');
            $madaSecret = $this->getConfig('secret');
            $isSandbox = $this->getConfig('sandbox', true);
            
            $baseUrl = $isSandbox ? 'https://api.sandbox.mada.com' : 'https://api.mada.com';
            
            $response = Http::withToken($madaApiKey)
                ->post($baseUrl . '/v1/refunds', [
                    'payment_id' => $transaction->transaction_id,
                    'amount' => $this->formatAmount($amount),
                    'reason' => $reason
                ]);
            
            if ($response->successful()) {
                $refundData = $response->json();
                
                return [
                    'success' => true,
                    'refund_id' => $refundData['id'],
                    'status' => $refundData['status'],
                    'data' => $refundData
                ];
            }
            
            throw new Exception($response->json()['message'] ?? 'فشل استرداد المبلغ عبر مدى');
        } catch (Exception $e) {
            $this->log('فشل استرداد المبلغ عبر مدى', [
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
     * Validate payment data for Mada
     *
     * @param array $paymentData
     * @return bool
     */
    public function validatePaymentData(array $paymentData): bool
    {
        return isset($paymentData['payment_method_id']);
    }
} 