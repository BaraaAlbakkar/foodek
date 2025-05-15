<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            [
                'name_en' => 'Manage Users',
                'name_ar' => 'إدارة المستخدمين'
            ],
            [
                'name_en' => 'Manage Orders',
                'name_ar' => 'إدارة الطلبات'
            ],
            [
                'name_en' => 'Manage Categories',
                'name_ar' => 'إدارة الفئات'
            ],
        ]);
    }
}
