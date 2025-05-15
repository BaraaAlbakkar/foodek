<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name_en' => 'super admin',
                'name_ar'=>'المشرف الرئيسي'
            ],
            [
                'name_en' => 'admin',
                'name_ar'=>'المشرف'
            ],
            [
                'name_en' => 'driver',
                'name_ar'=>'السائق'
            ],
            [
                'name_en' => 'client',
                'name_ar'=>'الزبون'
            ],
        ]);
    }
}
