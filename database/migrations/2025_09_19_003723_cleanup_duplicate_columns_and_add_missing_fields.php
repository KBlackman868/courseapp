<?php
// database/migrations/2025_09_18_cleanup_duplicate_columns_and_add_missing_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add is_suspended to users if it doesn't exist
        if (!Schema::hasColumn('users', 'is_suspended')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_suspended')->default(false)->after('profile_photo');
            });
        }
        
        // Add email_verified_at if missing
        if (!Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_suspended')) {
                $table->dropColumn('is_suspended');
            }
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }
};