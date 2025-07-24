<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use App\Services\AffiliateService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Track a click on an affiliate link and redirect to the target URL.
     *
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function trackLink($slug, Request $request)
    {
        // Find the affiliate link by slug
        $link = AffiliateLink::where('slug', $slug)->first();
        
        if (!$link || !$link->is_active || !$link->affiliate->isApproved()) {
            return redirect('/');
        }
        
        // Track the click
        $this->affiliateService->trackLinkClick($link, $request);
        
        // Redirect to the target URL
        return redirect($link->target_url);
    }

    /**
     * Store the affiliate code in a cookie and redirect to the homepage.
     *
     * @param string $code
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function trackReferral($code, Request $request)
    {
        // Set the affiliate cookie
        $this->affiliateService->setAffiliateCookie($code);
        
        // Create a referral record
        $affiliate = \App\Models\Affiliate::where('code', $code)->first();
        
        if ($affiliate && $affiliate->isApproved()) {
            $this->affiliateService->createReferral($affiliate, $request);
        }
        
        // Redirect to the homepage
        return redirect('/');
    }
} 