<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('enrollment_requests')) {
            Schema::create('enrollment_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('course_id')->constrained()->onDelete('cascade');
                $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');
                $table->text('request_reason')->nullable();
                $table->text('admin_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'course_id']);
                $table->index('status');
                $table->index(['user_id', 'status']);
                $table->index(['course_id', 'status']);
                $table->index('reviewed_at');
                
                // Use NO ACTION to avoid SQL Server cascade path error
                $table->foreign('reviewed_by')
                    ->references('id')
                    ->on('users')
                    ->onDelete('no action');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_requests');
    }
};