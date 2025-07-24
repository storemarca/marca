<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class LanguageSwitcher
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
        // Check session first
        if (Session::has('locale')) {
            $locale = Session::get('locale');
            if (in_array($locale, ['en', 'ar'])) {
                App::setLocale($locale);
            }
        }
        // Then check cookie
        elseif ($request->cookie('locale')) {
            $locale = $request->cookie('locale');
            if (in_array($locale, ['en', 'ar'])) {
                Session::put('locale', $locale);
                App::setLocale($locale);
            }
        }
        // Default to config
        else {
            $locale = config('app.locale', 'en');
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }
} 