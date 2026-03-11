<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * AccountRequest Model
 *
 * Handles registration requests for both MOH Staff and External Users.
 *
 * WORKFLOW:
 * 1. User registers → AccountRequest created with status = 'pending_verification'
 * 2. Verification email sent with signed link (24-hour expiry)
 * 3. User clicks link → status = 'email_verified'
 * 4. MOH Staff (@health.gov.tt): auto-approved → User created, Moodle synced
 * 5. External Users: admin reviews → approves or rejects
 * 6. Rejection = permanent deletion of all records (no email sent)
 */
class AccountRequest extends Model
{
    use HasFactory;

    // =========================================================================
    // STATUS CONSTANTS
    // =========================================================================
    public const STATUS_PENDING = 'pending';                     // Legacy compat
    public const STATUS_PENDING_VERIFICATION = 'pending_verification'; // Awaiting email click
    public const STATUS_EMAIL_VERIFIED = 'email_verified';       // Email verified, awaiting approval
    public const STATUS_APPROVED = 'approved';                   // Approved, user created
    public const STATUS_REJECTED = 'rejected';                   // Denied access
    public const STATUS_SUSPENDED = 'suspended';                 // Was approved, now suspended

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
        'email_verified_at',
        'verification_token',
    ];

    protected $hidden = [
        'password',
        'verification_token',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =========================================================================
    // STATUS CHECK METHODS
    // =========================================================================

    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PENDING_VERIFICATION,
            self::STATUS_EMAIL_VERIFIED,
        ]);
    }

    public function isPendingVerification(): bool
    {
        return $this->status === self::STATUS_PENDING_VERIFICATION;
    }

    public function isEmailVerified(): bool
    {
        return in_array($this->status, [
            self::STATUS_EMAIL_VERIFIED,
            self::STATUS_APPROVED,
        ]);
    }

    public function isAwaitingAdminApproval(): bool
    {
        return $this->status === self::STATUS_EMAIL_VERIFIED;
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

    public function isExternalRequest(): bool
    {
        return $this->request_type === self::TYPE_EXTERNAL;
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_PENDING_VERIFICATION,
            self::STATUS_EMAIL_VERIFIED,
        ]);
    }

    public function scopeAwaitingApproval($query)
    {
        return $query->where('status', self::STATUS_EMAIL_VERIFIED);
    }

    public function scopeMohStaff($query)
    {
        return $query->where('request_type', self::TYPE_MOH_STAFF);
    }

    public function scopeExternal($query)
    {
        return $query->where('request_type', self::TYPE_EXTERNAL);
    }

    public function scopePendingMohStaff($query)
    {
        return $query->pending()->mohStaff();
    }

    public function scopeForDepartment($query, string $department)
    {
        return $query->where('department', $department);
    }

    // =========================================================================
    // ACTION METHODS
    // =========================================================================

    /**
     * Mark email as verified. For MOH staff, this triggers auto-approval.
     */
    public function markEmailVerified(): void
    {
        $this->update([
            'status' => self::STATUS_EMAIL_VERIFIED,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Approve this account request — creates the user account.
     */
    public function approve(User $reviewer, ?string $notes = null): User
    {
        return DB::transaction(function () use ($reviewer, $notes) {
            $role = $this->isMohStaffRequest()
                ? User::ROLE_MOH_STAFF
                : User::ROLE_EXTERNAL_USER;

            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => $this->password, // Already hashed
                'department' => $this->department,
                'organization' => $this->organization,
                'user_type' => $this->isMohStaffRequest() ? User::TYPE_INTERNAL : User::TYPE_EXTERNAL,
                'account_status' => User::STATUS_ACTIVE,
                'email_verified_at' => $this->email_verified_at ?? now(),
                'verification_status' => 'verified',
                'auth_method' => 'local',
            ]);

            $user->assignRole($role);

            $this->update([
                'status' => self::STATUS_APPROVED,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
                'admin_notes' => $notes,
                'user_id' => $user->id,
            ]);

            return $user;
        });
    }

    /**
     * Reject — just marks as rejected. The controller handles the hard delete.
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

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING, self::STATUS_PENDING_VERIFICATION => 'yellow',
            self::STATUS_EMAIL_VERIFIED => 'blue',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_SUSPENDED => 'gray',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_PENDING_VERIFICATION => 'Pending Email Verification',
            self::STATUS_EMAIL_VERIFIED => 'Email Verified',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_SUSPENDED => 'Suspended',
            default => ucfirst($this->status),
        };
    }

    public static function createFromRegistration(array $data): self
    {
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
            'status' => self::STATUS_PENDING_VERIFICATION,
            'request_type' => $requestType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

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
