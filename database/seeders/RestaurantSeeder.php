<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class RestaurantSeeder extends Seeder
{
    public function run()
    {
        DB::table('restaurants')->insert([
            'name' => 'FoodTek',
            'description' => 'A modern tech-driven restaurant serving delicious meals.',
            'address' => '123 Street, Amman City, TX 75001',
            'phone' => '0787229760',
            'email' => 'foodtek00@gmail.com',
            'status' => 'open',
            'logo' => 'logos/foodtek.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
