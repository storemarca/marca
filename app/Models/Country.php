<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
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
        'currency_code',
        'currency_symbol',
        'tax_rate',
        'is_active',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the warehouses for the country.
     */
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
    
    /**
     * Get the customers who have this country as their default.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'default_country_id');
    }
    
    /**
     * Get the addresses in this country.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    
    /**
     * Get the orders placed in this country.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    
    /**
     * Get the suppliers in this country.
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }
}
