<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * CourseAccessRequest Model
 *
 * This model handles requests from users who want to enroll in courses
 * that have APPROVAL_REQUIRED enrollment type.
 *
 * WORKFLOW:
 * 1. User sees a course with APPROVAL_REQUIRED enrollment
 * 2. User clicks "Request Access" button
 * 3. Request created with status = 'pending'
 * 4. Course Admin reviews and approves/rejects
 * 5. On approval:
 *    - Moodle account created (if needed) via CreateMoodleUserJob
 *    - User enrolled in Moodle course via EnrollMoodleUserJob
 *    - User can now click "Go to Course"
 *
 * CTA BUTTON BEHAVIOR:
 * - OPEN_ENROLLMENT + eligible: "Enroll"
 * - APPROVAL_REQUIRED: "Request Access"
 * - Pending request: "Pending Approval" (disabled)
 * - Approved: "Go to Course"
 * - Rejected: "Rejected" + reason + optional "Request Again"
 */
class CourseAccessRequest extends Model
{
    use HasFactory;

    // =========================================================================
    // STATUS CONSTANTS
    // =========================================================================
    public const STATUS_PENDING = 'pending';     // Waiting for review
    public const STATUS_APPROVED = 'approved';   // Can access course
    public const STATUS_REJECTED = 'rejected';   // Access denied
    public const STATUS_REVOKED = 'revoked';     // Access was removed
    public const STATUS_EXPIRED = 'expired';     // Request timed out

    // =========================================================================
    // MOODLE SYNC STATUS CONSTANTS
    // =========================================================================
    public const SYNC_NOT_SYNCED = 'not_synced'; // Not synced yet
    public const SYNC_SYNCING = 'syncing';       // Sync in progress
    public const SYNC_SYNCED = 'synced';         // Successfully synced
    public const SYNC_FAILED = 'failed';         // Sync failed

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'request_reason',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'admin_notes',
        'moodle_sync_status',
        'moodle_sync_error',
        'moodle_sync_attempts',
        'last_sync_attempt',
        'requested_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'requested_at' => 'datetime',
        'last_sync_attempt' => 'datetime',
        'moodle_sync_attempts' => 'integer',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the user who made this request
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course being requested
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the admin who approved/rejected this request
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // =========================================================================
    // STATUS CHECK METHODS
    // =========================================================================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }

    public function canAccessCourse(): bool
    {
        return $this->isApproved() && $this->moodle_sync_status === self::SYNC_SYNCED;
    }

    public function hasSyncFailed(): bool
    {
        return $this->moodle_sync_status === self::SYNC_FAILED;
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Get only pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Get approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Get requests for a specific course
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Get requests with failed Moodle sync
     */
    public function scopeSyncFailed($query)
    {
        return $query->where('moodle_sync_status', self::SYNC_FAILED);
    }

    // =========================================================================
    // ACTION METHODS
    // =========================================================================

    /**
     * Approve this course access request
     *
     * @param User $approver The admin approving the request
     * @param string|null $notes Optional admin notes
     */
    public function approve(User $approver, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'admin_notes' => $notes,
            'moodle_sync_status' => self::SYNC_NOT_SYNCED, // Will be synced by job
        ]);
    }

    /**
     * Reject this course access request
     *
     * @param User $approver The admin rejecting the request
     * @param string|null $reason Reason shown to the user
     * @param string|null $notes Internal admin notes
     */
    public function reject(User $approver, ?string $reason = null, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Revoke access (after it was granted)
     *
     * @param User $revoker The admin revoking access
     * @param string|null $reason Reason for revocation
     */
    public function revoke(User $revoker, ?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REVOKED,
            'rejection_reason' => $reason,
            'admin_notes' => "Access revoked by {$revoker->full_name}",
        ]);
    }

    /**
     * Mark Moodle sync as in progress
     */
    public function markSyncing(): void
    {
        $this->update([
            'moodle_sync_status' => self::SYNC_SYNCING,
            'last_sync_attempt' => now(),
            'moodle_sync_attempts' => $this->moodle_sync_attempts + 1,
        ]);
    }

    /**
     * Mark Moodle sync as successful
     */
    public function markSynced(): void
    {
        $this->update([
            'moodle_sync_status' => self::SYNC_SYNCED,
            'moodle_sync_error' => null,
        ]);
    }

    /**
     * Mark Moodle sync as failed
     *
     * @param string $error Error message
     */
    public function markSyncFailed(string $error): void
    {
        $this->update([
            'moodle_sync_status' => self::SYNC_FAILED,
            'moodle_sync_error' => $error,
        ]);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Get a color for the status badge
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_REVOKED => 'gray',
            self::STATUS_EXPIRED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get a human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_REVOKED => 'Access Revoked',
            self::STATUS_EXPIRED => 'Expired',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the CTA button text for this request
     */
    public function getCtaTextAttribute(): string
    {
        if ($this->isPending()) {
            return 'Pending Approval';
        }

        if ($this->isApproved()) {
            if ($this->hasSyncFailed()) {
                return 'Enrollment Processing Failed';
            }
            if ($this->moodle_sync_status === self::SYNC_SYNCED) {
                return 'Go to Course';
            }
            return 'Processing...';
        }

        if ($this->isRejected()) {
            return 'Rejected';
        }

        return 'Unknown';
    }

    /**
     * Check if user can request again (after rejection)
     */
    public function canRequestAgain(): bool
    {
        return $this->isRejected();
    }

    /**
     * Create a new access request
     *
     * @param User $user The user requesting access
     * @param Course $course The course they want access to
     * @param string|null $reason Why they want access
     * @return CourseAccessRequest
     */
    public static function createRequest(User $user, Course $course, ?string $reason = null): self
    {
        return self::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => self::STATUS_PENDING,
            'request_reason' => $reason,
            'moodle_sync_status' => self::SYNC_NOT_SYNCED,
            'moodle_sync_attempts' => 0,
            'requested_at' => now(),
        ]);
    }

    /**
     * Find existing request for user and course
     *
     * @param int $userId
     * @param int $courseId
     * @return CourseAccessRequest|null
     */
    public static function findForUserAndCourse(int $userId, int $courseId): ?self
    {
        return self::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }
}
