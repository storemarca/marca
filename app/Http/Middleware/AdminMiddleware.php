<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole(['admin', 'warehouse_manager', 'financial_manager'])) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'غير مصرح لك بالوصول'], 403);
            }
            
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول كمدير للوصول إلى لوحة التحكم');
        }
        
        return $next($request);
    }
}
