<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\User;
use App\Notifications\CouponExpiring;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendCouponExpirationNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:notify-expiring {days=3 : Number of days before expiration to send notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for coupons that are about to expire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->argument('days');
        $this->info("Checking for coupons expiring in {$days} days...");
        
        $expiryDate = Carbon::now()->addDays($days)->startOfDay();
        
        // Find coupons that expire on the target date
        $coupons = Coupon::where('is_active', true)
            ->whereDate('expires_at', $expiryDate->toDateString())
            ->get();
            
        $this->info("Found {$coupons->count()} coupons expiring soon.");
        
        foreach ($coupons as $coupon) {
            $this->processCoupon($coupon, $days);
        }
        
        $this->info('Notification process completed.');
        
        return 0;
    }
    
    /**
     * Process a single coupon and send notifications.
     */
    protected function processCoupon(Coupon $coupon, int $days): void
    {
        $this->info("Processing coupon: {$coupon->code}");
        
        // If the coupon is restricted to specific users, notify only them
        if ($coupon->users()->count() > 0) {
            $users = $coupon->users;
            $this->info("Sending notifications to {$users->count()} specific users.");
            
            foreach ($users as $user) {
                $user->notify(new CouponExpiring($coupon, $days));
            }
        } else {
            // Otherwise, notify all active users
            $users = User::where('is_active', true)->get();
            $this->info("Sending notifications to {$users->count()} active users.");
            
            foreach ($users as $user) {
                $user->notify(new CouponExpiring($coupon, $days));
            }
        }
    }
} 