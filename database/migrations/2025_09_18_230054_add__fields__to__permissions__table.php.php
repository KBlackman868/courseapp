<?php
// database/migrations/2024_XX_XX_add_fields_to_permissions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'category')) {
                    $table->string('category', 50)->nullable()->after('guard_name');
                }
                if (!Schema::hasColumn('permissions', 'description')) {
                    $table->text('description')->nullable()->after('category');
                }
                
                // Add index if it doesn't exist
                $table->index('category', 'permissions_category_index');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropIndex('permissions_category_index');
                $table->dropColumn(['category', 'description']);
            });
        }
    }
};