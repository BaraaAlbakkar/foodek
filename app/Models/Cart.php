<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'item_id',
        'quantity',
        'total_price',
    ];
    public function client(){
        return $this->belongsTo(User::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function options(){
        return $this->belongsToMany(Option::class, 'cart_option')
        ->withPivot('price', 'range')
        ->withTimestamps();;
    }
}
