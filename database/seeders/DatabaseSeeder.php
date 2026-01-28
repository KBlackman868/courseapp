<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run: php artisan db:seed
     *
     * Or run individual seeders:
     * - php artisan db:seed --class=RolesAndPermissionsSeeder
     * - php artisan db:seed --class=AdminUsersSeeder
     * - php artisan db:seed --class=FixSuperAdminSeeder
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('  MOH LMS Database Seeder');
        $this->command->info('========================================');
        $this->command->info('');

        // 1. First, seed roles and permissions
        $this->command->info('Step 1: Seeding roles and permissions...');
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Then, create admin users
        $this->command->info('');
        $this->command->info('Step 2: Creating admin users...');
        $this->call(AdminUsersSeeder::class);

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('  Database seeding completed!');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Admin Accounts Created:');
        $this->command->info('  - kyle.blackman@health.gov.tt (superadmin)');
        $this->command->info('  - admin@health.gov.tt (admin)');
        $this->command->info('  - courseadmin@health.gov.tt (course_admin)');
        $this->command->info('');
        if (env('ADMIN_DEFAULT_PASSWORD')) {
            $this->command->warn('Password set from ADMIN_DEFAULT_PASSWORD environment variable.');
        } else {
            $this->command->warn('Random passwords were generated - check AdminUsersSeeder output above.');
        }
        $this->command->warn('Please change these passwords after first login!');
        $this->command->info('');
    }
}
