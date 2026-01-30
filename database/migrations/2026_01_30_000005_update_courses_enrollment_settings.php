<?php

/**
 * Update Courses Enrollment Settings
 *
 * This migration updates the courses table to use the new enrollment system.
 *
 * ENROLLMENT TYPE EXPLANATION:
 * - OPEN_ENROLLMENT: Anyone eligible can enroll immediately
 *   - Moodle account created automatically if needed
 *   - User redirected to Moodle course right away
 *
 * - APPROVAL_REQUIRED: Users must request access first
 *   - Request goes to pending queue
 *   - Course Admin approves/rejects
 *   - On approval: Moodle account created, user enrolled
 *   - User can then access the course
 *
 * AUDIENCE TYPE EXPLANATION:
 * - MOH_ONLY: Only MOH staff can see and enroll
 * - EXTERNAL_ONLY: Only external users can see and enroll
 * - BOTH: Everyone can see and enroll
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Add enrollment_type column
            // This replaces the is_free boolean with clearer enum values
            if (!Schema::hasColumn('courses', 'enrollment_type')) {
                $table->enum('enrollment_type', ['OPEN_ENROLLMENT', 'APPROVAL_REQUIRED'])
                      ->default('OPEN_ENROLLMENT')
                      ->after('audience_type');
            }
        });

        // Migrate existing data: is_free = true → OPEN_ENROLLMENT, is_free = false → APPROVAL_REQUIRED
        // This preserves existing course settings
        \DB::statement("
            UPDATE courses
            SET enrollment_type = CASE
                WHEN is_free = 1 OR is_free = true THEN 'OPEN_ENROLLMENT'
                ELSE 'APPROVAL_REQUIRED'
            END
            WHERE enrollment_type IS NULL OR enrollment_type = ''
        ");
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('enrollment_type');
        });
    }
};
