<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class  DiscountOrderSeeder extends Seeder
{
    public function run()
    {
        $validDiscounts = DB::table('discounts')
        ->whereIn('status', ['Active', 'New'])
        ->where('start_date', '<=', Carbon::now())
        ->where('end_date', '>=', Carbon::now())
        ->whereNotNull('code')
        ->pluck('id');

    $orders = DB::table('orders')->pluck('id');

    $discountOrders = [];

    // Shuffle orders to ensure uniqueness
    $shuffledOrders = $orders->shuffle();
    $i = 0;

    foreach ($validDiscounts as $discount) {
        // Ensure we don't exceed the number of available orders
        if ($i >= $shuffledOrders->count()) {
            break;
        }

        $discountOrders[] = [
            'discount_id' => $discount,
            'order_id' => $shuffledOrders[$i],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $i++;
    }

    if (count($discountOrders) > 0) {
        DB::table('discount_orders')->insert($discountOrders);
        $this->command->info(count($discountOrders) . ' order discounts inserted successfully.');
    } else {
        $this->command->warn('No valid discounts or orders found.');
    }
    }
}
