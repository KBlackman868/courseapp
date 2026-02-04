<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Jobs\EnrollUserIntoMoodleCourse;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TestMoodleUserFlow extends Command
{
    protected $signature = 'moodle:test-flow {--email=} {--course_id=}';
    protected $description = 'Test complete Moodle user creation and enrollment flow';

    public function handle()
    {
        $email = $this->option('email') ?? 'test_' . Str::random(8) . '@example.com';
        $courseId = $this->option('course_id');

        $this->info('Testing Moodle User Flow');
        $this->info('========================');

        // Step 1: Create or find user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->info("Creating new user: $email");
            
            $user = User::create([
                'first_name' => 'Test',
                'last_name' => 'User_' . Str::random(4),
                'email' => $email,
                'password' => bcrypt('password123'),
                'department' => 'Testing',
            ]);
            
            $user->assignRole('user');
            $this->info("✓ User created with ID: {$user->id}");
        } else {
            $this->info("✓ Using existing user: {$user->email}");
        }

        // Step 2: Create/Link Moodle user
        if (!$user->moodle_user_id) {
            $this->info("Creating Moodle user...");
            
            try {
                CreateOrLinkMoodleUser::dispatchSync(
                    $user,
                    $user->email,
                    $user->first_name,
                    $user->last_name
                );
                
                $user->refresh();
                
                if ($user->moodle_user_id) {
                    $this->info("✓ Moodle user created with ID: {$user->moodle_user_id}");
                } else {
                    $this->error("Failed to create Moodle user");
                    return 1;
                }
            } catch (\Exception $e) {
                $this->error("Error creating Moodle user: " . $e->getMessage());
                return 1;
            }
        } else {
            $this->info("✓ User already has Moodle ID: {$user->moodle_user_id}");
        }

        // Step 3: Enroll in course (if specified)
        if ($courseId) {
            $course = Course::find($courseId);
            
            if (!$course) {
                $this->error("Course with ID $courseId not found");
                return 1;
            }
            
            if (!$course->moodle_course_id) {
                $this->error("Course is not synced to Moodle");
                return 1;
            }
            
            $this->info("Enrolling user in course: {$course->title}");
            
            // Check for existing enrollment
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();
                
            if (!$enrollment) {
                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => 'approved',
                ]);
                $this->info("✓ Local enrollment created");
            } else {
                $this->info("✓ Enrollment already exists");
            }
            
            // Sync to Moodle
            try {
                EnrollUserIntoMoodleCourse::dispatchSync($user, $course);
                $this->info("✓ User enrolled in Moodle course");
            } catch (\Exception $e) {
                $this->error("Error enrolling in Moodle: " . $e->getMessage());
                return 1;
            }
        }

        $this->info("\n✅ Test completed successfully!");
        $this->info("User Email: {$user->email}");
        $this->info("Moodle User ID: {$user->moodle_user_id}");
        
        if ($courseId) {
            $this->info("Enrolled in Course ID: $courseId");
        }

        return 0;
    }
}