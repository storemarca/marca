<?php

namespace App\Services;

use Illuminate\Translation\Translator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

class CustomTranslator
{
    /**
     * The translator instance.
     *
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * Create a new CustomTranslator instance.
     *
     * @param  \Illuminate\Translation\Translator  $translator
     * @return void
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Get the translation for the given key.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string
     */
    public function get($key, array $replace = [], $locale = null)
    {
        $locale = $locale ?: App::getLocale();
        
        // Try to get from cache first
        $cacheKey = 'translation_' . $locale . '_' . md5($key);
        
        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
        } else {
            // Try to get the translation from JSON files first (flat keys)
            $jsonResult = $this->translator->get($key, [], $locale);
            
            // If the key was returned unchanged, try to get it from PHP files
            if ($jsonResult === $key) {
                $result = $this->translator->get($key, [], $locale);
                
                // If still not found, try to get from the default locale
                if ($result === $key && $locale !== config('app.fallback_locale')) {
                    $fallbackResult = $this->translator->get($key, [], config('app.fallback_locale'));
                    if ($fallbackResult !== $key) {
                        $result = $fallbackResult;
                    }
                }
            } else {
                $result = $jsonResult;
            }
            
            // If the result is an array, use the key's last part as a fallback
            if (is_array($result)) {
                if (strpos($key, '.') !== false) {
                    // Get the last part of the key as a fallback
                    $parts = explode('.', $key);
                    $result = ucfirst(str_replace('_', ' ', end($parts)));
                } else {
                    $result = ucfirst(str_replace('_', ' ', $key));
                }
            }
            
            // Cache the result
            Cache::put($cacheKey, $result, 60 * 24); // Store for a day
        }
        
        // Apply replacements if any
        if (!empty($replace) && is_string($result)) {
            foreach ($replace as $key => $value) {
                $result = str_replace(':' . $key, $value, $result);
            }
        }
        
        return $result;
    }
    
    /**
     * Clear the translation cache.
     *
     * @return void
     */
    public function clearCache()
    {
        $keys = Cache::get('translation_keys', []);
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        Cache::forget('translation_keys');
    }
} 