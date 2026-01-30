<?php

/**
 * Add Course Admin Permission to Users
 *
 * This migration adds the "is_course_admin" flag to users table.
 *
 * EXPLANATION:
 * - Course Administrator is NOT a separate role
 * - It's a permission/flag that SuperAdmin can grant to Admin users
 * - An Admin with is_course_admin = true can:
 *   - Approve MOH account requests
 *   - Approve course access requests
 *   - Manage courses and enrollments
 *   - View pending queues
 * - An Admin without this flag has limited access
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Course Admin permission flag
            // Only SuperAdmin can set this to true
            // When true, the Admin user gains Course Admin capabilities
            if (!Schema::hasColumn('users', 'is_course_admin')) {
                $table->boolean('is_course_admin')->default(false)->after('is_course_creator');
            }

            // Moodle sync status for better tracking
            // pending = not yet synced
            // synced = successfully synced
            // failed = sync failed, needs attention
            if (!Schema::hasColumn('users', 'moodle_sync_status')) {
                $table->enum('moodle_sync_status', ['pending', 'synced', 'failed'])
                      ->default('pending')
                      ->after('moodle_user_id');
            }

            // Error message if Moodle sync failed
            if (!Schema::hasColumn('users', 'moodle_sync_error')) {
                $table->text('moodle_sync_error')->nullable()->after('moodle_sync_status');
            }
        });

        // Add index for faster queries on course admin users
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_course_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_course_admin']);
            $table->dropColumn(['is_course_admin', 'moodle_sync_status', 'moodle_sync_error']);
        });
    }
};
