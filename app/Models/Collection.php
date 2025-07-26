<?php
 
 namespace App\Models;
 
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Casts\Attribute;
 
 class Collection extends Model
 {
     use HasFactory;
     
     /**
      * The attributes that are mass assignable.
      *
      * @var array<int, string>
      */
     protected $fillable = [
         'shipment_id',
         'amount',
         'status',
         'collected_by',
         'collected_at',
         'settled_at',
         'notes',
         'receipt_number',
         'receipt_image',
     ];
     
     /**
      * The attributes that should be cast.
      *
      * @var array<string, string>
      */
     protected $casts = [
         'amount' => 'decimal:2',
         'collected_at' => 'datetime',
         'settled_at' => 'datetime',
     ];
     
     /**
      * Get the currency_symbol attribute from the related order's country.
      */
     protected function currencySymbol(): Attribute
     {
         return Attribute::make(
             get: function () {
                 if ($this->shipment && $this->shipment->order && $this->shipment->order->country) {
                     return $this->shipment->order->country->currency_symbol;
                 }
                 return null;
             },
         );
     }
     
     /**
      * Get the shipment that owns the collection.
      */
     public function shipment()
     {
         return $this->belongsTo(Shipment::class);
     }
     
     /**
      * Get the user who collected the payment.
      */
     public function collector()
     {
         return $this->belongsTo(User::class, 'collected_by');
     }
     
     /**
      * Check if the collection is settled.
      */
     public function isSettled()
     {
         return $this->status === 'settled';
     }
     
     /**
      * Check if the collection is pending.
      */
     public function isPending()
     {
         return $this->status === 'pending';
     }
     
     /**
      * Check if the collection is collected but not settled.
      */
     public function isCollectedNotSettled()
     {
         return $this->status === 'collected';
     }
     
     /**
      * Get the order associated with this collection through the shipment.
      */
     public function order()
     {
         return $this->hasOneThrough(Order::class, Shipment::class, 'id', 'id', 'shipment_id', 'order_id');
     }
 }