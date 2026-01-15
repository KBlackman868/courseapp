<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // User type: internal (MOH staff) or external
            $table->enum('user_type', ['internal', 'external'])->default('external')->after('email');
            
            // Course creator flag
            $table->boolean('is_course_creator')->default(false)->after('user_type');
            
            // LDAP fields for internal users
            $table->string('ldap_guid')->nullable()->after('is_course_creator');
            $table->string('ldap_username')->nullable()->after('ldap_guid');
            $table->timestamp('ldap_synced_at')->nullable()->after('ldap_username');
            
            // OTP fields
            $table->string('otp_code')->nullable()->after('ldap_synced_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->boolean('otp_verified')->default(false)->after('otp_expires_at');
            $table->timestamp('otp_verified_at')->nullable()->after('otp_verified');
            $table->integer('otp_attempts')->default(0)->after('otp_verified_at');
            
            // One-time OTP verification tracking
            $table->boolean('initial_otp_completed')->default(false)->after('otp_attempts');
            $table->timestamp('initial_otp_completed_at')->nullable()->after('initial_otp_completed');
            
            // Authentication method
            $table->enum('auth_method', ['local', 'ldap', 'google', 'saml'])->default('local')->after('initial_otp_completed_at');
            
            // Indexes
            $table->index('user_type');
            $table->index('is_course_creator');
            $table->index('ldap_guid');
            $table->index('auth_method');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['user_type']);
            $table->dropIndex(['is_course_creator']);
            $table->dropIndex(['ldap_guid']);
            $table->dropIndex(['auth_method']);
            
            $table->dropColumn([
                'user_type',
                'is_course_creator',
                'ldap_guid',
                'ldap_username',
                'ldap_synced_at',
                'otp_code',
                'otp_expires_at',
                'otp_verified',
                'otp_verified_at',
                'otp_attempts',
                'initial_otp_completed',
                'initial_otp_completed_at',
                'auth_method',
            ]);
        });
    }
};