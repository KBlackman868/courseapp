<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(table: 'registrations', callback: function (Blueprint $table): void {
            $table->id(); // Unique ID for each registration record
            $table->unsignedBigInteger(column: 'user_id'); // Which user is signing up
            $table->unsignedBigInteger(column: 'course_id'); // Which course they are signing up for
            $table->enum(column: 'status', allowed: ['pending', 'approved', 'denied'])->default(value: 'pending');
            $table->timestamps();
            // This links the user_id to the id in the users table
            // If a user is deleted, their registrations will also be removed
            $table->foreign(columns: 'user_id')->references(columns: 'id')->on(table: 'users')->onDelete(action: 'cascade');
            // This links the course_id to the id in the courses table
            $table->foreign(columns: 'course_id')->references(columns: 'id')->on(table: 'courses')->onDelete(action: 'cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('registrations');
    }
};
