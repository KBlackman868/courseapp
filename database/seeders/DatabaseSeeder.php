<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles if they don't exist.
        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);

        // Create an admin user. Make sure not to include email_verified_at.
        $admin = User::create([
            'first_name' => 'Kyle',
            'last_name'  => 'Blackman',
            'email'      => 'kyle.blackman@health.gov.tt',
            'password'   => Hash::make('He@Lth2025!'),
            'department' => 'ICT',
        ]);

        // Assign the 'admin' role to this user.
        $admin->assignRole('admin');

        // Optionally, create dummy users here.
        // User::factory(10)->create()->each(function ($user) {
        //     $user->assignRole('user');
        // });
    }
}
