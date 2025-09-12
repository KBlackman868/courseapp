<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('moodle_course_id')->nullable()->index()
                ->after('id');
            $table->string('moodle_course_shortname')->nullable()->unique()
                ->after('moodle_course_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['moodle_course_id', 'moodle_course_shortname']);
        });
    }
};