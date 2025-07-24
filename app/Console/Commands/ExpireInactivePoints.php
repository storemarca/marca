<?php

namespace App\Console\Commands;

use App\Services\LoyaltyService;
use Illuminate\Console\Command;

class ExpireInactivePoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:expire-points {months=12 : Number of months of inactivity before points expire}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire loyalty points for users who have been inactive for a specified period';

    /**
     * Execute the console command.
     */
    public function handle(LoyaltyService $loyaltyService)
    {
        $months = $this->argument('months');
        $this->info("Starting to expire points for users inactive for {$months} months...");
        
        $expiredCount = $loyaltyService->expireInactivePoints($months);
        
        $this->info("Successfully expired points for {$expiredCount} users.");
        
        return 0;
    }
} 