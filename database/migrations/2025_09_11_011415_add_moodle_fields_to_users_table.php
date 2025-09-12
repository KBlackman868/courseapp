<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('moodle_user_id')->nullable()->index();
        });

        // Schema::table('courses', function (Blueprint $table) {
        //     $table->unsignedBigInteger('moodle_course_id')->nullable()->index();
        // });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('moodle_user_id');
        });

        // Schema::table('courses', function (Blueprint $table) {
        //     $table->dropColumn('moodle_course_id');
        // });
    }
};