<?php

namespace App\Console\Commands;

use App\Services\LoyaltyService;
use Illuminate\Console\Command;

class UpdateLoyaltyTiers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:update-tiers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update loyalty tiers for all users based on their points';

    /**
     * Execute the console command.
     */
    public function handle(LoyaltyService $loyaltyService)
    {
        $this->info('Starting to update loyalty tiers...');
        
        $updatedCount = $loyaltyService->updateUserTiers();
        
        $this->info("Successfully updated tiers for {$updatedCount} users.");
        
        return 0;
    }
} 