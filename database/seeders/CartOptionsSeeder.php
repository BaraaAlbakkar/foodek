<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Option;

class CartOptionsSeeder extends Seeder
{
    public function run()
    {
        // Fetch carts and options for linking
        $carts = Cart::all();
        $options = Option::all();

        if ($carts->isEmpty() || $options->isEmpty()) {
            $this->command->warn("No carts or options found. Please seed them first.");
            return;
        }
        // Link options to carts (Many-to-Many relationship)
        foreach ($carts as $cart) {
            foreach ($options as $option) {
                $cart->options()->syncWithoutDetaching([
                    $option->id => ['price' => $option->extra_price],
                ]);
            }
        }
    }
}

