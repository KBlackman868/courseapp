<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // allows the system to send notifications
use Spatie\Permission\Traits\HasRoles; // Import the trait

class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
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
     * Get user's role display name
     */
    public function getRoleDisplayName(): string
    {
        $role = $this->roles->first();
        return $role ? ($role->display_name ?? $role->name) : 'No Role';
    }

    /**
     * Check if user can manage course
     */
    public function canManageCourse($course): bool
    {
        if ($this->hasRole(['superadmin', 'course_admin'])) {
            return true;
        }
        
        // Instructors can only manage their own courses
        if ($this->hasRole('instructor')) {
            // Add logic to check if user is instructor of this course
            return $course->instructor_id === $this->id;
        }
        
        return false;
    }
}
