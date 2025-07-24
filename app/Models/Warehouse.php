<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'country_id',
        'address',
        'city',
        'state',
        'postal_code',
        'address_line1',
        'address_line2',
        'phone',
        'email',
        'manager_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the country that owns the warehouse.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the product stocks for the warehouse.
     */
    public function productStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    /**
     * Get the shipments for the warehouse.
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
