<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'order_id',
        'referral_id',
        'type',
        'amount',
        'balance_after',
        'status',
        'payment_method',
        'payment_reference',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'float',
        'balance_after' => 'float',
    ];

    /**
     * Transaction types
     */
    const TYPE_EARNED = 'earned';
    const TYPE_PAID = 'paid';
    const TYPE_REFUNDED = 'refunded';
    const TYPE_ADJUSTED = 'adjusted';

    /**
     * Transaction statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the affiliate that owns the transaction.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Get the order associated with the transaction.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the referral associated with the transaction.
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    /**
     * Mark the transaction as completed.
     */
    public function markAsCompleted(): bool
    {
        $this->status = self::STATUS_COMPLETED;
        
        return $this->save();
    }

    /**
     * Mark the transaction as cancelled.
     */
    public function markAsCancelled(): bool
    {
        $this->status = self::STATUS_CANCELLED;
        
        return $this->save();
    }

    /**
     * Check if the transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->attributes['status'] === self::STATUS_PENDING;
    }

    /**
     * Check if the transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->attributes['status'] === self::STATUS_COMPLETED;
    }

    /**
     * Check if the transaction is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->attributes['status'] === self::STATUS_CANCELLED;
    }

    /**
     * Get the sign of the transaction (positive or negative).
     */
    public function getSignAttribute(): string
    {
        return $this->amount >= 0 ? '+' : '-';
    }

    /**
     * Get the absolute value of the amount.
     */
    public function getAbsoluteAmountAttribute(): float
    {
        return abs($this->amount);
    }

    /**
     * Get the formatted amount with sign.
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->getSignAttribute() . number_format($this->getAbsoluteAmountAttribute(), 2);
    }

    /**
     * Get the type in a human-readable format.
     */
    public function getTypeTextAttribute(): string
    {
        $types = [
            self::TYPE_EARNED => 'مكتسبة',
            self::TYPE_PAID => 'مدفوعة',
            self::TYPE_REFUNDED => 'مستردة',
            self::TYPE_ADJUSTED => 'معدلة',
        ];

        return $types[$this->attributes['type']] ?? $this->attributes['type'];
    }

    /**
     * Get the status in a human-readable format.
     */
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_COMPLETED => 'مكتملة',
            self::STATUS_CANCELLED => 'ملغاة',
        ];

        return $statuses[$this->attributes['status']] ?? $this->attributes['status'];
    }

    /**
     * Scope a query to only include earned transactions.
     */
    public function scopeEarned($query)
    {
        return $query->where('type', self::TYPE_EARNED);
    }

    /**
     * Scope a query to only include paid transactions.
     */
    public function scopePaid($query)
    {
        return $query->where('type', self::TYPE_PAID);
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
} 