<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration  // Anonymous class - no name conflict
{
    public function up(): void
    {
        if (Schema::hasTable('failed_jobs') && !Schema::hasColumn('failed_jobs', 'created_at')) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });
            
            // Update existing records
            DB::table('failed_jobs')->whereNull('created_at')->update(['created_at' => now()]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('failed_jobs', 'created_at')) {
            Schema::table('failed_jobs', function (Blueprint $table) {
                $table->dropColumn('created_at');
            });
        }
    }
};