<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns for email verification workflow
        Schema::table('account_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('account_requests', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('account_requests', 'verification_token')) {
                $table->string('verification_token')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('account_requests', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('phone');
            }
        });

        // Update the status enum to include new statuses
        // SQLite doesn't support ALTER COLUMN, so we handle this at application level
        // The AccountRequest model will accept: pending, pending_verification, email_verified, approved, rejected, suspended
    }

    public function down(): void
    {
        Schema::table('account_requests', function (Blueprint $table) {
            if (Schema::hasColumn('account_requests', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('account_requests', 'verification_token')) {
                $table->dropColumn('verification_token');
            }
            if (Schema::hasColumn('account_requests', 'date_of_birth')) {
                $table->dropColumn('date_of_birth');
            }
        });
    }
};
