<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserLoyaltyPoints extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'points_balance',
        'lifetime_points',
        'current_tier_id',
        'points_updated_at',
        'tier_updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'points_balance' => 'integer',
        'lifetime_points' => 'integer',
        'points_updated_at' => 'datetime',
        'tier_updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the loyalty points.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the current loyalty tier of the user.
     */
    public function currentTier(): BelongsTo
    {
        return $this->belongsTo(LoyaltyTier::class, 'current_tier_id');
    }

    /**
     * Get the loyalty transactions for the user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Add points to the user's balance.
     *
     * @param int $points
     * @param string $type
     * @param string|null $description
     * @param Model|null $source
     * @param Order|null $order
     * @return LoyaltyTransaction
     */
    public function addPoints(int $points, string $type, ?string $description = null, ?Model $source = null, ?Order $order = null): LoyaltyTransaction
    {
        // Update points balance
        $this->points_balance += $points;
        $this->lifetime_points += $points;
        $this->points_updated_at = now();
        $this->save();

        // Check if user qualifies for a new tier
        $this->checkAndUpdateTier();

        // Create transaction record
        return LoyaltyTransaction::create([
            'user_id' => $this->user_id,
            'type' => $type,
            'points' => $points,
            'balance_after' => $this->points_balance,
            'description' => $description,
            'order_id' => $order ? $order->id : null,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source ? $source->id : null,
        ]);
    }

    /**
     * Deduct points from the user's balance.
     *
     * @param int $points
     * @param string $type
     * @param string|null $description
     * @param Model|null $source
     * @param Order|null $order
     * @return LoyaltyTransaction
     */
    public function deductPoints(int $points, string $type, ?string $description = null, ?Model $source = null, ?Order $order = null): LoyaltyTransaction
    {
        // Ensure points are positive for deduction
        $points = abs($points);

        // Update points balance
        $this->points_balance -= $points;
        $this->points_updated_at = now();
        $this->save();

        // Create transaction record (negative points for deduction)
        return LoyaltyTransaction::create([
            'user_id' => $this->user_id,
            'type' => $type,
            'points' => -$points,
            'balance_after' => $this->points_balance,
            'description' => $description,
            'order_id' => $order ? $order->id : null,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source ? $source->id : null,
        ]);
    }

    /**
     * Check and update the user's loyalty tier based on lifetime points.
     *
     * @return bool Whether the tier was updated
     */
    public function checkAndUpdateTier(): bool
    {
        $appropriateTier = LoyaltyTier::findTierByPoints($this->lifetime_points);
        
        if (!$appropriateTier) {
            return false;
        }

        // If no tier is assigned or a different tier is appropriate
        if (!$this->current_tier_id || $this->current_tier_id != $appropriateTier->id) {
            $this->current_tier_id = $appropriateTier->id;
            $this->tier_updated_at = now();
            $this->save();
            
            // Notify user about tier change
            if ($this->user) {
                // You can add notification logic here
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Calculate points needed to reach the next tier.
     */
    public function getPointsToNextTierAttribute(): ?int
    {
        if (!$this->currentTier) {
            $lowestTier = LoyaltyTier::active()->orderBy('required_points')->first();
            return $lowestTier ? $lowestTier->required_points - $this->lifetime_points : null;
        }
        
        $nextTier = $this->currentTier->getNextTier();
        
        if (!$nextTier) {
            return null;
        }
        
        return $nextTier->required_points - $this->lifetime_points;
    }

    /**
     * Get the percentage progress to the next tier.
     */
    public function getProgressToNextTierAttribute(): ?int
    {
        if (!$this->currentTier) {
            $lowestTier = LoyaltyTier::active()->orderBy('required_points')->first();
            if (!$lowestTier) {
                return null;
            }
            
            return min(100, round(($this->lifetime_points / $lowestTier->required_points) * 100));
        }
        
        $nextTier = $this->currentTier->getNextTier();
        
        if (!$nextTier) {
            return 100;
        }
        
        $pointsInCurrentTier = $this->lifetime_points - $this->currentTier->required_points;
        $pointsNeededForNextTier = $nextTier->required_points - $this->currentTier->required_points;
        
        return min(100, round(($pointsInCurrentTier / $pointsNeededForNextTier) * 100));
    }
} 