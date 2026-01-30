<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enrollment;
use App\Services\MoodleService;
use App\Jobs\DeleteMoodleUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserManagementController extends Controller
{
    // Display a list of all users with role management
    public function index()
    {
        $users = User::with('roles')->paginate(20);
        return view('admin.users_lists', compact('users'));
    }

    /**
     * Update a user's role
     *
     * SECURITY: Only SuperAdmins can change user roles.
     * This prevents privilege escalation attacks.
     */
    public function updateRole(Request $request, User $user)
    {
        // CRITICAL SECURITY CHECK: Only SuperAdmins can assign roles
        if (!auth()->user()->hasRole('superadmin')) {
            // Log the attempted privilege escalation
            Log::warning('Role escalation attempt blocked', [
                'attempted_by' => auth()->user()->email,
                'attempted_by_id' => auth()->id(),
                'target_user' => $user->email,
                'target_user_id' => $user->id,
                'requested_role' => $request->role,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->with('error', 'Only SuperAdmins can change user roles.');
        }

        // Prevent self-demotion from superadmin
        if ($user->id === auth()->id() && $user->hasRole('superadmin') && $request->role !== 'superadmin') {
            return back()->with('error', 'You cannot remove your own superadmin role.');
        }

        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $oldRoles = $user->getRoleNames()->toArray();

        // Update the user roles (removing existing ones first)
        $user->syncRoles([$request->role]);

        Log::info('User role updated', [
            'updated_by' => auth()->user()->email,
            'target_user' => $user->email,
            'old_roles' => $oldRoles,
            'new_role' => $request->role
        ]);

        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    /**
     * Delete a user from both Laravel and Moodle
     */
    public function destroy(Request $request, User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deletion of superadmin by non-superadmin
        if ($user->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
            return back()->with('error', 'Only superadmins can delete other superadmins.');
        }

        DB::beginTransaction();
        
        try {
            // Store Moodle ID before deletion
            $moodleUserId = $user->moodle_user_id;
            $userId = $user->id;

            // Delete related records first
            Enrollment::where('user_id', $user->id)->delete();
            
            // Delete the user from Laravel
            $user->delete();

            // Dispatch job to delete from Moodle if they have a Moodle account
            if ($moodleUserId) {
                DeleteMoodleUser::dispatch($moodleUserId, $userId)
                    ->onQueue('high');
            }

            DB::commit();

            Log::info('User deleted from system', [
                'deleted_user_id' => $userId,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User has been deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to delete user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Suspend a user (soft delete alternative)
     */
    public function suspend(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        try {
            // Suspend in Moodle
            if ($user->moodle_user_id) {
                $moodleService = new MoodleService();
                $moodleService->suspendUser($user);
            }

            // Mark user as suspended in Laravel
            $user->update(['is_suspended' => true]);

            Log::info('User suspended', [
                'user_id' => $user->id,
                'suspended_by' => auth()->id()
            ]);

            return back()->with('success', 'User has been suspended successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to suspend user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to suspend user.');
        }
    }

    /**
     * Reactivate a suspended user
     */
    public function reactivate(Request $request, User $user)
    {
        try {
            // Reactivate in Moodle
            if ($user->moodle_user_id) {
                $moodleService = new MoodleService();
                $moodleService->reactivateUser($user);
            }

            // Mark user as active in Laravel
            $user->update(['is_suspended' => false]);

            Log::info('User reactivated', [
                'user_id' => $user->id,
                'reactivated_by' => auth()->id()
            ]);

            return back()->with('success', 'User has been reactivated successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to reactivate user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to reactivate user.');
        }
    }

    /**
     * Bulk delete multiple users
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        $currentUserId = auth()->id();
        $deletedCount = 0;
        $failedCount = 0;
        $isSuperadmin = auth()->user()->hasRole('superadmin');

        // PERFORMANCE FIX: Load all users at once with their roles
        $users = User::whereIn('id', $request->user_ids)
            ->with('roles')
            ->get()
            ->keyBy('id');

        // Collect user IDs that can be deleted for batch enrollment deletion
        $deletableUserIds = [];

        foreach ($request->user_ids as $userId) {
            // Skip current user
            if ($userId == $currentUserId) {
                $failedCount++;
                continue;
            }

            $user = $users->get($userId);

            if (!$user) {
                $failedCount++;
                continue;
            }

            // Skip superadmins unless current user is superadmin
            if ($user->hasRole('superadmin') && !$isSuperadmin) {
                $failedCount++;
                continue;
            }

            $deletableUserIds[] = $userId;
        }

        // PERFORMANCE FIX: Batch delete enrollments for all deletable users
        if (!empty($deletableUserIds)) {
            Enrollment::whereIn('user_id', $deletableUserIds)->delete();
        }

        // Now delete users and queue Moodle deletions
        foreach ($deletableUserIds as $userId) {
            $user = $users->get($userId);

            try {
                $moodleUserId = $user->moodle_user_id;

                $user->delete();

                // Queue Moodle deletion
                if ($moodleUserId) {
                    DeleteMoodleUser::dispatch($moodleUserId, $userId);
                }

                $deletedCount++;
            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Failed to delete user in bulk operation', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = "Deleted {$deletedCount} users successfully.";
        if ($failedCount > 0) {
            $message .= " Failed to delete {$failedCount} users.";
        }

        return back()->with('success', $message);
    }
}