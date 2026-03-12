<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MohTestAccountSeeder extends Seeder
{
    /**
     * Seed a test MOH staff account.
     *
     * Run with: php artisan db:seed --class=MohTestAccountSeeder
     */
    public function run(): void
    {
        // Ensure moh_staff role exists
        Role::firstOrCreate(['name' => 'moh_staff', 'guard_name' => 'web']);

        $user = User::updateOrCreate(
            ['email' => 'mohtestaccount@health.gov.tt'],
            [
                'first_name' => 'MOH',
                'last_name' => 'Test Account',
                'password' => Hash::make('Ilovemoh2026!'),
                'department' => 'General',
                'user_type' => 'internal',
                'account_status' => 'active',
                'auth_method' => 'local',
                'email_verified_at' => now(),
                'initial_otp_completed' => true,
                'initial_otp_completed_at' => now(),
            ]
        );

        $user->syncRoles(['moh_staff']);

        $this->command->info("MOH test account ready: mohtestaccount@health.gov.tt");
    }
}
