<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'driver_id',
        'status',
        'total_price',
        'delivery_charge',
        'estimated_delivery_time',
        'cancel_reason'
    ];

    public function orderDiscounts(){
        return $this->belongsToMany(Discount::class);
    }

    public function orderOptions(){
        return $this->belongsToMany(Option::class);
    }

    // Each order belongs to a client (user)
    public function client(){
        return $this->belongsTo(User::class, 'client_id');
    }

    // Each order might belong to a driver (user), can be null
    public function driver(){
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    public function payment(){
        return $this->hasOne(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);  // An order can have many reviews
    }
}
