<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('moodle_category_id')->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('idnumber')->nullable(); // Moodle's category ID number
            $table->integer('sortorder')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
            
            $table->index('moodle_category_id');
            $table->index('parent_id');
        });
        
        // Add category_id to courses table if not exists
        if (!Schema::hasColumn('courses', 'category_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('moodle_course_id');
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        
        Schema::dropIfExists('categories');
    }
};