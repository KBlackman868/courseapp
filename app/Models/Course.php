<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    // Audience type constants
    public const AUDIENCE_MOH = 'moh';
    public const AUDIENCE_EXTERNAL = 'external';
    public const AUDIENCE_ALL = 'all';

    protected $fillable = [
        'title',
        'description',
        'status',
        'audience_type',
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
    public function getEnrollmentAdmins()
    {
        $admins = collect();

        // 1. Course creator (if exists and has appropriate permissions)
        if ($this->creator_id && $this->creator) {
            $admins->push($this->creator);
        }

        // 2. Users with course_admin or admin role
        $courseAdmins = User::role(['course_admin', 'admin'])->get();
        $admins = $admins->merge($courseAdmins);

        // 3. If no specific admins found, fall back to superadmins
        if ($admins->isEmpty()) {
            $admins = User::role('superadmin')->get();
        }

        return $admins->unique('id');
    }

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
     */
    public function isOpenEnrollment(): bool
    {
        return $this->is_free;
    }

    /**
     * Check if the course requires approval
     */
    public function requiresApproval(): bool
    {
        return !$this->is_free;
    }

    /**
     * Check if a user can view this course based on audience type
     */
    public function isVisibleTo(User $user): bool
    {
        if ($this->audience_type === self::AUDIENCE_ALL) {
            return true;
        }

        if ($this->audience_type === self::AUDIENCE_MOH && $user->isInternal()) {
            return true;
        }

        if ($this->audience_type === self::AUDIENCE_EXTERNAL && $user->isExternal()) {
            return true;
        }

        return false;
    }

    /**
     * Get audience type display label
     */
    public function getAudienceLabelAttribute(): string
    {
        return match($this->audience_type) {
            self::AUDIENCE_MOH => 'MOH Staff Only',
            self::AUDIENCE_EXTERNAL => 'External Users Only',
            self::AUDIENCE_ALL => 'All Users',
            default => 'Unknown',
        };
    }

    /**
     * Get enrollment type display label
     */
    public function getEnrollmentTypeLabelAttribute(): string
    {
        return $this->is_free ? 'Open Enrollment' : 'Requires Approval';
    }
}