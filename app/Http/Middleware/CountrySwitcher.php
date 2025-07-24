<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Country;

class CountrySwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // تعريف متغير $country بقيمة افتراضية null
        $country = null;
        
        // Check session first
        if (Session::has('country_id')) {
            $countryId = Session::get('country_id');
            $country = Country::find($countryId);
            if (!$country) {
                Session::forget('country_id');
            }
        }
        
        // Then check cookie
        if (!$country && $request->cookie('country_id')) {
            $countryId = $request->cookie('country_id');
            $country = Country::find($countryId);
            if ($country) {
                Session::put('country_id', $country->id);
            }
        }
        
        // Try to get country from IP
        if (!$country) {
            $ip = $request->ip();
            $countryCode = $this->getCountryCodeFromIP($ip);
            $country = Country::where('code', $countryCode)->first();
            if ($country) {
                Session::put('country_id', $country->id);
            }
        }
        
        // Default to first active country (Saudi Arabia)
        if (!$country) {
            $country = Country::where('code', 'SA')->orWhere('is_active', true)->first();
            if ($country) {
                Session::put('country_id', $country->id);
            }
        }

        return $next($request);
    }
    
    /**
     * Get country code from IP address
     * 
     * @param string $ip
     * @return string
     */
    protected function getCountryCodeFromIP($ip)
    {
        // مؤقتًا نرجع "SA"، ويمكنك تفعيل السطر التالي للواقع:
        return 'SA';

        // استخدام خدمة IP API (غير مفعّلة الآن لتجنب البطء):
        /*
        $response = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"));
        if ($response && $response->status === 'success') {
            return $response->countryCode;
        }
        return null;
        */
    }
} 