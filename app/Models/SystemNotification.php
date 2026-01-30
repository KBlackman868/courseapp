<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SystemNotification Model
 *
 * In-app notifications for users. These appear in the notification
 * dropdown/page in the navbar.
 *
 * NOTIFICATION TYPES:
 * - For Course Admins:
 *   - account_request_new: New MOH account request
 *   - course_request_new: New course access request
 *   - moodle_sync_failed: Moodle sync failed
 *
 * - For Users:
 *   - account_approved: Account was approved
 *   - account_rejected: Account was rejected
 *   - course_approved: Course access approved
 *   - course_rejected: Course access rejected
 *   - enrollment_ready: Can now access course
 */
class SystemNotification extends Model
{
    use HasFactory;

    // =========================================================================
    // NOTIFICATION TYPE CONSTANTS
    // =========================================================================

    // Admin notifications
    public const TYPE_ACCOUNT_REQUEST_NEW = 'account_request_new';
    public const TYPE_COURSE_REQUEST_NEW = 'course_request_new';
    public const TYPE_MOODLE_SYNC_FAILED = 'moodle_sync_failed';

    // User notifications
    public const TYPE_ACCOUNT_APPROVED = 'account_approved';
    public const TYPE_ACCOUNT_REJECTED = 'account_rejected';
    public const TYPE_COURSE_APPROVED = 'course_approved';
    public const TYPE_COURSE_REJECTED = 'course_rejected';
    public const TYPE_ENROLLMENT_READY = 'enrollment_ready';

    // =========================================================================
    // PRIORITY CONSTANTS
    // =========================================================================
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'action_url',
        'action_text',
        'priority',
        'is_read',
        'read_at',
        'related_model_type',
        'related_model_id',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the user this notification belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (polymorphic)
     */
    public function relatedModel()
    {
        if ($this->related_model_type && $this->related_model_id) {
            return $this->related_model_type::find($this->related_model_id);
        }
        return null;
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Get notifications of specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get high priority notifications
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HIGH);
    }

    /**
     * Get recent notifications
     */
    public function scopeRecent($query, int $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // =========================================================================
    // ACTION METHODS
    // =========================================================================

    /**
     * Mark this notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark this notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    // =========================================================================
    // STATIC FACTORY METHODS
    // These make it easy to create notifications throughout the app
    // =========================================================================

    /**
     * Notify Course Admins about new MOH account request
     *
     * @param AccountRequest $request
     */
    public static function notifyNewAccountRequest(AccountRequest $request): void
    {
        // Find all users who can approve accounts (SuperAdmin and Course Admins)
        $admins = User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', User::ROLE_SUPERADMIN);
            })->orWhere(function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->where('name', User::ROLE_ADMIN);
                })->where('is_course_admin', true);
            });
        })->get();

        foreach ($admins as $admin) {
            self::create([
                'user_id' => $admin->id,
                'type' => self::TYPE_ACCOUNT_REQUEST_NEW,
                'title' => 'New Account Request',
                'message' => "{$request->full_name} ({$request->email}) has submitted an account request.",
                'action_url' => route('admin.account-requests.index'),
                'action_text' => 'Review Request',
                'priority' => self::PRIORITY_MEDIUM,
                'related_model_type' => AccountRequest::class,
                'related_model_id' => $request->id,
                'data' => [
                    'requester_name' => $request->full_name,
                    'requester_email' => $request->email,
                    'department' => $request->department,
                ],
            ]);
        }
    }

    /**
     * Notify Course Admins about new course access request
     *
     * @param CourseAccessRequest $request
     */
    public static function notifyNewCourseRequest(CourseAccessRequest $request): void
    {
        $request->load(['user', 'course']);

        $admins = User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', User::ROLE_SUPERADMIN);
            })->orWhere(function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->where('name', User::ROLE_ADMIN);
                })->where('is_course_admin', true);
            });
        })->get();

        foreach ($admins as $admin) {
            self::create([
                'user_id' => $admin->id,
                'type' => self::TYPE_COURSE_REQUEST_NEW,
                'title' => 'New Course Access Request',
                'message' => "{$request->user->full_name} is requesting access to '{$request->course->title}'.",
                'action_url' => route('admin.course-access-requests.index'),
                'action_text' => 'Review Request',
                'priority' => self::PRIORITY_LOW,
                'related_model_type' => CourseAccessRequest::class,
                'related_model_id' => $request->id,
                'data' => [
                    'user_name' => $request->user->full_name,
                    'course_title' => $request->course->title,
                ],
            ]);
        }
    }

    /**
     * Notify Course Admins about Moodle sync failure
     *
     * @param CourseAccessRequest $request
     */
    public static function notifyMoodleSyncFailed(CourseAccessRequest $request): void
    {
        $request->load(['user', 'course']);

        $admins = User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', User::ROLE_SUPERADMIN);
            })->orWhere(function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->where('name', User::ROLE_ADMIN);
                })->where('is_course_admin', true);
            });
        })->get();

        foreach ($admins as $admin) {
            self::create([
                'user_id' => $admin->id,
                'type' => self::TYPE_MOODLE_SYNC_FAILED,
                'title' => 'Moodle Sync Failed',
                'message' => "Failed to enroll {$request->user->full_name} in '{$request->course->title}'. Error: {$request->moodle_sync_error}",
                'action_url' => route('admin.moodle.status'),
                'action_text' => 'View Moodle Status',
                'priority' => self::PRIORITY_HIGH,
                'related_model_type' => CourseAccessRequest::class,
                'related_model_id' => $request->id,
                'data' => [
                    'user_name' => $request->user->full_name,
                    'course_title' => $request->course->title,
                    'error' => $request->moodle_sync_error,
                ],
            ]);
        }
    }

    /**
     * Notify user that their account was approved
     *
     * @param User $user
     */
    public static function notifyAccountApproved(User $user): void
    {
        self::create([
            'user_id' => $user->id,
            'type' => self::TYPE_ACCOUNT_APPROVED,
            'title' => 'Account Approved!',
            'message' => 'Your account has been approved. You can now access the Learning Management System.',
            'action_url' => route('dashboard'),
            'action_text' => 'Go to Dashboard',
            'priority' => self::PRIORITY_HIGH,
        ]);
    }

    /**
     * Notify user that their account was rejected
     *
     * @param AccountRequest $request
     */
    public static function notifyAccountRejected(AccountRequest $request): void
    {
        // We can't notify the user directly since they don't have an account
        // This would need to be handled via email
    }

    /**
     * Notify user that their course access was approved
     *
     * @param CourseAccessRequest $request
     */
    public static function notifyCourseApproved(CourseAccessRequest $request): void
    {
        $request->load('course');

        self::create([
            'user_id' => $request->user_id,
            'type' => self::TYPE_COURSE_APPROVED,
            'title' => 'Course Access Approved!',
            'message' => "Your request to access '{$request->course->title}' has been approved.",
            'action_url' => route('courses.show', $request->course_id),
            'action_text' => 'Go to Course',
            'priority' => self::PRIORITY_HIGH,
            'related_model_type' => CourseAccessRequest::class,
            'related_model_id' => $request->id,
        ]);
    }

    /**
     * Notify user that their course access was rejected
     *
     * @param CourseAccessRequest $request
     */
    public static function notifyCourseRejected(CourseAccessRequest $request): void
    {
        $request->load('course');

        $message = "Your request to access '{$request->course->title}' has been rejected.";
        if ($request->rejection_reason) {
            $message .= " Reason: {$request->rejection_reason}";
        }

        self::create([
            'user_id' => $request->user_id,
            'type' => self::TYPE_COURSE_REJECTED,
            'title' => 'Course Access Rejected',
            'message' => $message,
            'action_url' => route('catalog.index'),
            'action_text' => 'Browse Courses',
            'priority' => self::PRIORITY_MEDIUM,
            'related_model_type' => CourseAccessRequest::class,
            'related_model_id' => $request->id,
        ]);
    }

    /**
     * Notify user that they can now access a course (enrollment complete)
     *
     * @param CourseAccessRequest $request
     */
    public static function notifyEnrollmentReady(CourseAccessRequest $request): void
    {
        $request->load('course');

        self::create([
            'user_id' => $request->user_id,
            'type' => self::TYPE_ENROLLMENT_READY,
            'title' => 'Course Ready!',
            'message' => "You can now access '{$request->course->title}'. Click below to start learning!",
            'action_url' => route('courses.access-moodle', $request->course_id),
            'action_text' => 'Start Course',
            'priority' => self::PRIORITY_HIGH,
            'related_model_type' => CourseAccessRequest::class,
            'related_model_id' => $request->id,
        ]);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Get icon for notification type
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ACCOUNT_REQUEST_NEW => 'user-plus',
            self::TYPE_COURSE_REQUEST_NEW => 'book-open',
            self::TYPE_MOODLE_SYNC_FAILED => 'exclamation-triangle',
            self::TYPE_ACCOUNT_APPROVED => 'check-circle',
            self::TYPE_ACCOUNT_REJECTED => 'x-circle',
            self::TYPE_COURSE_APPROVED => 'check-circle',
            self::TYPE_COURSE_REJECTED => 'x-circle',
            self::TYPE_ENROLLMENT_READY => 'academic-cap',
            default => 'bell',
        };
    }

    /**
     * Get color for notification type
     */
    public function getColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_MOODLE_SYNC_FAILED, self::TYPE_ACCOUNT_REJECTED, self::TYPE_COURSE_REJECTED => 'red',
            self::TYPE_ACCOUNT_APPROVED, self::TYPE_COURSE_APPROVED, self::TYPE_ENROLLMENT_READY => 'green',
            self::TYPE_ACCOUNT_REQUEST_NEW, self::TYPE_COURSE_REQUEST_NEW => 'blue',
            default => 'gray',
        };
    }
}
