<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_count',
        'user_usage_limit',
        'is_active',
        'starts_at',
        'expires_at',
        'description',
    ];

    protected $casts = [
        'value' => 'float',
        'min_order_amount' => 'float',
        'max_discount_amount' => 'float',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'user_usage_limit' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Coupon types
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENTAGE = 'percentage';

    /**
     * Get the categories associated with the coupon.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_categories');
    }

    /**
     * Get the products associated with the coupon.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_products');
    }

    /**
     * Get the users associated with the coupon.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'coupon_users')
            ->withPivot('usage_count')
            ->withTimestamps();
    }

    /**
     * Check if the coupon is valid.
     */
    public function isValid(): bool
    {
        // Check if coupon is active
        if (!$this->is_active) {
            return false;
        }

        // Check if coupon has reached its usage limit
        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        // Check if coupon has started
        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return false;
        }

        // Check if coupon has expired
        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the coupon is valid for a specific user.
     */
    public function isValidForUser(User $user): bool
    {
        // First check if the coupon is generally valid
        if (!$this->isValid()) {
            return false;
        }

        // If the coupon is restricted to specific users
        if ($this->users()->count() > 0) {
            $userCoupon = $this->users()->where('user_id', $user->id)->first();
            
            // Check if the user is allowed to use this coupon
            if (!$userCoupon) {
                return false;
            }
            
            // Check if the user has reached their usage limit
            if ($userCoupon->pivot->usage_count >= $this->user_usage_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        // Check if the minimum order amount is met
        if ($subtotal < $this->min_order_amount) {
            return 0;
        }

        // Calculate discount based on type
        $discount = 0;
        if ($this->type === self::TYPE_FIXED) {
            $discount = $this->value;
        } elseif ($this->type === self::TYPE_PERCENTAGE) {
            $discount = $subtotal * ($this->value / 100);
            
            // Apply maximum discount amount if set
            if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
                $discount = $this->max_discount_amount;
            }
        }

        // Ensure discount doesn't exceed the subtotal
        return min($discount, $subtotal);
    }

    /**
     * Increment the usage count for this coupon.
     */
    public function incrementUsage(User $user = null): void
    {
        $this->usage_count++;
        $this->save();

        // If a user is provided, increment their usage count too
        if ($user) {
            $this->users()->updateExistingPivot($user->id, [
                'usage_count' => \DB::raw('usage_count + 1'),
            ]);
        }
    }

    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include valid coupons (active, not expired, etc.).
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('usage_limit')
                    ->orWhereRaw('usage_count < usage_limit');
            })
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', Carbon::now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', Carbon::now());
            });
    }
} 