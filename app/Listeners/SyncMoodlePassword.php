<?php

namespace App\Listeners;

use App\Services\MoodleClient;
use Illuminate\Support\Facades\Log;

class SyncMoodlePassword
{
    public function __construct(private MoodleClient $moodleClient)
    {
    }

    public function handle($event)
    {
        $user = $event->user;
        
        if (!$user->moodle_user_id) {
            return;
        }

        try {
            $updateData = [
                'users' => [
                    [
                        'id' => $user->moodle_user_id,
                        'password' => $event->plainPassword,
                    ],
                ],
            ];

            $this->moodleClient->call('core_user_update_users', $updateData);
            
            Log::info('Synced password change to Moodle', [
                'user_id' => $user->id,
                'moodle_user_id' => $user->moodle_user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync password to Moodle', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}