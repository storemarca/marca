<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'base_cost',
        'is_active',
        'description',
        'weight_based',
        'cost_per_kg',
        'free_shipping_threshold',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'base_cost' => 'decimal:2',
        'weight_based' => 'boolean',
        'cost_per_kg' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
    ];

    /**
     * Get the price for this shipping method.
     *
     * @return float
     */
    public function getPriceAttribute()
    {
        return $this->base_cost;
    }

    /**
     * Get orders that used this shipping method.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the countries this shipping method is available in.
     */
    public function countries()
    {
        return $this->belongsToMany(Country::class)
            ->withPivot('cost', 'is_available')
            ->withTimestamps();
    }
} 