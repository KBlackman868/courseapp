<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'image',
        'moodle_course_id',
        'moodle_course_shortname',
        'category_id',
        'creator_id'
    ];

    protected $casts = [
        'moodle_course_id' => 'integer',
    ];

    /**
     * Get the enrollments for the course
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
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
}