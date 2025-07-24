<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PageCache
{
    /**
     * The number of seconds to cache the response.
     *
     * @var int
     */
    protected $ttl = 3600; // 1 hour by default

    /**
     * Routes that should be cached.
     *
     * @var array
     */
    protected $cacheableRoutes = [
        'products.index',
        'products.show',
        'home',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|null  $ttl
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ?int $ttl = null): Response
    {
        // Skip caching for authenticated users
        if ($request->user()) {
            return $next($request);
        }

        // Skip caching for non-GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Skip caching if the route is not cacheable
        $routeName = $request->route() ? $request->route()->getName() : null;
        if (!$routeName || !in_array($routeName, $this->cacheableRoutes)) {
            return $next($request);
        }

        // Set TTL from parameter or use default
        $ttl = $ttl ?: $this->ttl;

        // Generate a unique cache key
        $cacheKey = $this->generateCacheKey($request);

        // Check if we have a cached response
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Get the response
        $response = $next($request);

        // Only cache successful responses
        if ($response->isSuccessful()) {
            // Store the response in the cache
            Cache::put($cacheKey, $response, $ttl);
        }

        return $response;
    }

    /**
     * Generate a unique cache key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function generateCacheKey(Request $request): string
    {
        $uri = $request->getPathInfo();
        $queryString = $request->getQueryString();
        $locale = app()->getLocale();
        $country = session()->get('country_id');
        
        $key = 'page_cache:' . $uri;
        
        if ($queryString) {
            $key .= '?' . $queryString;
        }
        
        $key .= ':' . $locale . ':' . ($country ?: 'default');
        
        return md5($key);
    }
} 