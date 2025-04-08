<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a superadmin user
        $superadmin = User::factory()->create([
            'first_name' => 'Super',
            'last_name'  => 'Admin',
            'email'      => 'superadmin@example.com',
            'department' => 'Administration',
            'password'   => Hash::make('password123'),
        ]);
        $superadmin->assignRole('superadmin');

        // Create an admin user
        $admin = User::factory()->create([
            'first_name' => 'Admin',
            'last_name'  => 'User',
            'email'      => 'admin@example.com',
            'department' => 'Administration',
            'password'   => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        // Create 10 regular users and assign them the 'user' role
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('user');
        });
    }
}
