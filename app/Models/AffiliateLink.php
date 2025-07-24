<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AffiliateLink extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_id',
        'name',
        'slug',
        'target_type',
        'target_id',
        'custom_url',
        'clicks',
        'conversions',
        'earnings',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'target_id' => 'integer',
        'clicks' => 'integer',
        'conversions' => 'integer',
        'earnings' => 'float',
        'is_active' => 'boolean',
    ];

    /**
     * Target types
     */
    const TARGET_TYPE_PRODUCT = 'product';
    const TARGET_TYPE_CATEGORY = 'category';
    const TARGET_TYPE_PAGE = 'page';
    const TARGET_TYPE_CUSTOM = 'custom';

    /**
     * Get the affiliate that owns the link.
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Get the link statistics.
     */
    public function stats(): HasMany
    {
        return $this->hasMany(AffiliateLinkStat::class);
    }

    /**
     * Get the target product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'target_id')
            ->when($this->target_type === self::TARGET_TYPE_PRODUCT);
    }

    /**
     * Get the target category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'target_id')
            ->when($this->target_type === self::TARGET_TYPE_CATEGORY);
    }

    /**
     * Generate a unique slug for the affiliate link.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        
        // If the slug is empty, generate a random one
        if (empty($slug)) {
            $slug = Str::random(8);
        }
        
        // Ensure the slug is unique
        $count = 1;
        $originalSlug = $slug;
        
        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        
        return $slug;
    }

    /**
     * Get the full URL for the affiliate link.
     */
    public function getFullUrlAttribute(): string
    {
        $baseUrl = config('app.url');
        
        switch ($this->target_type) {
            case self::TARGET_TYPE_PRODUCT:
                $targetUrl = route('user.products.show', ['product' => $this->product->slug], false);
                break;
                
            case self::TARGET_TYPE_CATEGORY:
                $targetUrl = route('user.products.index', ['category' => $this->category->slug], false);
                break;
                
            case self::TARGET_TYPE_PAGE:
                $targetUrl = '/' . $this->target_id; // Assuming target_id is the page slug
                break;
                
            case self::TARGET_TYPE_CUSTOM:
                $targetUrl = $this->custom_url;
                break;
                
            default:
                $targetUrl = '/';
        }
        
        // Add affiliate code to the URL
        $separator = parse_url($targetUrl, PHP_URL_QUERY) ? '&' : '?';
        $targetUrl .= $separator . 'ref=' . $this->affiliate->code;
        
        // Add the tracking slug
        return $baseUrl . '/go/' . $this->slug;
    }

    /**
     * Get the target URL for the affiliate link.
     */
    public function getTargetUrlAttribute(): string
    {
        switch ($this->target_type) {
            case self::TARGET_TYPE_PRODUCT:
                return route('user.products.show', ['product' => $this->product->slug]);
                
            case self::TARGET_TYPE_CATEGORY:
                return route('user.products.index', ['category' => $this->category->slug]);
                
            case self::TARGET_TYPE_PAGE:
                return url('/' . $this->target_id); // Assuming target_id is the page slug
                
            case self::TARGET_TYPE_CUSTOM:
                return $this->custom_url;
                
            default:
                return url('/');
        }
    }

    /**
     * Get the target name for the affiliate link.
     */
    public function getTargetNameAttribute(): string
    {
        switch ($this->target_type) {
            case self::TARGET_TYPE_PRODUCT:
                return $this->product->name ?? 'منتج غير موجود';
                
            case self::TARGET_TYPE_CATEGORY:
                return $this->category->name ?? 'تصنيف غير موجود';
                
            case self::TARGET_TYPE_PAGE:
                return 'صفحة #' . $this->target_id;
                
            case self::TARGET_TYPE_CUSTOM:
                return 'رابط مخصص';
                
            default:
                return 'غير معروف';
        }
    }

    /**
     * Get the conversion rate for the affiliate link.
     */
    public function getConversionRateAttribute(): float
    {
        if ($this->clicks === 0) {
            return 0;
        }
        
        return round(($this->conversions / $this->clicks) * 100, 2);
    }

    /**
     * Get the earnings per click for the affiliate link.
     */
    public function getEpcAttribute(): float
    {
        if ($this->clicks === 0) {
            return 0;
        }
        
        return round($this->earnings / $this->clicks, 2);
    }

    /**
     * Increment the click count for the affiliate link.
     */
    public function incrementClicks(): bool
    {
        $this->clicks++;
        
        return $this->save();
    }

    /**
     * Increment the conversion count and earnings for the affiliate link.
     */
    public function incrementConversions(float $earnings): bool
    {
        $this->conversions++;
        $this->earnings += $earnings;
        
        return $this->save();
    }

    /**
     * Get the target type in a human-readable format.
     */
    public function getTargetTypeTextAttribute(): string
    {
        $types = [
            self::TARGET_TYPE_PRODUCT => 'منتج',
            self::TARGET_TYPE_CATEGORY => 'تصنيف',
            self::TARGET_TYPE_PAGE => 'صفحة',
            self::TARGET_TYPE_CUSTOM => 'مخصص',
        ];

        return $types[$this->target_type] ?? $this->target_type;
    }

    /**
     * Scope a query to only include active affiliate links.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 