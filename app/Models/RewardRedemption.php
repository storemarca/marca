<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class RewardRedemption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'reward_id',
        'points_spent',
        'status',
        'code',
        'redeemed_at',
        'expires_at',
        'order_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'points_spent' => 'integer',
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Redemption statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the user that owns the redemption.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reward that was redeemed.
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(LoyaltyReward::class, 'reward_id');
    }

    /**
     * Get the order associated with the redemption.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Generate a unique redemption code.
     */
    public static function generateUniqueCode(): string
    {
        $code = strtoupper(Str::random(8));
        
        // Ensure the code is unique
        while (self::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }
        
        return $code;
    }

    /**
     * Mark the redemption as completed.
     */
    public function markAsCompleted(?Order $order = null): bool
    {
        $this->status = self::STATUS_COMPLETED;
        $this->redeemed_at = now();
        
        if ($order) {
            $this->order_id = $order->id;
        }
        
        return $this->save();
    }

    /**
     * Mark the redemption as cancelled.
     */
    public function markAsCancelled(): bool
    {
        // Only allow cancellation if the status is pending
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }
        
        $this->status = self::STATUS_CANCELLED;
        
        // Refund the points to the user
        if ($this->user) {
            $loyaltyPoints = UserLoyaltyPoints::where('user_id', $this->user_id)->first();
            
            if ($loyaltyPoints) {
                $loyaltyPoints->addPoints(
                    $this->points_spent,
                    LoyaltyTransaction::TYPE_ADJUSTED,
                    'استرداد نقاط من إلغاء استبدال المكافأة: ' . $this->reward->localized_name,
                    $this
                );
            }
        }
        
        return $this->save();
    }

    /**
     * Check if the redemption is valid for use.
     */
    public function isValid(): bool
    {
        // Check if the status is completed
        if ($this->status !== self::STATUS_COMPLETED) {
            return false;
        }
        
        // Check if the redemption has expired
        if ($this->expires_at && now()->gt($this->expires_at)) {
            return false;
        }
        
        // Check if the redemption has been used in an order
        if ($this->order_id) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the status in a human-readable format.
     */
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_CANCELLED => 'ملغي',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
} 