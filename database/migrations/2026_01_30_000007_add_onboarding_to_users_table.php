<?php

/**
 * Add Onboarding Tracking to Users Table
 *
 * This migration adds fields for tracking user onboarding state.
 *
 * ONBOARDING FLOW:
 * - New users see a welcome banner explaining the system
 * - Banner can be dismissed by clicking "Got it" or similar
 * - Once dismissed, onboarding_completed_at is set
 * - Banner never shows again
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Track when user completed/dismissed onboarding
            if (!Schema::hasColumn('users', 'onboarding_completed_at')) {
                $table->timestamp('onboarding_completed_at')->nullable()->after('email_verified_at');
            }

            // Track user account status (for MOH Staff pending approval)
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('onboarding_completed_at');
            }
        });

        // Add index for status queries
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->index('status', 'users_status_index');
            });
        } catch (\Exception $e) {
            // Index may already exist
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed_at', 'status']);
        });
    }
};
