<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class CacheService
{
    /**
     * Cache TTL in seconds (1 hour default)
     */
    private const DEFAULT_TTL = 3600;

    /**
     * Cache keys
     */
    private const KEYS = [
        'active_courses' => 'courses:active',
        'all_courses' => 'courses:all',
        'course' => 'course:',
        'categories' => 'categories:all',
        'category_tree' => 'categories:tree',
        'course_stats' => 'stats:courses',
        'user_stats' => 'stats:users',
    ];

    /**
     * Get all active courses (cached).
     */
    public function getActiveCourses(): Collection
    {
        return Cache::remember(
            self::KEYS['active_courses'],
            self::DEFAULT_TTL,
            fn() => Course::where('status', 'active')
                ->with('category')
                ->orderBy('title')
                ->get()
        );
    }

    /**
     * Get all courses (cached).
     */
    public function getAllCourses(): Collection
    {
        return Cache::remember(
            self::KEYS['all_courses'],
            self::DEFAULT_TTL,
            fn() => Course::with('category')->orderBy('title')->get()
        );
    }

    /**
     * Get a single course by ID (cached).
     */
    public function getCourse(int $id): ?Course
    {
        return Cache::remember(
            self::KEYS['course'] . $id,
            self::DEFAULT_TTL,
            fn() => Course::with(['category', 'creator'])->find($id)
        );
    }

    /**
     * Get all categories (cached).
     */
    public function getCategories(): Collection
    {
        return Cache::remember(
            self::KEYS['categories'],
            self::DEFAULT_TTL,
            fn() => Category::orderBy('name')->get()
        );
    }

    /**
     * Get categories as a tree structure (cached).
     */
    public function getCategoryTree(): Collection
    {
        return Cache::remember(
            self::KEYS['category_tree'],
            self::DEFAULT_TTL,
            fn() => Category::whereNull('parent_id')
                ->with('children')
                ->orderBy('sortorder')
                ->get()
        );
    }

    /**
     * Get course statistics (cached).
     */
    public function getCourseStats(): array
    {
        return Cache::remember(
            self::KEYS['course_stats'],
            300, // 5 minutes for stats
            fn() => [
                'total' => Course::count(),
                'active' => Course::where('status', 'active')->count(),
                'inactive' => Course::where('status', 'inactive')->count(),
                'with_moodle' => Course::whereNotNull('moodle_course_id')->count(),
            ]
        );
    }

    /**
     * Get user statistics (cached).
     */
    public function getUserStats(): array
    {
        return Cache::remember(
            self::KEYS['user_stats'],
            300, // 5 minutes for stats
            fn() => [
                'total' => \App\Models\User::count(),
                'internal' => \App\Models\User::where('user_type', 'internal')->count(),
                'external' => \App\Models\User::where('user_type', 'external')->count(),
                'with_moodle' => \App\Models\User::whereNotNull('moodle_user_id')->count(),
            ]
        );
    }

    /**
     * Clear course-related cache.
     */
    public function clearCourseCache(?int $courseId = null): void
    {
        Cache::forget(self::KEYS['active_courses']);
        Cache::forget(self::KEYS['all_courses']);
        Cache::forget(self::KEYS['course_stats']);

        if ($courseId) {
            Cache::forget(self::KEYS['course'] . $courseId);
        }
    }

    /**
     * Clear category cache.
     */
    public function clearCategoryCache(): void
    {
        Cache::forget(self::KEYS['categories']);
        Cache::forget(self::KEYS['category_tree']);
    }

    /**
     * Clear all application cache.
     */
    public function clearAll(): void
    {
        foreach (self::KEYS as $key) {
            if (!str_ends_with($key, ':')) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Warm up the cache by pre-loading commonly accessed data.
     */
    public function warmUp(): void
    {
        $this->getActiveCourses();
        $this->getCategories();
        $this->getCategoryTree();
        $this->getCourseStats();
        $this->getUserStats();
    }
}
