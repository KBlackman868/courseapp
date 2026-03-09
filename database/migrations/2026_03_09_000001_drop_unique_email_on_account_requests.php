<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remove the unique constraint on account_requests.email
 *
 * The unique constraint blocks re-registration when old requests
 * (rejected/approved) still exist in the table. Email uniqueness
 * for pending requests is now enforced at the application level.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_requests', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('account_requests', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->unique('email');
        });
    }
};
