<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Country;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class CurrentCountryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('current_country', function ($app) {
            // Obtener país de la sesión
            if (Session::has('country_id')) {
                $countryId = Session::get('country_id');
                if (is_string($countryId) || is_numeric($countryId)) {
                    $country = Country::find($countryId);
                    if ($country && $country->is_active) {
                        // Registrar en el log que se está usando el país de la sesión
                        Log::debug("Using country from session: " . $country->getAttribute('name') . " (ID: " . $country->getKey() . ")");
                        return $country;
                    }
                }
                // Si no se encuentra el país o no está activo, eliminar de la sesión
                Session::forget('country_id');
            }
            
            // Intentar obtener país de la cookie
            if ($app->request->hasCookie('country_id')) {
                $countryId = $app->request->cookie('country_id');
                if (is_string($countryId) || is_numeric($countryId)) {
                    $country = Country::find($countryId);
                    if ($country && $country->is_active) {
                        // Guardar en sesión y registrar en el log
                        Session::put('country_id', $country->getKey());
                        Log::debug("Using country from cookie: " . $country->getAttribute('name') . " (ID: " . $country->getKey() . ")");
                        return $country;
                    }
                }
            }
            
            // Intentar obtener país por IP
            $ip = $app->request->ip();
            $countryCode = $this->getCountryCodeFromIP($ip);
            $country = Country::where('code', $countryCode)->where('is_active', true)->first();
            if ($country) {
                Session::put('country_id', $country->getKey());
                Log::debug("Using country from IP: " . $country->getAttribute('name') . " (ID: " . $country->getKey() . ")");
                return $country;
            }
            
            // Usar مصر como país predeterminado
            $country = Country::where('code', 'EG')->where('is_active', true)->first();
            if (!$country) {
                // Si مصر no está disponible, usar el primer país activo
                $country = Country::where('is_active', true)->first();
            }
            
            if ($country) {
                Session::put('country_id', $country->getKey());
                Log::debug("Using default country: " . $country->getAttribute('name') . " (ID: " . $country->getKey() . ")");
                return $country;
            }
            
            // Si no hay países activos, registrar error y devolver null
            Log::error("No active countries found in the database!");
            return null;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
    
    /**
     * Get country code from IP address
     * 
     * @param string $ip
     * @return string
     */
    protected function getCountryCodeFromIP($ip)
    {
        // استخدام خدمة تحديد الموقع الجغرافي
        try {
            $response = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));
            if ($response && isset($response->status) && $response->status === "success" && isset($response->countryCode)) {
                return $response->countryCode;
            }
        } catch (\Exception $e) {
            Log::error("Error getting country from IP: " . $e->getMessage());
        }
        
        // إذا فشل التحديد، نعيد مصر كقيمة افتراضية
        return 'EG';
    }
}