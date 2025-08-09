<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'pieces_count',
        'images',
        'videos',
        'colors',
        'sizes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
        'videos' => 'array',
        'colors' => 'array',
        'sizes' => 'array',
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
        'pieces_count' => 'integer',
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
     * Get the product stocks for the product.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    /**
     * Get the product name based on the current locale.
     */
    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->name_ar
            ? $this->name_ar
            : $this->name;
    }

    /**
     * Get the product description based on the current locale.
     */
    public function getLocalizedDescriptionAttribute(): string
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
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include products with prices for a specific country.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $countryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasPriceForCountry($query, $countryId)
    {
        return $query->whereHas('prices', function ($query) use ($countryId) {
            $query->where('country_id', $countryId)
                  ->where('is_active', true);
        });
    }
    
    /**
     * Scope a query to include products visible for a specific country.
     * Combines active status and having prices for the country.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $countryId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisibleInCountry($query, $countryId)
    {
        return $query->active()->hasPriceForCountry($countryId);
    }

    /**
     * Get the countries that the product is available in.
     */
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'product_prices')
                    ->withPivot('price', 'sale_price', 'is_active', 'sale_price_start_date', 'sale_price_end_date')
                    ->where('product_prices.is_active', true)
                    ->distinct();
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

    /**
     * Get the main image of the product.
     * 
     * @return string
     */
    public function getMainImageAttribute(): string
    {
        if (!empty($this->images) && count($this->images) > 0) {
            return $this->images[0];
        }
        
        return asset('images/product-placeholder.svg');
    }
    
    /**
     * Check if the product has images.
     * 
     * @return bool
     */
    public function getHasImagesAttribute(): bool
    {
        return !empty($this->images) && count($this->images) > 0;
    }
    
    /**
     * Get the status attribute based on is_active boolean.
     * Returns 'active' or 'inactive' for use in views.
     * 
     * @return string
     */
    public function getStatusAttribute(): string
    {
        return $this->is_active ? 'active' : 'inactive';
    }
    
    /**
     * Set the status attribute and sync with is_active boolean.
     * Accepts 'active' or 'inactive' and sets is_active accordingly.
     * 
     * @param string $value
     * @return void
     */
    public function setStatusAttribute($value): void
    {
        $this->attributes['is_active'] = ($value === 'active');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Generate a unique SKU from category ID
     * 
     * @param int|null $categoryId
     * @return string
     */
    public static function generateSKUFromCategory($categoryId = null): string
    {
        if (!$categoryId) {
            return 'PRD-' . strtoupper(substr(uniqid(), -6));
        }

        $category = Category::find($categoryId);
        if (!$category) {
            return 'PRD-' . strtoupper(substr(uniqid(), -6));
        }

        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $category->name), 0, 3));
        $count = self::where('category_id', $categoryId)->count() + 1;
        $sku = $prefix . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        while (self::where('sku', $sku)->exists()) {
            $sku = $prefix . '-' . str_pad($count, 5, '0', STR_PAD_LEFT) . strtoupper(substr(uniqid(), -3));
        }

        return $sku;
    }
}
