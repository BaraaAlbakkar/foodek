<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'client_id',
        'rating',
        'comment'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);  // A review belongs to one order
    }

    public function user()
    {
        return $this->belongsTo(User::class);  // A review belongs to one user
    }
}
