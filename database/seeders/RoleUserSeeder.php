<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the users and roles
        $users = User::all();
        $roles = Role::all();

        // Example: Assigning a random role to each user (or you can define a specific one)
        foreach ($users as $user) {
            // Randomly assign a role to the user
            $role = $roles->random();
            $user->roles()->attach($role->id); // This assumes you have a roles relationship defined in your User model
        }
    }
}
