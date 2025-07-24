<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountUsage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'discount_id',
        'user_id',
        'order_id',
        'discount_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the discount associated with the usage.
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Get the user associated with the usage.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the usage.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
} 