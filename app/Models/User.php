<?php

namespace App\Models;

use App\Traits\HasEnhancedVerification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * User Model
 *
 * This model represents all users in the system.
 *
 * ROLE STRUCTURE (Only these 4 roles exist):
 * - SuperAdmin: Has ALL permissions, cannot be deleted, always exists
 * - Admin: Can have Course Admin permission granted by SuperAdmin
 * - MOH_Staff: Ministry of Health employees (@health.gov.tt)
 * - External_User: External users who can request course access
 *
 * COURSE ADMIN PERMISSION:
 * - NOT a separate role, it's a permission flag (is_course_admin)
 * - Only SuperAdmin can grant this to Admin users
 * - Admins with this permission can manage courses and approve requests
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory,
        Notifiable,
        HasRoles,
        HasEnhancedVerification;

    // =========================================================================
    // ROLE CONSTANTS
    // These are the roles allowed in the system
    // =========================================================================
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_COURSE_ADMIN = 'course_admin';
    public const ROLE_MOH_STAFF = 'moh_staff';
    public const ROLE_EXTERNAL_USER = 'external_user';

    // =========================================================================
    // USER TYPE CONSTANTS
    // Determines how the user was identified
    // =========================================================================
    public const TYPE_INTERNAL = 'internal';  // MOH staff with @health.gov.tt email
    public const TYPE_EXTERNAL = 'external';  // External users

    // =========================================================================
    // ACCOUNT STATUS CONSTANTS
    // Workflow states for user accounts
    // =========================================================================
    public const STATUS_PENDING = 'pending';    // Awaiting approval
    public const STATUS_ACTIVE = 'active';      // Can use the system
    public const STATUS_INACTIVE = 'inactive';  // Deactivated

    // =========================================================================
    // MOH EMAIL DOMAIN
    // Users with this email domain are identified as MOH Staff
    // =========================================================================
    public const MOH_EMAIL_DOMAIN = 'health.gov.tt';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'department',
        'organization',
        'profile_photo',
        'moodle_user_id',
        'moodle_sync_status',
        'moodle_sync_error',
        'temp_moodle_password',
        'verification_status',
        'verification_sent_at',
        'verification_attempts',
        'must_verify_before',
        'google_id',
        'is_suspended',
        'user_type',
        'account_status',
        'is_course_creator',
        'is_course_admin',
        'ldap_guid',
        'ldap_username',
        'ldap_synced_at',
        'otp_code',
        'otp_expires_at',
        'otp_verified',
        'otp_verified_at',
        'otp_attempts',
        'initial_otp_completed',
        'initial_otp_completed_at',
        'auth_method',
        'onboarding_completed_at',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'temp_moodle_password',
        'otp_code',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verification_sent_at' => 'datetime',
            'must_verify_before' => 'datetime',
            'verification_attempts' => 'integer',
            'otp_expires_at' => 'datetime',
            'otp_verified_at' => 'datetime',
            'ldap_synced_at' => 'datetime',
            'initial_otp_completed_at' => 'datetime',
            'is_course_creator' => 'boolean',
            'is_course_admin' => 'boolean',
            'otp_verified' => 'boolean',
            'initial_otp_completed' => 'boolean',
            'is_suspended' => 'boolean',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    // =========================================================================
    // ROLE CHECK METHODS
    // Methods to check what role/permissions the user has
    // =========================================================================

    /**
     * Check if user is a SuperAdmin
     * SuperAdmins have ALL permissions and cannot be deleted
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPERADMIN);
    }

    /**
     * Check if user is an Admin (regular admin, may or may not have Course Admin permission)
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is MOH Staff
     *
     * @return bool
     */
    public function isMohStaff(): bool
    {
        return $this->hasRole(self::ROLE_MOH_STAFF);
    }

    /**
     * Check if user is an External User
     *
     * @return bool
     */
    public function isExternalUser(): bool
    {
        return $this->hasRole(self::ROLE_EXTERNAL_USER);
    }

    /**
     * Check if user has Course Admin permission
     * Can be either:
     * - A user with the course_admin role
     * - An Admin user with the is_course_admin flag
     *
     * @return bool
     */
    public function isCourseAdmin(): bool
    {
        // SuperAdmin always has course admin capabilities
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check for the course_admin role
        if ($this->hasRole(self::ROLE_COURSE_ADMIN)) {
            return true;
        }

        // Admin users can also have the is_course_admin flag
        if ($this->isAdmin()) {
            return $this->is_course_admin ?? false;
        }

        return false;
    }

    /**
     * Check if user can manage courses (create, edit, delete, approve enrollments)
     *
     * @return bool
     */
    public function canManageCourses(): bool
    {
        return $this->isSuperAdmin() || $this->isCourseAdmin();
    }

    /**
     * Check if user can approve account requests
     *
     * @return bool
     */
    public function canApproveAccounts(): bool
    {
        return $this->isSuperAdmin() || $this->isCourseAdmin();
    }

    /**
     * Check if user can approve course access requests
     *
     * @return bool
     */
    public function canApproveCourseAccess(): bool
    {
        return $this->isSuperAdmin() || $this->isCourseAdmin();
    }

    /**
     * Check if user can view the pending approvals section
     *
     * @return bool
     */
    public function canViewPendingApprovals(): bool
    {
        return $this->isSuperAdmin() || $this->isCourseAdmin();
    }

    /**
     * Check if user can assign the Course Admin permission to others
     * Only SuperAdmin can do this
     *
     * @return bool
     */
    public function canAssignCourseAdminPermission(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Check if this user can change another user's role
     *
     * @param User $targetUser The user whose role is being changed
     * @param string $newRole The role to assign
     * @return bool
     */
    public function canChangeUserRole(User $targetUser, string $newRole): bool
    {
        // SuperAdmin can change any role
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Course Admin can only change between MOH_Staff and External_User
        if ($this->isCourseAdmin()) {
            // Cannot modify SuperAdmin users
            if ($targetUser->isSuperAdmin()) {
                return false;
            }

            // Can only assign MOH_Staff or External_User roles
            $allowedRoles = [self::ROLE_MOH_STAFF, self::ROLE_EXTERNAL_USER];
            return in_array($newRole, $allowedRoles);
        }

        return false;
    }

    /**
     * Check if this user can view another user
     * Course Admins cannot see SuperAdmins
     *
     * @param User $targetUser The user being viewed
     * @return bool
     */
    public function canViewUser(User $targetUser): bool
    {
        // SuperAdmin can view everyone
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Course Admin cannot view SuperAdmins
        if ($this->isCourseAdmin() && $targetUser->isSuperAdmin()) {
            return false;
        }

        // Admins and Course Admins can view everyone else
        if ($this->isAdmin() || $this->isCourseAdmin()) {
            return true;
        }

        // Users can only view themselves
        return $this->id === $targetUser->id;
    }

    // =========================================================================
    // EMAIL DOMAIN METHODS
    // Methods for checking email domains
    // =========================================================================

    /**
     * Check if email belongs to MOH domain
     *
     * @param string $email
     * @return bool
     */
    public static function isMohEmail(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, "@"), 1));
        return $domain === self::MOH_EMAIL_DOMAIN;
    }

    /**
     * Check if this user has an MOH email
     *
     * @return bool
     */
    public function hasMohEmail(): bool
    {
        return self::isMohEmail($this->email);
    }

    // =========================================================================
    // RELATIONSHIP METHODS
    // =========================================================================

    /**
     * Get the course access requests for this user
     */
    public function courseAccessRequests()
    {
        return $this->hasMany(CourseAccessRequest::class);
    }

    /**
     * Get notifications for this user
     */
    public function systemNotifications()
    {
        return $this->hasMany(SystemNotification::class);
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount(): int
    {
        return $this->systemNotifications()->where('is_read', false)->count();
    }

    // ADD THESE NEW SCOPES
    /**
     * Scope to get only verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }
    
    /**
     * Scope to get only unverified users
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }
    
    /**
     * Scope to get users pending verification
     */
    public function scopePendingVerification($query)
    {
        return $query->whereNull('email_verified_at')
                    ->where('verification_status', 'pending');
    }

    // Your existing methods remain unchanged...
    public function hasMoodleAccount(): bool
    {
        return !is_null($this->moodle_user_id);
    }
    
    public function hasAnyPermission(...$permissions): bool
    {
        if ($this->hasRole('superadmin')) {
            return true;
        }
        
        return $this->hasAnyDirectPermission($permissions) || 
            $this->hasAnyPermissionViaRole($permissions);
    }

    /**
     * Get a human-readable display name for the user's role
     *
     * @return string
     */
    public function getRoleDisplayName(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Super Administrator';
        }
        if ($this->hasRole(self::ROLE_COURSE_ADMIN)) {
            return 'Course Administrator';
        }
        if ($this->isAdmin()) {
            return $this->is_course_admin ? 'Course Administrator' : 'Administrator';
        }
        if ($this->isMohStaff()) {
            return 'MOH Staff';
        }
        if ($this->isExternalUser()) {
            return 'External User';
        }

        $role = $this->roles->first();
        return $role ? ($role->display_name ?? ucfirst($role->name)) : 'No Role';
    }

    /**
     * Check if user can manage a specific course
     * SuperAdmin and Course Admins can manage all courses
     * Course creators can manage their own courses
     *
     * @param mixed $course The course to check
     * @return bool
     */
    public function canManageCourse($course): bool
    {
        // SuperAdmin can manage all courses
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Course Admin can manage all courses
        if ($this->isCourseAdmin()) {
            return true;
        }

        // Course creator can manage their own courses
        if ($this->is_course_creator && $course->creator_id === $this->id) {
            return true;
        }

        return false;
    }

    // User type check methods
    public function isInternal(): bool
    {
        return $this->user_type === self::TYPE_INTERNAL;
    }

    public function isExternal(): bool
    {
        return $this->user_type === self::TYPE_EXTERNAL;
    }

    // Account status check methods
    public function isPending(): bool
    {
        return $this->account_status === self::STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return $this->account_status === self::STATUS_ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->account_status === self::STATUS_INACTIVE;
    }

    // Account status scopes
    public function scopePendingApproval($query)
    {
        return $query->where('account_status', self::STATUS_PENDING);
    }

    public function scopeActiveAccounts($query)
    {
        return $query->where('account_status', self::STATUS_ACTIVE);
    }

    public function scopeInactiveAccounts($query)
    {
        return $query->where('account_status', self::STATUS_INACTIVE);
    }

    // Enrollment requests relationship
    public function enrollmentRequests()
    {
        return $this->hasMany(EnrollmentRequest::class);
    }

    // Get full name accessor
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

        public function canCreateCourses(): bool
        {
            return $this->is_course_creator || $this->isSuperAdmin() || $this->isCourseAdmin();
        }

        public function hasCompletedOtp(): bool
        {
            return $this->initial_otp_completed;
        }

        // Scopes
        public function scopeInternal($query)
        {
            return $query->where('user_type', self::TYPE_INTERNAL);
        }

        public function scopeExternal($query)
        {
            return $query->where('user_type', self::TYPE_EXTERNAL);
        }

        public function scopeCourseCreators($query)
        {
            return $query->where('is_course_creator', true);
        }

        // Actions
        public function grantCourseCreatorStatus(): void
        {
            $this->update(['is_course_creator' => true]);
        }

        public function revokeCourseCreatorStatus(): void
        {
            $this->update(['is_course_creator' => false]);
        }
}