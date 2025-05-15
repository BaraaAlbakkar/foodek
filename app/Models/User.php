<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birth_date',
        'phone_number',
        'picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(){
        return $this->belongsToMany(Role::class);
    }

    public function favorites(){
    return $this->belongsToMany(Item::class, 'favorites', 'client_id', 'item_id')->withTimestamps();
    }

    public function cartItems(){
        return $this->hasMany(Cart::class);
    }

    public function addresses(){
        return $this->hasMany(Address::class);
    }

    // public function notifications(){
    //     return $this->morphMany(Notification::class, 'notifiable');
    // }

    // Orders where this user is the client
    public function clientOrders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    // Orders where this user is the driver
    public function driverOrders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);  // A user can have many reviews
    }
}
