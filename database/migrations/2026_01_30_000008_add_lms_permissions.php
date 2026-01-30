<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Add LMS-specific permissions for permission-driven access control.
 *
 * This creates granular permissions that can be assigned to roles
 * instead of relying on hard-coded role checks.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define new permissions with categories
        $permissions = [
            // User Management Permissions
            ['name' => 'users.view', 'category' => 'users', 'description' => 'View user list'],
            ['name' => 'users.approve', 'category' => 'users', 'description' => 'Approve pending user accounts'],
            ['name' => 'users.suspend', 'category' => 'users', 'description' => 'Suspend user accounts'],
            ['name' => 'users.delete', 'category' => 'users', 'description' => 'Delete user accounts'],

            // Role Management Permissions - SuperAdmin ONLY
            ['name' => 'roles.view', 'category' => 'roles', 'description' => 'View roles and permissions'],
            ['name' => 'roles.manage', 'category' => 'roles', 'description' => 'Create, edit, delete roles'],
            ['name' => 'roles.assign', 'category' => 'roles', 'description' => 'Assign roles to users'],

            // Course Management Permissions
            ['name' => 'courses.view', 'category' => 'courses', 'description' => 'View all courses'],
            ['name' => 'courses.create', 'category' => 'courses', 'description' => 'Create new courses'],
            ['name' => 'courses.edit', 'category' => 'courses', 'description' => 'Edit existing courses'],
            ['name' => 'courses.delete', 'category' => 'courses', 'description' => 'Delete courses'],
            ['name' => 'courses.manage', 'category' => 'courses', 'description' => 'Full course management'],

            // Enrollment/Access Request Permissions
            ['name' => 'enrollments.view', 'category' => 'enrollments', 'description' => 'View enrollment requests'],
            ['name' => 'enrollments.approve', 'category' => 'enrollments', 'description' => 'Approve enrollment requests'],
            ['name' => 'enrollments.manage', 'category' => 'enrollments', 'description' => 'Full enrollment management'],

            // System Permissions - SuperAdmin ONLY
            ['name' => 'system.settings', 'category' => 'system', 'description' => 'Manage system settings'],
            ['name' => 'system.logs', 'category' => 'system', 'description' => 'View system logs'],
            ['name' => 'system.moodle', 'category' => 'system', 'description' => 'Manage Moodle integration'],
            ['name' => 'system.superadmin', 'category' => 'system', 'description' => 'Access SuperAdmin dashboard'],
        ];

        // Create permissions
        foreach ($permissions as $permData) {
            Permission::firstOrCreate(
                ['name' => $permData['name'], 'guard_name' => 'web'],
                ['category' => $permData['category'] ?? null, 'description' => $permData['description'] ?? null]
            );
        }

        // Get roles
        $superadmin = Role::where('name', 'superadmin')->first();
        $admin = Role::where('name', 'admin')->first();

        // SuperAdmin gets ALL permissions
        if ($superadmin) {
            $superadmin->syncPermissions(Permission::all());
        }

        // Admin gets course/enrollment management BUT NOT role management
        if ($admin) {
            $adminPermissions = [
                'users.view',
                'users.approve',
                'users.suspend',
                // NO 'users.delete' - only superadmin
                // NO 'roles.*' - only superadmin can manage roles
                'courses.view',
                'courses.create',
                'courses.edit',
                'courses.manage',
                // NO 'courses.delete' - only superadmin
                'enrollments.view',
                'enrollments.approve',
                'enrollments.manage',
                // NO 'system.*' - only superadmin
            ];

            $admin->givePermissionTo($adminPermissions);
        }

        // Create course_admin role if it doesn't exist (for dedicated course admins)
        $courseAdmin = Role::firstOrCreate(
            ['name' => 'course_admin', 'guard_name' => 'web'],
            ['display_name' => 'Course Administrator', 'description' => 'Manages courses and enrollments only']
        );

        // Course Admin gets same as Admin for course/enrollment management
        $courseAdminPermissions = [
            'users.view',
            'users.approve',
            'courses.view',
            'courses.create',
            'courses.edit',
            'courses.manage',
            'enrollments.view',
            'enrollments.approve',
            'enrollments.manage',
        ];

        $courseAdmin->syncPermissions($courseAdminPermissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionsToRemove = [
            'users.view', 'users.approve', 'users.suspend', 'users.delete',
            'roles.view', 'roles.manage', 'roles.assign',
            'courses.view', 'courses.create', 'courses.edit', 'courses.delete', 'courses.manage',
            'enrollments.view', 'enrollments.approve', 'enrollments.manage',
            'system.settings', 'system.logs', 'system.moodle', 'system.superadmin',
        ];

        Permission::whereIn('name', $permissionsToRemove)->delete();
        Role::where('name', 'course_admin')->delete();
    }
};
