<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Artisan;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register custom translator service
        $this->app->singleton('custom.translator', function ($app) {
            return new \App\Services\CustomTranslator($app['translator']);
        });
        
        // Define global safe_trans function
        if (!function_exists('safe_trans')) {
            require_once app_path('helpers.php');
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load JSON translations
        $this->loadJsonTranslations();
        
        // Load PHP translations
        $this->loadPhpTranslations();
        
        // Clear translation cache when environment is local and debug is true
        if (config('app.env') === 'local' && config('app.debug')) {
            $this->clearTranslationCache();
        }
        
        // Add command to clear translation cache
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\ClearTranslationCache::class,
            ]);
        }
    }

    /**
     * Load JSON translations from translation files
     */
    protected function loadJsonTranslations()
    {
        $locale = App::getLocale();
        
        // Use cache to store translations
        $cacheKey = 'translations_json_' . $locale;
        
        if (!Cache::has($cacheKey) || config('app.debug')) {
            $jsonPath = resource_path("lang/{$locale}.json");
            
            if (File::exists($jsonPath)) {
                $translations = json_decode(File::get($jsonPath), true);
                if (is_array($translations)) {
                    Cache::put($cacheKey, $translations, now()->addDay()); // Store for a day
                    
                    // Register the translations with the translator
                    Lang::addJsonPath(resource_path('lang'));
                }
            }
            
            // Also check for fallback locale if different from current
            $fallbackLocale = config('app.fallback_locale');
            if ($locale !== $fallbackLocale) {
                $fallbackPath = resource_path("lang/{$fallbackLocale}.json");
                if (File::exists($fallbackPath)) {
                    $fallbackTranslations = json_decode(File::get($fallbackPath), true);
                    if (is_array($fallbackTranslations)) {
                        Cache::put('translations_json_' . $fallbackLocale, $fallbackTranslations, now()->addDay());
                    }
                }
            }
        }
    }
    
    /**
     * Load PHP translations from translation files
     */
    protected function loadPhpTranslations()
    {
        $locale = App::getLocale();
        
        // Use cache to store translations
        $cacheKey = 'translations_php_' . $locale;
        
        if (!Cache::has($cacheKey) || config('app.debug')) {
            $phpPath = resource_path("lang/{$locale}");
            
            if (File::isDirectory($phpPath)) {
                $translations = [];
                
                foreach (File::files($phpPath) as $file) {
                    $filename = pathinfo($file, PATHINFO_FILENAME);
                    $translations[$filename] = require $file->getPathname();
                }
                
                if (!empty($translations)) {
                    Cache::put($cacheKey, $translations, now()->addDay()); // Store for a day
                }
            }
            
            // Also check for fallback locale if different from current
            $fallbackLocale = config('app.fallback_locale');
            if ($locale !== $fallbackLocale) {
                $fallbackPath = resource_path("lang/{$fallbackLocale}");
                if (File::isDirectory($fallbackPath)) {
                    $fallbackTranslations = [];
                    
                    foreach (File::files($fallbackPath) as $file) {
                        $filename = pathinfo($file, PATHINFO_FILENAME);
                        $fallbackTranslations[$filename] = require $file->getPathname();
                    }
                    
                    if (!empty($fallbackTranslations)) {
                        Cache::put('translations_php_' . $fallbackLocale, $fallbackTranslations, now()->addDay());
                    }
                }
            }
        }
    }
    
    /**
     * Clear translation cache
     */
    protected function clearTranslationCache()
    {
        $locales = ['en', 'ar']; // Add all supported locales
        
        foreach ($locales as $locale) {
            Cache::forget('translations_json_' . $locale);
            Cache::forget('translations_php_' . $locale);
        }
        
        // Also clear the Laravel translation cache
        if (function_exists('artisan_call_silent')) {
            artisan_call_silent('cache:clear');
        }
    }
}
