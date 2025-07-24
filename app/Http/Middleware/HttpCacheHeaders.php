<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpCacheHeaders
{
    /**
     * The cache control directives.
     *
     * @var array
     */
    protected $cacheableRoutes = [
        'products.index' => 600,    // 10 minutes
        'products.show' => 1800,    // 30 minutes
        'home' => 300,              // 5 minutes
        'categories.*' => 1800,     // 30 minutes
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Skip cache for authenticated users or non-GET requests
        if ($request->user() || $request->method() !== 'GET') {
            $this->setNoCacheHeaders($response);
            return $response;
        }

        // Check if the current route is cacheable
        $routeName = $request->route() ? $request->route()->getName() : null;
        if (!$routeName) {
            return $response;
        }

        // Find matching route pattern
        $ttl = $this->getRouteTtl($routeName);
        if ($ttl) {
            $this->setCacheHeaders($response, $ttl);
        } else {
            $this->setNoCacheHeaders($response);
        }

        return $response;
    }

    /**
     * Get the TTL for a route.
     *
     * @param  string  $routeName
     * @return int|null
     */
    protected function getRouteTtl(string $routeName): ?int
    {
        // Direct match
        if (isset($this->cacheableRoutes[$routeName])) {
            return $this->cacheableRoutes[$routeName];
        }

        // Wildcard match
        foreach ($this->cacheableRoutes as $pattern => $ttl) {
            if (str_ends_with($pattern, '*') && 
                str_starts_with($routeName, substr($pattern, 0, -1))) {
                return $ttl;
            }
        }

        return null;
    }

    /**
     * Set cache headers for the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @param  int  $ttl
     * @return void
     */
    protected function setCacheHeaders(Response $response, int $ttl): void
    {
        $response->setCache([
            'public' => true,
            'max_age' => $ttl,
            's_maxage' => $ttl,
        ]);
        
        $response->headers->addCacheControlDirective('must-revalidate');
        
        // Set expiration time
        $response->setExpires(now()->addSeconds($ttl));
        
        // Set ETag for conditional requests
        if (!$response->headers->has('ETag')) {
            $response->setEtag(md5($response->getContent()));
        }
        
        // Set Last-Modified if not present
        if (!$response->headers->has('Last-Modified')) {
            $response->setLastModified(now());
        }
    }

    /**
     * Set no-cache headers for the response.
     *
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    protected function setNoCacheHeaders(Response $response): void
    {
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
} 