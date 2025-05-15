<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PaymentsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Fetch valid order IDs
        $orderIds = Order::with('user')->pluck('id');


        if ($orderIds->isEmpty()) {
            return; // Avoid inserting payments if there are no orders
        }

        // Static Data (Key Scenarios for Testing)
        $payments = [
            ['order_id' => $orderIds->random(), 'method' => 'visa', 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()],
            ['order_id' => $orderIds->random(), 'method' => 'mastercard', 'status' => 'completed', 'created_at' => now(), 'updated_at' => now()],
            ['order_id' => $orderIds->random(), 'method' => 'cash_on_delivery', 'status' => 'failed', 'created_at' => now(), 'updated_at' => now()],
            ['order_id' => $orderIds->random(), 'method' => 'visa', 'status' => 'refunded', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Generate dynamic payments using Faker
        foreach (range(1, 10) as $i) {
            $payments[] = [
                'order_id' => $orderIds->random(),
                'method' => $faker->randomElement(['visa', 'mastercard', 'cash_on_delivery']),
                'status' => $faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk Insert for Performance
        Payment::insert($payments);
    }
}
