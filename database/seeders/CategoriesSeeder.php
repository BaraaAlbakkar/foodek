<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_en' => 'Pizza',
                'name_ar' => 'بيتزا',
                'image' => 'images/Pizza.jpg',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'name_en' => 'Burger',
                'name_ar' => 'برجر',
                'image' => 'images/burger.jpg',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'name_en' => 'Drinks',
                'name_ar' => 'مشروبات',
                'image' => 'images/Drinks.jpg',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'name_en' => 'Sea Food',
                'name_ar' => 'المأكولات البحرية',
                'image' => 'images/Sea Food.jpg',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'name_en' => 'Dessert',
                'name_ar' => 'حلويات',
                'image' => 'images/Dessert.avif',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert into database
        DB::table('categories')->insert($categories);
    }
}
