<?php
// database/migrations/2025_09_19_[timestamp]_add_category_description_to_permission_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add fields to permissions table
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'category')) {
                    $table->string('category', 50)->nullable()->after('guard_name');
                }
                if (!Schema::hasColumn('permissions', 'description')) {
                    $table->text('description')->nullable()->after('category');
                }
            });
        }

        // Add fields to roles table  
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'display_name')) {
                    $table->string('display_name')->nullable()->after('name');
                }
                if (!Schema::hasColumn('roles', 'description')) {
                    $table->text('description')->nullable()->after('display_name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropColumn(['category', 'description']);
            });
        }

        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn(['display_name', 'description']);
            });
        }
    }
};