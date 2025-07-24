<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseHeaders
{
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
        
        // Add common API headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'DENY');
        
        // Set CORS headers for API
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        
        // Add API version header
        $response->headers->set('X-API-Version', '1.0');
        
        // Set cache control for API responses
        if ($request->method() === 'GET') {
            // For GET requests, allow short caching
            $response->headers->set('Cache-Control', 'public, max-age=60');
        } else {
            // For non-GET requests, no caching
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
        }
        
        // Add rate limit headers if they exist
        if ($request->attributes->has('rate_limit')) {
            $rateLimit = $request->attributes->get('rate_limit');
            $response->headers->set('X-RateLimit-Limit', $rateLimit['limit']);
            $response->headers->set('X-RateLimit-Remaining', $rateLimit['remaining']);
            $response->headers->set('X-RateLimit-Reset', $rateLimit['reset']);
        }
        
        return $response;
    }
} 