<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            RoleUserSeeder::class, // Pivot: role_user
            CategoriesSeeder::class,
            ItemsSeeder::class,
            OrdersSeeder::class,
            OrderItemSeeder::class, // Pivot: order_item
            CartsSeeder::class,
            PaymentsSeeder::class,
            DiscountsSeeder::class,
            CategoryDiscountSeeder::class, // Pivot: category_discount
            DiscountItemSeeder::class, // Pivot: discount_item
            DiscountOrderSeeder::class, // Pivot: discount_order
            AddressSeeder::class,
            ReviewsSeeder::class,
            OptionsSeeder::class,
            FavoriteSeeder::class, // Pivot: favorite
            RestaurantSeeder::class,
            PermissionSeeder::class,
            OrderOptionsSeeder::class,
            ItemOptionsSeeder::class,
            CartOptionsSeeder::class,
        ]);
    }
}
