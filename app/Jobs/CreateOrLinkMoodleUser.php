<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Exceptions\MoodleException;
use App\Models\User;
use App\Services\MoodleClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateOrLinkMoodleUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private User $user,
        private ?string $email = null,
        private ?string $firstName = null,
        private ?string $lastName = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(MoodleClient $moodleClient): void
    {
        // If user already has a Moodle ID, update their profile
        if ($this->user->moodle_user_id) {
            $this->updateMoodleUser($moodleClient);
            return;
        }

        // Try to find existing Moodle user by email
        $email = $this->email ?? $this->user->email;
        $existingMoodleUser = $this->findMoodleUserByEmail($moodleClient, $email);

        if ($existingMoodleUser) {
            // Link existing Moodle user
            $this->user->update(['moodle_user_id' => $existingMoodleUser['id']]);
            Log::info('Linked existing Moodle user', [
                'user_id' => $this->user->id,
                'moodle_user_id' => $existingMoodleUser['id'],
            ]);
        } else {
            // Create new Moodle user
            $moodleUserId = $this->createMoodleUser($moodleClient);
            $this->user->update(['moodle_user_id' => $moodleUserId]);
            Log::info('Created new Moodle user', [
                'user_id' => $this->user->id,
                'moodle_user_id' => $moodleUserId,
            ]);
        }
    }

    /**
     * Find a Moodle user by email
     */
    private function findMoodleUserByEmail(MoodleClient $moodleClient, string $email): ?array
    {
        try {
            $response = $moodleClient->call('core_user_get_users_by_field', [
                'field' => 'email',
                'values' => [$email],
            ]);

            if (!empty($response) && isset($response[0])) {
                return $response[0];
            }
        } catch (MoodleException $e) {
            Log::warning('Failed to search for Moodle user by email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Create a new user in Moodle
     */
    private function createMoodleUser(MoodleClient $moodleClient): int
    {
        $username = $this->generateUsername();
        $password = $this->generateSecurePassword();
        
        $userData = [
            'users' => [
                [
                    'username' => $username,
                    'password' => $password,
                    'firstname' => $this->firstName ?? $this->user->name ?? 'User',
                    'lastname' => $this->lastName ?? 'User',
                    'email' => $this->email ?? $this->user->email,
                    'auth' => 'manual',
                    'lang' => 'en',
                    'confirmed' => 1,
                    'createpassword' => 0,
                ],
            ],
        ];

        $response = $moodleClient->call('core_user_create_users', $userData);

        if (!isset($response[0]['id'])) {
            throw new MoodleException('Failed to create Moodle user: Invalid response');
        }

        // Optionally store the generated password somewhere secure or email it to the user
        // For now, we'll log it (in production, you should handle this more securely)
        Log::info('Moodle user created with credentials', [
            'user_id' => $this->user->id,
            'username' => $username,
            // Don't log passwords in production!
            'temporary_password' => app()->environment('local') ? $password : '[HIDDEN]',
        ]);

        return (int) $response[0]['id'];
    }

    /**
     * Update an existing Moodle user
     */
    private function updateMoodleUser(MoodleClient $moodleClient): void
    {
        $updateData = [
            'users' => [
                [
                    'id' => $this->user->moodle_user_id,
                ],
            ],
        ];

        if ($this->email) {
            $updateData['users'][0]['email'] = $this->email;
        }
        if ($this->firstName) {
            $updateData['users'][0]['firstname'] = $this->firstName;
        }
        if ($this->lastName) {
            $updateData['users'][0]['lastname'] = $this->lastName;
        }

        // Only call update if there are fields to update
        if (count($updateData['users'][0]) > 1) {
            $moodleClient->call('core_user_update_users', $updateData);
            
            Log::info('Updated Moodle user profile', [
                'user_id' => $this->user->id,
                'moodle_user_id' => $this->user->moodle_user_id,
            ]);
        }
    }

    /**
     * Generate a unique username for Moodle
     */
    private function generateUsername(): string
    {
        // Generate unique username based on email or ID
        $base = $this->email 
            ? Str::before($this->email, '@')
            : 'user' . $this->user->id;
        
        // Clean the username (remove special characters, make lowercase)
        $base = preg_replace('/[^a-z0-9_]/', '', strtolower($base));
        
        // Ensure uniqueness by adding random suffix
        return $base . '_' . Str::random(4);
    }

    /**
     * Generate a strong random password
     */
    private function generateSecurePassword(): string
    {
        $length = 16;
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $password = '';
        // Ensure at least one of each type
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Fill the rest randomly
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        // Shuffle to avoid predictable pattern
        return str_shuffle($password);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to create/link Moodle user', [
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}