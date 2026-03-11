<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix the CHECK constraint on account_requests.status for SQL Server.
 *
 * The original migration created an enum with only: pending, approved, rejected, suspended.
 * The verification workflow migration (2026_03_09_100000) added columns but never updated
 * the CHECK constraint to include: pending_verification, email_verified.
 *
 * This caused: SQLSTATE[23000] The INSERT statement conflicted with the CHECK constraint
 * "CK__account_r__statu__11D4A34F" when registering with status 'pending_verification'.
 */
return new class extends Migration
{
    public function up(): void
    {
        // SQL Server: drop the old CHECK constraint and create a new one with all valid statuses
        if (DB::getDriverName() === 'sqlsrv') {
            // Find and drop any existing CHECK constraints on the status column
            $constraints = DB::select("
                SELECT cc.name AS constraint_name
                FROM sys.check_constraints cc
                INNER JOIN sys.columns c ON cc.parent_object_id = c.object_id AND cc.parent_column_id = c.column_id
                WHERE cc.parent_object_id = OBJECT_ID('account_requests')
                AND c.name = 'status'
            ");

            foreach ($constraints as $constraint) {
                DB::statement("ALTER TABLE [account_requests] DROP CONSTRAINT [{$constraint->constraint_name}]");
            }

            // Add updated CHECK constraint with all valid statuses
            DB::statement("
                ALTER TABLE [account_requests]
                ADD CONSTRAINT CK_account_requests_status
                CHECK ([status] IN ('pending', 'pending_verification', 'email_verified', 'approved', 'rejected', 'suspended'))
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlsrv') {
            DB::statement("ALTER TABLE [account_requests] DROP CONSTRAINT IF EXISTS [CK_account_requests_status]");

            DB::statement("
                ALTER TABLE [account_requests]
                ADD CONSTRAINT CK_account_requests_status
                CHECK ([status] IN ('pending', 'approved', 'rejected', 'suspended'))
            ");
        }
    }
};
