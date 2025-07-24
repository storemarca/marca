<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\LoyaltyTransaction;
use App\Models\RewardRedemption;
use App\Models\UserLoyaltyPoints;
use App\Models\Affiliate;
use App\Models\Referral;
use App\Models\Address;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    /**
     * Get the customer associated with the user.
     */
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Get the loyalty points record for the user.
     */
    public function loyaltyPoints()
    {
        return $this->hasOne(UserLoyaltyPoints::class);
    }

    /**
     * Get the loyalty transactions for the user.
     */
    public function loyaltyTransactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    /**
     * Get the reward redemptions for the user.
     */
    public function rewardRedemptions()
    {
        return $this->hasMany(RewardRedemption::class);
    }

    /**
     * Get the current loyalty tier of the user.
     */
    public function loyaltyTier()
    {
        return $this->loyaltyPoints ? $this->loyaltyPoints->currentTier : null;
    }

    /**
     * Get the points balance of the user.
     */
    public function getPointsBalanceAttribute()
    {
        return $this->loyaltyPoints ? $this->loyaltyPoints->points_balance : 0;
    }

    /**
     * Get the lifetime points of the user.
     */
    public function getLifetimePointsAttribute()
    {
        return $this->loyaltyPoints ? $this->loyaltyPoints->lifetime_points : 0;
    }

    /**
     * Add points to the user's balance.
     */
    public function addLoyaltyPoints(int $points, string $type, ?string $description = null, ?Model $source = null, ?Order $order = null)
    {
        $loyaltyPoints = $this->loyaltyPoints;
        
        if (!$loyaltyPoints) {
            $loyaltyPoints = UserLoyaltyPoints::create([
                'user_id' => $this->getKey(),
                'points_balance' => 0,
                'lifetime_points' => 0,
            ]);
        }
        
        return $loyaltyPoints->addPoints($points, $type, $description, $source, $order);
    }

    /**
     * Deduct points from the user's balance.
     */
    public function deductLoyaltyPoints(int $points, string $type, ?string $description = null, ?Model $source = null, ?Order $order = null)
    {
        $loyaltyPoints = $this->loyaltyPoints;
        
        if (!$loyaltyPoints || $loyaltyPoints->points_balance < $points) {
            return false;
        }
        
        return $loyaltyPoints->deductPoints($points, $type, $description, $source, $order);
    }

    /**
     * Get the affiliate account for the user.
     */
    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    /**
     * Get the referrals made by the user.
     */
    public function referrals()
    {
        return $this->hasOneThrough(Referral::class, Affiliate::class);
    }

    /**
     * Check if the user has an affiliate account.
     */
    public function hasAffiliate(): bool
    {
        return $this->affiliate()->exists();
    }

    /**
     * Check if the user has an approved affiliate account.
     */
    public function hasApprovedAffiliate(): bool
    {
        return $this->affiliate()->where('status', Affiliate::STATUS_APPROVED)->exists();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
