<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminUsersSeeder extends Seeder
{
    /**
     * Seed admin users with proper roles.
     *
     * Run with: php artisan db:seed --class=AdminUsersSeeder
     *
     * Set ADMIN_DEFAULT_PASSWORD in .env or it will generate random passwords
     */
    public function run(): void
    {
        $this->command->info('Setting up admin users...');

        // Ensure roles exist first
        $this->ensureRolesExist();

        // Get password from environment or generate secure random ones
        $defaultPassword = env('ADMIN_DEFAULT_PASSWORD');
        $generatedPasswords = [];

        // Define admin users
        $adminUsers = [
            // Superadmin
            [
                'first_name' => 'Kyle',
                'last_name' => 'Blackman',
                'email' => 'kyle.blackman@health.gov.tt',
                'department' => 'ICT',
                'role' => 'superadmin',
                'user_type' => 'internal',
            ],
            // System Admin (backup)
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'email' => 'admin@health.gov.tt',
                'department' => 'ICT',
                'role' => 'admin',
                'user_type' => 'internal',
            ],
            // Course Administrators
            [
                'first_name' => 'Course',
                'last_name' => 'Admin',
                'email' => 'courseadmin@health.gov.tt',
                'department' => 'Training',
                'role' => 'course_admin',
                'user_type' => 'internal',
            ],
        ];

        foreach ($adminUsers as $userData) {
            // Use env password or generate a random one
            if ($defaultPassword) {
                $password = $defaultPassword;
            } else {
                $password = Str::random(16);
                $generatedPasswords[$userData['email']] = $password;
            }

            $this->createOrUpdateUser($userData, $password);
        }

        $this->command->info('');
        $this->command->info('Admin users setup complete!');

        if (!empty($generatedPasswords)) {
            $this->command->info('');
            $this->command->warn('Generated passwords (save these securely!):');
            foreach ($generatedPasswords as $email => $pwd) {
                $this->command->line("  {$email}: {$pwd}");
            }
            $this->command->warn('');
            $this->command->warn('Set ADMIN_DEFAULT_PASSWORD in .env to use a specific password.');
        } else {
            $this->command->info('');
            $this->command->warn('Password set from ADMIN_DEFAULT_PASSWORD environment variable.');
            $this->command->warn('Remember to change passwords after first login!');
        }
    }

    /**
     * Ensure all required roles exist
     */
    private function ensureRolesExist(): void
    {
        $roles = ['superadmin', 'admin', 'course_admin', 'instructor', 'user', 'moh_staff', 'external_user'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->command->info('Roles verified.');
    }

    /**
     * Create or update a user with specified role
     */
    private function createOrUpdateUser(array $userData, string $password): void
    {
        $user = User::where('email', $userData['email'])->first();

        $attributes = [
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'password' => Hash::make($password),
            'department' => $userData['department'] ?? null,
            'user_type' => $userData['user_type'] ?? 'internal',
            'account_status' => 'active',
            'auth_method' => 'local',
            'email_verified_at' => now(),
            'initial_otp_completed' => true,
            'initial_otp_completed_at' => now(),
        ];

        if ($user) {
            // Update existing user
            $user->update($attributes);
            $action = 'Updated';
        } else {
            // Create new user
            $user = User::create($attributes);
            $action = 'Created';
        }

        // Sync role (replaces existing roles)
        $user->syncRoles([$userData['role']]);

        $this->command->info("{$action}: {$user->email} ({$userData['role']})");
    }
}
