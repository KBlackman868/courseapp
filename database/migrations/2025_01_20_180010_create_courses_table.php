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
        Schema::create('courses', function (Blueprint $table) {
            $table->id(); // Unique ID for each course
            $table->string('title'); // Name of the course, like "PowerPoint Basics"
            $table->unsignedBigInteger('moodle_course_id')->nullable()->index()->after('id');
            $table->string('moodle_course_shortname')->nullable()->unique()->after('moodle_course_id');
            $table->text('description')->nullable(); // A short paragraph about the course
            $table->enum('status', ['active','inactive'])->default('active'); // Whether the course is available or not
            $table->timestamps(); // Track when each course was created or updated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
