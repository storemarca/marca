<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    /**
     * Change the current language
     *
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang($locale)
    {
        // Validate that the locale is supported
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = config('app.fallback_locale', 'en');
        }

        // Store language preference in session and cookie
        Session::put('locale', $locale);
        Cookie::queue('locale', $locale, 60 * 24 * 365); // Store for a year
        App::setLocale($locale);
        
        // Clear translation cache
        Cache::forget('translations_' . $locale);

        // Redirect back to the previous page
        return redirect()->back();
    }
} 