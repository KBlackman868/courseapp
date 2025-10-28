<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix existing TEST USER entries and clean up names
     */
    public function up()
    {
        // Find and fix all users with TEST or generic names
        $usersToFix = DB::table('users')
            ->where(function($query) {
                $query->where('first_name', 'LIKE', '%TEST%')
                      ->orWhere('first_name', '=', 'User')
                      ->orWhere('first_name', '=', '')
                      ->orWhereNull('first_name');
            })
            ->get();

        foreach ($usersToFix as $user) {
            $updates = [];
            
            // Try to extract name from email
            $emailParts = explode('@', $user->email);
            $localPart = $emailParts[0] ?? 'user';
            
            // Remove common prefixes/suffixes
            $localPart = preg_replace('/[._-]+/', ' ', $localPart);
            $localPart = preg_replace('/\d+$/', '', $localPart); // Remove trailing numbers
            
            // Parse the name
            $nameParts = explode(' ', trim($localPart));
            
            if (count($nameParts) >= 2) {
                $updates['first_name'] = ucfirst(strtolower($nameParts[0]));
                $updates['last_name'] = ucfirst(strtolower($nameParts[1]));
            } elseif (count($nameParts) == 1) {
                $updates['first_name'] = ucfirst(strtolower($nameParts[0]));
                $updates['last_name'] = '';
            } else {
                // Fallback
                $updates['first_name'] = 'MOH';
                $updates['last_name'] = 'User';
            }
            
            // Update the user
            if (!empty($updates)) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(array_merge($updates, [
                        'updated_at' => now()
                    ]));
                    
                echo "Fixed user {$user->id}: {$user->email} -> {$updates['first_name']} {$updates['last_name']}\n";
            }
        }
        
        // Add department field if it doesn't exist
        if (!Schema::hasColumn('users', 'department')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('department')->nullable()->after('last_name');
            });
        }
        
        // Add organization field if it doesn't exist
        if (!Schema::hasColumn('users', 'organization')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('organization')->default('Ministry of Health Trinidad and Tobago')->after('department');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // This migration is not reversible for the name fixes
        // But we can remove the added columns
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['department', 'organization']);
        });
    }
};