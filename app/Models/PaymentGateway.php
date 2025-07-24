<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'config',
        'logo',
        'is_active',
        'is_default',
        'fee_percentage',
        'fee_fixed',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'fee_percentage' => 'float',
        'fee_fixed' => 'float',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function calculateFee(float $amount): float
    {
        return ($amount * $this->fee_percentage / 100) + $this->fee_fixed;
    }
} 