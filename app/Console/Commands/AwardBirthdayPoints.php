<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\LoyaltyService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AwardBirthdayPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:award-birthday-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Award loyalty points to users whose birthday is today';

    /**
     * Execute the console command.
     */
    public function handle(LoyaltyService $loyaltyService)
    {
        $this->info('Starting to award birthday points...');
        
        $today = Carbon::today();
        $month = $today->month;
        $day = $today->day;
        
        // Find customers whose birthday is today
        $customers = Customer::whereMonth('date_of_birth', $month)
            ->whereDay('date_of_birth', $day)
            ->with('user')
            ->get();
        
        $this->info("Found {$customers->count()} customers with birthdays today.");
        
        $awardedCount = 0;
        
        foreach ($customers as $customer) {
            if (!$customer->user) {
                continue;
            }
            
            $transaction = $loyaltyService->awardBirthdayPoints($customer->user);
            
            if ($transaction) {
                $awardedCount++;
                $this->info("Awarded birthday points to {$customer->name} (ID: {$customer->id})");
            }
        }
        
        $this->info("Successfully awarded birthday points to {$awardedCount} customers.");
        
        return 0;
    }
} 