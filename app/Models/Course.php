<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Course Model
 *
 * Represents a course in the LMS system.
 *
 * AUDIENCE TYPES (Who can see/enroll):
 * - MOH_ONLY: Only MOH staff can see and enroll
 * - EXTERNAL_ONLY: Only external users can see and enroll
 * - BOTH: Everyone can see and enroll
 *
 * ENROLLMENT TYPES (How enrollment works):
 * - OPEN_ENROLLMENT: Anyone eligible can enroll immediately
 *   - Moodle account created automatically
 *   - User redirected to course right away
 *
 * - APPROVAL_REQUIRED: Users must request access
 *   - Request goes to pending queue
 *   - Course Admin approves/rejects
 *   - On approval: Moodle account created, user enrolled
 */
class Course extends Model
{
    use HasFactory;

    // =========================================================================
    // AUDIENCE TYPE CONSTANTS
    // These control WHO can see and enroll in the course
    // =========================================================================
    public const AUDIENCE_MOH_ONLY = 'MOH_ONLY';         // MOH staff only
    public const AUDIENCE_EXTERNAL_ONLY = 'EXTERNAL_ONLY'; // External users only
    public const AUDIENCE_BOTH = 'BOTH';                 // Everyone

    // Legacy constants for backward compatibility
    public const AUDIENCE_MOH = 'moh';
    public const AUDIENCE_EXTERNAL = 'external';
    public const AUDIENCE_ALL = 'all';

    // =========================================================================
    // ENROLLMENT TYPE CONSTANTS
    // These control HOW enrollment works
    // =========================================================================
    public const ENROLLMENT_OPEN = 'OPEN_ENROLLMENT';      // Immediate enrollment
    public const ENROLLMENT_APPROVAL = 'APPROVAL_REQUIRED'; // Needs admin approval

    protected $fillable = [
        'title',
        'description',
        'status',
        'audience_type',
        'enrollment_type',
        'is_free',
        'is_active',
        'image',
        'moodle_course_id',
        'moodle_course_shortname',
        'category_id',
        'creator_id'
    ];

    protected $casts = [
        'moodle_course_id' => 'integer',
        'is_free' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the enrollments for the course
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the enrollment requests for the course
     */
    public function enrollmentRequests(): HasMany
    {
        return $this->hasMany(EnrollmentRequest::class);
    }

    /**
     * Get the creator/instructor of the course
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the category for the course
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get administrators who should receive enrollment notifications for this course
     * Priority: Course creator > Course admins > General admins > Superadmins
     */


    /**
     * Check if course is synced with Moodle
     */
    public function hasMoodleIntegration(): bool
    {
        return !is_null($this->moodle_course_id);
    }

    /**
     * Get Moodle sync status
     */
    public function getMoodleSyncStatusAttribute(): string
    {
        if ($this->moodle_course_id) {
            return 'synced';
        }
        return 'not_synced';
    }

    /**
     * Scope to get only Moodle-synced courses
     */
    public function scopeMoodleSynced($query)
    {
        return $query->whereNotNull('moodle_course_id');
    }

    /**
     * Scope to get only non-synced courses
     */
    public function scopeNotMoodleSynced($query)
    {
        return $query->whereNull('moodle_course_id');
    }

    /**
     * Scope to get only active courses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get courses visible to MOH staff
     */
    public function scopeForMohStaff($query)
    {
        return $query->whereIn('audience_type', [self::AUDIENCE_MOH, self::AUDIENCE_ALL]);
    }

    /**
     * Scope to get courses visible to external users
     */
    public function scopeForExternalUsers($query)
    {
        return $query->whereIn('audience_type', [self::AUDIENCE_EXTERNAL, self::AUDIENCE_ALL]);
    }

    /**
     * Scope to get courses for a specific user based on their type
     */
    public function scopeForUser($query, User $user)
    {
        if ($user->isInternal()) {
            return $query->forMohStaff();
        }
        return $query->forExternalUsers();
    }

    /**
     * Scope to get free (open enrollment) courses
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope to get courses requiring approval
     */
    public function scopeRequiresApproval($query)
    {
        return $query->where('is_free', false);
    }

    /**
     * Check if the course is open for direct enrollment
     * Uses enrollment_type if set, falls back to is_free for legacy support
     *
     * @return bool
     */
    public function isOpenEnrollment(): bool
    {
        // Use new enrollment_type if set
        if ($this->enrollment_type) {
            return $this->enrollment_type === self::ENROLLMENT_OPEN;
        }

        // Legacy fallback: is_free = true means open enrollment
        return $this->is_free ?? true;
    }

    /**
     * Check if the course requires approval
     *
     * @return bool
     */
    public function requiresApproval(): bool
    {
        // Use new enrollment_type if set
        if ($this->enrollment_type) {
            return $this->enrollment_type === self::ENROLLMENT_APPROVAL;
        }

        // Legacy fallback
        return !($this->is_free ?? true);
    }

    // =========================================================================
    // COURSE ACCESS REQUEST METHODS
    // =========================================================================

    /**
     * Get course access requests for this course
     */
    public function courseAccessRequests(): HasMany
    {
        return $this->hasMany(CourseAccessRequest::class);
    }

    /**
     * Get pending course access requests
     */
    public function pendingAccessRequests(): HasMany
    {
        return $this->courseAccessRequests()->where('status', CourseAccessRequest::STATUS_PENDING);
    }

    /**
     * Check if a user has a pending access request for this course
     *
     * @param User $user
     * @return bool
     */
    public function hasPendingRequestFrom(User $user): bool
    {
        return $this->courseAccessRequests()
            ->where('user_id', $user->id)
            ->where('status', CourseAccessRequest::STATUS_PENDING)
            ->exists();
    }

    /**
     * Check if a user has an approved access request for this course
     *
     * @param User $user
     * @return bool
     */
    public function hasApprovedRequestFrom(User $user): bool
    {
        return $this->courseAccessRequests()
            ->where('user_id', $user->id)
            ->where('status', CourseAccessRequest::STATUS_APPROVED)
            ->exists();
    }

    /**
     * Get the access request for a specific user
     *
     * @param User $user
     * @return CourseAccessRequest|null
     */
    public function getAccessRequestFrom(User $user): ?CourseAccessRequest
    {
        return $this->courseAccessRequests()
            ->where('user_id', $user->id)
            ->first();
    }

    /**
     * Check if a user can enroll in this course
     * This checks audience type and enrollment type
     *
     * @param User $user
     * @return array ['can_enroll' => bool, 'reason' => string, 'cta' => string]
     */
    public function getEnrollmentStatusFor(User $user): array
    {
        // First, check if user can see this course based on audience
        if (!$this->isVisibleTo($user)) {
            return [
                'can_enroll' => false,
                'reason' => 'This course is not available for your user type.',
                'cta' => null,
                'cta_disabled' => true,
            ];
        }

        // Check for existing enrollment
        $enrollment = $this->enrollments()->where('user_id', $user->id)->first();
        if ($enrollment && $enrollment->status === 'approved') {
            return [
                'can_enroll' => false,
                'reason' => 'You are already enrolled in this course.',
                'cta' => 'Go to Course',
                'cta_action' => 'access',
                'cta_disabled' => false,
            ];
        }

        // Check for existing access request
        $accessRequest = $this->getAccessRequestFrom($user);

        if ($accessRequest) {
            if ($accessRequest->isPending()) {
                return [
                    'can_enroll' => false,
                    'reason' => 'Your access request is pending approval.',
                    'cta' => 'Pending Approval',
                    'cta_disabled' => true,
                ];
            }

            if ($accessRequest->isApproved()) {
                if ($accessRequest->hasSyncFailed()) {
                    return [
                        'can_enroll' => false,
                        'reason' => 'Enrollment processing failed. An administrator has been notified.',
                        'cta' => 'Processing Failed',
                        'cta_disabled' => true,
                    ];
                }

                if ($accessRequest->moodle_sync_status === CourseAccessRequest::SYNC_SYNCED) {
                    return [
                        'can_enroll' => false,
                        'reason' => 'Your access has been approved!',
                        'cta' => 'Go to Course',
                        'cta_action' => 'access',
                        'cta_disabled' => false,
                    ];
                }

                return [
                    'can_enroll' => false,
                    'reason' => 'Your enrollment is being processed.',
                    'cta' => 'Processing...',
                    'cta_disabled' => true,
                ];
            }

            if ($accessRequest->isRejected()) {
                $reason = 'Your access request was rejected.';
                if ($accessRequest->rejection_reason) {
                    $reason .= " Reason: {$accessRequest->rejection_reason}";
                }

                return [
                    'can_enroll' => false,
                    'reason' => $reason,
                    'cta' => 'Request Again',
                    'cta_action' => 'request_again',
                    'cta_disabled' => false,
                    'can_request_again' => true,
                ];
            }
        }

        // No existing request - check enrollment type
        if ($this->isOpenEnrollment()) {
            return [
                'can_enroll' => true,
                'reason' => 'You can enroll in this course immediately.',
                'cta' => 'Enroll Now',
                'cta_action' => 'enroll',
                'cta_disabled' => false,
            ];
        }

        // Requires approval
        return [
            'can_enroll' => true,
            'reason' => 'This course requires approval. Submit a request to get access.',
            'cta' => 'Request Access',
            'cta_action' => 'request',
            'cta_disabled' => false,
        ];
    }

    /**
     * Check if a user can view this course based on audience type
     * Supports both new constants and legacy constants
     *
     * @param User $user
     * @return bool
     */
    public function isVisibleTo(User $user): bool
    {
        // SuperAdmin and Admin can see all courses
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }

        $audience = $this->audience_type;

        // Handle new constants
        if ($audience === self::AUDIENCE_BOTH || $audience === self::AUDIENCE_ALL) {
            return true;
        }

        // MOH only courses
        if ($audience === self::AUDIENCE_MOH_ONLY || $audience === self::AUDIENCE_MOH) {
            return $user->isInternal() || $user->isMohStaff() || $user->hasMohEmail();
        }

        // External only courses
        if ($audience === self::AUDIENCE_EXTERNAL_ONLY || $audience === self::AUDIENCE_EXTERNAL) {
            return $user->isExternal() || $user->isExternalUser();
        }

        // Default: visible to all if no audience type set
        return true;
    }

    /**
     * Get audience type display label
     */
    public function getAudienceLabelAttribute(): string
    {
        return match ($this->audience_type) {
            self::AUDIENCE_MOH_ONLY, self::AUDIENCE_MOH => 'MOH Staff Only',
            self::AUDIENCE_EXTERNAL_ONLY, self::AUDIENCE_EXTERNAL => 'External Users Only',
            self::AUDIENCE_BOTH, self::AUDIENCE_ALL => 'All Users',
            default => 'All Users',
        };
    }

    /**
     * Get enrollment type display label
     */
    public function getEnrollmentTypeLabelAttribute(): string
    {
        if ($this->enrollment_type === self::ENROLLMENT_APPROVAL) {
            return 'Requires Approval';
        }

        if ($this->enrollment_type === self::ENROLLMENT_OPEN) {
            return 'Open Enrollment';
        }

        // Legacy fallback
        return $this->is_free ? 'Open Enrollment' : 'Requires Approval';
    }

    /**
     * Get administrators who should receive enrollment notifications for this course
     * Priority: Course creator > Course admins > General admins > Superadmins
     *
     * @return \Illuminate\Support\Collection
     */
    public function getEnrollmentAdmins()
    {
        $admins = collect();

        // 1. Course creator (if exists and has appropriate permissions)
        if ($this->creator_id && $this->creator) {
            $admins->push($this->creator);
        }

        // 2. Users with Course Admin permission
        $courseAdmins = User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', User::ROLE_SUPERADMIN);
            })->orWhere(function ($q) {
                $q->whereHas('roles', function ($r) {
                    $r->where('name', User::ROLE_ADMIN);
                })->where('is_course_admin', true);
            });
        })->get();

        $admins = $admins->merge($courseAdmins);

        return $admins->unique('id');
    }

    /**
     * Normalize audience type to new format
     * Used when migrating or displaying
     *
     * @return string
     */
    public function getNormalizedAudienceType(): string
    {
        return match ($this->audience_type) {
            self::AUDIENCE_MOH, 'moh' => self::AUDIENCE_MOH_ONLY,
            self::AUDIENCE_EXTERNAL, 'external' => self::AUDIENCE_EXTERNAL_ONLY,
            self::AUDIENCE_ALL, 'all', 'both' => self::AUDIENCE_BOTH,
            default => self::AUDIENCE_BOTH,
        };
    }

    /**
     * Get the color class for audience badge
     */
    public function getAudienceColorAttribute(): string
    {
        return match ($this->audience_type) {
            self::AUDIENCE_MOH_ONLY, self::AUDIENCE_MOH => 'purple',
            self::AUDIENCE_EXTERNAL_ONLY, self::AUDIENCE_EXTERNAL => 'green',
            default => 'blue',
        };
    }

    /**
     * Get the color class for enrollment type badge
     */
    public function getEnrollmentTypeColorAttribute(): string
    {
        return $this->isOpenEnrollment() ? 'green' : 'yellow';
    }
}
