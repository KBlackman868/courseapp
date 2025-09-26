<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class DebugMailConfig extends Command
{
    protected $signature = 'debug:mail';
    protected $description = 'Debug mail configuration and test sending';

    public function handle()
    {
        $this->info('🔍 Debugging Mail Configuration');
        $this->info('=====================================');
        
        // 1. Check basic mail config
        $this->info("\n📧 Mail Configuration:");
        $this->table(
            ['Setting', 'Value'],
            [
                ['Default Mailer', config('mail.default')],
                ['Queue Default', config('queue.default')],
                ['From Address', config('mail.from.address')],
                ['From Name', config('mail.from.name')],
            ]
        );
        
        // 2. Check SMTP settings if using SMTP
        if (config('mail.default') === 'smtp') {
            $this->info("\n📬 SMTP Settings:");
            $this->table(
                ['Setting', 'Value'],
                [
                    ['Host', config('mail.mailers.smtp.host')],
                    ['Port', config('mail.mailers.smtp.port')],
                    ['Encryption', config('mail.mailers.smtp.encryption')],
                    ['Username', config('mail.mailers.smtp.username') ? '***SET***' : 'NOT SET'],
                    ['Password', config('mail.mailers.smtp.password') ? '***SET***' : 'NOT SET'],
                ]
            );
        }
        
        // 3. Test with a simple raw email
        $this->info("\n🧪 Test 1: Sending RAW email (no queue)...");
        try {
            $testEmail = config('mail.from.address', 'test@example.com');
            
            Mail::raw('This is a test email from Laravel', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('Test Email - ' . now()->format('Y-m-d H:i:s'));
            });
            
            $this->info("✅ Raw email sent successfully to {$testEmail}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send raw email:");
            $this->error($e->getMessage());
            $this->info("\nFull error:");
            $this->error($e->getTraceAsString());
        }
        
        // 4. Check if EnrollmentConfirmationEmail class exists
        $this->info("\n🧪 Test 2: Checking Email Classes...");
        
        $emailClasses = [
            'App\Mail\EnrollmentConfirmationEmail',
            'App\Mail\EnrollmentApprovedEmail', 
            'App\Mail\NewCourseEnrollmentEmail',
        ];
        
        foreach ($emailClasses as $class) {
            if (class_exists($class)) {
                $this->info("✅ {$class} exists");
                
                // Check if it implements ShouldQueue
                $reflection = new \ReflectionClass($class);
                $interfaces = $reflection->getInterfaceNames();
                if (in_array('Illuminate\Contracts\Queue\ShouldQueue', $interfaces)) {
                    $this->warn("   ⚠️  This email is QUEUED - needs queue:work running");
                } else {
                    $this->info("   ✓ This email is SYNCHRONOUS - sends immediately");
                }
            } else {
                $this->error("❌ {$class} NOT FOUND");
            }
        }
        
        // 5. Check blade templates
        $this->info("\n🧪 Test 3: Checking Email Templates...");
        
        $templates = [
            'emails.enrollment-confirmation',
            'emails.enrollment-approved',
            'emails.new-enrollment-admin',
            'emails.welcome',
        ];
        
        foreach ($templates as $template) {
            $path = resource_path('views/' . str_replace('.', '/', $template) . '.blade.php');
            if (file_exists($path)) {
                $this->info("✅ {$template} exists");
            } else {
                $this->error("❌ {$template} NOT FOUND at {$path}");
                
                // Check for common typos
                $typoPath = str_replace('enrollment', 'enrollement', $path);
                if (file_exists($typoPath)) {
                    $this->warn("   ⚠️  Found with typo at: {$typoPath}");
                    $this->warn("   Run: mv {$typoPath} {$path}");
                }
            }
        }
        
        // 6. Test Telescope recording
        $this->info("\n🔭 Test 4: Telescope Status...");
        
        if (class_exists('\Laravel\Telescope\Telescope')) {
            $this->info("✅ Telescope is installed");
            
            // Check if Telescope is enabled
            if (config('telescope.enabled', true)) {
                $this->info("✅ Telescope is enabled");
            } else {
                $this->warn("⚠️  Telescope is DISABLED");
            }
            
            // Check storage
            $telescopeStorage = config('telescope.storage.database.connection');
            $this->info("   Storage connection: " . ($telescopeStorage ?: 'default'));
            
            // Try to count entries
            try {
                $count = \DB::table('telescope_entries')->count();
                $this->info("   Total Telescope entries: {$count}");
                
                $recentMail = \DB::table('telescope_entries')
                    ->where('type', 'mail')
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($recentMail) {
                    $this->info("   Last mail entry: " . $recentMail->created_at);
                } else {
                    $this->warn("   No mail entries found in Telescope");
                }
            } catch (\Exception $e) {
                $this->error("   Could not query Telescope entries: " . $e->getMessage());
            }
        } else {
            $this->error("❌ Telescope is NOT installed");
        }
        
        // 7. Check queue status
        $this->info("\n⚙️ Test 5: Queue Status...");
        
        if (config('queue.default') === 'sync') {
            $this->info("✅ Queue is SYNC - jobs run immediately");
        } else {
            $queueDriver = config('queue.default');
            $this->warn("⚠️  Queue is {$queueDriver} - needs 'php artisan queue:work'");
            
            // Check failed jobs
            try {
                $failedCount = \DB::table('failed_jobs')->count();
                if ($failedCount > 0) {
                    $this->error("   ❌ You have {$failedCount} failed jobs!");
                    $this->info("   Run: php artisan queue:failed");
                }
            } catch (\Exception $e) {
                $this->info("   Could not check failed jobs table");
            }
        }
        
        // 8. Try to clear caches that might affect this
        $this->info("\n🧹 Clearing potential cache issues...");
        
        $this->call('config:clear');
        $this->info("✅ Config cache cleared");
        
        $this->call('view:clear');
        $this->info("✅ View cache cleared");
        
        if (class_exists('\Laravel\Telescope\Telescope')) {
            try {
                \Artisan::call('telescope:clear');
                $this->info("✅ Telescope entries cleared");
            } catch (\Exception $e) {
                $this->warn("Could not clear Telescope: " . $e->getMessage());
            }
        }
        
        $this->info("\n✨ Debugging complete!");
        $this->info("\n📋 Next steps:");
        $this->info("1. Fix any ❌ errors shown above");
        $this->info("2. If using queued mail, run: php artisan queue:work");
        $this->info("3. Try sending a test email again");
        $this->info("4. Check /telescope/mail for new entries");
        
        return 0;
    }
}