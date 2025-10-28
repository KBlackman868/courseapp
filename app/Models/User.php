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
        'profile_photo',
        'moodle_user_id',
        'temp_moodle_password',
        'verification_status',
        'verification_sent_at',
        'verification_attempts',
        'must_verify_before',
        'google_id',
        'is_suspended',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'temp_moodle_password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // ADD THESE NEW CASTS
            'verification_sent_at' => 'datetime',
            'must_verify_before' => 'datetime',
            'verification_attempts' => 'integer',
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
}