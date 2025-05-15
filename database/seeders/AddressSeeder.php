<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;
use Faker\Factory as Faker;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Fetch all user IDs (not just clients)
        $userIds = User::pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->warn('No users found. Skipping address seeding.');
            return;
        }

        // Define a list of provinces and regions for Jordan
        $jordanProvinces = [
            'Amman', 'Irbid', 'Zarqa', 'Aqaba', 'Mafraq', 'Karak', 'Madaba', 'Ajloun', 'Jerash', 'Tafilah', 'Ma’an'
        ];

        $jordanRegions = [
            'Abdali Area', 'Jabal Amman', 'Rainbow Street', 'Mecca Street', 'Shmeisani', 'Al-Sweifieh', 'Jabal Al-Hussein', 'Al-Madina', 'Al-Jubaiha', 'Marj Al-Hamam'
        ];

        $addresses = [];

        // Critical test cases (Static addresses)
        $staticAddresses = [
            [
                'client_id' => $userIds[array_rand($userIds)],
                'title' => 'Head Office',
                'description' => 'Main company headquarters',
                'province' => 'Amman',
                'region' => 'Abdali Area',
                'address_detail' => '123 Main St, Amman',
                'created_at' => now(),
                 'updated_at' => now(),
            ],
            [
                'client_id' => $userIds[array_rand($userIds)],
                'title' => 'Branch Office',
                'description' => 'Secondary branch office',
                'province' => 'Amman',
                'region' => 'Jabal Amman',
                'address_detail' => '456 Zahran Street, Amman',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $addresses = array_merge($addresses, $staticAddresses);

        // Dynamic addresses using Faker (Ensuring valid addresses for Jordan)
        for ($i = 0; $i < 5; $i++) {
            $addresses[] = [
                'client_id' => $faker->randomElement($userIds),
                'title' => $faker->randomElement(['Home', 'Work', 'Vacation House', 'Warehouse']),
                'description' => $faker->sentence(),
                'province' => $faker->randomElement($jordanProvinces),
                'region' => $faker->randomElement($jordanRegions),
                'address_detail' => $faker->streetAddress() . ', Jordan',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert for speed
        Address::insert($addresses);
    }
}
