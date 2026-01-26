<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentRequest extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DENIED = 'denied';

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'request_reason',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user who made the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for the request
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the admin who reviewed the request
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Status check methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isDenied(): bool
    {
        return $this->status === self::STATUS_DENIED;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeDenied($query)
    {
        return $query->where('status', self::STATUS_DENIED);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Approve the enrollment request
     */
    public function approve(?int $reviewerId = null, ?string $notes = null): bool
    {
        $this->status = self::STATUS_APPROVED;
        $this->reviewed_by = $reviewerId ?? auth()->id();
        $this->reviewed_at = now();

        if ($notes) {
            $this->admin_notes = $notes;
        }

        return $this->save();
    }

    /**
     * Deny the enrollment request
     */
    public function deny(?int $reviewerId = null, ?string $notes = null): bool
    {
        $this->status = self::STATUS_DENIED;
        $this->reviewed_by = $reviewerId ?? auth()->id();
        $this->reviewed_at = now();

        if ($notes) {
            $this->admin_notes = $notes;
        }

        return $this->save();
    }

    /**
     * Get status badge color for display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_DENIED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_DENIED => 'Denied',
            default => 'Unknown',
        };
    }
}
