<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Option;
use Illuminate\Support\Facades\DB;

class ItemOptionsSeeder extends Seeder
{
    public function run()
    {
        
        $items = Item::all();
        $options = Option::all();

        if ($items->isEmpty() || $options->isEmpty()) {
            $this->command->warn("No items or options found. Please seed them first.");
            return;
        }

        // Clear previous data (Optional)
        DB::table('item_options')->truncate();

        foreach ($items as $item) {
            $randomOptions = $options->random(rand(1, 2)); // Each item gets 1 to 3 options
            foreach ($randomOptions as $option) {
                DB::table('item_options')->insert([
                    'item_id' => $item->id,
                    'option_id' => $option->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Item options seeded successfully!');
    }
}

