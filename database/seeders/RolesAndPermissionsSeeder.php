<?php
// database/seeders/RolesAndPermissionsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        DB::beginTransaction();
        
        try {
            $this->command->info('Starting roles and permissions seeder...');
            
            // Define permissions with their categories and descriptions
            $permissionsData = [
                // System Management
                ['name' => 'manage_users', 'category' => 'system', 'description' => 'Manage all users'],
                ['name' => 'manage_roles', 'category' => 'system', 'description' => 'Manage roles and permissions'],
                ['name' => 'view_system_logs', 'category' => 'system', 'description' => 'View system logs'],
                ['name' => 'manage_settings', 'category' => 'system', 'description' => 'Manage system settings'],
                ['name' => 'manage_moodle_sync', 'category' => 'system', 'description' => 'Manage Moodle synchronization'],
                
                // Course Management
                ['name' => 'create_courses', 'category' => 'course', 'description' => 'Create new courses'],
                ['name' => 'edit_courses', 'category' => 'course', 'description' => 'Edit existing courses'],
                ['name' => 'delete_courses', 'category' => 'course', 'description' => 'Delete courses'],
                ['name' => 'view_all_courses', 'category' => 'course', 'description' => 'View all courses'],
                ['name' => 'manage_course_categories', 'category' => 'course', 'description' => 'Manage course categories'],
                ['name' => 'sync_courses_moodle', 'category' => 'course', 'description' => 'Sync courses to Moodle'],
                ['name' => 'archive_courses', 'category' => 'course', 'description' => 'Archive/unarchive courses'],
                
                // Enrollment Management
                ['name' => 'view_enrollment_requests', 'category' => 'enrollment', 'description' => 'View enrollment requests'],
                ['name' => 'approve_enrollments', 'category' => 'enrollment', 'description' => 'Approve enrollment requests'],
                ['name' => 'reject_enrollments', 'category' => 'enrollment', 'description' => 'Reject enrollment requests'],
                ['name' => 'force_enroll_users', 'category' => 'enrollment', 'description' => 'Force enroll users'],
                ['name' => 'unenroll_users', 'category' => 'enrollment', 'description' => 'Unenroll users from courses'],
                ['name' => 'view_enrollment_reports', 'category' => 'enrollment', 'description' => 'View enrollment reports'],
                ['name' => 'bulk_enroll', 'category' => 'enrollment', 'description' => 'Bulk enroll users'],
                
                // Content Management
                ['name' => 'upload_course_materials', 'category' => 'content', 'description' => 'Upload course materials'],
                ['name' => 'delete_course_materials', 'category' => 'content', 'description' => 'Delete course materials'],
                ['name' => 'manage_announcements', 'category' => 'content', 'description' => 'Manage course announcements'],
                ['name' => 'manage_assignments', 'category' => 'content', 'description' => 'Manage assignments'],
                ['name' => 'grade_assignments', 'category' => 'content', 'description' => 'Grade student assignments'],
                ['name' => 'view_submissions', 'category' => 'content', 'description' => 'View student submissions'],
                ['name' => 'manage_quizzes', 'category' => 'content', 'description' => 'Manage quizzes and exams'],
                
                // Reports & Analytics
                ['name' => 'view_reports', 'category' => 'reports', 'description' => 'View system reports'],
                ['name' => 'view_user_reports', 'category' => 'reports', 'description' => 'View user reports'],
                ['name' => 'view_course_reports', 'category' => 'reports', 'description' => 'View course reports'],
                ['name' => 'view_enrollment_analytics', 'category' => 'reports', 'description' => 'View enrollment analytics'],
                ['name' => 'export_reports', 'category' => 'reports', 'description' => 'Export reports'],
                ['name' => 'view_progress_tracking', 'category' => 'reports', 'description' => 'View student progress'],
                ['name' => 'view_completion_rates', 'category' => 'reports', 'description' => 'View completion rates'],
                
                // Student Permissions
                ['name' => 'enroll_courses', 'category' => 'student', 'description' => 'Enroll in courses'],
                ['name' => 'view_enrolled_courses', 'category' => 'student', 'description' => 'View enrolled courses'],
                ['name' => 'submit_assignments', 'category' => 'student', 'description' => 'Submit assignments'],
                ['name' => 'view_grades', 'category' => 'student', 'description' => 'View own grades'],
                ['name' => 'download_materials', 'category' => 'student', 'description' => 'Download course materials'],
                ['name' => 'participate_discussions', 'category' => 'student', 'description' => 'Participate in discussions'],
            ];

            // Create or update permissions
            $created = 0;
            $updated = 0;
            foreach ($permissionsData as $permData) {
                $permission = Permission::firstOrNew(
                    [
                        'name' => $permData['name'],
                        'guard_name' => 'web'
                    ]
                );
                
                $isNew = !$permission->exists;
                
                // Only update category and description if columns exist
                if (\Schema::hasColumn('permissions', 'category')) {
                    $permission->category = $permData['category'];
                }
                if (\Schema::hasColumn('permissions', 'description')) {
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

            // Define roles with their details and permissions
            $rolesData = [
                'superadmin' => [
                    'display_name' => 'Super Administrator',
                    'description' => 'Full system access with all permissions',
                    'permissions' => 'all' // Special case
                ],
                'course_admin' => [
                    'display_name' => 'Course Administrator',
                    'description' => 'Manages courses and enrollments',
                    'permissions' => [
                        'create_courses', 'edit_courses', 'delete_courses', 'view_all_courses',
                        'manage_course_categories', 'sync_courses_moodle', 'archive_courses',
                        'view_enrollment_requests', 'approve_enrollments', 'reject_enrollments',
                        'force_enroll_users', 'unenroll_users', 'view_enrollment_reports', 'bulk_enroll',
                        'upload_course_materials', 'delete_course_materials', 'manage_announcements',
                        'view_course_reports', 'view_enrollment_analytics', 'export_reports',
                    ]
                ],
                'instructor' => [
                    'display_name' => 'Instructor/Teacher',
                    'description' => 'Teaches courses and manages content',
                    'permissions' => [
                        'view_all_courses', 'edit_courses',
                        'upload_course_materials', 'delete_course_materials',
                        'manage_announcements', 'manage_assignments', 'grade_assignments',
                        'view_submissions', 'manage_quizzes',
                        'view_course_reports', 'view_progress_tracking', 'view_completion_rates',
                    ]
                ],
                'registrar' => [
                    'display_name' => 'Registrar',
                    'description' => 'Manages enrollments and student records',
                    'permissions' => [
                        'view_enrollment_requests', 'approve_enrollments', 'reject_enrollments',
                        'force_enroll_users', 'unenroll_users', 'view_enrollment_reports',
                        'bulk_enroll', 'view_user_reports', 'export_reports',
                    ]
                ],
                'student' => [
                    'display_name' => 'Student/Learner',
                    'description' => 'Enrolled in courses for learning',
                    'permissions' => [
                        'enroll_courses', 'view_enrolled_courses', 'submit_assignments',
                        'view_grades', 'download_materials', 'participate_discussions',
                    ]
                ],
                'admin' => [ // Keep existing admin role
                    'display_name' => 'Administrator',
                    'description' => 'System administrator',
                    'permissions' => [
                        'manage_users', 'view_enrollment_requests', 'approve_enrollments',
                        'reject_enrollments', 'view_reports', 'view_user_reports',
                    ]
                ],
                'user' => [ // Keep existing user role for backward compatibility
                    'display_name' => 'Basic User',
                    'description' => 'Default user role',
                    'permissions' => [
                        'enroll_courses', 'view_enrolled_courses', 'view_grades',
                    ]
                ],
            ];

            // Create or update roles and assign permissions
            $rolesCreated = 0;
            $rolesUpdated = 0;
            foreach ($rolesData as $roleName => $roleInfo) {
                $role = Role::firstOrNew(
                    [
                        'name' => $roleName,
                        'guard_name' => 'web'
                    ]
                );
                
                $isNewRole = !$role->exists;
                
                // Only update display_name and description if columns exist
                if (\Schema::hasColumn('roles', 'display_name')) {
                    $role->display_name = $roleInfo['display_name'];
                }
                if (\Schema::hasColumn('roles', 'description')) {
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

            // Optionally assign superadmin to first user
            $firstUser = User::first();
            if ($firstUser) {
                if (!$firstUser->hasAnyRole()) {
                    $firstUser->assignRole('superadmin');
                    $this->command->info('✅ Assigned superadmin role to: ' . $firstUser->email);
                } else {
                    $this->command->info('ℹ️ First user already has role: ' . $firstUser->getRoleNames()->first());
                }
            }

            DB::commit();
            $this->command->info('✅ Roles and permissions seeding completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }
}