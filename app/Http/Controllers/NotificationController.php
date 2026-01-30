<?php

namespace App\Http\Controllers;

use App\Models\SystemNotification;
use Illuminate\Http\Request;

/**
 * NotificationController
 *
 * Handles the notification system for users.
 * Notifications appear in the navbar dropdown and notification page.
 */
class NotificationController extends Controller
{
    /**
     * Display the notification page
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $filter = $request->input('filter', 'all');

        $query = $user->systemNotifications()->orderBy('created_at', 'desc');

        if ($filter === 'unread') {
            $query->unread();
        }

        $notifications = $query->paginate(20);

        $unreadCount = $user->unreadNotificationsCount();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Get recent notifications for the navbar dropdown
     * Returns JSON for AJAX requests
     */
    public function recent()
    {
        $user = auth()->user();

        $notifications = $user->systemNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = $user->unreadNotificationsCount();

        if (request()->expectsJson()) {
            return response()->json([
                'notifications' => $notifications->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'type' => $n->type,
                        'title' => $n->title,
                        'message' => $n->message,
                        'action_url' => $n->action_url,
                        'action_text' => $n->action_text,
                        'is_read' => $n->is_read,
                        'created_at' => $n->created_at->diffForHumans(),
                        'icon' => $n->icon,
                        'color' => $n->color,
                    ];
                }),
                'unread_count' => $unreadCount,
            ]);
        }

        return view('notifications.partials.dropdown', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(SystemNotification $notification)
    {
        // Ensure user owns this notification
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        // If there's an action URL, redirect to it
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->systemNotifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification
     */
    public function destroy(SystemNotification $notification)
    {
        // Ensure user owns this notification
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Delete all read notifications
     */
    public function clearRead()
    {
        auth()->user()->systemNotifications()
            ->read()
            ->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Read notifications cleared.');
    }
}
