<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class DiagnoseGoogleAuth extends Command
{
    protected $signature = 'diagnose:google-auth';
    protected $description = 'Diagnose Google OAuth authentication setup and issues';

    public function handle()
    {
        $this->info('🔍 Diagnosing Google OAuth Setup');
        $this->info('=====================================');
        
        $issues = [];
        $warnings = [];
        $success = [];
        
        // 1. Check database columns
        $this->info("\n📊 Checking Database Schema...");
        $this->checkDatabaseSchema($issues, $success);
        
        // 2. Check User model
        $this->info("\n📁 Checking User Model...");
        $this->checkUserModel($issues, $warnings, $success);
        
        // 3. Check Google OAuth configuration
        $this->info("\n🔑 Checking Google OAuth Configuration...");
        $this->checkGoogleConfig($issues, $warnings, $success);
        
        // 4. Check routes
        $this->info("\n🛣️  Checking Routes...");
        $this->checkRoutes($issues, $success);
        
        // 5. Check existing Google users
        $this->info("\n👥 Checking Existing Google Users...");
        $this->checkGoogleUsers();
        
        // 6. Summary
        $this->info("\n📋 Diagnosis Summary");
        $this->info("=====================================");
        
        if (count($success) > 0) {
            $this->info("\n✅ Working correctly:");
            foreach ($success as $item) {
                $this->info("   • " . $item);
            }
        }
        
        if (count($warnings) > 0) {
            $this->warn("\n⚠️  Warnings:");
            foreach ($warnings as $warning) {
                $this->warn("   • " . $warning);
            }
        }
        
        if (count($issues) > 0) {
            $this->error("\n❌ Issues found:");
            foreach ($issues as $issue) {
                $this->error("   • " . $issue);
            }
            
            $this->info("\n🔧 Recommended fixes:");
            $this->info("1. Run: php artisan migrate");
            $this->info("2. Update your User model fillable array");
            $this->info("3. Replace GoogleAuthController with the fixed version");
            $this->info("4. Clear caches: php artisan optimize:clear");
        } else {
            $this->info("\n✨ No critical issues found! Google OAuth should work correctly.");
        }
        
        return count($issues) > 0 ? 1 : 0;
    }
    
    private function checkDatabaseSchema(&$issues, &$success)
    {
        $requiredColumns = [
            'google_id' => 'string',
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'string',
            'department' => 'string',
            'profile_photo' => 'string',
            'verification_status' => 'string',
            'verification_sent_at' => 'datetime',
            'verification_attempts' => 'integer',
            'must_verify_before' => 'datetime'
        ];
        
        foreach ($requiredColumns as $column => $type) {
            if (!Schema::hasColumn('users', $column)) {
                $issues[] = "Missing column 'users.{$column}' ({$type})";
            } else {
                $success[] = "Column 'users.{$column}' exists";
            }
        }
        
        // Check if is_suspended exists (optional but recommended)
        if (!Schema::hasColumn('users', 'is_suspended')) {
            $issues[] = "Missing optional column 'users.is_suspended' (boolean)";
        }
    }
    
    private function checkUserModel(&$issues, &$warnings, &$success)
    {
        $userClass = \App\Models\User::class;
        
        if (!class_exists($userClass)) {
            $issues[] = "User model not found at App\\Models\\User";
            return;
        }
        
        $user = new $userClass();
        $fillable = $user->getFillable();
        
        $requiredFillable = [
            'google_id',
            'first_name',
            'last_name',
            'email',
            'password',
            'department',
            'profile_photo',
            'verification_status'
        ];
        
        foreach ($requiredFillable as $field) {
            if (!in_array($field, $fillable)) {
                $issues[] = "Field '{$field}' is not in User model fillable array";
            }
        }
        
        // Check for wrong field names
        if (in_array('name', $fillable)) {
            $warnings[] = "User model has 'name' field but Google Auth uses 'first_name' and 'last_name'";
        }
        
        if (in_array('avatar', $fillable)) {
            $warnings[] = "User model has 'avatar' field but should use 'profile_photo'";
        }
        
        // Check traits
        $traits = class_uses($userClass);
        if (!isset($traits['Spatie\Permission\Traits\HasRoles'])) {
            $warnings[] = "User model doesn't use HasRoles trait";
        } else {
            $success[] = "User model has HasRoles trait";
        }
        
        if (!isset($traits['App\Traits\HasEnhancedVerification'])) {
            $warnings[] = "User model doesn't use HasEnhancedVerification trait";
        } else {
            $success[] = "User model has HasEnhancedVerification trait";
        }
    }
    
    private function checkGoogleConfig(&$issues, &$warnings, &$success)
    {
        $clientId = config('services.google.client_id') ?? env('GOOGLE_CLIENT_ID');
        $clientSecret = config('services.google.client_secret') ?? env('GOOGLE_CLIENT_SECRET');
        $redirectUri = config('services.google.redirect') ?? env('GOOGLE_REDIRECT_URI');
        
        if (empty($clientId)) {
            $issues[] = "GOOGLE_CLIENT_ID not configured in .env";
        } else {
            $success[] = "Google Client ID is configured";
            if (!str_ends_with($clientId, '.apps.googleusercontent.com')) {
                $warnings[] = "Google Client ID doesn't look like a valid Google OAuth client ID";
            }
        }
        
        if (empty($clientSecret)) {
            $issues[] = "GOOGLE_CLIENT_SECRET not configured in .env";
        } else {
            $success[] = "Google Client Secret is configured";
        }
        
        if (empty($redirectUri)) {
            $issues[] = "GOOGLE_REDIRECT_URI not configured";
        } else {
            $success[] = "Google Redirect URI is configured: " . $redirectUri;
            
            // Check if it matches the current app URL
            $appUrl = config('app.url');
            if (!str_starts_with($redirectUri, $appUrl)) {
                $warnings[] = "Google Redirect URI doesn't match APP_URL. Expected to start with: {$appUrl}";
            }
            
            // Check protocol
            if (app()->environment('production') && !str_starts_with($redirectUri, 'https://')) {
                $issues[] = "Google Redirect URI must use HTTPS in production";
            }
        }
    }
    
    private function checkRoutes(&$issues, &$success)
    {
        $routes = app('router')->getRoutes();
        
        $requiredRoutes = [
            'auth.google' => 'GET /auth/google',
            'auth.google.callback' => 'GET /auth/google/callback'
        ];
        
        foreach ($requiredRoutes as $name => $signature) {
            $route = $routes->getByName($name);
            if (!$route) {
                // Try to find by URI
                [$method, $uri] = explode(' ', $signature);
                $found = false;
                foreach ($routes as $r) {
                    if ($r->uri() === trim($uri, '/') && in_array($method, $r->methods())) {
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $issues[] = "Route '{$name}' ({$signature}) not found";
                } else {
                    $success[] = "Route {$signature} exists";
                }
            } else {
                $success[] = "Route '{$name}' is registered";
            }
        }
    }
    
    private function checkGoogleUsers()
    {
        try {
            $googleUsers = DB::table('users')
                ->whereNotNull('google_id')
                ->count();
                
            $this->info("   Total users with Google accounts: " . $googleUsers);
            
            if ($googleUsers > 0) {
                $sampleUser = DB::table('users')
                    ->whereNotNull('google_id')
                    ->first();
                    
                $this->info("   Sample Google user:");
                $this->info("      Email: " . $sampleUser->email);
                $this->info("      Name: " . $sampleUser->first_name . ' ' . $sampleUser->last_name);
                $this->info("      Verified: " . ($sampleUser->email_verified_at ? 'Yes' : 'No'));
            }
            
            // Check for potential issues
            $wrongFieldUsers = DB::table('users')
                ->whereNotNull('google_id')
                ->where(function($query) {
                    $query->whereNull('first_name')
                          ->orWhereNull('last_name');
                })
                ->count();
                
            if ($wrongFieldUsers > 0) {
                $this->warn("   ⚠️  Found {$wrongFieldUsers} Google users with missing first_name or last_name");
            }
            
        } catch (\Exception $e) {
            $this->error("   Could not query users table: " . $e->getMessage());
        }
    }
}