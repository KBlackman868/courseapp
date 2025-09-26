<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if email_verified_at already exists (Laravel default)
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            
            // Add additional verification tracking fields
            if (!Schema::hasColumn('users', 'verification_status')) {
                $table->enum('verification_status', ['unverified', 'pending', 'verified', 'expired'])
                      ->default('unverified')
                      ->after('email_verified_at')
                      ->comment('Current verification status of the user');
            }
            
            if (!Schema::hasColumn('users', 'verification_sent_at')) {
                $table->timestamp('verification_sent_at')->nullable()
                      ->after('verification_status')
                      ->comment('When the last verification email was sent');
            }
            
            if (!Schema::hasColumn('users', 'verification_attempts')) {
                $table->unsignedSmallInteger('verification_attempts')->default(0)
                      ->after('verification_sent_at')
                      ->comment('Number of verification emails sent');
            }
            
            if (!Schema::hasColumn('users', 'must_verify_before')) {
                $table->timestamp('must_verify_before')->nullable()
                      ->after('verification_attempts')
                      ->comment('Deadline for email verification');
            }
            
            // Add indexes for better query performance
            $table->index('verification_status', 'idx_verification_status');
            $table->index('email_verified_at', 'idx_email_verified_at');
        });
        
        // Update existing users to have correct verification status
        DB::statement("
            UPDATE users 
            SET verification_status = CASE 
                WHEN email_verified_at IS NOT NULL THEN 'verified'
                ELSE 'unverified'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('idx_verification_status');
            $table->dropIndex('idx_email_verified_at');
            
            // Drop columns
            $table->dropColumn([
                'verification_status',
                'verification_sent_at',
                'verification_attempts',
                'must_verify_before'
            ]);
        });
    }
};