<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call other seeders (ensure you pass them as an array)
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        // Create an additional test user and assign the 'admin' role
        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => 'test@example.com',
            'password'   => Hash::make('password123'),
        ]);

        $user->assignRole('admin');
    }
}
