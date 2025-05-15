<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Password@123'),
            'phone_number' => '0780000000',
            'birth_date' => '2001-01-01',
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        DB::table('users')->insert([
            [
                'name' => 'ragad nofal',
                'email' => 'ragadnofal2001@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Password@123'),
                'phone_number' => '0788808776',
                'birth_date' => '2001-11-05',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'baraa albakkar',
                'email' => 'baraa.bakar2002@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Password@123'),
                'phone_number' => '0779228750',
                'birth_date' => '2002-10-03',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
