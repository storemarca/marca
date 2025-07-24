<?php

namespace App\Http\Middleware;

use App\Models\Affiliate;
use App\Services\AffiliateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackAffiliateReferral
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request has a ref parameter
        if ($request->has('ref')) {
            $affiliateCode = $request->get('ref');
            
            // Find the affiliate by code
            $affiliate = Affiliate::where('code', $affiliateCode)->first();
            
            if ($affiliate && $affiliate->isApproved()) {
                // Set the affiliate cookie
                $this->affiliateService->setAffiliateCookie($affiliateCode);
                
                // Create a referral record
                $this->affiliateService->createReferral($affiliate, $request);
            }
        }

        return $next($request);
    }
} 