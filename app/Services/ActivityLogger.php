<?php
// app/Services/ActivityLogger.php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * Log an activity
     */
    public static function log(
        string $action,
        string $description,
        Model $subject = null,
        array $properties = [],
        string $status = 'success',
        string $severity = 'info'
    ): ActivityLog {
        $user = Auth::user();
        
        return ActivityLog::create([
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_name' => $user ? "{$user->first_name} {$user->last_name}" : 'System',
            'action' => $action,
            'description' => $description,
            'model_type' => $subject ? get_class($subject) : null,
            'model_id' => $subject?->id,
            'properties' => !empty($properties) ? $properties : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'method' => Request::method(),
            'url' => Request::fullUrl(),
            'status' => $status,
            'severity' => $severity,
        ]);
    }

    /**
     * Log authentication activity
     */
    public static function logAuth(string $action, string $description, array $properties = []): ActivityLog
    {
        return self::log("auth.{$action}", $description, null, $properties);
    }

    /**
     * Log user management activity
     */
    public static function logUser(string $action, User $user, string $description, array $properties = []): ActivityLog
    {
        return self::log("user.{$action}", $description, $user, $properties);
    }

    /**
     * Log course activity
     */
    public static function logCourse(string $action, $course, string $description, array $properties = []): ActivityLog
    {
        return self::log("course.{$action}", $description, $course, $properties);
    }

    /**
     * Log enrollment activity
     */
    public static function logEnrollment(string $action, $enrollment, string $description, array $properties = []): ActivityLog
    {
        return self::log("enrollment.{$action}", $description, $enrollment, $properties);
    }

    /**
     * Log Moodle integration activity
     */
    public static function logMoodle(string $action, string $description, Model $subject = null, array $properties = []): ActivityLog
    {
        return self::log("moodle.{$action}", $description, $subject, $properties);
    }

    /**
     * Log system activity
     */
    public static function logSystem(string $action, string $description, array $properties = [], string $severity = 'info'): ActivityLog
    {
        return self::log("system.{$action}", $description, null, $properties, 'success', $severity);
    }

    /**
     * Log failed activity
     */
    public static function logFailure(string $action, string $description, array $properties = [], string $severity = 'error'): ActivityLog
    {
        return self::log($action, $description, null, $properties, 'failed', $severity);
    }
}