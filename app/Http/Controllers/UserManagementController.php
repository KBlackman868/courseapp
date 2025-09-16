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

    // Update a user's role
    public function updateRole(Request $request, User $user)
    {
        if ($user->hasRole('superadmin')) {
            return back()->with('error', 'You cannot change the superadmin role.');
        }
        
        $request->validate([
            'role' => 'required|in:superadmin,admin,user',
        ]);

        // Update the user roles (removing existing ones first)
        $user->syncRoles([$request->role]);

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

        foreach ($request->user_ids as $userId) {
            // Skip current user
            if ($userId == $currentUserId) {
                $failedCount++;
                continue;
            }

            $user = User::find($userId);
            
            // Skip superadmins unless current user is superadmin
            if ($user->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
                $failedCount++;
                continue;
            }

            try {
                $moodleUserId = $user->moodle_user_id;
                
                // Delete enrollments and user
                Enrollment::where('user_id', $user->id)->delete();
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