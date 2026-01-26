<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Audience type: who can see/enroll in this course
            // moh = MOH Staff only (internal users)
            // external = External users only
            // all = Both MOH Staff and External users
            $table->enum('audience_type', ['moh', 'external', 'all'])
                ->default('moh')
                ->after('status');

            // Free courses allow direct enrollment without approval
            // Non-free courses require enrollment request approval
            $table->boolean('is_free')->default(false)->after('audience_type');

            // Course visibility/active status
            $table->boolean('is_active')->default(true)->after('is_free');

            $table->index('audience_type');
            $table->index('is_free');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['audience_type']);
            $table->dropIndex(['is_free']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['audience_type', 'is_free', 'is_active']);
        });
    }
};
