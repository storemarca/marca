<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateLinkStat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'affiliate_link_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city',
        'device_type',
        'is_conversion',
        'order_id',
        'commission_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_conversion' => 'boolean',
        'commission_amount' => 'float',
    ];

    /**
     * Get the affiliate link that owns the stat.
     */
    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(AffiliateLink::class);
    }

    /**
     * Get the order associated with the stat.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Mark the stat as a conversion.
     */
    public function markAsConversion(Order $order, float $commissionAmount): bool
    {
        $this->is_conversion = true;
        $this->order_id = $order->id;
        $this->commission_amount = $commissionAmount;
        
        return $this->save();
    }

    /**
     * Get the device type from the user agent.
     */
    public static function getDeviceType(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);
        
        if (strpos($userAgent, 'mobile') !== false || strpos($userAgent, 'android') !== false || strpos($userAgent, 'iphone') !== false) {
            return 'mobile';
        } elseif (strpos($userAgent, 'tablet') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Scope a query to only include conversions.
     */
    public function scopeConversions($query)
    {
        return $query->where('is_conversion', true);
    }

    /**
     * Scope a query to only include clicks.
     */
    public function scopeClicks($query)
    {
        return $query->where('is_conversion', false);
    }

    /**
     * Scope a query to filter by device type.
     */
    public function scopeByDevice($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope a query to filter by country.
     */
    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }
} 