<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'price',
        'is_active',
        'image',
        'has_options'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function favoritedBy(){
        return $this->belongsToMany(User::class);
    }

    public function options(){
        return $this->belongsToMany(Option::class);
    }

    public function carts(){
        return $this->hasMany(cart::class);
    }

    public function itemDiscounts(){
        return $this->belongsToMany(Discount::class,'discount_items', 'item_id', 'discount_id');
    }

    public function orderItems(){
        return $this->hasMany(OrderItem::class,'order_items');
    }
}
