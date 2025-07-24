<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Exception;

class BankTransferPaymentGateway extends AbstractPaymentGateway
{
    /**
     * Process a bank transfer payment
     *
     * @param Order $order
     * @param array $paymentData
     * @return array
     */
    public function processPayment(Order $order, array $paymentData): array
    {
        $this->log('معالجة دفع تحويل بنكي', [
            'order_id' => $order->id,
            'amount' => $order->total
        ]);
        
        // Generate a unique reference number for the bank transfer
        $referenceNumber = 'BT-' . $order->id . '-' . time();
        
        // Get bank account details from configuration
        $bankDetails = $this->getConfig('bank_details', [
            'bank_name' => 'البنك الأهلي السعودي',
            'account_name' => 'شركة ماركا',
            'account_number' => 'SA0380000000608010167519',
            'swift_code' => 'NCBKSAJE',
        ]);
        
        return [
            'success' => true,
            'transaction_id' => $referenceNumber,
            'status' => 'pending',
            'message' => 'يرجى تحويل المبلغ إلى الحساب البنكي المذكور واستخدام رقم المرجع في تفاصيل التحويل',
            'data' => [
                'payment_method' => 'bank_transfer',
                'amount' => $order->total,
                'currency' => $order->currency ?? 'SAR',
                'reference_number' => $referenceNumber,
                'bank_details' => $bankDetails,
                'instructions' => 'يرجى تحويل المبلغ المطلوب إلى الحساب البنكي المذكور واستخدام رقم المرجع في تفاصيل التحويل. سيتم تأكيد الطلب بعد التحقق من استلام المبلغ.'
            ]
        ];
    }
    
    /**
     * Process a refund for bank transfer
     *
     * @param PaymentTransaction $transaction
     * @param float $amount
     * @param string $reason
     * @return array
     */
    public function processRefund(PaymentTransaction $transaction, float $amount, string $reason = ''): array
    {
        $this->log('معالجة استرداد تحويل بنكي', [
            'transaction_id' => $transaction->transaction_id,
            'amount' => $amount
        ]);
        
        // For bank transfer refunds, we need to collect the customer's bank details
        // and process the refund manually
        $refundId = 'REFUND-BT-' . $transaction->id . '-' . time();
        
        return [
            'success' => true,
            'refund_id' => $refundId,
            'status' => 'pending',
            'message' => 'سيتم معالجة الاسترداد يدويًا. يرجى الاتصال بخدمة العملاء لتقديم تفاصيل الحساب البنكي الخاص بك.',
            'data' => [
                'payment_method' => 'bank_transfer',
                'amount' => $amount,
                'currency' => $transaction->currency,
                'reason' => $reason,
                'instructions' => 'سيتم معالجة الاسترداد يدويًا. يرجى الاتصال بخدمة العملاء لتقديم تفاصيل الحساب البنكي الخاص بك لإتمام عملية الاسترداد.'
            ]
        ];
    }
    
    /**
     * Validate payment data for bank transfer
     *
     * @param array $paymentData
     * @return bool
     */
    public function validatePaymentData(array $paymentData): bool
    {
        // For bank transfer, we don't need specific payment data
        return true;
    }
} 