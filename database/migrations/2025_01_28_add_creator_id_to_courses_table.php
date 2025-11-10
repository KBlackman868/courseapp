<?php
// database/migrations/2025_01_06_create_activity_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_email')->nullable(); // Keep email even if user is deleted
            $table->string('user_name')->nullable(); // Keep name for reference
            $table->string('action'); // e.g., 'course.created', 'user.login', 'enrollment.approved'
            $table->string('description');
            $table->string('model_type')->nullable(); // e.g., 'App\Models\Course'
            $table->unsignedBigInteger('model_id')->nullable(); // ID of affected model
            $table->json('properties')->nullable(); // Additional data (old values, new values, etc.)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE
            $table->string('url')->nullable();
            $table->string('status')->default('success'); // success, failed, pending
            $table->string('severity')->default('info'); // info, warning, error, critical
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index('model_type');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};