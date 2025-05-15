<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Item;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run()
    {
        $orderItems = [
            [
                'order_id' => 1,
                'item_id' => 5,
                'quantity' => 2,
                'price' => 50.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 1,
                'item_id' => 7,
                'quantity' => 1,
                'price' => 30.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 2,
                'item_id' => 3,
                'quantity' => 3,
                'price' => 40.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 3,
                'item_id' => 2,
                'quantity' => 1,
                'price' => 60.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_id' => 3,
                'item_id' => 4,
                'quantity' => 2,
                'price' => 25.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];
        // Retrieve orders that have valid clients
        $orders = Order::whereNotNull('client_id')->get();
        $items = Item::all();

        // Ensure items exist before proceeding
        if ($items->isEmpty() || $orders->isEmpty()) {
            return;
        }

        $orderItems = [];

        foreach ($orders as $order) {
            $randomItems = $items->random(rand(1, 5));

            foreach ($randomItems as $item) {
                $orderItems[] = [
                    'order_id' => $order->id,
                    'item_id'  => $item->id,
                    'quantity' => rand(1, 3),
                    'price'    => $item->price ?? 10.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Bulk insert order items
        if (!empty($orderItems)) {
            OrderItem::insert($orderItems);
        }
    }
}
