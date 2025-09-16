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

class CreateMoodleUserWithPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    private User $user;
    private string $username;
    private string $password;
    private string $firstName;
    private string $lastName;

    public function __construct(
        User $user,
        string $username,
        string $password,
        string $firstName,
        string $lastName
    ) {
        $this->user = $user;
        $this->password = $password;
        
        // Validate and clean username
        $this->username = $this->validateUsername($username, $user->email);
        
        // Validate and clean names
        $this->firstName = $this->validateName($firstName, 'First');
        $this->lastName = $this->validateName($lastName, 'Last');
    }

    public function handle(MoodleClient $moodleClient): void
    {
        // Check if user already has Moodle ID
        if ($this->user->moodle_user_id) {
            Log::info('User already has Moodle ID', ['user_id' => $this->user->id]);
            return;
        }

        // Check if user exists in Moodle by email
        $existingUser = $this->findMoodleUserByEmail($moodleClient);
        
        if ($existingUser) {
            // User exists, just link them
            $this->user->update(['moodle_user_id' => $existingUser['id']]);
            Log::info('Linked existing Moodle user', [
                'user_id' => $this->user->id,
                'moodle_user_id' => $existingUser['id']
            ]);
            return;
        }

        // Check if username already exists in Moodle
        $existingUsername = $this->findMoodleUserByUsername($moodleClient);
        if ($existingUsername) {
            // Username exists, generate a unique one
            $this->username = $this->generateUniqueUsername($this->username);
            Log::info('Username already exists, using alternative', [
                'original' => $username,
                'new' => $this->username
            ]);
        }

        // Create new Moodle user with same password
        $moodleUserId = $this->createMoodleUser($moodleClient);
        $this->user->update(['moodle_user_id' => $moodleUserId]);
        
        Log::info('Created Moodle user with same credentials', [
            'user_id' => $this->user->id,
            'moodle_user_id' => $moodleUserId,
            'username' => $this->username
        ]);
    }

    private function validateUsername(string $username, string $email): string
    {
        // Clean the username
        $username = strtolower(trim($username));
        $username = preg_replace('/[^a-z0-9_.-]/', '', $username);
        
        // If username is too short or empty, generate from email
        if (strlen($username) < 2) {
            $emailPart = strtolower(explode('@', $email)[0]);
            $emailPart = preg_replace('/[^a-z0-9]/', '', $emailPart);
            
            // If still too short, add random suffix
            if (strlen($emailPart) < 2) {
                $emailPart = 'user' . rand(1000, 9999);
            }
            
            $username = $emailPart;
        }
        
        // Ensure username is not too long (Moodle limit is typically 100)
        if (strlen($username) > 100) {
            $username = substr($username, 0, 100);
        }
        
        return $username;
    }
    
    private function validateName(string $name, string $default): string
    {
        $name = trim($name);
        
        // If name is empty or too short, use default
        if (strlen($name) < 1) {
            return $default;
        }
        
        // Remove any special characters that might cause issues
        $name = preg_replace('/[<>\"\'%;&]/', '', $name);
        
        // Ensure name is not too long (Moodle limit is typically 100)
        if (strlen($name) > 100) {
            $name = substr($name, 0, 100);
        }
        
        return $name;
    }
    
    private function generateUniqueUsername(string $baseUsername): string
    {
        return $baseUsername . rand(100, 999);
    }

    private function findMoodleUserByEmail(MoodleClient $moodleClient): ?array
    {
        try {
            $response = $moodleClient->call('core_user_get_users_by_field', [
                'field' => 'email',
                'values[0]' => $this->user->email,
            ]);

            return !empty($response) && isset($response[0]) ? $response[0] : null;
        } catch (MoodleException $e) {
            Log::warning('Failed to find Moodle user by email', [
                'email' => $this->user->email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    private function findMoodleUserByUsername(MoodleClient $moodleClient): ?array
    {
        try {
            $response = $moodleClient->call('core_user_get_users_by_field', [
                'field' => 'username',
                'values[0]' => $this->username,
            ]);

            return !empty($response) && isset($response[0]) ? $response[0] : null;
        } catch (MoodleException $e) {
            Log::warning('Failed to check username availability', [
                'username' => $this->username,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function createMoodleUser(MoodleClient $moodleClient): int
    {
        // Log what we're about to send
        Log::debug('Creating Moodle user with parameters', [
            'username' => $this->username,
            'email' => $this->user->email,
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'username_length' => strlen($this->username),
            'password_length' => strlen($this->password)
        ]);
        
        // Validate password meets Moodle requirements
        if (strlen($this->password) < 8) {
            // Generate a compliant password if the provided one is too short
            $this->password = 'TempPass' . rand(1000, 9999) . '!';
            Log::warning('Password too short, using temporary password', [
                'user_id' => $this->user->id
            ]);
        }
        
        // Build parameters for Moodle
        $params = [
            'users[0][username]' => $this->username,
            'users[0][password]' => $this->password,
            'users[0][firstname]' => $this->firstName,
            'users[0][lastname]' => $this->lastName,
            'users[0][email]' => $this->user->email,
            'users[0][auth]' => 'manual',
            'users[0][lang]' => 'en',
            'users[0][country]' => 'TT',
            'users[0][mailformat]' => '1',
        ];

        try {
            $response = $moodleClient->call('core_user_create_users', $params);
            
            Log::debug('Moodle response from core_user_create_users', [
                'response' => $response
            ]);
            
            if (empty($response) || !isset($response[0]['id'])) {
                throw new MoodleException(
                    'Unexpected response format from Moodle: ' . json_encode($response)
                );
            }
            
            return (int) $response[0]['id'];
        } catch (MoodleException $e) {
            Log::error('Failed to create Moodle user - Full Details', [
                'user_id' => $this->user->id,
                'username' => $this->username,
                'email' => $this->user->email,
                'error_message' => $e->getMessage(),
                'firstname' => $this->firstName,
                'lastname' => $this->lastName,
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CreateMoodleUserWithPassword job failed permanently', [
            'user_id' => $this->user->id,
            'username' => $this->username,
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}