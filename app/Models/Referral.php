<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'referral_code',
        'status',
        'source',
        'ip_address',
        'user_agent',
        'converted_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'converted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Referral statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONVERTED = 'converted';
    const STATUS_EXPIRED = 'expired';

    /**
     * Get the affiliate that owns the referral.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Get the referred user.
     */
    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * Get the commission transactions for the referral.
     */
    public function commissionTransactions(): HasMany
    {
        return $this->hasMany(CommissionTransaction::class);
    }

    /**
     * Mark the referral as converted.
     */
    public function markAsConverted(User $user): bool
    {
        $this->referred_user_id = $user->id;
        $this->status = self::STATUS_CONVERTED;
        $this->converted_at = now();
        
        return $this->save();
    }

    /**
     * Mark the referral as expired.
     */
    public function markAsExpired(): bool
    {
        $this->status = self::STATUS_EXPIRED;
        
        return $this->save();
    }

    /**
     * Check if the referral is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the referral is converted.
     */
    public function isConverted(): bool
    {
        return $this->status === self::STATUS_CONVERTED;
    }

    /**
     * Check if the referral is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * Check if the referral has expired based on the expiration date.
     */
    public function hasExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    /**
     * Get the status in a human-readable format.
     */
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_CONVERTED => 'تم التحويل',
            self::STATUS_EXPIRED => 'منتهي الصلاحية',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Scope a query to only include pending referrals.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include converted referrals.
     */
    public function scopeConverted($query)
    {
        return $query->where('status', self::STATUS_CONVERTED);
    }

    /**
     * Scope a query to only include expired referrals.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }
} 