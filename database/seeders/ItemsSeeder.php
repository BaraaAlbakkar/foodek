<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'category_id' => '1',
                'name_en' => 'Margherita Pizza',
                'name_ar' => 'بيتزا مارغريتا',
                'description_en' => 'Classic Italian pizza with tomatoes and mozzarella.',
                'description_ar' => 'بيتزا إيطالية كلاسيكية مع الطماطم والموزاريلا.',
                'price' => 12.99,
                'is_active' => true,
                'image' => 'images/Margherita Pizza.webp',
                'has_options' => false,
            ],
            [
                'category_id' => '1',
                'name_en' => 'Pepperoni Pizza',
                'name_ar' => 'بيتزا بيبروني',
                'description_en' => 'Spicy pepperoni with melted cheese.',
                'description_ar' => 'بيبروني حار مع جبن ذائب.',
                'price' => 14.99,
                'is_active' => true,
                'image' => 'images/Pepperoni Pizza.webp',
                'has_options' => false,
            ],
            [
                'category_id' => '2',
                'name_en' => 'Cheeseburger',
                'name_ar' => 'تشيز برجر',
                'description_en' => 'Delicious beef burger with cheese.',
                'description_ar' => 'برجر لذيذ باللحم مع الجبن.',
                'price' => 9.99,
                'is_active' => true,
                'image' => 'images/Cheeseburger.avif',
                'has_options' => false,
            ],
            [
                'category_id' => '3',
                'name_en' => 'Coca Cola',
                'name_ar' => 'كوكا كولا',
                'description_en' => 'Refreshing cold drink.',
                'description_ar' => 'مشروب بارد منعش.',
                'price' => 1.99,
                'is_active' => true,
                'image' => 'images/Coca Cola.webp',
                'has_options' => false,
            ],
            [
                'category_id' => '4',
                'name_en' => 'Grilled Salmon',
                'name_ar' => 'سلمون مشوي',
                'description_en' => 'Fresh salmon grilled to perfection.',
                'description_ar' => 'سلمون طازج مشوي بإتقان.',
                'price' => 19.99,
                'is_active' => true,
                'image' => 'images/Grilled Salmon.avif',
                'has_options' => false,
            ],
            [
                'category_id' => '5',
                'name_en' => 'Chocolate Cake',
                'name_ar' => 'كعكة الشوكولاتة',
                'description_en' => 'Delicious chocolate cake with creamy frosting.',
                'description_ar' => 'كعكة لذيذة بالشوكولاتة مع كريمة الزينة.',
                'price' => 5.99,
                'is_active' => true,
                'image' => 'images/Chocolate Cake.avif',
                'has_options' => false,
            ],
            [
                'category_id' => '5',
                'name_en' => 'Ice Cream',
                'name_ar' => 'آيس كريم',
                'description_en' => 'Delicious vanilla ice cream with chocolate topping.',
                'description_ar' => 'آيس كريم لذيذ بنكهة الفانيليا مع تغطية الشوكولاتة.',
                'price' => 3.99,
                'is_active' => true,
                'image' => 'images/Ice Cream.webp',
                'has_options' => false,
            ],
    ];

        foreach ($items as $item) {
            Item::create($item); // Directly insert the item using mass assignment
        }
    }
}
