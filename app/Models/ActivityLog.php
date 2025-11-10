<?php
// app/Models/ActivityLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_email',
        'user_name',
        'action',
        'description',
        'model_type',
        'model_id',
        'properties',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'status',
        'severity',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity
     */
    public function subject()
    {
        if ($this->model_type) {
            return $this->morphTo('subject', 'model_type', 'model_id');
        }
        return null;
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Get severity badge color
     */
    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'error' => 'orange',
            'warning' => 'yellow',
            'info' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'success' => 'green',
            'failed' => 'red',
            'pending' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Get formatted action name
     */
    public function getFormattedActionAttribute(): string
    {
        return str_replace('.', ' ', ucfirst($this->action));
    }

    /**
     * Get icon for action type
     */
    public function getActionIconAttribute(): string
    {
        $actionParts = explode('.', $this->action);
        $category = $actionParts[0] ?? '';
        
        return match($category) {
            'auth' => '🔐',
            'user' => '👤',
            'course' => '📚',
            'enrollment' => '📝',
            'moodle' => '🎓',
            'admin' => '⚙️',
            'email' => '✉️',
            'system' => '🖥️',
            default => '📌'
        };
    }
}