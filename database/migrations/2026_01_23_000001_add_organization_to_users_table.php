<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds organization field to support external users
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'organization')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('organization')->nullable()->after('department');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'organization')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('organization');
            });
        }
    }
};
