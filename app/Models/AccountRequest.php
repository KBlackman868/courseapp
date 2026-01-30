<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * AccountRequest Model
 *
 * This model handles registration requests, especially for MOH Staff.
 *
 * WORKFLOW:
 * 1. User submits registration form with @health.gov.tt email
 * 2. Request is created with status = 'pending'
 * 3. Course Admin reviews and approves/rejects
 * 4. On approval: User record created, account activated
 * 5. User receives notification and can log in
 *
 * WHY THIS EXISTS:
 * - MOH Staff registrations need approval before access
 * - This provides an audit trail of who was approved and by whom
 * - Allows bulk approval by department for efficiency
 */
class AccountRequest extends Model
{
    use HasFactory;

    // =========================================================================
    // STATUS CONSTANTS
    // =========================================================================
    public const STATUS_PENDING = 'pending';     // Waiting for review
    public const STATUS_APPROVED = 'approved';   // Approved, user created
    public const STATUS_REJECTED = 'rejected';   // Denied access
    public const STATUS_SUSPENDED = 'suspended'; // Was approved, now suspended

    // =========================================================================
    // REQUEST TYPE CONSTANTS
    // =========================================================================
    public const TYPE_MOH_STAFF = 'moh_staff';   // @health.gov.tt email
    public const TYPE_EXTERNAL = 'external';     // Other emails

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'department',
        'organization',
        'phone',
        'status',
        'request_type',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'admin_notes',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $hidden = [
        'password', // Don't expose hashed password
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the admin who reviewed this request
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user created from this request (if approved)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isMohStaffRequest(): bool
    {
        return $this->request_type === self::TYPE_MOH_STAFF;
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
     * Get only MOH staff requests
     */
    public function scopeMohStaff($query)
    {
        return $query->where('request_type', self::TYPE_MOH_STAFF);
    }

    /**
     * Get pending MOH staff requests
     */
    public function scopePendingMohStaff($query)
    {
        return $query->pending()->mohStaff();
    }

    /**
     * Filter by department
     */
    public function scopeForDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    // =========================================================================
    // ACTION METHODS
    // =========================================================================

    /**
     * Approve this account request
     * Creates the user account and activates it
     *
     * @param User $reviewer The admin approving the request
     * @param string|null $notes Optional admin notes
     * @return User The created user
     */
    public function approve(User $reviewer, ?string $notes = null): User
    {
        // Determine the role based on request type
        $role = $this->isMohStaffRequest()
            ? User::ROLE_MOH_STAFF
            : User::ROLE_EXTERNAL_USER;

        // Create the user account
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password, // Already hashed
            'department' => $this->department,
            'organization' => $this->organization,
            'user_type' => $this->isMohStaffRequest() ? User::TYPE_INTERNAL : User::TYPE_EXTERNAL,
            'account_status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(), // Email verified through approval process
            'verification_status' => 'verified',
            'auth_method' => 'local',
        ]);

        // Assign the appropriate role
        $user->assignRole($role);

        // Update this request
        $this->update([
            'status' => self::STATUS_APPROVED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
            'user_id' => $user->id,
        ]);

        return $user;
    }

    /**
     * Reject this account request
     *
     * @param User $reviewer The admin rejecting the request
     * @param string|null $reason Reason shown to the user
     * @param string|null $notes Internal admin notes
     */
    public function reject(User $reviewer, ?string $reason = null, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $notes,
        ]);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Get the full name of the requester
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get a color for the status badge
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_SUSPENDED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get a human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_SUSPENDED => 'Suspended',
            default => ucfirst($this->status),
        };
    }

    /**
     * Create a new account request from registration data
     *
     * @param array $data Registration form data
     * @return AccountRequest
     */
    public static function createFromRegistration(array $data): self
    {
        // Determine request type based on email domain
        $requestType = User::isMohEmail($data['email'])
            ? self::TYPE_MOH_STAFF
            : self::TYPE_EXTERNAL;

        return self::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'department' => $data['department'] ?? null,
            'organization' => $data['organization'] ?? null,
            'phone' => $data['phone'] ?? null,
            'status' => self::STATUS_PENDING,
            'request_type' => $requestType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get distinct departments from pending requests
     * Used for department-based bulk approval
     */
    public static function getPendingDepartments(): array
    {
        return self::pending()
            ->mohStaff()
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->toArray();
    }
}
