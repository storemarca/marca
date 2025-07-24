<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Exception;
use Illuminate\Support\Facades\Http;

class PayPalPaymentGateway extends AbstractPaymentGateway
{
    /**
     * Process a payment via PayPal
     *
     * @param Order $order
     * @param array $paymentData
     * @return array
     * @throws Exception
     */
    public function processPayment(Order $order, array $paymentData): array
    {
        if (!$this->validatePaymentData($paymentData)) {
            throw new Exception('Invalid payment data for PayPal');
        }
        
        $this->log('Processing PayPal payment', [
            'order_id' => $order->id,
            'amount' => $order->total
        ]);
        
        try {
            // Get PayPal configuration from config file
            $paypalClientId = $this->getConfig('client_id', Config::get('payment.paypal.client_id'));
            $paypalSecret = $this->getConfig('secret', Config::get('payment.paypal.secret'));
            $isSandbox = $this->getConfig('sandbox', Config::get('payment.paypal.sandbox', true));
            
            // Get access token
            $tokenResponse = Http::withBasicAuth($paypalClientId, $paypalSecret)
                ->asForm()
                ->post(($isSandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com') . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);
            
            if (!$tokenResponse->successful()) {
                throw new Exception('Failed to authenticate with PayPal');
            }
            
            $accessToken = $tokenResponse->json()['access_token'];
            
            // Create order
            $response = Http::withToken($accessToken)
                ->post(($isSandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com') . '/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'reference_id' => $order->id,
                            'description' => "Order #{$order->id}",
                            'amount' => [
                                'currency_code' => strtoupper($order->currency ?? $this->currency),
                                'value' => $this->formatAmount($order->total)
                            ]
                        ]
                    ],
                    'application_context' => [
                        'return_url' => route('checkout.success', ['order' => $order->id]),
                        'cancel_url' => route('checkout.index')
                    ]
                ]);
            
            if ($response->successful()) {
                $paypalOrder = $response->json();
                
                // Capture the order if we have an order ID and approval URL
                if (isset($paypalOrder['id']) && $paymentData['capture'] === true) {
                    $captureResponse = Http::withToken($accessToken)
                        ->post(($isSandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com') . "/v2/checkout/orders/{$paypalOrder['id']}/capture");
                    
                    if ($captureResponse->successful()) {
                        $captureData = $captureResponse->json();
                        
                        return [
                            'success' => true,
                            'transaction_id' => $captureData['id'],
                            'status' => $captureData['status'],
                            'data' => $captureData
                        ];
                    }
                    
                    throw new Exception($captureResponse->json()['message'] ?? 'PayPal capture failed');
                }
                
                // Return the approval URL for the frontend to redirect to
                $approvalUrl = collect($paypalOrder['links'])
                    ->firstWhere('rel', 'approve')['href'] ?? null;
                
                return [
                    'success' => true,
                    'transaction_id' => $paypalOrder['id'],
                    'status' => $paypalOrder['status'],
                    'approval_url' => $approvalUrl,
                    'data' => $paypalOrder
                ];
            }
            
            throw new Exception($response->json()['message'] ?? 'PayPal payment failed');
        } catch (Exception $e) {
            $this->log('PayPal payment failed', [
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
     * Process a refund via PayPal
     *
     * @param PaymentTransaction $transaction
     * @param float $amount
     * @param string $reason
     * @return array
     */
    public function processRefund(PaymentTransaction $transaction, float $amount, string $reason = ''): array
    {
        $this->log('Processing PayPal refund', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $amount
        ]);
        
        try {
            // Get PayPal configuration from config file
            $paypalClientId = $this->getConfig('client_id', Config::get('payment.paypal.client_id'));
            $paypalSecret = $this->getConfig('secret', Config::get('payment.paypal.secret'));
            $isSandbox = $this->getConfig('sandbox', Config::get('payment.paypal.sandbox', true));
            
            // Get access token
            $tokenResponse = Http::withBasicAuth($paypalClientId, $paypalSecret)
                ->asForm()
                ->post(($isSandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com') . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials'
                ]);
            
            if (!$tokenResponse->successful()) {
                throw new Exception('Failed to authenticate with PayPal');
            }
            
            $accessToken = $tokenResponse->json()['access_token'];
            
            // Get capture ID from transaction data
            $captureId = $transaction->response_data['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
            
            if (!$captureId) {
                throw new Exception('No capture ID found in transaction data');
            }
            
            // Process refund
            $response = Http::withToken($accessToken)
                ->post(($isSandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com') . "/v2/payments/captures/{$captureId}/refund", [
                    'amount' => [
                        'currency_code' => strtoupper($transaction->currency),
                        'value' => $this->formatAmount($amount)
                    ],
                    'note_to_payer' => $reason
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
            
            throw new Exception($response->json()['message'] ?? 'PayPal refund failed');
        } catch (Exception $e) {
            $this->log('PayPal refund failed', [
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
     * Validate payment data for PayPal
     *
     * @param array $paymentData
     * @return bool
     */
    public function validatePaymentData(array $paymentData): bool
    {
        // For initial payment, we don't need specific data
        // For capture, we need the PayPal order ID
        if (isset($paymentData['capture']) && $paymentData['capture'] === true) {
            return isset($paymentData['paypal_order_id']);
        }
        
        return true;
    }
}