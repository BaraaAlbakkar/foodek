<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoryDiscountSeeder extends Seeder
{
    public function run()
    {
        // Ensure categories and discounts exist before seeding
        $categoryDiscounts = [
            ['discount_id' => 1, 'category_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Black Friday Sale on Pizza
            ['discount_id' => 1, 'category_id' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Black Friday Sale on Burgers
            ['discount_id' => 2, 'category_id' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // New Year Feast on Burgers
            ['discount_id' => 3, 'category_id' => 1, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Pizza Lovers Discount on Pizza
            ['discount_id' => 4, 'category_id' => 4, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Healthy Eating Discount on Sea Food
            ['discount_id' => 7, 'category_id' => 2, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()], // Burger Bonanza on Burgers
        ];

        DB::table('discount_categories')->insert($categoryDiscounts);

        $this->command->info('CategoryDiscountSeeder: Realistic static test data inserted successfully!');
    }
}
//     public function run()
//     {


//         // Fetch all active or new discounts that are valid (not cancelled)
//         $validDiscounts = DB::table('discounts')
//             ->whereIn('status', ['Active', 'New']) // Filter by 'Active' or 'New' status
//             ->where('start_date', '<=', Carbon::now()) // Discount has started
//             ->where('end_date', '>=', Carbon::now()) // Discount is still valid
//             ->whereNull('code') // Ensure the discount has no code (for categories only)
//             ->pluck('id'); // Only retrieve the discount ids

//         if ($validDiscounts->isEmpty()) {
//             return; // Exit early if no valid discounts
//         }

//         // Fetch categories that can be used for discounts
//         $categories = DB::table('categories')->pluck('id'); // Fetch all category ids

//         // Create records for valid discounts and random categories
//         $categoryDiscounts = [];
//         foreach ($validDiscounts as $discountId) {
//             foreach ($categories as $categoryId) {
//                 $categoryDiscounts[] = [
//                     'discount_id' => $discountId,
//                     'category_id' => $categoryId,
//                 ];
//             }
//         }

//         // Insert all category discounts in bulk
//         DB::table('category_discounts')->insert($categoryDiscounts);
//     }
// }
