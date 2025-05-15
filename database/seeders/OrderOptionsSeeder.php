<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderItem;
use App\Models\Option;
use Illuminate\Support\Facades\DB;

class OrderOptionsSeeder extends Seeder
{
    public function run()
    {
        // Fetch all order items and options for linking
    $orderItems = OrderItem::all();
    $options = Option::all();

    // Seed data into the order_options table
    foreach ($orderItems as $orderItem) {
        foreach ($options as $option) {
            // Assuming the 'order_options' table has the columns 'order_item_id', 'option_id', and 'price'
            DB::table('order_options')->insert([
                'order_item_id' => $orderItem->id,
                'option_id' => $option->id,
                'price' => $option->extra_price, // Or another price if applicable
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    }
}

