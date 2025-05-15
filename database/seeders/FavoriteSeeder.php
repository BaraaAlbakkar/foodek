<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run()
    {
        $now = now();

    // Static data
    $staticFavorites = [
        ['client_id' => 1, 'item_id' => 1, 'created_at' => $now, 'updated_at' => $now],
        ['client_id' => 1, 'item_id' => 2, 'created_at' => $now, 'updated_at' => $now],
        ['client_id' => 2, 'item_id' => 1, 'created_at' => $now, 'updated_at' => $now],
    ];

    $existingPairs = [];
    foreach ($staticFavorites as $fav) {
        $existingPairs["{$fav['client_id']}-{$fav['item_id']}"] = true;
    }

    $clients = User::all()->keyBy('id');
    $items = Item::all()->keyBy('id');
    $dynamicFavorites = [];

    foreach ($clients as $client) {
        $randomItems = $items->random(rand(1, 5));

        foreach ($randomItems as $item) {
            $key = "{$client->id}-{$item->id}";
            if (!isset($existingPairs[$key])) {
                $dynamicFavorites[] = [
                    'client_id' => $client->id,
                    'item_id'   => $item->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $existingPairs[$key] = true;
            }
        }
    }

    Favorite::insert(array_merge($staticFavorites, $dynamicFavorites));
    }
}
