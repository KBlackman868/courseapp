<?php

namespace App\Models;

use App\Traits\HasEnhancedVerification; 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, 
        Notifiable, 
        HasRoles, 
        HasEnhancedVerification;

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
        'temp_moodle_password',
        'verification_status',
        'verification_sent_at',
        'verification_attempts',
        'must_verify_before',
        'google_id',
        'is_suspended',
        'user_type',
        'is_course_creator',
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
            'otp_verified' => 'boolean',
            'initial_otp_completed' => 'boolean',
        ];
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

    public function getRoleDisplayName(): string
    {
        $role = $this->roles->first();
        return $role ? ($role->display_name ?? $role->name) : 'No Role';
    }

    public function canManageCourse($course): bool
    {
        if ($this->hasRole(['superadmin', 'course_admin'])) {
            return true;
        }
        
        if ($this->hasRole('instructor')) {
            return $course->instructor_id === $this->id;
        }
        
        return false;
    }
            // Constants
        public const TYPE_INTERNAL = 'internal';
        public const TYPE_EXTERNAL = 'external';

        // Check methods
        public function isInternal(): bool
        {
            return $this->user_type === self::TYPE_INTERNAL;
        }

        public function isExternal(): bool
        {
            return $this->user_type === self::TYPE_EXTERNAL;
        }

        public function canCreateCourses(): bool
        {
            return $this->is_course_creator || $this->hasRole('superadmin');
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