<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Services\AffiliateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessAffiliateCommission implements ShouldQueue
{
    use InteractsWithQueue;

    protected $affiliateService;

    /**
     * Create the event listener.
     */
    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        $order = $event->order;
        
        // Process the affiliate commission
        $this->affiliateService->processOrderCommission($order);
    }
} 