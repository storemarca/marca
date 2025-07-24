<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Configuraciones generales
        $generalSettings = [
            'site_name' => 'Marca',
            'site_description' => 'Tienda en línea de productos',
            'site_email' => 'info@marca.com',
            'site_phone' => '+1234567890',
            'site_address' => 'Dirección de la tienda',
            'site_currency' => 'EGP',
            'site_currency_symbol' => 'ج.م',
            'site_logo' => 'logo.png',
            'site_favicon' => 'favicon.ico',
            'site_language' => 'ar',
            'site_timezone' => 'Africa/Cairo',
        ];

        // Configuraciones de envío
        $shippingSettings = [
            'shipping_enabled' => '1',
            'shipping_method' => 'flat_rate',
            'flat_rate_cost' => '50',
            'free_shipping_threshold' => '500',
        ];

        // Configuraciones de pagos
        $paymentSettings = [
            'payment_cash_on_delivery' => '1',
            'payment_bank_transfer' => '1',
            'payment_credit_card' => '1',
        ];

        // Guardar configuraciones
        foreach ($generalSettings as $key => $value) {
            Setting::set($key, $value, 'general');
        }

        foreach ($shippingSettings as $key => $value) {
            Setting::set($key, $value, 'shipping');
        }

        foreach ($paymentSettings as $key => $value) {
            Setting::set($key, $value, 'payment');
        }
    }
} 