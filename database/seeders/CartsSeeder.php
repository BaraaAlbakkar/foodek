<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class CartsSeeder extends Seeder
{
    public function run()
    {
        // Retrieve only clients by role name
        $clients = User::whereHas('roles', function ($query) {
            $query->where('name_en', 'client');
        })->pluck('id');

        // Get items with price and id
        $items = Item::select('id', 'price')->get();

        // Stop if no clients or no items
        if ($clients->isEmpty() || $items->isEmpty()) {
            return;
        }

        $carts = [];

        foreach ($clients as $client_id) {
            // Get unique random items (1 to 3) per client
            $randomItems = $items->shuffle()->take(rand(1, min(3, $items->count())));

            foreach ($randomItems as $item) {
                $quantity = rand(1, 5);
                $carts[] = [
                    'client_id'   => $client_id,
                    'item_id'     => $item->id,
                    'quantity'    => $quantity,
                    'total_price' => ($item->price ?? 10.00) * $quantity,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        // Bulk insert into carts table
        if (!empty($carts)) {
            DB::table('carts')->insert($carts);
        }

        // Optional: output how many were inserted
        $this->command->info('Seeded ' . count($carts) . ' cart items.');
    }
}
