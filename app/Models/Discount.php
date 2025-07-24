<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit_per_user',
        'usage_limit_total',
        'starts_at',
        'ends_at',
        'is_active',
        'applies_to',
        'product_id',
        'category_id',
        'country_id',
        'conditions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit_per_user' => 'integer',
        'usage_limit_total' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'conditions' => 'array',
    ];

    /**
     * Get the product associated with the discount.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the category associated with the discount.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the country associated with the discount.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the discount usages.
     */
    public function usages()
    {
        return $this->hasMany(DiscountUsage::class);
    }

    /**
     * Scope a query to only include active discounts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();
        
        return $query->where('is_active', true)
            ->where(function ($query) use ($now) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now);
            });
    }

    /**
     * Scope a query to only include discounts applicable to a specific product.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $productId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForProduct(Builder $query, $productId): Builder
    {
        $product = Product::findOrFail($productId);
        
        return $query->where(function ($query) use ($productId, $product) {
            $query->where('applies_to', 'all')
                ->orWhere(function ($query) use ($productId) {
                    $query->where('applies_to', 'product')
                        ->where('product_id', $productId);
                })
                ->orWhere(function ($query) use ($product) {
                    $query->where('applies_to', 'category')
                        ->where('category_id', $product->category_id);
                });
        });
    }

    /**
     * Scope a query to only include discounts applicable to a specific country.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $countryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCountry(Builder $query, $countryId): Builder
    {
        return $query->where(function ($query) use ($countryId) {
            $query->whereNull('country_id')
                ->orWhere('country_id', $countryId);
        });
    }

    /**
     * Check if the discount is valid for a user.
     *
     * @param  \App\Models\User|null  $user
     * @return bool
     */
    public function isValidForUser(?User $user): bool
    {
        if (!$user) {
            return true;
        }
        
        if ($this->usage_limit_per_user > 0) {
            $userUsageCount = $this->usages()
                ->where('user_id', $user->id)
                ->count();
                
            if ($userUsageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if the discount has reached its total usage limit.
     *
     * @return bool
     */
    public function hasReachedUsageLimit(): bool
    {
        if ($this->usage_limit_total > 0) {
            $totalUsageCount = $this->usages()->count();
            
            if ($totalUsageCount >= $this->usage_limit_total) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     *
     * @param  float  $subtotal
     * @return float
     */
    public function calculateDiscountAmount(float $subtotal): float
    {
        if ($this->min_order_amount > 0 && $subtotal < $this->min_order_amount) {
            return 0;
        }
        
        $discountAmount = 0;
        
        if ($this->type === 'percentage') {
            $discountAmount = $subtotal * ($this->value / 100);
        } elseif ($this->type === 'fixed') {
            $discountAmount = $this->value;
        }
        
        if ($this->max_discount_amount > 0 && $discountAmount > $this->max_discount_amount) {
            $discountAmount = $this->max_discount_amount;
        }
        
        return $discountAmount;
    }
} 