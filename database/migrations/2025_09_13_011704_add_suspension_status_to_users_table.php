<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Update the empty migration: 2025_09_13_011704_add_suspension_status_to_users_table.php
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_suspended')->default(false)->after('department');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
