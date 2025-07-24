<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'points',
        'balance_after',
        'source_type',
        'source_id',
        'description',
        'order_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'points' => 'integer',
        'balance_after' => 'integer',
    ];

    /**
     * Transaction types
     */
    const TYPE_EARNED = 'earned';
    const TYPE_REDEEMED = 'redeemed';
    const TYPE_EXPIRED = 'expired';
    const TYPE_ADJUSTED = 'adjusted';
    const TYPE_REFERRAL = 'referral';
    const TYPE_SIGNUP = 'signup';
    const TYPE_BIRTHDAY = 'birthday';
    const TYPE_REVIEW = 'review';

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the source of the transaction (polymorphic).
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include earned points.
     */
    public function scopeEarned($query)
    {
        return $query->where('type', self::TYPE_EARNED);
    }

    /**
     * Scope a query to only include redeemed points.
     */
    public function scopeRedeemed($query)
    {
        return $query->where('type', self::TYPE_REDEEMED);
    }

    /**
     * Scope a query to only include expired points.
     */
    public function scopeExpired($query)
    {
        return $query->where('type', self::TYPE_EXPIRED);
    }

    /**
     * Scope a query to only include adjusted points.
     */
    public function scopeAdjusted($query)
    {
        return $query->where('type', self::TYPE_ADJUSTED);
    }

    /**
     * Get the sign of the transaction (positive or negative).
     */
    public function getSignAttribute(): string
    {
        return $this->points >= 0 ? '+' : '-';
    }

    /**
     * Get the absolute value of points.
     */
    public function getAbsolutePointsAttribute(): int
    {
        return abs($this->points);
    }

    /**
     * Get the formatted points with sign.
     */
    public function getFormattedPointsAttribute(): string
    {
        return $this->sign . $this->absolute_points;
    }

    /**
     * Get the transaction type in a human-readable format.
     */
    public function getTypeTextAttribute(): string
    {
        $types = [
            self::TYPE_EARNED => 'مكتسبة',
            self::TYPE_REDEEMED => 'مستبدلة',
            self::TYPE_EXPIRED => 'منتهية',
            self::TYPE_ADJUSTED => 'معدلة',
            self::TYPE_REFERRAL => 'إحالة',
            self::TYPE_SIGNUP => 'تسجيل',
            self::TYPE_BIRTHDAY => 'عيد ميلاد',
            self::TYPE_REVIEW => 'تقييم',
        ];

        return $types[$this->type] ?? $this->type;
    }
} 