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
use Illuminate\Support\Facades\Cache;
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
        // If user already has a Moodle ID, try to update their profile.
        // If the Moodle user was deleted, updateMoodleUser() returns false
        // and we fall through to re-create the account.
        if ($this->user->moodle_user_id) {
            $userStillExists = $this->updateMoodleUser($moodleClient);
            if ($userStillExists) {
                return;
            }
            // Moodle user was deleted — clear the stale ID and fall through
            Log::warning('Moodle user was deleted, clearing stale ID and re-creating', [
                'user_id' => $this->user->id,
                'stale_moodle_user_id' => $this->user->moodle_user_id,
            ]);
            $this->user->update(['moodle_user_id' => null]);
            $this->user->refresh();
        }

        // Try to find existing Moodle user by email
        $email = $this->email ?? $this->user->email;
        $existingMoodleUser = $this->findMoodleUserByEmail($moodleClient, $email);

        if ($existingMoodleUser) {
            // Link existing Moodle user to our local user record
            $this->user->update(['moodle_user_id' => $existingMoodleUser['id']]);

            // Ensure the Moodle user's auth method is set to 'userkey' so SSO works.
            // Existing users may have been created with 'manual' or 'email' auth,
            // which causes auth_userkey_request_login_url to fail with HTTP 500.
            $requiredAuth = config('moodle.sso_enabled', true) ? 'userkey' : 'manual';
            $currentAuth = $existingMoodleUser['auth'] ?? 'unknown';

            if ($currentAuth !== $requiredAuth) {
                try {
                    $moodleClient->call('core_user_update_users', [
                        'users' => [[
                            'id' => $existingMoodleUser['id'],
                            'auth' => $requiredAuth,
                        ]],
                    ]);
                    Log::info('Updated Moodle user auth method for SSO', [
                        'moodle_user_id' => $existingMoodleUser['id'],
                        'old_auth' => $currentAuth,
                        'new_auth' => $requiredAuth,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Could not update Moodle user auth method', [
                        'moodle_user_id' => $existingMoodleUser['id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Linked existing Moodle user', [
                'user_id' => $this->user->id,
                'moodle_user_id' => $existingMoodleUser['id'],
                'auth' => $requiredAuth,
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
        
        // Try to get password from cache (set during registration)
        // If not found (e.g., user created by admin), generate a secure one
        // Check if user has a stored password from registration
        if ($this->user->temp_moodle_password) {
            $password = decrypt($this->user->temp_moodle_password);
            $passwordSource = 'from_registration';
            
            // Clear it after use for security
            $this->user->update(['temp_moodle_password' => null]);
        } else {
            // Generate password for admin-created users
            $password = $this->generateSecurePassword();
            $passwordSource = 'generated';
        }
        
        // Use 'userkey' auth for SSO if enabled, otherwise 'manual'
        $authMethod = config('moodle.sso_enabled', true) ? 'userkey' : 'manual';

        $userData = [
            'users' => [
                [
                    'username' => $username,
                    'password' => $password,
                    'firstname' => $this->firstName ?? $this->user->first_name ?? 'User',
                    'lastname' => $this->lastName ?? $this->user->last_name ?? 'User',
                    'email' => $this->email ?? $this->user->email,
                    'auth' => $authMethod,
                    'lang' => 'en',
                    // Don't include 'confirmed' as it caused issues
                    'createpassword' => 0,
                ],
            ],
        ];

        $response = $moodleClient->call('core_user_create_users', $userData);

        if (!isset($response[0]['id'])) {
            throw new MoodleException('Failed to create Moodle user: Invalid response');
        }

        // Log successful creation
        Log::info('Moodle user created with credentials', [
            'user_id' => $this->user->id,
            'username' => $username,
            'password_source' => $passwordSource,
            'moodle_user_id' => $response[0]['id'],
            // Only log actual password in local environment for debugging
            'temporary_password' => app()->environment('local') && $passwordSource === 'generated' ? $password : '[HIDDEN]',
        ]);

        // If password was generated (not from registration), email it to the user
        if ($passwordSource === 'generated') {
            // TODO: Implement email notification with generated password
            Log::warning('Generated password for Moodle user - implement email notification', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
            ]);
        }

        return (int) $response[0]['id'];
    }

    /**
     * Update an existing Moodle user.
     * Also ensures their auth method is set correctly for SSO.
     *
     * @return bool True if the user still exists in Moodle, false if deleted/gone.
     */
    private function updateMoodleUser(MoodleClient $moodleClient): bool
    {
        // Always ensure auth method is correct for SSO to work
        $requiredAuth = config('moodle.sso_enabled', true) ? 'userkey' : 'manual';

        $updateData = [
            'users' => [
                [
                    'id' => $this->user->moodle_user_id,
                    'auth' => $requiredAuth,
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

        $response = $moodleClient->call('core_user_update_users', $updateData);

        // Check if Moodle reported the user as deleted or non-existent.
        // Moodle returns: {"warnings":[{"warningcode":"usernotupdateddeleted",...}]}
        if (!empty($response['warnings'])) {
            foreach ($response['warnings'] as $warning) {
                $code = $warning['warningcode'] ?? '';
                if ($code === 'usernotupdateddeleted' || $code === 'usernotupdatednotexist') {
                    Log::warning('Moodle user is deleted or does not exist', [
                        'user_id' => $this->user->id,
                        'moodle_user_id' => $this->user->moodle_user_id,
                        'warning' => $warning,
                    ]);
                    return false;
                }
            }
        }

        Log::info('Updated Moodle user profile and auth method', [
            'user_id' => $this->user->id,
            'moodle_user_id' => $this->user->moodle_user_id,
            'auth' => $requiredAuth,
        ]);

        return true;
    }

    /**
     * Generate a unique username for Moodle
     */
    private function generateUsername(): string
    {
        $email = $this->email ?? $this->user->email;
        
        // Keep the email prefix as-is with the dot
        $username = strtolower(Str::before($email, '@'));
        
        // Only remove spaces and other special characters, but KEEP dots
        $username = preg_replace('/[^a-z0-9.]/', '', $username);
        
        return $username; // Results in: gerardo.olivier
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