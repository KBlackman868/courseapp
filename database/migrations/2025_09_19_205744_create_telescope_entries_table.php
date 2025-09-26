<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelescopeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the Telescope database connection
        $connection = config('telescope.storage.database.connection') ?? config('database.default');
        
        Schema::connection($connection)->create('telescope_entries', function (Blueprint $table) {
            $table->bigIncrements('sequence');
            $table->uuid('uuid');
            $table->uuid('batch_id');
            $table->string('family_hash')->nullable()->index();
            $table->boolean('should_display_on_index')->default(true);
            $table->string('type', 20);
            $table->longText('content');
            $table->dateTime('created_at')->nullable()->index();

            $table->unique('uuid');
            $table->index('batch_id');
            $table->index(['type', 'should_display_on_index']);
        });

        Schema::connection($connection)->create('telescope_entries_tags', function (Blueprint $table) {
            $table->uuid('entry_uuid');
            $table->string('tag')->index();

            $table->primary(['entry_uuid', 'tag']);
            $table->foreign('entry_uuid')
                  ->references('uuid')
                  ->on('telescope_entries')
                  ->onDelete('cascade');
        });

        Schema::connection($connection)->create('telescope_monitoring', function (Blueprint $table) {
            $table->string('tag')->primary();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = config('telescope.storage.database.connection') ?? config('database.default');
        
        Schema::connection($connection)->dropIfExists('telescope_monitoring');
        Schema::connection($connection)->dropIfExists('telescope_entries_tags');
        Schema::connection($connection)->dropIfExists('telescope_entries');
    }
}