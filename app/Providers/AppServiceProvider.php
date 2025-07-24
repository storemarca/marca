<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Country;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for database migrations
        Schema::defaultStringLength(191);
        
        // Disable wrapping of JSON resources
        JsonResource::withoutWrapping();
        
        // Enable strict mode in development
        if ($this->app->environment('local')) {
            Model::shouldBeStrict();
        }
        
        // تفعيل تتبع التحميل الكسول مؤقتًا للكشف عن المشكلة
        Model::preventLazyLoading(!app()->isProduction());
        Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
            $class = get_class($model);
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
            $traceInfo = '';
            foreach ($trace as $frame) {
                if (isset($frame['file']) && isset($frame['line'])) {
                    $traceInfo .= $frame['file'] . ':' . $frame['line'] . "\n";
                }
            }
            info("Attempted to lazy load [{$relation}] on model [{$class}]. Trace:\n{$traceInfo}");
        });
        
        // Share common data with all views
        $this->shareCommonData();
        
        // Set up global helper functions
        $this->registerHelpers();
        
        // Configure caching for better performance
        $this->configureCaching();
        
        // Register custom log channels
        $this->registerCustomLogChannels();
    }
    
    /**
     * Share common data with all views.
     */
    protected function shareCommonData(): void
    {
        // Cache categories for navigation
        View::composer(['layouts.user', 'user.products.index'], function ($view) {
            $categories = Cache::remember('nav_categories', now()->addHours(24), function () {
                return Category::where('is_active', true)
                    ->orderBy('name')
                    ->get();
            });
            
            $view->with('navCategories', $categories);
        });
        
        // Cache countries for country switcher
        View::composer(['layouts.user'], function ($view) {
            $countries = Cache::remember('available_countries', now()->addHours(24), function () {
                return Country::where('is_active', true)
                    ->orderBy('name')
                    ->get();
            });
            
            $view->with('availableCountries', $countries);
        });
        
        // Cache general settings
        View::composer('*', function ($view) {
            $settings = Cache::remember('general_settings', now()->addHours(24), function () {
                return Setting::getGroup('general');
            });
            
            $view->with('generalSettings', $settings);
        });
    }
    
    /**
     * Register global helper functions.
     */
    protected function registerHelpers(): void
    {
        // Add 'setting' helper function if it doesn't exist
        if (!function_exists('setting')) {
            function setting($key, $default = null) {
                return Cache::remember('setting_'.$key, now()->addHours(24), function () use ($key, $default) {
                    return Setting::get($key, $default);
                });
            }
        }
    }
    
    /**
     * Configure caching for better performance.
     */
    protected function configureCaching(): void
    {
        // Configure model caching for frequently accessed models
        $this->configureCategoryCaching();
        $this->configureProductCaching();
        $this->configureSettingCaching();
    }
    
    /**
     * Configure caching for categories.
     */
    protected function configureCategoryCaching(): void
    {
        Category::saved(function ($category) {
            $this->clearCategoryCache();
        });
        
        Category::deleted(function ($category) {
            $this->clearCategoryCache();
        });
    }
    
    /**
     * Configure caching for products.
     */
    protected function configureProductCaching(): void
    {
        // Clear product caches when a product is saved or deleted
        \App\Models\Product::saved(function ($product) {
            Cache::forget('featured_products');
            Cache::forget('bestselling_products');
            Cache::forget('product_'.$product->id);
            Cache::forget('product_'.$product->slug);
        });
        
        \App\Models\Product::deleted(function ($product) {
            Cache::forget('featured_products');
            Cache::forget('bestselling_products');
            Cache::forget('product_'.$product->id);
            Cache::forget('product_'.$product->slug);
        });
    }
    
    /**
     * Configure caching for settings.
     */
    protected function configureSettingCaching(): void
    {
        Setting::saved(function ($setting) {
            Cache::forget('setting_'.$setting->key);
            Cache::forget('general_settings');
            Cache::forget('payment_settings');
            Cache::forget('shipping_settings');
            Cache::forget('mail_settings');
        });
    }
    
    /**
     * Clear category cache.
     */
    protected function clearCategoryCache(): void
    {
        Cache::forget('nav_categories');
        Cache::forget('all_categories');
    }
    
    /**
     * Register custom log channels
     */
    protected function registerCustomLogChannels(): void
    {
        // Payment log channel
     /*   Log::channel('single')->build([
            'driver' => 'single',
            'path' => storage_path('logs/payment.log'),
            'level' => 'debug',
        ]);
        */
    }
}
