<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (DB::getDriverName() === 'sqlsrv') {
            // SQL Server: find and drop unique constraints by column name
            // (constraint names may not match Laravel's naming convention)
            $constraints = DB::select("
                SELECT i.name AS index_name
                FROM sys.indexes i
                INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
                INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
                WHERE i.object_id = OBJECT_ID('account_requests')
                AND c.name = 'email'
                AND i.is_unique = 1
                AND i.is_primary_key = 0
            ");

            foreach ($constraints as $constraint) {
                DB::statement("DROP INDEX [{$constraint->index_name}] ON [account_requests]");
            }

            // Add a non-unique index for query performance
            if (empty($constraints)) {
                // No unique index found — might already have been dropped
            } else {
                DB::statement("CREATE INDEX [account_requests_email_index] ON [account_requests] ([email])");
            }
        } else {
            Schema::table('account_requests', function (Blueprint $table) {
                try {
                    $table->dropUnique(['email']);
                } catch (\Exception $e) {
                    // Index might not exist or have a different name
                }
                $table->index('email');
            });
        }
    }

    public function down(): void
    {
        Schema::table('account_requests', function (Blueprint $table) {
            try {
                $table->dropIndex(['email']);
            } catch (\Exception $e) {
                // ignore
            }
            $table->unique('email');
        });
    }
};
