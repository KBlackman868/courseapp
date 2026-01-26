<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Account status for approval workflow
            // pending = external users awaiting admin approval
            // active = approved and can log in
            // inactive = disabled account
            $table->enum('account_status', ['pending', 'active', 'inactive'])
                ->default('active')
                ->after('user_type');

            $table->index('account_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['account_status']);
            $table->dropColumn('account_status');
        });
    }
};
