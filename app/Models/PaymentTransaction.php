<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_gateway_id',
        'transaction_id',
        'amount',
        'fee',
        'currency',
        'status',
        'response_data',
        'notes',
    ];

    protected $casts = [
        'amount' => 'float',
        'fee' => 'float',
        'response_data' => 'array',
    ];

    // Payment statuses
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PARTIALLY_REFUNDED = 'partially_refunded';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }
} 