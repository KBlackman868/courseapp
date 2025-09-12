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