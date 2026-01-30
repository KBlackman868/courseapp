<?php

/**
 * Enhance Audit Logging
 *
 * This migration adds additional columns to the activity_logs table
 * for better audit trail tracking.
 *
 * AUDIT TRAIL REQUIREMENTS:
 * - Account approvals/rejections/suspensions
 * - Bulk approvals (which accounts were affected)
 * - Course request approvals/rejections
 * - Role changes (who changed what)
 * - Course edits (audience/enrollment_type changes)
 * - Moodle sync actions (create user, enroll user) and failures
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Before/after snapshot for tracking changes
            // Stored as JSON: {"audience_type": "MOH_ONLY"} → {"audience_type": "BOTH"}
            if (!Schema::hasColumn('activity_logs', 'before_state')) {
                $table->json('before_state')->nullable()->after('properties');
            }

            if (!Schema::hasColumn('activity_logs', 'after_state')) {
                $table->json('after_state')->nullable()->after('before_state');
            }

            // For bulk operations, store array of affected IDs
            // e.g., [1, 2, 3, 4, 5] for bulk account approval
            if (!Schema::hasColumn('activity_logs', 'affected_ids')) {
                $table->json('affected_ids')->nullable()->after('after_state');
            }

            // Category for grouping audit logs
            // e.g., 'account', 'course', 'enrollment', 'moodle', 'role'
            if (!Schema::hasColumn('activity_logs', 'category')) {
                $table->string('category')->nullable()->after('action');
            }
        });

        // Add indexes for better query performance
        // Note: Index creation wrapped in try-catch for SQL Server compatibility
        try {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->index('category', 'activity_logs_category_index');
            });
        } catch (\Exception $e) {
            // Index may already exist, ignore
        }
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['before_state', 'after_state', 'affected_ids', 'category']);
        });
    }
};
