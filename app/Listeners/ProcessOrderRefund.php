<?php

namespace App\Listeners;

use App\Events\OrderRefunded;
use App\Services\AffiliateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessOrderRefund implements ShouldQueue
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
    public function handle(OrderRefunded $event): void
    {
        $order = $event->order;
        
        // Process the affiliate commission refund
        $this->affiliateService->processOrderRefund($order);
    }
} 