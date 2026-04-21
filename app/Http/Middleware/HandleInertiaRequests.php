<?php

namespace App\Http\Middleware;

use App\Models\AccountRequest;
use App\Models\CourseAccessRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $role = null;

        if ($user) {
            $user->load('roles');
            $roleModel = $user->roles->first();
            $role = $roleModel ? $roleModel->name : null;
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'role' => $role,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],
            // Notification bell data — lazy-evaluated so guest pages don't pay the cost.
            'notifications' => fn () => $this->notificationPayload($user),
            // Pending request counts for admin nav badges — only populated for admins.
            'adminPending' => fn () => $this->adminPendingCounts($user),
        ];
    }

    /**
     * Recent notifications + unread count for the navbar bell.
     * Returns nulls when no user is authenticated.
     */
    private function notificationPayload(?User $user): array
    {
        if (!$user) {
            return ['recent' => [], 'unread_count' => 0];
        }

        $recent = $user->systemNotifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'message' => $n->message,
                'action_url' => $n->action_url,
                'action_text' => $n->action_text,
                'is_read' => (bool) $n->is_read,
                'created_at' => optional($n->created_at)->diffForHumans(),
                'icon' => $n->icon,
                'color' => $n->color,
            ])
            ->all();

        return [
            'recent' => $recent,
            'unread_count' => $user->unreadNotificationsCount(),
        ];
    }

    /**
     * Counts of items requiring admin attention — surfaced as nav badges.
     * Only computed for SuperAdmin and Course Admins (Admin + is_course_admin).
     */
    private function adminPendingCounts(?User $user): array
    {
        $empty = ['account_requests' => 0, 'course_access_requests' => 0];

        if (!$user) {
            return $empty;
        }

        $isSuperAdmin = $user->hasRole(User::ROLE_SUPERADMIN);
        $isCourseAdmin = $user->hasRole(User::ROLE_ADMIN) && ($user->is_course_admin ?? false);

        if (!$isSuperAdmin && !$isCourseAdmin) {
            return $empty;
        }

        return [
            'account_requests' => AccountRequest::whereIn('status', [
                AccountRequest::STATUS_PENDING,
                AccountRequest::STATUS_PENDING_VERIFICATION,
                AccountRequest::STATUS_EMAIL_VERIFIED,
            ])->count(),
            'course_access_requests' => CourseAccessRequest::where('status', CourseAccessRequest::STATUS_PENDING)->count(),
        ];
    }
}
