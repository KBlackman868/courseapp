<?php

/**
 * Course Access Requests Migration
 *
 * This table stores requests from users who want to enroll in courses
 * that require approval (APPROVAL_REQUIRED enrollment type).
 *
 * WORKFLOW EXPLANATION:
 * - User sees a course with APPROVAL_REQUIRED enrollment type
 * - User clicks "Request Access" button
 * - Request is stored here with "pending" status
 * - Course Admin reviews and approves/rejects
 * - On approval: Moodle account is created (if needed), user is enrolled
 * - User can then access the course via "Go to Course" button
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_access_requests', function (Blueprint $table) {
            $table->id();

            // Who is requesting access
            $table->unsignedBigInteger('user_id');

            // Which course they want access to
            $table->unsignedBigInteger('course_id');

            // Request status workflow
            // pending = waiting for Course Admin review
            // approved = can access the course
            // rejected = cannot access, shown rejection reason
            // revoked = access was granted but later removed
            // expired = request was not acted on in time (optional cleanup)
            $table->enum('status', ['pending', 'approved', 'rejected', 'revoked', 'expired'])
                  ->default('pending');

            // User's reason for wanting access (optional, helps admins decide)
            $table->text('request_reason')->nullable();

            // Admin review information
            $table->unsignedBigInteger('approved_by')->nullable(); // Who approved/rejected
            $table->timestamp('approved_at')->nullable(); // When was decision made
            $table->text('rejection_reason')->nullable(); // Why rejected (shown to user)
            $table->text('admin_notes')->nullable(); // Internal notes for admins

            // Moodle sync status tracking
            // not_synced = hasn't been synced to Moodle yet
            // syncing = sync in progress
            // synced = successfully enrolled in Moodle
            // failed = sync failed, needs retry
            $table->enum('moodle_sync_status', ['not_synced', 'syncing', 'synced', 'failed'])
                  ->default('not_synced');
            $table->text('moodle_sync_error')->nullable(); // Error message if sync failed
            $table->integer('moodle_sync_attempts')->default(0); // How many times we tried
            $table->timestamp('last_sync_attempt')->nullable();

            // Tracking when request was made
            $table->timestamp('requested_at')->useCurrent();

            // Standard timestamps
            $table->timestamps();

            // Indexes for faster queries
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index('moodle_sync_status');
            $table->index('requested_at');

            // Each user can only have one request per course
            $table->unique(['user_id', 'course_id']);

            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('course_id')
                  ->references('id')
                  ->on('courses')
                  ->onDelete('cascade');

            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_access_requests');
    }
};
