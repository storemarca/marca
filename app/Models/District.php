<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'governorate_id',
        'name',
        'name_ar',
        'code',
        'additional_shipping_cost',
        'is_active',
    ];

    protected $casts = [
        'additional_shipping_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the governorate that owns the district.
     */
    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Get the areas for the district.
     */
    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Get the localized name based on current locale.
     */
    public function getLocalizedNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    /**
     * Get the total shipping cost including the governorate's cost.
     */
    public function getTotalShippingCostAttribute(): float
    {
        return $this->governorate->shipping_cost + $this->additional_shipping_cost;
    }
} 