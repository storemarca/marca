<?php

namespace App\Providers;

use App\Services\SettingService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingService::class, function ($app) {
            return new SettingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartir configuraciones generales con todas las vistas
        try {
            $settingService = $this->app->make(SettingService::class);
            $settings = $settingService->getGroup('general');
            View::share('settings', $settings);
        } catch (\Exception $e) {
            // Si hay un error (por ejemplo, la tabla no existe todavía), simplemente continuamos
            // Esto puede ocurrir durante las migraciones iniciales
        }
    }
} 