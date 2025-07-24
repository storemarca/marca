<?php

namespace App\Services;

use App\Models\LoyaltyReward;
use App\Models\LoyaltyTier;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\RewardRedemption;
use App\Models\User;
use App\Models\UserLoyaltyPoints;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
    /**
     * Award points for a completed order.
     *
     * @param Order $order
     * @return LoyaltyTransaction|null
     */
    public function awardPointsForOrder(Order $order): ?LoyaltyTransaction
    {
        try {
            $user = $order->user;
            
            if (!$user) {
                return null;
            }
            
            // Calculate points based on order total
            $orderTotal = $order->total_amount;
            $basePoints = floor($orderTotal); // 1 point per currency unit
            
            // Apply tier multiplier if applicable
            $loyaltyPoints = $user->loyaltyPoints;
            $pointsToAward = $basePoints;
            
            if ($loyaltyPoints && $loyaltyPoints->currentTier) {
                $multiplier = $loyaltyPoints->currentTier->points_multiplier;
                $pointsToAward = $basePoints * $multiplier;
            }
            
            // Award the points
            $description = 'نقاط مكتسبة من الطلب #' . $order->order_number;
            return $user->addLoyaltyPoints($pointsToAward, LoyaltyTransaction::TYPE_EARNED, $description, null, $order);
        } catch (Exception $e) {
            Log::error('Error awarding loyalty points for order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'exception' => $e,
            ]);
            
            return null;
        }
    }
    
    /**
     * Award points for a product review.
     *
     * @param ProductReview $review
     * @return LoyaltyTransaction|null
     */
    public function awardPointsForReview(ProductReview $review): ?LoyaltyTransaction
    {
        try {
            $user = $review->user;
            
            if (!$user) {
                return null;
            }
            
            // Only award points for approved reviews
            if (!$review->is_approved) {
                return null;
            }
            
            // Check if points have already been awarded for this review
            $existingTransaction = LoyaltyTransaction::where('user_id', $user->id)
                ->where('type', LoyaltyTransaction::TYPE_REVIEW)
                ->where('source_type', ProductReview::class)
                ->where('source_id', $review->id)
                ->first();
                
            if ($existingTransaction) {
                return null;
            }
            
            // Award points (e.g., 10 points per review)
            $pointsToAward = 10;
            
            // Additional points for reviews with images
            if ($review->images()->count() > 0) {
                $pointsToAward += 5;
            }
            
            $description = 'نقاط مكتسبة من تقييم المنتج: ' . $review->product->name;
            return $user->addLoyaltyPoints($pointsToAward, LoyaltyTransaction::TYPE_REVIEW, $description, $review);
        } catch (Exception $e) {
            Log::error('Error awarding loyalty points for review: ' . $e->getMessage(), [
                'review_id' => $review->id,
                'exception' => $e,
            ]);
            
            return null;
        }
    }
    
    /**
     * Award points for user signup.
     *
     * @param User $user
     * @return LoyaltyTransaction|null
     */
    public function awardPointsForSignup(User $user): ?LoyaltyTransaction
    {
        try {
            // Check if points have already been awarded for signup
            $existingTransaction = LoyaltyTransaction::where('user_id', $user->id)
                ->where('type', LoyaltyTransaction::TYPE_SIGNUP)
                ->first();
                
            if ($existingTransaction) {
                return null;
            }
            
            // Award points (e.g., 50 points for signup)
            $pointsToAward = 50;
            $description = 'نقاط ترحيبية للتسجيل في الموقع';
            
            return $user->addLoyaltyPoints($pointsToAward, LoyaltyTransaction::TYPE_SIGNUP, $description);
        } catch (Exception $e) {
            Log::error('Error awarding loyalty points for signup: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'exception' => $e,
            ]);
            
            return null;
        }
    }
    
    /**
     * Award points for referral.
     *
     * @param User $referrer
     * @param User $referee
     * @return LoyaltyTransaction|null
     */
    public function awardPointsForReferral(User $referrer, User $referee): ?LoyaltyTransaction
    {
        try {
            // Check if points have already been awarded for this referral
            $existingTransaction = LoyaltyTransaction::where('user_id', $referrer->id)
                ->where('type', LoyaltyTransaction::TYPE_REFERRAL)
                ->where('source_type', User::class)
                ->where('source_id', $referee->id)
                ->first();
                
            if ($existingTransaction) {
                return null;
            }
            
            // Award points (e.g., 100 points per referral)
            $pointsToAward = 100;
            $description = 'نقاط مكتسبة من دعوة صديق: ' . $referee->name;
            
            return $referrer->addLoyaltyPoints($pointsToAward, LoyaltyTransaction::TYPE_REFERRAL, $description, $referee);
        } catch (Exception $e) {
            Log::error('Error awarding loyalty points for referral: ' . $e->getMessage(), [
                'referrer_id' => $referrer->id,
                'referee_id' => $referee->id,
                'exception' => $e,
            ]);
            
            return null;
        }
    }
    
    /**
     * Award birthday points.
     *
     * @param User $user
     * @return LoyaltyTransaction|null
     */
    public function awardBirthdayPoints(User $user): ?LoyaltyTransaction
    {
        try {
            // Check if the user has a birthday set
            $customer = $user->customer;
            
            if (!$customer || !$customer->date_of_birth) {
                return null;
            }
            
            $birthday = Carbon::parse($customer->date_of_birth);
            $today = Carbon::today();
            
            // Check if today is the user's birthday (ignoring year)
            if ($birthday->month != $today->month || $birthday->day != $today->day) {
                return null;
            }
            
            // Check if points have already been awarded for this year's birthday
            $thisYear = $today->year;
            $startOfYear = Carbon::create($thisYear, 1, 1, 0, 0, 0);
            
            $existingTransaction = LoyaltyTransaction::where('user_id', $user->id)
                ->where('type', LoyaltyTransaction::TYPE_BIRTHDAY)
                ->where('created_at', '>=', $startOfYear)
                ->first();
                
            if ($existingTransaction) {
                return null;
            }
            
            // Award points (e.g., 200 points for birthday)
            $pointsToAward = 200;
            $description = 'نقاط هدية بمناسبة عيد ميلادك';
            
            return $user->addLoyaltyPoints($pointsToAward, LoyaltyTransaction::TYPE_BIRTHDAY, $description);
        } catch (Exception $e) {
            Log::error('Error awarding birthday loyalty points: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'exception' => $e,
            ]);
            
            return null;
        }
    }
    
    /**
     * Redeem a reward.
     *
     * @param User $user
     * @param LoyaltyReward $reward
     * @return RewardRedemption|null
     */
    public function redeemReward(User $user, LoyaltyReward $reward): ?RewardRedemption
    {
        try {
            DB::beginTransaction();
            
            // Check if the reward is available
            if (!$reward->isAvailable()) {
                throw new Exception('المكافأة غير متاحة حالياً.');
            }
            
            // Check if the user has enough points
            if ($user->points_balance < $reward->points_required) {
                throw new Exception('نقاط غير كافية لاستبدال هذه المكافأة.');
            }
            
            // Deduct points from user's balance
            $description = 'استبدال نقاط مقابل مكافأة: ' . $reward->localized_name;
            $transaction = $user->deductLoyaltyPoints($reward->points_required, LoyaltyTransaction::TYPE_REDEEMED, $description, $reward);
            
            if (!$transaction) {
                throw new Exception('فشل في خصم النقاط.');
            }
            
            // Create redemption record
            $redemption = RewardRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_required,
                'status' => RewardRedemption::STATUS_PENDING,
                'code' => RewardRedemption::generateUniqueCode(),
                'expires_at' => Carbon::now()->addDays(30), // Expires in 30 days
            ]);
            
            // Decrement reward stock if applicable
            $reward->decrementStock();
            
            // Mark the redemption as completed
            $redemption->markAsCompleted();
            
            DB::commit();
            
            return $redemption;
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error redeeming reward: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'exception' => $e,
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Apply a redeemed reward to an order.
     *
     * @param Order $order
     * @param RewardRedemption $redemption
     * @return bool
     */
    public function applyRewardToOrder(Order $order, RewardRedemption $redemption): bool
    {
        try {
            DB::beginTransaction();
            
            // Check if the redemption is valid
            if (!$redemption->isValid()) {
                throw new Exception('كود الاستبدال غير صالح.');
            }
            
            // Check if the user matches
            if ($order->user_id !== $redemption->user_id) {
                throw new Exception('كود الاستبدال غير صالح لهذا المستخدم.');
            }
            
            $reward = $redemption->reward;
            
            // Apply the reward based on its type
            switch ($reward->type) {
                case LoyaltyReward::TYPE_DISCOUNT:
                    $this->applyDiscountReward($order, $reward);
                    break;
                    
                case LoyaltyReward::TYPE_FREE_SHIPPING:
                    $order->shipping_cost = 0;
                    break;
                    
                case LoyaltyReward::TYPE_FREE_PRODUCT:
                    // This would typically be handled separately by adding the product to the cart
                    // before creating the order
                    break;
                    
                case LoyaltyReward::TYPE_GIFT_CARD:
                    // This would typically credit the user's account with the gift card value
                    break;
            }
            
            // Update the order
            $order->total = $order->calculateTotal();
            $order->save();
            
            // Mark the redemption as used with this order
            $redemption->order_id = $order->id;
            $redemption->save();
            
            DB::commit();
            
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error applying reward to order: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'redemption_id' => $redemption->id,
                'exception' => $e,
            ]);
            
            return false;
        }
    }
    
    /**
     * Apply a discount reward to an order.
     *
     * @param Order $order
     * @param LoyaltyReward $reward
     * @return void
     */
    protected function applyDiscountReward(Order $order, LoyaltyReward $reward): void
    {
        $rewardData = $reward->reward_data;
        $discountType = $rewardData['discount_type'] ?? 'fixed';
        $discountValue = $rewardData['value'] ?? 0;
        
        if ($discountType === 'percentage') {
            $discountAmount = $order->subtotal * ($discountValue / 100);
            
            // Apply maximum discount if set
            $maxDiscount = $rewardData['max_discount'] ?? null;
            if ($maxDiscount !== null && $discountAmount > $maxDiscount) {
                $discountAmount = $maxDiscount;
            }
        } else {
            $discountAmount = $discountValue;
        }
        
        // Ensure discount doesn't exceed the order subtotal
        $discountAmount = min($discountAmount, $order->subtotal);
        
        // Apply the discount
        $order->discount_amount = ($order->discount_amount ?? 0) + $discountAmount;
    }
    
    /**
     * Check and update tiers for all users.
     *
     * @return int Number of users whose tier was updated
     */
    public function updateUserTiers(): int
    {
        $updatedCount = 0;
        
        UserLoyaltyPoints::chunk(100, function ($userPoints) use (&$updatedCount) {
            foreach ($userPoints as $points) {
                if ($points->checkAndUpdateTier()) {
                    $updatedCount++;
                }
            }
        });
        
        return $updatedCount;
    }
    
    /**
     * Expire points that have been inactive for a specified period.
     *
     * @param int $monthsInactive
     * @return int Number of users whose points were expired
     */
    public function expireInactivePoints(int $monthsInactive = 12): int
    {
        $expiredCount = 0;
        $cutoffDate = Carbon::now()->subMonths($monthsInactive);
        
        UserLoyaltyPoints::where('points_balance', '>', 0)
            ->where('points_updated_at', '<', $cutoffDate)
            ->chunk(100, function ($userPoints) use (&$expiredCount, $cutoffDate) {
                foreach ($userPoints as $points) {
                    $user = $points->user;
                    $pointsToExpire = $points->points_balance;
                    
                    if ($user && $pointsToExpire > 0) {
                        $description = 'انتهاء صلاحية النقاط بسبب عدم النشاط منذ ' . $cutoffDate->diffInMonths(Carbon::now()) . ' شهر';
                        $user->deductLoyaltyPoints($pointsToExpire, LoyaltyTransaction::TYPE_EXPIRED, $description);
                        $expiredCount++;
                    }
                }
            });
        
        return $expiredCount;
    }
} 