<?php

namespace App\Console\Commands;

use App\Services\AffiliateService;
use Illuminate\Console\Command;

class ExpireOldReferrals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliates:expire-referrals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire old referrals that have passed their expiration date';

    /**
     * Execute the console command.
     */
    public function handle(AffiliateService $affiliateService)
    {
        $this->info('Starting to expire old referrals...');
        
        $expiredCount = $affiliateService->expireOldReferrals();
        
        $this->info("Successfully expired {$expiredCount} old referrals.");
        
        return 0;
    }
} 