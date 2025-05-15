<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $table = 'options';

    protected $fillable = [
        'name_en',
        'name_ar',
        'type',
        'range',
        'extra_price',
    ];

    public function items(){
        return $this->belongsToMany(Item::class);
    }

    public function carts(){
        return $this->belongsToMany(Cart::class);
    }

    public function orderItems(){
    return $this->belongsToMany(OrderItem::class, 'order_options')
        ->withPivot('price')
        ->withTimestamps();
    }
}
//
