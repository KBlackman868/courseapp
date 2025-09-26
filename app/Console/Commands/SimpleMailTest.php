<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class SimpleMailTest extends Command
{
    protected $signature = 'test:simple-mail {email?}';
    protected $description = 'Send a simple test email to verify mail is working';

    public function handle()
    {
        $email = $this->argument('email') ?? config('mail.from.address', 'test@example.com');
        
        $this->info("🚀 Simple Mail Test");
        $this->info("===================");
        $this->info("Sending to: {$email}");
        $this->info("Mail driver: " . config('mail.default'));
        
        // 1. First, try the absolute simplest email
        $this->info("\n📧 Sending simple text email...");
        
        try {
            Mail::raw('If you see this, mail is working!', function ($message) use ($email) {
                $message->to($email)->subject('Simple Test - ' . now());
            });
            
            $this->info("✅ Email sent!");
            
            // Now let's check what Telescope saw
            if (class_exists('\Laravel\Telescope\Telescope')) {
                sleep(1); // Give Telescope a moment to record
                
                $lastMail = \DB::table('telescope_entries')
                    ->where('type', 'mail')
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($lastMail) {
                    $this->info("\n🔭 Telescope recorded mail at: " . $lastMail->created_at);
                    $content = json_decode($lastMail->content, true);
                    $this->info("   To: " . implode(', ', $content['to'] ?? []));
                    $this->info("   Subject: " . ($content['subject'] ?? 'N/A'));
                } else {
                    $this->warn("\n⚠️  No mail entry found in Telescope");
                    $this->info("This could mean:");
                    $this->info("  1. Mail driver is 'log' (check storage/logs/laravel.log)");
                    $this->info("  2. Telescope is not recording properly");
                    $this->info("  3. Database connection issue");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email!");
            $this->error("Error: " . $e->getMessage());
            
            // Provide specific guidance based on error
            if (str_contains($e->getMessage(), 'Connection could not be established')) {
                $this->warn("\n📌 SMTP Connection Issue Detected!");
                $this->info("Check your .env file:");
                $this->info("  MAIL_HOST=" . config('mail.mailers.smtp.host'));
                $this->info("  MAIL_PORT=" . config('mail.mailers.smtp.port'));
                $this->info("  MAIL_USERNAME=" . (config('mail.mailers.smtp.username') ? '***SET***' : 'NOT SET'));
            }
            
            if (str_contains($e->getMessage(), 'Failed to authenticate')) {
                $this->warn("\n📌 SMTP Authentication Issue!");
                $this->info("Check your MAIL_USERNAME and MAIL_PASSWORD in .env");
            }
        }
        
        // 2. Now test with an actual enrollment if it exists
        $this->info("\n📧 Testing with EnrollmentConfirmationEmail class...");
        
        $enrollment = Enrollment::with(['user', 'course'])->first();
        
        if (!$enrollment) {
            $this->warn("No enrollments found. Creating test data...");
            
            $user = User::first();
            $course = Course::first();
            
            if (!$user || !$course) {
                $this->error("No users or courses found. Please create some test data first.");
                return 1;
            }
            
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'pending'
            ]);
        }
        
        try {
            // First check if the class exists
            if (!class_exists(\App\Mail\EnrollmentConfirmationEmail::class)) {
                $this->error("❌ EnrollmentConfirmationEmail class not found!");
                $this->info("Create it with: php artisan make:mail EnrollmentConfirmationEmail");
                return 1;
            }
            
            $mailable = new \App\Mail\EnrollmentConfirmationEmail($enrollment);
            Mail::to($email)->send($mailable);
            
            $this->info("✅ EnrollmentConfirmationEmail sent!");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send EnrollmentConfirmationEmail!");
            $this->error("Error: " . $e->getMessage());
            
            // Check for view errors
            if (str_contains($e->getMessage(), 'View') || str_contains($e->getMessage(), 'not found')) {
                $this->warn("\n📌 Template Issue Detected!");
                $this->info("Looking for template issues...");
                
                $correctPath = resource_path('views/emails/enrollment-confirmation.blade.php');
                $typoPath = resource_path('views/emails/enrollement-confirmation.blade.php');
                
                if (file_exists($typoPath) && !file_exists($correctPath)) {
                    $this->error("Found typo in filename!");
                    $this->info("Run: mv {$typoPath} {$correctPath}");
                } elseif (!file_exists($correctPath)) {
                    $this->error("Template not found at: {$correctPath}");
                }
            }
        }
        
        $this->info("\n✨ Test complete!");
        $this->info("\nCheck:");
        $this->info("1. /telescope/mail - for mail entries");
        $this->info("2. /telescope/logs - for log entries");
        $this->info("3. storage/logs/laravel.log - if MAIL_MAILER=log");
        
        return 0;
    }
}