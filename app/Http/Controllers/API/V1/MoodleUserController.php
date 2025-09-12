<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMoodleUserRequest;
use App\Http\Requests\UpdateMoodleUserRequest;
use App\Jobs\CreateOrLinkMoodleUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class MoodleUserController extends Controller
{
    /**
     * Create or link a Moodle user
     */
    public function store(CreateMoodleUserRequest $request): JsonResponse
    {
        $user = User::findOrFail($request->user_id);
        
        CreateOrLinkMoodleUser::dispatch(
            $user,
            $request->email,
            $request->first_name,
            $request->last_name
        );

        // If synchronous processing is needed, use dispatchSync instead
        // CreateOrLinkMoodleUser::dispatchSync(...);
        
        return response()->json([
            'status' => 'ok',
            'moodle_user_id' => $user->fresh()->moodle_user_id ?? null,
            'message' => $user->moodle_user_id 
                ? 'Moodle user linked successfully' 
                : 'Moodle user creation queued',
        ], 201);
    }

    /**
     * Update/sync Moodle user profile
     */
    public function update(UpdateMoodleUserRequest $request, User $user): JsonResponse
    {
        if (!$user->moodle_user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not linked to Moodle',
            ], 404);
        }

        CreateOrLinkMoodleUser::dispatch(
            $user,
            $request->email,
            $request->first_name,
            $request->last_name
        );

        return response()->json([
            'status' => 'ok',
            'moodle_user_id' => $user->moodle_user_id,
            'message' => 'Moodle user update queued',
        ]);
    }
}