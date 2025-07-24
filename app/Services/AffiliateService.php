<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateLink;
use App\Models\AffiliateLinkStat;
use App\Models\CommissionTransaction;
use App\Models\Order;
use App\Models\Referral;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

class AffiliateService
{
    /**
     * Create a new affiliate account for a user.
     *
     * @param User $user
     * @param array $data
     * @return Affiliate
     */
    public function createAffiliate(User $user, array $data): Affiliate
    {
        // Generate a unique affiliate code
        $code = Affiliate::generateUniqueCode();
        
        // Create the affiliate
        $affiliate = Affiliate::create([
            'user_id' => $user->id,
            'code' => $code,
            'status' => Affiliate::STATUS_PENDING,
            'commission_rate' => $data['commission_rate'] ?? 10.00, // Default 10%
            'website' => $data['website'] ?? null,
            'social_media' => $data['social_media'] ?? null,
            'marketing_methods' => $data['marketing_methods'] ?? null,
            'payment_details' => $data['payment_details'] ?? null,
        ]);
        
        return $affiliate;
    }
    
    /**
     * Track a click on an affiliate link.
     *
     * @param AffiliateLink $link
     * @param Request $request
     * @return AffiliateLinkStat
     */
    public function trackLinkClick(AffiliateLink $link, Request $request): AffiliateLinkStat
    {
        // Get IP address and user agent
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        // Get location data
        $location = Location::get($ipAddress);
        $country = $location ? $location->countryName : null;
        $city = $location ? $location->cityName : null;
        
        // Get device type
        $deviceType = AffiliateLinkStat::getDeviceType($userAgent);
        
        // Create link stat record
        $stat = AffiliateLinkStat::create([
            'affiliate_link_id' => $link->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'referrer' => $request->header('referer'),
            'country' => $country,
            'city' => $city,
            'device_type' => $deviceType,
            'is_conversion' => false,
        ]);
        
        // Increment link clicks
        $link->incrementClicks();
        
        // Set affiliate cookie
        $this->setAffiliateCookie($link->affiliate->code);
        
        return $stat;
    }
    
    /**
     * Set the affiliate cookie.
     *
     * @param string $affiliateCode
     * @param int $days
     * @return void
     */
    public function setAffiliateCookie(string $affiliateCode, int $days = 30): void
    {
        Cookie::queue('affiliate_code', $affiliateCode, $days * 24 * 60);
    }
    
    /**
     * Get the affiliate code from the cookie.
     *
     * @param Request $request
     * @return string|null
     */
    public function getAffiliateCodeFromCookie(Request $request): ?string
    {
        return $request->cookie('affiliate_code');
    }
    
    /**
     * Create a referral record.
     *
     * @param Affiliate $affiliate
     * @param Request $request
     * @param int $expirationDays
     * @return Referral
     */
    public function createReferral(Affiliate $affiliate, Request $request, int $expirationDays = 30): Referral
    {
        // Create the referral
        $referral = Referral::create([
            'affiliate_id' => $affiliate->id,
            'referral_code' => $affiliate->code,
            'source' => $request->header('referer'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => Referral::STATUS_PENDING,
            'expires_at' => Carbon::now()->addDays($expirationDays),
        ]);
        
        return $referral;
    }
    
    /**
     * Convert a referral when a user registers.
     *
     * @param User $user
     * @param string $affiliateCode
     * @return Referral|null
     */
    public function convertReferral(User $user, string $affiliateCode): ?Referral
    {
        // Find the affiliate by code
        $affiliate = Affiliate::where('code', $affiliateCode)->first();
        
        if (!$affiliate || !$affiliate->isApproved()) {
            return null;
        }
        
        // Find the most recent referral for this affiliate code
        $referral = Referral::where('referral_code', $affiliateCode)
            ->where('status', Referral::STATUS_PENDING)
            ->where('expires_at', '>', Carbon::now())
            ->orderByDesc('created_at')
            ->first();
        
        if (!$referral) {
            // Create a new referral if none exists
            $referral = Referral::create([
                'affiliate_id' => $affiliate->id,
                'referred_user_id' => $user->id,
                'referral_code' => $affiliateCode,
                'status' => Referral::STATUS_CONVERTED,
                'converted_at' => Carbon::now(),
            ]);
        } else {
            // Mark the referral as converted
            $referral->markAsConverted($user);
        }
        
        return $referral;
    }
    
    /**
     * Process commission for an order.
     *
     * @param Order $order
     * @param string|null $affiliateCode
     * @return CommissionTransaction|null
     */
    public function processOrderCommission(Order $order, ?string $affiliateCode = null): ?CommissionTransaction
    {
        try {
            DB::beginTransaction();
            
            // If no affiliate code is provided, try to get it from the cookie
            if (!$affiliateCode && request()) {
                $affiliateCode = $this->getAffiliateCodeFromCookie(request());
            }
            
            // If still no affiliate code, check if the order has an affiliate link stat
            if (!$affiliateCode && $order->affiliateLinkStat) {
                $affiliateCode = $order->affiliateLinkStat->affiliateLink->affiliate->code;
            }
            
            // If no affiliate code, return null
            if (!$affiliateCode) {
                DB::commit();
                return null;
            }
            
            // Find the affiliate by code
            $affiliate = Affiliate::where('code', $affiliateCode)->first();
            
            // Check if the affiliate exists and is approved
            if (!$affiliate || !$affiliate->isApproved()) {
                DB::commit();
                return null;
            }
            
            // Check if the order is by the affiliate themselves
            if ($order->user_id === $affiliate->user_id) {
                DB::commit();
                return null;
            }
            
            // Calculate commission amount
            $commissionRate = $affiliate->commission_rate / 100; // Convert percentage to decimal
            $commissionAmount = $order->total_amount * $commissionRate;
            
            // Add commission to affiliate's balance
            $transaction = $affiliate->addCommission(
                $commissionAmount,
                CommissionTransaction::TYPE_EARNED,
                $order,
                null,
                'عمولة من الطلب #' . $order->order_number
            );
            
            // If there's an affiliate link stat, mark it as a conversion
            if ($order->affiliateLinkStat) {
                $order->affiliateLinkStat->markAsConversion($order, $commissionAmount);
                
                // Update the affiliate link's conversions and earnings
                $affiliateLink = $order->affiliateLinkStat->affiliateLink;
                $affiliateLink->incrementConversions($commissionAmount);
            }
            
            DB::commit();
            
            return $transaction;
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error processing order commission: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'affiliate_code' => $affiliateCode,
                'exception' => $e,
            ]);
            
            return null;
        }
    }
    
    /**
     * Process a withdrawal request.
     *
     * @param Affiliate $affiliate
     * @param float $amount
     * @param string $paymentMethod
     * @param string $paymentDetails
     * @return WithdrawalRequest|null
     */
    public function requestWithdrawal(Affiliate $affiliate, float $amount, string $paymentMethod, string $paymentDetails): ?WithdrawalRequest
    {
        try {
            DB::beginTransaction();
            
            // Check if the affiliate has enough balance
            if ($affiliate->balance < $amount) {
                throw new Exception('رصيد غير كافي');
            }
            
            // Deduct the amount from the affiliate's balance
            $transaction = $affiliate->deductCommission(
                $amount,
                CommissionTransaction::TYPE_PAID,
                'طلب سحب #' . (WithdrawalRequest::count() + 1)
            );
            
            if (!$transaction) {
                throw new Exception('فشل في خصم المبلغ من الرصيد');
            }
            
            // Create the withdrawal request
            $withdrawalRequest = WithdrawalRequest::create([
                'affiliate_id' => $affiliate->id,
                'amount' => $amount,
                'status' => WithdrawalRequest::STATUS_PENDING,
                'payment_method' => $paymentMethod,
                'payment_details' => $paymentDetails,
            ]);
            
            DB::commit();
            
            return $withdrawalRequest;
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error processing withdrawal request: ' . $e->getMessage(), [
                'affiliate_id' => $affiliate->id,
                'amount' => $amount,
                'exception' => $e,
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Process a refund for an order.
     *
     * @param Order $order
     * @return CommissionTransaction|null
     */
    public function processOrderRefund(Order $order): ?CommissionTransaction
    {
        try {
            DB::beginTransaction();
            
            // Check if the order has commission transactions
            $commissionTransaction = CommissionTransaction::where('order_id', $order->id)
                ->where('type', CommissionTransaction::TYPE_EARNED)
                ->first();
            
            if (!$commissionTransaction) {
                DB::commit();
                return null;
            }
            
            $affiliate = $commissionTransaction->affiliate;
            $commissionAmount = $commissionTransaction->amount;
            
            // Deduct the commission from the affiliate's balance
            $refundTransaction = $affiliate->deductCommission(
                $commissionAmount,
                CommissionTransaction::TYPE_REFUNDED,
                'استرداد عمولة من الطلب #' . $order->order_number
            );
            
            if (!$refundTransaction) {
                // If the affiliate doesn't have enough balance, create a negative balance
                $refundTransaction = CommissionTransaction::create([
                    'affiliate_id' => $affiliate->id,
                    'order_id' => $order->id,
                    'type' => CommissionTransaction::TYPE_REFUNDED,
                    'amount' => -$commissionAmount,
                    'balance_after' => $affiliate->balance - $commissionAmount,
                    'status' => CommissionTransaction::STATUS_COMPLETED,
                    'notes' => 'استرداد عمولة من الطلب #' . $order->order_number,
                ]);
                
                // Update the affiliate's balance
                $affiliate->balance -= $commissionAmount;
                $affiliate->save();
            }
            
            DB::commit();
            
            return $refundTransaction;
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error processing order refund: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e,
            ]);
            
            return null;
        }
    }
    
    /**
     * Create an affiliate link.
     *
     * @param Affiliate $affiliate
     * @param array $data
     * @return AffiliateLink
     */
    public function createAffiliateLink(Affiliate $affiliate, array $data): AffiliateLink
    {
        // Generate a unique slug
        $slug = AffiliateLink::generateUniqueSlug($data['name']);
        
        // Create the affiliate link
        $link = AffiliateLink::create([
            'affiliate_id' => $affiliate->id,
            'name' => $data['name'],
            'slug' => $slug,
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'] ?? null,
            'custom_url' => $data['custom_url'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
        
        return $link;
    }
    
    /**
     * Get the top-performing affiliates.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopAffiliates(int $limit = 10)
    {
        return Affiliate::approved()
            ->orderByDesc('lifetime_earnings')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get the top-performing affiliate links.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopAffiliateLinks(int $limit = 10)
    {
        return AffiliateLink::active()
            ->orderByDesc('earnings')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get the conversion rate for all affiliate links.
     *
     * @return float
     */
    public function getOverallConversionRate(): float
    {
        $totalClicks = AffiliateLink::sum('clicks');
        $totalConversions = AffiliateLink::sum('conversions');
        
        if ($totalClicks === 0) {
            return 0;
        }
        
        return round(($totalConversions / $totalClicks) * 100, 2);
    }
    
    /**
     * Get the total earnings for all affiliates.
     *
     * @return float
     */
    public function getTotalEarnings(): float
    {
        return Affiliate::sum('lifetime_earnings');
    }
    
    /**
     * Get the total pending withdrawals.
     *
     * @return float
     */
    public function getTotalPendingWithdrawals(): float
    {
        return WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PENDING)
            ->sum('amount');
    }
    
    /**
     * Get the total paid withdrawals.
     *
     * @return float
     */
    public function getTotalPaidWithdrawals(): float
    {
        return WithdrawalRequest::where('status', WithdrawalRequest::STATUS_PAID)
            ->sum('amount');
    }
    
    /**
     * Expire old referrals.
     *
     * @return int Number of expired referrals
     */
    public function expireOldReferrals(): int
    {
        $expiredCount = 0;
        
        Referral::pending()
            ->where('expires_at', '<', Carbon::now())
            ->chunk(100, function ($referrals) use (&$expiredCount) {
                foreach ($referrals as $referral) {
                    $referral->markAsExpired();
                    $expiredCount++;
                }
            });
        
        return $expiredCount;
    }
} 