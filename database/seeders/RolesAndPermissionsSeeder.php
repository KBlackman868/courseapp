<?php

/**
 * Roles and Permissions Seeder
 *
 * This seeder sets up the role and permission structure for the LMS.
 *
 * ROLE STRUCTURE (Only these 4 roles exist):
 * 1. SuperAdmin - Has ALL permissions, cannot be deleted
 * 2. Admin - System administrator, can be granted Course Admin permission
 * 3. MOH_Staff - Ministry of Health employees
 * 4. External_User - External users who can request course access
 *
 * IMPORTANT: Course Administrator is NOT a role!
 * It's a permission flag (is_course_admin) on Admin users.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::beginTransaction();

        try {
            $this->command->info('Starting roles and permissions seeder...');

            // =====================================================================
            // STEP 1: Create all permissions with categories
            // =====================================================================
            $permissionsData = [
                // System Management - SuperAdmin only
                ['name' => 'manage_users', 'category' => 'system', 'description' => 'Manage all users'],
                ['name' => 'manage_roles', 'category' => 'system', 'description' => 'Manage roles and permissions'],
                ['name' => 'view_system_logs', 'category' => 'system', 'description' => 'View system logs'],
                ['name' => 'manage_settings', 'category' => 'system', 'description' => 'Manage system settings'],
                ['name' => 'manage_moodle_sync', 'category' => 'system', 'description' => 'Manage Moodle synchronization'],

                // Account Management - For approving registrations
                ['name' => 'view_account_requests', 'category' => 'account', 'description' => 'View pending account requests'],
                ['name' => 'approve_accounts', 'category' => 'account', 'description' => 'Approve account requests'],
                ['name' => 'reject_accounts', 'category' => 'account', 'description' => 'Reject account requests'],
                ['name' => 'bulk_approve_accounts', 'category' => 'account', 'description' => 'Bulk approve account requests'],
                ['name' => 'suspend_accounts', 'category' => 'account', 'description' => 'Suspend user accounts'],

                // Course Management
                ['name' => 'create_courses', 'category' => 'course', 'description' => 'Create new courses'],
                ['name' => 'edit_courses', 'category' => 'course', 'description' => 'Edit existing courses'],
                ['name' => 'delete_courses', 'category' => 'course', 'description' => 'Delete courses'],
                ['name' => 'view_all_courses', 'category' => 'course', 'description' => 'View all courses'],
                ['name' => 'manage_course_categories', 'category' => 'course', 'description' => 'Manage course categories'],
                ['name' => 'sync_courses_moodle', 'category' => 'course', 'description' => 'Sync courses to Moodle'],
                ['name' => 'archive_courses', 'category' => 'course', 'description' => 'Archive/unarchive courses'],

                // Course Access Request Management
                ['name' => 'view_access_requests', 'category' => 'enrollment', 'description' => 'View course access requests'],
                ['name' => 'approve_access_requests', 'category' => 'enrollment', 'description' => 'Approve course access requests'],
                ['name' => 'reject_access_requests', 'category' => 'enrollment', 'description' => 'Reject course access requests'],
                ['name' => 'bulk_approve_access', 'category' => 'enrollment', 'description' => 'Bulk approve access requests'],

                // Enrollment Management
                ['name' => 'view_enrollment_requests', 'category' => 'enrollment', 'description' => 'View enrollment requests'],
                ['name' => 'approve_enrollments', 'category' => 'enrollment', 'description' => 'Approve enrollment requests'],
                ['name' => 'reject_enrollments', 'category' => 'enrollment', 'description' => 'Reject enrollment requests'],
                ['name' => 'force_enroll_users', 'category' => 'enrollment', 'description' => 'Force enroll users'],
                ['name' => 'unenroll_users', 'category' => 'enrollment', 'description' => 'Unenroll users from courses'],
                ['name' => 'view_enrollment_reports', 'category' => 'enrollment', 'description' => 'View enrollment reports'],
                ['name' => 'bulk_enroll', 'category' => 'enrollment', 'description' => 'Bulk enroll users'],

                // Reports & Analytics
                ['name' => 'view_reports', 'category' => 'reports', 'description' => 'View system reports'],
                ['name' => 'view_user_reports', 'category' => 'reports', 'description' => 'View user reports'],
                ['name' => 'view_course_reports', 'category' => 'reports', 'description' => 'View course reports'],
                ['name' => 'view_enrollment_analytics', 'category' => 'reports', 'description' => 'View enrollment analytics'],
                ['name' => 'export_reports', 'category' => 'reports', 'description' => 'Export reports'],

                // User Permissions - What regular users can do
                ['name' => 'view_courses', 'category' => 'user', 'description' => 'View available courses'],
                ['name' => 'enroll_courses', 'category' => 'user', 'description' => 'Enroll in courses'],
                ['name' => 'request_course_access', 'category' => 'user', 'description' => 'Request access to courses'],
                ['name' => 'view_enrolled_courses', 'category' => 'user', 'description' => 'View enrolled courses'],
                ['name' => 'access_moodle_courses', 'category' => 'user', 'description' => 'Access Moodle courses'],

                // Notification permissions
                ['name' => 'view_notifications', 'category' => 'notification', 'description' => 'View notifications'],
                ['name' => 'manage_notifications', 'category' => 'notification', 'description' => 'Manage system notifications'],
            ];

            // Create or update permissions
            $created = 0;
            $updated = 0;
            foreach ($permissionsData as $permData) {
                $permission = Permission::firstOrNew([
                    'name' => $permData['name'],
                    'guard_name' => 'web'
                ]);

                $isNew = !$permission->exists;

                // Update category and description if columns exist
                if (Schema::hasColumn('permissions', 'category')) {
                    $permission->category = $permData['category'];
                }
                if (Schema::hasColumn('permissions', 'description')) {
                    $permission->description = $permData['description'];
                }

                $permission->save();

                if ($isNew) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            $this->command->info("Permissions: {$created} created, {$updated} updated");

            // =====================================================================
            // STEP 2: Define the 4 roles with their permissions
            // =====================================================================
            $rolesData = [
                // SuperAdmin: Has all permissions
                User::ROLE_SUPERADMIN => [
                    'display_name' => 'Super Administrator',
                    'description' => 'Full system access with all permissions. Cannot be deleted.',
                    'permissions' => 'all' // Special case - gets all permissions
                ],

                // Admin: System administrator
                User::ROLE_ADMIN => [
                    'display_name' => 'Administrator',
                    'description' => 'System administrator. Can be granted Course Admin permission by SuperAdmin.',
                    'permissions' => [
                        // Basic admin permissions
                        'manage_users',
                        'view_enrollment_requests',
                        'approve_enrollments',
                        'reject_enrollments',
                        'view_reports',
                        'view_user_reports',
                        'view_courses',
                        'view_all_courses',
                        'view_notifications',
                    ]
                ],

                // MOH Staff: Ministry of Health employees
                User::ROLE_MOH_STAFF => [
                    'display_name' => 'MOH Staff',
                    'description' => 'Ministry of Health employee. Can enroll in MOH and public courses.',
                    'permissions' => [
                        'view_courses',
                        'enroll_courses',
                        'request_course_access',
                        'view_enrolled_courses',
                        'access_moodle_courses',
                        'view_notifications',
                    ]
                ],

                // External User: Outside users
                User::ROLE_EXTERNAL_USER => [
                    'display_name' => 'External User',
                    'description' => 'External user. Can request access to available courses.',
                    'permissions' => [
                        'view_courses',
                        'request_course_access',
                        'view_enrolled_courses',
                        'access_moodle_courses',
                        'view_notifications',
                    ]
                ],
            ];

            // Create or update roles and assign permissions
            $rolesCreated = 0;
            $rolesUpdated = 0;

            foreach ($rolesData as $roleName => $roleInfo) {
                $role = Role::firstOrNew([
                    'name' => $roleName,
                    'guard_name' => 'web'
                ]);

                $isNewRole = !$role->exists;

                // Update display_name and description if columns exist
                if (Schema::hasColumn('roles', 'display_name')) {
                    $role->display_name = $roleInfo['display_name'];
                }
                if (Schema::hasColumn('roles', 'description')) {
                    $role->description = $roleInfo['description'];
                }

                $role->save();

                if ($isNewRole) {
                    $rolesCreated++;
                } else {
                    $rolesUpdated++;
                }

                // Sync permissions
                if ($roleInfo['permissions'] === 'all') {
                    $role->syncPermissions(Permission::all());
                } else {
                    $role->syncPermissions($roleInfo['permissions']);
                }
            }

            $this->command->info("Roles: {$rolesCreated} created, {$rolesUpdated} updated");

            // =====================================================================
            // STEP 3: Clean up old roles that are no longer needed
            // =====================================================================
            $deprecatedRoles = [
                'instructor',
                'registrar',
                'student',
                'course_admin', // This is now a permission flag, not a role
                'user',
                'course_creator',
            ];

            $deletedRoles = 0;
            foreach ($deprecatedRoles as $oldRoleName) {
                $oldRole = Role::where('name', $oldRoleName)->first();
                if ($oldRole) {
                    // Move users from old role to appropriate new role
                    $usersWithOldRole = User::role($oldRoleName)->get();
                    foreach ($usersWithOldRole as $user) {
                        // Determine new role based on user type
                        if ($user->hasMohEmail() || $user->user_type === 'internal') {
                            $user->syncRoles([User::ROLE_MOH_STAFF]);
                        } else {
                            $user->syncRoles([User::ROLE_EXTERNAL_USER]);
                        }
                    }

                    $oldRole->delete();
                    $deletedRoles++;
                    $this->command->info("Migrated users from '{$oldRoleName}' and deleted role");
                }
            }

            if ($deletedRoles > 0) {
                $this->command->info("Cleaned up {$deletedRoles} deprecated roles");
            }

            // =====================================================================
            // STEP 4: Ensure first user is SuperAdmin
            // =====================================================================
            $firstUser = User::first();
            if ($firstUser) {
                if (!$firstUser->hasRole(User::ROLE_SUPERADMIN)) {
                    $firstUser->syncRoles([User::ROLE_SUPERADMIN]);
                    $this->command->info('Assigned SuperAdmin role to: ' . $firstUser->email);
                } else {
                    $this->command->info('First user already has SuperAdmin role: ' . $firstUser->email);
                }
            }

            DB::commit();
            $this->command->info('Roles and permissions seeding completed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
