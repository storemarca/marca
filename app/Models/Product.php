<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Product extends Model
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
        'slug',
        'sku',
        'description',
        'description_ar',
        'category_id',
        'base_price',
        'sale_price',
        'cost',
        'weight',
        'weight_unit',
        'length',
        'width',
        'height',
        'dimension_unit',
        'is_active',
        'is_featured',
        'is_digital',
        'is_virtual',
        'is_backorder',
        'is_preorder',
        'stock_quantity',
        'stock_status',
        'tax_class',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_digital' => 'boolean',
        'is_virtual' => 'boolean',
        'is_backorder' => 'boolean',
        'is_preorder' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the product prices for the product.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Get the product name based on the current locale.
     */
    public function getLocalizedNameAttribute()
    {
        return app()->getLocale() === 'ar' && $this->name_ar
            ? $this->name_ar
            : $this->name;
    }

    /**
     * Get the product description based on the current locale.
     */
    public function getLocalizedDescriptionAttribute()
    {
        return app()->getLocale() === 'ar' && $this->description_ar
            ? $this->description_ar
            : $this->description;
    }

    /**
     * Get the price for a specific country.
     */
    public function getPriceForCountry($countryId)
    {
        $price = $this->prices()->where('country_id', $countryId)->where('is_active', true)->first();
        return $price ? $price->price : $this->base_price;
    }

    /**
     * Get the sale price for a specific country.
     */
    public function getSalePriceForCountry($countryId)
    {
        $price = $this->prices()->where('country_id', $countryId)->where('is_active', true)->first();
        return $price && $price->sale_price ? $price->sale_price : $this->sale_price;
    }

    /**
     * Get the current price (sale price if available, otherwise regular price) for a specific country.
     */
    public function getCurrentPriceForCountry($countryId)
    {
        $salePrice = $this->getSalePriceForCountry($countryId);
        return $salePrice ?: $this->getPriceForCountry($countryId);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include products with prices for a specific country.
     */
    public function scopeHasPriceForCountry($query, $countryId)
    {
        return $query->whereHas('prices', function ($query) use ($countryId) {
            $query->where('country_id', $countryId)
                  ->where('is_active', true);
        });
    }

    /**
     * Scope a query to only include in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($query) {
            $query->where('stock_status', 'in_stock')
                  ->orWhere('is_backorder', true)
                  ->orWhere('is_preorder', true);
        });
    }
}
