<?php

use App\Models\Country;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

if (!function_exists('current_country')) {
    /**
     * Get the current country instance.
     *
     * @return \App\Models\Country
     */
    function current_country()
    {
        return app('current_country');
    }
}

if (!function_exists('safe_trans')) {
    /**
     * Translate the given message safely, ensuring the result is a string.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string
     */
    function safe_trans($key = null, $replace = [], $locale = null)
    {
        if (is_null($key)) {
            return '';
        }
        
        $locale = $locale ?: App::getLocale();
        
        // Check if we have a custom translator
        if (app()->bound('custom.translator')) {
            return app('custom.translator')->get($key, $replace, $locale);
        }
        
        // Try to get the translation from JSON files first (for flat keys)
        $jsonTranslation = trans($key, $replace, $locale);
        
        // If the key was returned unchanged, it means the translation wasn't found in JSON
        // Try to get it from the PHP files
        if ($jsonTranslation === $key) {
            // Try to get the translation from PHP files
            $phpTranslation = __($key, $replace, $locale);
            
            // If still not found, try to get from the default locale
            if ($phpTranslation === $key && $locale !== config('app.fallback_locale')) {
                $fallbackTranslation = __($key, $replace, config('app.fallback_locale'));
                if ($fallbackTranslation !== $key) {
                    return $fallbackTranslation;
                }
            } else if ($phpTranslation !== $key) {
                return $phpTranslation;
            }
        } else {
            return $jsonTranslation;
        }
        
        // If we reach here, the translation wasn't found
        // Check if this is a nested key (contains dots)
        if (strpos($key, '.') !== false) {
            // Get the last part of the key as a fallback
            $parts = explode('.', $key);
            $lastPart = end($parts);
            
            // Convert to title case as a last resort
            return ucfirst(str_replace('_', ' ', $lastPart));
        }
        
        // Convert to title case as a last resort
        return ucfirst(str_replace('_', ' ', $key));
    }
}

if (!function_exists('htmlspecialchars_array')) {
    /**
     * Convert special characters to HTML entities in a string or array.
     *
     * @param  string|array  $value
     * @param  int  $flags
     * @param  string|null  $encoding
     * @param  bool  $double_encode
     * @return string|array
     */
    function htmlspecialchars_array($value, $flags = ENT_QUOTES, $encoding = 'UTF-8', $double_encode = true)
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        
        if (is_null($value)) {
            return '';
        }
        
        if (!is_string($value)) {
            $value = (string) $value;
        }
        
        return htmlspecialchars($value, $flags, $encoding, $double_encode);
    }
} 

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        // Try to get from cache first
        return Cache::remember('setting_' . $key, 60 * 24, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
}

if (!function_exists('html_safe')) {
    /**
     * Make a string safe for HTML output
     *
     * @param string $string
     * @return string
     */
    function html_safe($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
} 