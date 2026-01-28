<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip - indexes already exist on activity_logs from its creation migration
        // Other tables may already have these indexes too
    }

    public function down(): void
    {
        // Nothing to reverse
    }
};