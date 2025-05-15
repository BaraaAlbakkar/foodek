<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'image',
        'is_active',
    ];

    public function items(){
        return $this->hasMany(Item::class);
    }

    public function categoryDiscounts(){
        return $this->belongsToMany(Discount::class,'discount_categories');
    }
}
