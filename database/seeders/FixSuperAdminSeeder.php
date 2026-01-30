<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class FixSuperAdminSeeder extends Seeder
{
    /**
     * Fix or create the superadmin account.
     *
     * Run with: php artisan db:seed --class=FixSuperAdminSeeder
     *
     * Set ADMIN_DEFAULT_PASSWORD in .env or it will generate a random password
     */
    public function run(): void
    {
        // Ensure superadmin role exists
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        // Get password from environment or generate a secure random one
        $password = env('ADMIN_DEFAULT_PASSWORD');
        $passwordGenerated = false;

        if (!$password) {
            $password = Str::random(16);
            $passwordGenerated = true;
        }

        // Find or create the superadmin user
        $user = User::where('email', 'kyle.blackman@health.gov.tt')->first();

        if (!$user) {
            // Try alternate email formats
            $user = User::where('email', 'like', '%kyle.blackman%')->first();
        }

        if ($user) {
            // Update existing user
            $user->update([
                'password' => Hash::make($password),
                'account_status' => 'active',
                'user_type' => 'internal',
            ]);

            // Ensure they have the superadmin role
            if (!$user->hasRole('superadmin')) {
                $user->syncRoles(['superadmin']);
            }

            $this->command->info("Updated existing user: {$user->email}");
        } else {
            // Create new superadmin user
            $user = User::create([
                'first_name' => 'Kyle',
                'last_name' => 'Blackman',
                'email' => 'kyle.blackman@health.gov.tt',
                'password' => Hash::make('H3@lth100%'),
                'user_type' => 'internal',
                'account_status' => 'active',
                'auth_method' => 'local',
                'email_verified_at' => now(),
                'initial_otp_completed' => true,
                'initial_otp_completed_at' => now(),
            ]);

            $user->assignRole('superadmin');

            $this->command->info("Created new superadmin user: {$user->email}");
        }

        $this->command->info("");
        $this->command->info("Superadmin account ready:");
        $this->command->info("  Email: {$user->email}");
        $this->command->info("  Roles: " . $user->roles->pluck('name')->implode(', '));

        if ($passwordGenerated) {
            $this->command->warn("");
            $this->command->warn("Generated password (save this securely!): {$password}");
            $this->command->warn("Set ADMIN_DEFAULT_PASSWORD in .env to use a specific password.");
        } else {
            $this->command->info("");
            $this->command->warn("Password set from ADMIN_DEFAULT_PASSWORD environment variable.");
            $this->command->warn("Remember to change password after first login!");
        }
    }
}
