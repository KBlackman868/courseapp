<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PERFORMANCE FIX: Add indexes for frequently queried columns
 *
 * These indexes improve query performance for:
 * - Activity log filtering and statistics
 * - Course status and Moodle sync lookups
 * - Enrollment status filtering
 * - User authentication lookups (Moodle, Google, LDAP)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Activity logs indexes for filtering and statistics queries
        Schema::table('activity_logs', function (Blueprint $table) {
            // Index for date-based queries (most common filter)
            $table->index('created_at', 'activity_logs_created_at_index');

            // Composite index for user + date queries
            $table->index(['user_id', 'created_at'], 'activity_logs_user_date_index');

            // Index for status filtering
            $table->index(['status', 'created_at'], 'activity_logs_status_date_index');

            // Index for severity filtering
            $table->index(['severity', 'created_at'], 'activity_logs_severity_date_index');
        });

        // Course indexes for admin dashboard and sync operations
        Schema::table('courses', function (Blueprint $table) {
            // Index for Moodle sync lookups
            $table->index('moodle_course_id', 'courses_moodle_id_index');

            // Composite index for status + date filtering
            $table->index(['status', 'created_at'], 'courses_status_date_index');
        });

        // Enrollment indexes for bulk operations
        Schema::table('registrations', function (Blueprint $table) {
            // Composite index for course + status queries (bulk sync)
            $table->index(['course_id', 'status'], 'registrations_course_status_index');

            // Composite index for user + status queries
            $table->index(['user_id', 'status'], 'registrations_user_status_index');
        });

        // User indexes for authentication lookups
        Schema::table('users', function (Blueprint $table) {
            // Index for Moodle user sync
            $table->index('moodle_user_id', 'users_moodle_id_index');

            // Index for Google OAuth lookups
            $table->index('google_id', 'users_google_id_index');

            // Index for LDAP authentication
            $table->index('ldap_guid', 'users_ldap_guid_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('activity_logs_created_at_index');
            $table->dropIndex('activity_logs_user_date_index');
            $table->dropIndex('activity_logs_status_date_index');
            $table->dropIndex('activity_logs_severity_date_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_moodle_id_index');
            $table->dropIndex('courses_status_date_index');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex('registrations_course_status_index');
            $table->dropIndex('registrations_user_status_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_moodle_id_index');
            $table->dropIndex('users_google_id_index');
            $table->dropIndex('users_ldap_guid_index');
        });
    }
};
