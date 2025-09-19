<?php
// app/Models/Permission.php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'category',
        'description',
    ];

    /**
     * Get permissions grouped by category
     */
    public static function getGroupedByCategory()
    {
        return self::all()->groupBy('category');
    }

    /**
     * Scope to filter by category
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}