<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'user_id',
        'name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'governorate_id',
        'district_id',
        'area_id',
        'is_default',
        'is_default_shipping',
        'is_default_billing',
        'label',
        'delivery_instructions',
        'latitude',
        'longitude',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_default_shipping' => 'boolean',
        'is_default_billing' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
    
    /**
     * Get the customer that owns the address.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    
    /**
     * Get the country that the address belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the governorate that the address belongs to.
     */
    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }
    
    /**
     * Get the district that the address belongs to.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
    
    /**
     * Get the area that the address belongs to.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
    
    /**
     * Get the shipping cost based on the selected governorate, district, and area.
     */
    public function getShippingCostAttribute(): float
    {
        $shippingCost = 0;
        
        if ($this->governorate) {
            $shippingCost += $this->governorate->shipping_cost;
        }
        
        if ($this->district) {
            $shippingCost += $this->district->additional_shipping_cost;
        }
        
        if ($this->area) {
            $shippingCost += $this->area->additional_shipping_cost;
        }
        
        return $shippingCost;
    }
    
    /**
     * Get formatted address.
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = [];
        
        if ($this->address_line1) {
            $parts[] = $this->address_line1;
        }
        
        if ($this->address_line2) {
            $parts[] = $this->address_line2;
        }
        
        $locationParts = [];
        
        if ($this->area) {
            $locationParts[] = $this->area->localized_name;
        }
        
        if ($this->district) {
            $locationParts[] = $this->district->localized_name;
        }
        
        if ($this->governorate) {
            $locationParts[] = $this->governorate->localized_name;
        } elseif ($this->city) {
            $locationParts[] = $this->city;
        }
        
        if ($this->country) {
            $locationParts[] = $this->country->name;
        }
        
        if (!empty($locationParts)) {
            $parts[] = implode(', ', $locationParts);
        }
        
        if ($this->postal_code) {
            $parts[] = $this->postal_code;
        }
        
        return implode(', ', $parts);
    }
}
