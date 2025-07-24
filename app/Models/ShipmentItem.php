<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shipment_id',
        'order_item_id',
        'product_id',
        'quantity',
    ];

    /**
     * Obtener el envío al que pertenece este ítem.
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * Obtener el ítem de pedido asociado.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Obtener el producto asociado.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
