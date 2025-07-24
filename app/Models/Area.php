<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'district_id',
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
     * Get the district that owns the area.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the governorate through the district.
     */
    public function governorate(): BelongsTo
    {
        return $this->district->governorate();
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
     * Get the total shipping cost including district and governorate costs.
     */
    public function getTotalShippingCostAttribute(): float
    {
        return $this->district->total_shipping_cost + $this->additional_shipping_cost;
    }
} 