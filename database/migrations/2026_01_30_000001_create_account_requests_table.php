<?php

/**
 * Account Requests Migration
 *
 * This table stores registration requests from users, particularly MOH Staff
 * who register with @health.gov.tt email addresses.
 *
 * WORKFLOW EXPLANATION:
 * - When someone with @health.gov.tt registers, their request goes here as "pending"
 * - A Course Admin (Admin with course admin permission) reviews and approves/rejects
 * - On approval, a User record is created/activated with MOH_Staff role
 * - All actions are logged for audit trail purposes
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_requests', function (Blueprint $table) {
            $table->id();

            // Basic user information submitted during registration
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique(); // The email they're registering with
            $table->string('password'); // Hashed password they chose
            $table->string('department')->nullable(); // Required for MOH staff for filtering
            $table->string('organization')->nullable(); // Their organization name
            $table->string('phone')->nullable(); // Optional contact number

            // Request status workflow
            // pending = waiting for Course Admin review
            // approved = request approved, user account created
            // rejected = request denied, they cannot access the system
            // suspended = was approved but later suspended
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])
                  ->default('pending');

            // Who is this request for? MOH staff get special treatment
            // moh_staff = @health.gov.tt email domain users
            // external = all other users (auto-approved typically)
            $table->enum('request_type', ['moh_staff', 'external'])->default('external');

            // Admin review information
            $table->unsignedBigInteger('reviewed_by')->nullable(); // Which admin handled this
            $table->timestamp('reviewed_at')->nullable(); // When was it reviewed
            $table->text('rejection_reason')->nullable(); // Why was it rejected (shown to user)
            $table->text('admin_notes')->nullable(); // Internal notes (not shown to user)

            // Link to created user (after approval)
            $table->unsignedBigInteger('user_id')->nullable(); // The User record created on approval

            // Tracking
            $table->string('ip_address')->nullable(); // For security auditing
            $table->text('user_agent')->nullable(); // Browser/device info

            $table->timestamps();

            // Indexes for faster queries
            $table->index('status');
            $table->index('request_type');
            $table->index(['status', 'request_type']); // Common filter combination
            $table->index('reviewed_at');
            $table->index('department'); // For bulk approval by department

            // Foreign keys - Using NO ACTION for SQL Server compatibility
            // SQL Server doesn't allow multiple cascade paths to the same table
            $table->foreign('reviewed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('no action');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('no action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_requests');
    }
};
