<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyTier extends Model
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
        'required_points',
        'discount_percentage',
        'free_shipping',
        'points_multiplier',
        'description',
        'description_ar',
        'badge_image',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'required_points' => 'integer',
        'discount_percentage' => 'float',
        'free_shipping' => 'boolean',
        'points_multiplier' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that belong to this tier.
     */
    public function users(): HasMany
    {
        return $this->hasMany(UserLoyaltyPoints::class, 'current_tier_id');
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
     * Scope a query to only include active tiers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get all tiers ordered by required points.
     */
    public static function getAllTiersOrdered()
    {
        return self::active()->orderBy('required_points')->get();
    }

    /**
     * Find the appropriate tier for the given points.
     */
    public static function findTierByPoints(int $points)
    {
        return self::active()
            ->where('required_points', '<=', $points)
            ->orderByDesc('required_points')
            ->first();
    }

    /**
     * Get the next tier after this one.
     */
    public function getNextTier()
    {
        return self::active()
            ->where('required_points', '>', $this->required_points)
            ->orderBy('required_points')
            ->first();
    }

    /**
     * Calculate points needed to reach the next tier.
     */
    public function getPointsToNextTierAttribute(): ?int
    {
        $nextTier = $this->getNextTier();
        
        if (!$nextTier) {
            return null;
        }
        
        return $nextTier->required_points - $this->required_points;
    }
} 