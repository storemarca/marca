<?php

namespace App\Services\Payments;

use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    /**
     * @var PaymentGateway|null
     */
    protected $gateway;
    
    /**
     * @var array
     */
    protected $config;
    
    /**
     * @var string
     */
    protected $currency;
    
    /**
     * Set payment gateway
     *
     * @param PaymentGateway $gateway
     * @return $this
     */
    public function setGateway(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
        $this->config = $gateway->config ?? [];
        $this->currency = Config::get('payment.default_currency', 'USD');
        return $this;
    }
    
    /**
     * Set currency for the transaction
     *
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
        return $this;
    }
    
    /**
     * Log payment activity
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log(string $message, array $context = [])
    {
        $gatewayName = $this->gateway ? $this->gateway->name : static::class;
        Log::channel('payment')->info("[{$gatewayName}] {$message}", $context);
    }
    
    /**
     * Format money amount based on currency
     *
     * @param float $amount
     * @return float
     */
    protected function formatAmount(float $amount): float
    {
        $decimalPlaces = Config::get("payment.currencies.{$this->currency}.decimal_places", 2);
        return round($amount, $decimalPlaces);
    }
    
    /**
     * Get currency symbol
     *
     * @return string
     */
    protected function getCurrencySymbol(): string
    {
        return Config::get("payment.currencies.{$this->currency}.symbol", '$');
    }
    
    /**
     * Format money amount with currency symbol
     *
     * @param float $amount
     * @return string
     */
    protected function formatAmountWithCurrency(float $amount): string
    {
        return $this->getCurrencySymbol() . $this->formatAmount($amount);
    }
    
    /**
     * Get gateway configuration
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}