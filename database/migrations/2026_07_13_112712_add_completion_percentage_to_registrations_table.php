<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->unsignedTinyInteger('completion_percentage')->default(0)->after('status');
            $table->timestamp('completion_synced_at')->nullable()->after('completion_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['completion_percentage', 'completion_synced_at']);
        });
    }
};
