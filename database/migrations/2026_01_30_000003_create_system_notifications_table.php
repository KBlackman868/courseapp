<?php

/**
 * System Notifications Migration
 *
 * This table stores in-app notifications for users.
 * These appear in the notification dropdown/page in the navbar.
 *
 * NOTIFICATION TYPES:
 * - account_request: New MOH account request submitted (for Course Admins)
 * - account_approved: Your account was approved (for users)
 * - account_rejected: Your account was rejected (for users)
 * - course_request: New course access request (for Course Admins)
 * - course_approved: Your course request was approved (for users)
 * - course_rejected: Your course request was rejected (for users)
 * - enrollment_ready: You can now access the course (for users)
 * - moodle_sync_failed: Moodle sync failed (for Course Admins)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();

            // Who receives this notification
            $table->unsignedBigInteger('user_id');

            // What type of notification is this
            $table->string('type'); // e.g., 'account_request', 'course_approved'

            // Human-readable title shown in the notification list
            $table->string('title');

            // Detailed message body
            $table->text('message');

            // Optional: Link to related action (e.g., view request, go to course)
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable(); // e.g., "View Request", "Go to Course"

            // Priority for sorting/highlighting
            // low = normal notification
            // medium = slightly more important
            // high = urgent, should stand out
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');

            // Has the user read this notification?
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();

            // Optional: Related model for context
            // e.g., model_type = 'App\Models\CourseAccessRequest', model_id = 123
            $table->string('related_model_type')->nullable();
            $table->unsignedBigInteger('related_model_id')->nullable();

            // Additional data stored as JSON (flexible for future needs)
            $table->json('data')->nullable();

            $table->timestamps();

            // Indexes for faster queries
            $table->index(['user_id', 'is_read']); // Unread notifications for a user
            $table->index(['user_id', 'created_at']); // Recent notifications
            $table->index('type');
            $table->index('priority');

            // Foreign key - Using NO ACTION for SQL Server compatibility
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
