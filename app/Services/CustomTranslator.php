<?php

namespace App\Services;

use Illuminate\Translation\Translator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

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
        if (empty($key)) {
            return '';
        }
        
        $locale = $locale ?: App::getLocale();
        
        // Try to get from cache first
        $cacheKey = 'translation_' . $locale . '_' . md5($key);
        $useCache = config('translations.cache', true);
        
        if ($useCache && Cache::has($cacheKey) && !config('app.debug')) {
            $result = Cache::get($cacheKey);
            return $this->applyReplacements($result, $replace);
        }
        
        // First try JSON translations (flat keys)
        $result = $this->getJsonTranslation($key, $locale);
        
        // If not found in JSON, try PHP translations
        if ($result === $key) {
            $result = $this->getPhpTranslation($key, $locale);
        }
        
        // If still not found, try fallback locale
        if ($result === $key && $locale !== config('app.fallback_locale')) {
            $fallbackLocale = config('app.fallback_locale');
            
            // Try JSON in fallback locale
            $fallbackResult = $this->getJsonTranslation($key, $fallbackLocale);
            
            // If not found in JSON, try PHP in fallback locale
            if ($fallbackResult === $key) {
                $fallbackResult = $this->getPhpTranslation($key, $fallbackLocale);
            }
            
            if ($fallbackResult !== $key) {
                $result = $fallbackResult;
            }
        }
        
        // If still not found, use the key as a fallback
        if ($result === $key) {
            $result = $this->formatKeyAsFallback($key);
        }
        
        // Cache the result
        if ($useCache) {
            Cache::put($cacheKey, $result, now()->addMinutes(config('translations.cache_lifetime', 1440)));
            
            // Add to translation keys list for cache clearing
            $this->addToCacheKeysList($cacheKey);
        }
        
        return $this->applyReplacements($result, $replace);
    }
    
    /**
     * Get translation from JSON file.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return string
     */
    protected function getJsonTranslation($key, $locale)
    {
        // Try to get the translation directly from the JSON file
        $jsonPath = resource_path("lang/{$locale}.json");
        
        if (file_exists($jsonPath)) {
            try {
                $translations = Cache::remember('json_translations_' . $locale, now()->addMinutes(config('translations.cache_lifetime', 1440)), function () use ($jsonPath) {
                    return json_decode(file_get_contents($jsonPath), true) ?: [];
                });
                
                if (isset($translations[$key])) {
                    return $translations[$key];
                }
            } catch (\Exception $e) {
                Log::error("Error loading JSON translation for key '{$key}' in locale '{$locale}': " . $e->getMessage());
            }
        }
        
        // If not found in JSON file, try Laravel's translator
        $jsonResult = $this->translator->get($key, [], $locale);
        return ($jsonResult === $key) ? $key : $jsonResult;
    }
    
    /**
     * Get translation from PHP files.
     *
     * @param  string  $key
     * @param  string  $locale
     * @return string
     */
    protected function getPhpTranslation($key, $locale)
    {
        // If the key contains a dot, it's a namespaced key
        if (strpos($key, '.') !== false) {
            list($namespace, $item) = explode('.', $key, 2);
            
            // Try to get the translation directly from the PHP file
            $phpPath = resource_path("lang/{$locale}/{$namespace}.php");
            
            if (file_exists($phpPath)) {
                try {
                    $translations = Cache::remember('php_translations_' . $locale . '_' . $namespace, now()->addMinutes(config('translations.cache_lifetime', 1440)), function () use ($phpPath) {
                        return require $phpPath;
                    });
                    
                    if (isset($translations[$item])) {
                        return $translations[$item];
                    }
                } catch (\Exception $e) {
                    Log::error("Error loading PHP translation for key '{$key}' in locale '{$locale}': " . $e->getMessage());
                }
            }
        }
        
        // If not found in PHP file, try Laravel's translator
        $phpResult = $this->translator->get($key, [], $locale);
        return ($phpResult === $key) ? $key : $phpResult;
    }
    
    /**
     * Format the key as a fallback string.
     *
     * @param  string  $key
     * @return string
     */
    protected function formatKeyAsFallback($key)
    {
        // If the key contains a dot, use the last part
        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key);
            $key = end($parts);
        }
        
        // Convert underscore to space and title case
        return ucfirst(str_replace('_', ' ', $key));
    }
    
    /**
     * Apply replacements to the translation.
     *
     * @param  string  $translation
     * @param  array  $replace
     * @return string
     */
    protected function applyReplacements($translation, array $replace)
    {
        if (!empty($replace) && is_string($translation)) {
            foreach ($replace as $key => $value) {
                $translation = str_replace(':' . $key, $value, $translation);
            }
        }
        
        return $translation;
    }
    
    /**
     * Add a cache key to the list of translation keys.
     *
     * @param  string  $cacheKey
     * @return void
     */
    protected function addToCacheKeysList($cacheKey)
    {
        $keys = Cache::get('translation_keys', []);
        $keys[] = $cacheKey;
        Cache::put('translation_keys', array_unique($keys), now()->addDay());
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
        
        // Also clear the JSON and PHP translations cache
        $locales = config('translations.locales', ['en', 'ar']);
        
        foreach ($locales as $locale) {
            Cache::forget('json_translations_' . $locale);
            
            // Clear PHP translations for each namespace
            $phpPath = resource_path("lang/{$locale}");
            if (is_dir($phpPath)) {
                foreach (scandir($phpPath) as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        $namespace = pathinfo($file, PATHINFO_FILENAME);
                        Cache::forget('php_translations_' . $locale . '_' . $namespace);
                    }
                }
            }
        }
        
        Log::info('Translation cache cleared');
    }
} 
 