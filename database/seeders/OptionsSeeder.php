<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Option;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class OptionsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Static options data
        $options = [
            [
                'name_en'     => 'Extra Cheese',
                'name_ar'     => 'جبنة إضافية',
                'extra_price' => 2.00,
                'type'        => 'addition',
                'range'       => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name_en'     => 'Olives',
                'name_ar'     => 'زيتون',
                'extra_price' => 1.50,
                'type'        => 'addition',
                'range'       => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name_en'     => 'Spicy',
                'name_ar'     => 'حار',
                'extra_price' => 0.00,
                'type'        => 'flavor',
                'range'       => json_encode(["Mild Spice", "Medium Spice", "Hot Spice"]),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name_en'     => 'No Onions',
                'name_ar'     => 'بدون بصل',
                'extra_price' => 0.00,
                'type'        => 'removed',
                'range'       => null, // Explicitly set to null
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];



        // Bulk insert options
        if (!empty($options)) {
            DB::transaction(function () use ($options) {
                Option::insert($options);
            });
        }
    }
}

