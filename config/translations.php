<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Translation Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure how translations are loaded and cached.
    |
    */

    // Whether to cache translations
    'cache' => env('TRANSLATION_CACHE', true),

    // Cache lifetime in minutes
    'cache_lifetime' => env('TRANSLATION_CACHE_LIFETIME', 1440), // 24 hours

    // Supported locales
    'locales' => ['en', 'ar'],

    // Default locale
    'default_locale' => env('APP_LOCALE', 'en'),

    // Fallback locale
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    // Whether to show the language switcher
    'show_language_switcher' => env('SHOW_LANGUAGE_SWITCHER', true),

    // Translation files paths
    'paths' => [
        'json' => resource_path('lang'),
        'php' => resource_path('lang'),
    ],
]; 