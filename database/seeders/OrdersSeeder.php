<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Static orders (critical test cases)
        $orders = [
            [
                'client_id' => 2,
                'driver_id' => 1,
                'status' => 'Active',
                'total_price' => 150,
                'estimated_delivery_time' => '13:45:00',
                'delivery_charge' => 10,
                'cancel_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 3,
                'driver_id' => 1,
                'status' => 'On Shipment',
                'total_price' => 200,
                'estimated_delivery_time' => '14:30:00',
                'delivery_charge' => 15,
                'cancel_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'client_id' => 3,
                'driver_id' => null,
                'status' => 'Active', // Initially set to Active since no driver
                'total_price' => 120,
                'estimated_delivery_time' => '15:00:00',
                'delivery_charge' => 5,
                'cancel_reason' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Fetch real client and driver IDs
        $clients = User::whereHas('roles', function ($query) {
            $query->where('name_en', 'client');
        })->pluck('id');

        $drivers = User::whereHas('roles', function ($query) {
            $query->where('name_en', 'driver');
        })->pluck('id');

        if ($clients->isEmpty()) {
            return; // Avoid inserting invalid orders
        }

        // Generate dynamic orders using Faker
        foreach (range(1, 5) as $i) {
            $timestamp = Carbon::now();

            // Randomly set a status, default to 'Active' if null
            $status = $faker->randomElement(['Active', 'On Shipment', 'Completed', 'canceled', 'Active']);

            // Initialize the order array
            $order = [
                'client_id' => $clients->random(),
                'driver_id' => $drivers->isNotEmpty() ? $drivers->random() : null,
                'status' => $status,
                'total_price' => $faker->randomFloat(2, 10, 100), // More realistic prices
                'estimated_delivery_time' => $timestamp->addMinutes(rand(30, 120))->format('H:i:s'),
                'delivery_charge' => rand(0, 20),
                'cancel_reason' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            // If the status is 'Cancelled', add a cancel reason
            if ($status === 'canceled') {
                $order['cancel_reason'] = $faker->randomElement([
                    'Customer changed their mind',
                    'Item out of stock',
                    'Delivery address is incorrect',
                    'Payment issue',
                    'Customer requested cancellation',
                ]);
            }

            // If no driver is assigned, set the status to 'Active'
            if ($order['driver_id'] === null) {
                $order['status'] = 'Active';
            }

            // Add the order to the orders array
            $orders[] = $order;
        }

        // Bulk insert for speed
        Order::insert($orders);
    }
}
