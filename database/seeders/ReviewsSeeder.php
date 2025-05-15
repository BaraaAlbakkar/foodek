<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Review;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ReviewsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
    $now = Carbon::now();

    // Get clients who have completed orders
    $clients = User::whereHas('clientOrders', function ($query) {
        $query->where('status', 'completed');
    })->pluck('id');

    if ($clients->isEmpty()) {
        $this->command->warn("No clients with completed orders found! Please seed users and orders first.");
        return;
    }

    // Get all completed orders
    $orders = Order::where('status', 'completed')->pluck('id');

    if ($orders->isEmpty()) {
        $this->command->warn("No completed orders found! Please seed completed orders first.");
        return;
    }

    $reviews = [];
    $usedOrderIds = [];

    // Static reviews
    $staticReviews = [
        ['rating' => 5, 'comment' => 'Excellent service! The food arrived hot and fresh.'],
        ['rating' => 4, 'comment' => 'Great taste, but the delivery took longer than expected.'],
        ['rating' => 3, 'comment' => 'Decent meal, but the portion size was a bit small.'],
        ['rating' => 2, 'comment' => 'Food was cold when it arrived. Needs improvement.'],
        ['rating' => 1, 'comment' => 'Horrible experience. The order was wrong and late.'],
    ];

    foreach ($staticReviews as $static) {
        // Ensure we pick a unique order ID
        $orderId = $orders->whereNotIn('id', $usedOrderIds)->random();
        $usedOrderIds[] = $orderId;

        $reviews[] = [
            'order_id'   => $orderId,
            'client_id'  => $clients->random(),
            'rating'     => $static['rating'],
            'comment'    => $static['comment'],
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    // Dynamic reviews
    for ($i = 0; $i < 10; $i++) {
        // Ensure we don't go beyond available orders
        if ($orders->count() <= count($usedOrderIds)) break;

        // Ensure we pick a unique order ID
        $orderId = $orders->whereNotIn('id', $usedOrderIds)->random();
        $usedOrderIds[] = $orderId;

        // Generate random rating and comment
        $rating = $faker->numberBetween(1, 5);
        $foodItem = $faker->randomElement(['pizza', 'burger', 'sushi', 'pasta', 'dessert', 'beverage']);

        $comment = match (true) {
            $rating >= 4 => "I absolutely loved the $foodItem! The flavor was amazing, and the portion was perfect. Highly recommend it to anyone looking for a great meal. Will definitely be coming back for more! #HighlyRecommended",
            $rating == 3 => "The $foodItem was okay. It was decent, but I expected more. Still, not bad overall.",
            default => "I didn't enjoy the $foodItem. The taste was off, and the portion was small. I won’t be ordering unless there is improvement."
        };

        $reviews[] = [
            'order_id'   => $orderId,
            'client_id'  => $clients->random(),
            'rating'     => $rating,
            'comment'    => $comment,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    // Insert reviews into the database
    Review::insert($reviews);
    }
}
