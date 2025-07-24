<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyReward extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'name_ar',
        'type',
        'points_required',
        'reward_data',
        'description',
        'description_ar',
        'image',
        'is_active',
        'stock',
        'starts_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'points_required' => 'integer',
        'reward_data' => 'json',
        'is_active' => 'boolean',
        'stock' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Reward types
     */
    const TYPE_DISCOUNT = 'discount';
    const TYPE_FREE_PRODUCT = 'free_product';
    const TYPE_GIFT_CARD = 'gift_card';
    const TYPE_FREE_SHIPPING = 'free_shipping';

    /**
     * Get the redemptions for this reward.
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class, 'reward_id');
    }

    /**
     * Get the localized name based on the current locale.
     */
    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name;
    }

    /**
     * Get the localized description based on the current locale.
     */
    public function getLocalizedDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description;
    }

    /**
     * Scope a query to only include active rewards.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) {
                $now = Carbon::now();
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', $now);
            })
            ->where(function ($query) {
                $query->whereNull('stock')
                    ->orWhere('stock', '>', 0);
            });
    }

    /**
     * Check if the reward is available.
     */
    public function isAvailable(): bool
    {
        // Check if reward is active
        if (!$this->is_active) {
            return false;
        }

        // Check if reward has started
        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return false;
        }

        // Check if reward has expired
        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return false;
        }

        // Check if reward is in stock
        if ($this->stock !== null && $this->stock <= 0) {
            return false;
        }

        return true;
    }

    /**
     * Decrement the stock of the reward.
     */
    public function decrementStock(): bool
    {
        if ($this->stock === null) {
            return true;
        }

        if ($this->stock <= 0) {
            return false;
        }

        $this->stock--;
        return $this->save();
    }

    /**
     * Get the reward value based on the type.
     */
    public function getRewardValueAttribute(): string
    {
        switch ($this->type) {
            case self::TYPE_DISCOUNT:
                $value = $this->reward_data['value'] ?? 0;
                $type = $this->reward_data['discount_type'] ?? 'fixed';
                
                if ($type === 'percentage') {
                    return $value . '%';
                } else {
                    return $value . ' ر.س';
                }
                
            case self::TYPE_FREE_PRODUCT:
                $productId = $this->reward_data['product_id'] ?? null;
                $product = Product::find($productId);
                return $product ? $product->name : 'منتج مجاني';
                
            case self::TYPE_GIFT_CARD:
                $value = $this->reward_data['value'] ?? 0;
                return $value . ' ر.س';
                
            case self::TYPE_FREE_SHIPPING:
                return 'شحن مجاني';
                
            default:
                return '';
        }
    }

    /**
     * Get the reward type in a human-readable format.
     */
    public function getTypeTextAttribute(): string
    {
        $types = [
            self::TYPE_DISCOUNT => 'خصم',
            self::TYPE_FREE_PRODUCT => 'منتج مجاني',
            self::TYPE_GIFT_CARD => 'بطاقة هدية',
            self::TYPE_FREE_SHIPPING => 'شحن مجاني',
        ];

        return $types[$this->type] ?? $this->type;
    }
} 