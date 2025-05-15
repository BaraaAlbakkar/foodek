<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'totle_price'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function options()
{
    return $this->belongsToMany(Option::class, 'order_options')
                ->withPivot('price')
                ->withTimestamps();
}
}
