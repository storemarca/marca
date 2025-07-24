<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
        'status',
        'commission_rate',
        'balance',
        'lifetime_earnings',
        'rejection_reason',
        'payment_details',
        'website',
        'social_media',
        'marketing_methods',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'commission_rate' => 'float',
        'balance' => 'float',
        'lifetime_earnings' => 'float',
        'approved_at' => 'datetime',
    ];

    /**
     * Affiliate statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Get the user that owns the affiliate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referrals for the affiliate.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Get the commission transactions for the affiliate.
     */
    public function commissionTransactions(): HasMany
    {
        return $this->hasMany(CommissionTransaction::class);
    }

    /**
     * Get the withdrawal requests for the affiliate.
     */
    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    /**
     * Get the affiliate links for the affiliate.
     */
    public function affiliateLinks(): HasMany
    {
        return $this->hasMany(AffiliateLink::class);
    }

    /**
     * Generate a unique affiliate code.
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
     * Approve the affiliate.
     */
    public function approve(): bool
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_at = now();
        
        return $this->save();
    }

    /**
     * Reject the affiliate.
     */
    public function reject(string $reason = null): bool
    {
        $this->status = self::STATUS_REJECTED;
        $this->rejection_reason = $reason;
        
        return $this->save();
    }

    /**
     * Suspend the affiliate.
     */
    public function suspend(string $reason = null): bool
    {
        $this->status = self::STATUS_SUSPENDED;
        $this->rejection_reason = $reason;
        
        return $this->save();
    }

    /**
     * Check if the affiliate is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the affiliate is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the affiliate is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if the affiliate is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Add commission to the affiliate's balance.
     */
    public function addCommission(float $amount, string $type, ?Order $order = null, ?Referral $referral = null, ?string $notes = null): CommissionTransaction
    {
        // Update affiliate balance
        $this->balance += $amount;
        $this->lifetime_earnings += $amount;
        $this->save();

        // Create transaction record
        return CommissionTransaction::create([
            'affiliate_id' => $this->id,
            'order_id' => $order ? $order->id : null,
            'referral_id' => $referral ? $referral->id : null,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $this->balance,
            'status' => 'completed',
            'notes' => $notes,
        ]);
    }

    /**
     * Deduct commission from the affiliate's balance.
     */
    public function deductCommission(float $amount, string $type, ?string $notes = null): ?CommissionTransaction
    {
        // Ensure amount is positive for deduction
        $amount = abs($amount);

        // Check if there's enough balance
        if ($this->balance < $amount) {
            return null;
        }

        // Update affiliate balance
        $this->balance -= $amount;
        $this->save();

        // Create transaction record
        return CommissionTransaction::create([
            'affiliate_id' => $this->id,
            'type' => $type,
            'amount' => -$amount,
            'balance_after' => $this->balance,
            'status' => 'completed',
            'notes' => $notes,
        ]);
    }

    /**
     * Get the status in a human-readable format.
     */
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'مفعل',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_SUSPENDED => 'معلق',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Scope a query to only include approved affiliates.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include pending affiliates.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
} 