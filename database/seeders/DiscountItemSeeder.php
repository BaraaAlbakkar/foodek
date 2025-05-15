<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DiscountItemSeeder extends Seeder
{
    public function run()
    {
        $discountItems = [
            ['discount_id' => 3, 'item_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['discount_id' => 3, 'item_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['discount_id' => 4, 'item_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['discount_id' => 5, 'item_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('discount_items')->insert($discountItems);

        $this->command->info('DiscountItemsSeeder: Realistic static test data inserted successfully!');
    }
}


        // // Fetch all active, new, and valid discounts that have no code (for items only)
        // $validDiscounts = DB::table('discounts')
        //     ->whereIn('status', ['Active', 'New']) // Check if status is 'Active' or 'New'
        //     ->where('start_date', '<=', Carbon::now()) // Discount has started
        //     ->where('end_date', '>=', Carbon::now())  // Discount is still valid
        //     ->whereNull('code') // Ensure the discount has no code (for items only)
        //     ->pluck('id'); // Fetch all valid discount IDs with no code

        // // Prepare the array to insert records
        // $discountItems = [];

        // // Iterate through the valid discount IDs
        // foreach ($validDiscounts as $discount) {
        //     // Apply discount to a random item
        //     $item_id = DB::table('items')->inRandomOrder()->first()->id;

        //     // Add the record to the insert array
        //     $discountItems[] = [
        //         'discount_id' => $discount, // Use the discount ID directly
        //         'item_id' => $item_id, // Apply to a random item
        //     ];
        // }

        // // Insert all discount items in bulk if there are any records to insert
        // if (count($discountItems) > 0) {
        //     DB::table('discount_items')->insert($discountItems);
        // }


