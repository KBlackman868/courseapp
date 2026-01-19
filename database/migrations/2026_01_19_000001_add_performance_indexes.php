<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes for commonly queried columns to improve performance.
     */
    public function up(): void
    {
        // Indexes for registrations (enrollments) table
        Schema::table('registrations', function (Blueprint $table) {
            // Index for finding enrollments by user
            if (!$this->hasIndex('registrations', 'registrations_user_id_index')) {
                $table->index('user_id', 'registrations_user_id_index');
            }

            // Index for finding enrollments by course
            if (!$this->hasIndex('registrations', 'registrations_course_id_index')) {
                $table->index('course_id', 'registrations_course_id_index');
            }

            // Index for filtering by status (pending, approved, denied)
            if (!$this->hasIndex('registrations', 'registrations_status_index')) {
                $table->index('status', 'registrations_status_index');
            }

            // Composite index for common queries
            if (!$this->hasIndex('registrations', 'registrations_user_course_status_index')) {
                $table->index(['user_id', 'course_id', 'status'], 'registrations_user_course_status_index');
            }
        });

        // Indexes for users table
        Schema::table('users', function (Blueprint $table) {
            // Index for Moodle user lookup
            if (!$this->hasIndex('users', 'users_moodle_user_id_index')) {
                $table->index('moodle_user_id', 'users_moodle_user_id_index');
            }

            // Index for user type filtering
            if (!$this->hasIndex('users', 'users_user_type_index')) {
                $table->index('user_type', 'users_user_type_index');
            }

            // Index for suspended users
            if (!$this->hasIndex('users', 'users_is_suspended_index')) {
                $table->index('is_suspended', 'users_is_suspended_index');
            }
        });

        // Indexes for courses table
        Schema::table('courses', function (Blueprint $table) {
            // Index for Moodle course lookup
            if (!$this->hasIndex('courses', 'courses_moodle_course_id_index')) {
                $table->index('moodle_course_id', 'courses_moodle_course_id_index');
            }

            // Index for category filtering
            if (!$this->hasIndex('courses', 'courses_category_id_index')) {
                $table->index('category_id', 'courses_category_id_index');
            }

            // Index for status filtering
            if (!$this->hasIndex('courses', 'courses_status_index')) {
                $table->index('status', 'courses_status_index');
            }
        });

        // Indexes for activity_logs table (if exists)
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                // Index for filtering by user
                if (!$this->hasIndex('activity_logs', 'activity_logs_user_id_index')) {
                    $table->index('user_id', 'activity_logs_user_id_index');
                }

                // Index for filtering by action
                if (!$this->hasIndex('activity_logs', 'activity_logs_action_index')) {
                    $table->index('action', 'activity_logs_action_index');
                }

                // Index for date range queries
                if (!$this->hasIndex('activity_logs', 'activity_logs_created_at_index')) {
                    $table->index('created_at', 'activity_logs_created_at_index');
                }

                // Composite index for common queries
                if (!$this->hasIndex('activity_logs', 'activity_logs_user_action_created_index')) {
                    $table->index(['user_id', 'action', 'created_at'], 'activity_logs_user_action_created_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex('registrations_user_id_index');
            $table->dropIndex('registrations_course_id_index');
            $table->dropIndex('registrations_status_index');
            $table->dropIndex('registrations_user_course_status_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_moodle_user_id_index');
            $table->dropIndex('users_user_type_index');
            $table->dropIndex('users_is_suspended_index');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex('courses_moodle_course_id_index');
            $table->dropIndex('courses_category_id_index');
            $table->dropIndex('courses_status_index');
        });

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex('activity_logs_user_id_index');
                $table->dropIndex('activity_logs_action_index');
                $table->dropIndex('activity_logs_created_at_index');
                $table->dropIndex('activity_logs_user_action_created_index');
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $indexes = $connection->getDoctrineSchemaManager()->listTableIndexes($table);

        return isset($indexes[$indexName]);
    }
};
