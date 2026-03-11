<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix the status column on account_requests to accept the new verification statuses.
 *
 * The original migration created an enum with only: pending, approved, rejected, suspended.
 * The verification workflow migration (2026_03_09_100000) added columns but never updated
 * the status column to include: pending_verification, email_verified.
 *
 * This fixes:
 * - MySQL: ALTER the enum column to include the new values
 * - SQL Server: DROP/recreate the CHECK constraint
 * - SQLite: uses string columns (no constraint to fix)
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE `account_requests` MODIFY `status` VARCHAR(30) NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'sqlsrv') {
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
        // SQLite: no constraint to fix (enum creates a text column)
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE `account_requests` MODIFY `status` ENUM('pending', 'approved', 'rejected', 'suspended') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'sqlsrv') {
            DB::statement("ALTER TABLE [account_requests] DROP CONSTRAINT IF EXISTS [CK_account_requests_status]");

            DB::statement("
                ALTER TABLE [account_requests]
                ADD CONSTRAINT CK_account_requests_status
                CHECK ([status] IN ('pending', 'approved', 'rejected', 'suspended'))
            ");
        }
    }
};
