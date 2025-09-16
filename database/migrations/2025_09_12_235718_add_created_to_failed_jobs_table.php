<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedAtToFailedJobsTable extends Migration
{
    public function up()
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('failed_jobs', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
        });
        
        // Update existing records with current timestamp
        DB::table('failed_jobs')->whereNull('created_at')->update(['created_at' => now()]);
    }

    public function down()
    {
        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->dropColumn('created_at');
        });
    }
}