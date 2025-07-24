<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'amount',
        'status',
        'payment_method',
        'payment_details',
        'rejection_reason',
        'transaction_reference',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'processed_at' => 'datetime',
    ];

    /**
     * Withdrawal request statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';

    /**
     * Get the affiliate that owns the withdrawal request.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Approve the withdrawal request.
     */
    public function approve(): bool
    {
        $this->status = self::STATUS_APPROVED;
        
        return $this->save();
    }

    /**
     * Reject the withdrawal request.
     */
    public function reject(string $reason = null): bool
    {
        $this->status = self::STATUS_REJECTED;
        $this->rejection_reason = $reason;
        
        // Refund the amount to the affiliate's balance
        $this->affiliate->addCommission(
            $this->amount,
            CommissionTransaction::TYPE_ADJUSTED,
            null,
            null,
            'استرداد المبلغ من طلب السحب المرفوض #' . $this->getAttribute('id')
        );
        
        return $this->save();
    }

    /**
     * Mark the withdrawal request as paid.
     */
    public function markAsPaid(string $transactionReference = null): bool
    {
        $this->status = self::STATUS_PAID;
        $this->transaction_reference = $transactionReference;
        $this->processed_at = now();
        
        return $this->save();
    }

    /**
     * Check if the withdrawal request is pending.
     */
    public function isPending(): bool
    {
        return $this->getAttribute('status') === self::STATUS_PENDING;
    }

    /**
     * Check if the withdrawal request is approved.
     */
    public function isApproved(): bool
    {
        return $this->getAttribute('status') === self::STATUS_APPROVED;
    }

    /**
     * Check if the withdrawal request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->getAttribute('status') === self::STATUS_REJECTED;
    }

    /**
     * Check if the withdrawal request is paid.
     */
    public function isPaid(): bool
    {
        return $this->getAttribute('status') === self::STATUS_PAID;
    }

    /**
     * Get the status in a human-readable format.
     */
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_PAID => 'مدفوع',
        ];

        return $statuses[$this->getAttribute('status')] ?? $this->getAttribute('status');
    }

    /**
     * Scope a query to only include pending withdrawal requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved withdrawal requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected withdrawal requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include paid withdrawal requests.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
} 