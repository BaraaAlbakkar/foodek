<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discount;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class DiscountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $discounts = [];

        // Critical test cases (Static discounts)
        $staticDiscounts = [
            [
                'title_en' => 'Black Friday Sale - Huge Discounts on All Products!',
                'title_ar' => 'تخفيضات الجمعة السوداء - خصومات ضخمة على جميع المنتجات!',
                'description_en' => 'Massive discounts on electronics, clothing, and more! Don\'t miss out on the biggest sale of the year.',
                'description_ar' => 'خصومات ضخمة ! لا تفوت أكبر تخفيضات في العام.',
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays(10)->toDateString(),
                'limit_amount' => 100,
                'image' => 'black_friday.jpg',
                'code' => 'BF2025',
                'percentage' => 50,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_en' => 'New Year Feast - Start the Year with Delicious Offers!',
                'title_ar' => 'عروض رأس السنة - ابدأ العام بعروض لذيذة!',
                'description_en' => 'Celebrate the new year with amazing discounts on select food items. Limited time offer!',
                'description_ar' => 'احتفل برأس السنة مع خصومات رائعة على بعض الأطعمة. عرض محدود!',
                'start_date' => now()->addDays(5)->toDateString(),
                'end_date' => now()->addDays(20)->toDateString(),
                'limit_amount' => 500,
                'image' => 'new_year_feast.jpg',
                'code' => 'NY2025',
                'percentage' => 30,
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_en' => 'Pizza Lovers - Special Discount on All Pizzas!',
                'title_ar' => 'عشاق البيتزا - خصم خاص على جميع أنواع البيتزا!',
                'description_en' => 'Enjoy a 20% discount on all pizzas. From classic Margherita to gourmet options, we have it all!',
                'description_ar' => 'استمتع بخصم 20% على جميع أنواع البيتزا. من مارغريتا الكلاسيكية إلى الخيارات الفاخرة!',
                'start_date' => '2025-05-01',
                'end_date' => '2025-05-15',
                'limit_amount' => 300,
                'image' => 'pizza_offer.jpg',
                'code' => 'PIZZA20',
                'percentage' => 20,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_en' => 'Healthy Eating - Special Discounts on Organic Products!',
                'title_ar' => 'تناول طعام صحي - خصومات خاصة على المنتجات العضوية!',
                'description_en' => 'Get 15% off on all organic food items, including fruits, vegetables, and healthy snacks.',
                'description_ar' => 'احصل على خصم 15% على جميع المنتجات العضوية، بما في ذلك الفواكه والخضروات والوجبات الخفيفة الصحية.',
                'start_date' => '2025-04-15',
                'end_date' => '2025-04-30',
                'limit_amount' => 200,
                'image' => 'organic_food_offer.jpg',
                'code' => null,
                'percentage' => 15,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_en' => 'Summer BBQ Bonanza - Get Ready for Grilling!',
                'title_ar' => 'عرض شواء الصيف - استعد للشوي!',
                'description_en' => 'Save 25% on BBQ essentials including meats, sauces, and grilling tools. Perfect for your summer BBQ!',
                'description_ar' => 'وفر 25% على مستلزمات الشواء بما في ذلك اللحوم والصلصات وأدوات الشواء. مثالي لشواء الصيف!',
                'start_date' => '2025-06-01',
                'end_date' => '2025-06-30',
                'limit_amount' => 150,
                'image' => 'bbq_offer.jpg',
                'code' => 'BBQ25',
                'percentage' => 25,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_en' => 'Dessert Delights - Sweet Deals Just for You!',
                'title_ar' => 'لذائذ الحلويات - عروض حلوة فقط لك!',
                'description_en' => 'Satisfy your sweet tooth with up to 30% off on all desserts including cakes, pastries, and ice creams.',
                'description_ar' => 'أرضي أسنانك الحلوة مع خصم يصل إلى 30% على جميع الحلويات بما في ذلك الكعك والمعجنات والآيس كريم.',
                'start_date' => '2025-07-01',
                'end_date' => '2025-07-15',
                'limit_amount' => 250,
                'image' => 'dessert_deal.jpg',
                'code' => null,
                'percentage' => 30,
                'status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_en' => 'Burger Bonanza - Taste the Savings!',
                'title_ar' => 'عرض البرجر - تذوق التوفير!',
                'description_en' => 'Get 10% off on all cheese burgers. Perfect for your weekend cravings.',
                'description_ar' => 'احصل على خصم 10% على جميع برجر الجبن. مثالي لاحتياجاتك في عطلة نهاية الأسبوع.',
                'start_date' => '2025-04-01',
                'end_date' => '2025-04-15',
                'limit_amount' => 50,
                'image' => 'burger_offer.jpg',
                'code' => null,
                'percentage' => 10,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $discounts = array_merge($discounts, $staticDiscounts);

        // Dynamic Discounts using Faker
        for ($i = 0; $i < 3; $i++) {
            $startDate = $faker->dateTimeBetween('-1 months', '+1 months');
            $endDate = $faker->dateTimeBetween($startDate, '+2 months');

            $discounts[] = [
                // Make the title realistic
                'title_en' => 'Exclusive ' . $faker->randomElement(['Winter', 'Spring', 'Weekend', 'Holiday']) . ' Sale - ' . $faker->word() . ' Discount!',
                'title_ar' => 'عرض حصري ' . $faker->randomElement(['الشتاء', 'الربيع', 'عطلة نهاية الأسبوع', 'العيد']) . ' - خصم ' . $faker->word(),
                
                'description_en' => 'Save up to ' . $faker->numberBetween(10, 70) . '% on ' . $faker->randomElement(['pizzas', 'burgers', 'sushi', 'pastas', 'desserts', 'beverages']) . '. Don\'t miss this limited-time offer.',
                'description_ar' => 'وفر حتى ' . $faker->numberBetween(10, 70) . '% على ' . $faker->randomElement(['البيتزا', 'البرجر', 'السوشي', 'المعكرونة', 'الحلويات', 'المشروبات']) . '. لا تفوت هذا العرض المحدود.',
                
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'limit_amount' => $faker->numberBetween(100, 2000),
                'image' => $faker->randomElement([null, 'discount_' . $faker->numberBetween(1, 10) . '.jpg']),
                'code' => strtoupper(Str::random(6)),
                'percentage' => $faker->numberBetween(1, 50),
                'status' => $faker->randomElement(['new', 'active', 'expired', 'canceled']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert for performance
        Discount::insert($discounts);
    }
}
