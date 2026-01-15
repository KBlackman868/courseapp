<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseCreatorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin|admin']);
    }

    /**
     * Display list of course creators grouped by type
     */
    public function index(Request $request)
    {
        $query = User::where('is_course_creator', true);

        // Filter by user type
        if ($request->filled('user_type') && $request->user_type !== 'all') {
            $query->where('user_type', $request->user_type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $courseCreators = $query->orderBy('user_type')->orderBy('first_name')->paginate(20);

        // Get statistics
        $stats = [
            'total_creators' => User::where('is_course_creator', true)->count(),
            'internal_creators' => User::where('is_course_creator', true)->where('user_type', 'internal')->count(),
            'external_creators' => User::where('is_course_creator', true)->where('user_type', 'external')->count(),
            'total_courses_created' => \App\Models\Course::whereNotNull('creator_id')->count(),
        ];

        // Get users who can potentially become course creators
        $potentialCreators = User::where('is_course_creator', false)
            ->whereNotNull('email_verified_at')
            ->orderBy('first_name')
            ->get();

        return view('admin.course-creators.index', compact('courseCreators', 'stats', 'potentialCreators'));
    }

    /**
     * Grant course creator status to a user
     */
    public function grant(Request $request, User $user)
    {
        // Check if already a course creator
        if ($user->is_course_creator) {
            return back()->with('info', "{$user->full_name} is already a course creator.");
        }

        // Grant course creator status
        $user->grantCourseCreatorStatus();

        // Log the action
        ActivityLogger::logSystem('course_creator_granted',
            "Course creator status granted to {$user->full_name}",
            [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_type' => $user->user_type,
                'granted_by' => auth()->user()->email,
            ]
        );

        Log::info('Course creator status granted', [
            'user_id' => $user->id,
            'granted_by' => auth()->id(),
        ]);

        return back()->with('success', "Course creator status granted to {$user->full_name}.");
    }

    /**
     * Revoke course creator status from a user
     */
    public function revoke(Request $request, User $user)
    {
        // Check if not a course creator
        if (!$user->is_course_creator) {
            return back()->with('info', "{$user->full_name} is not a course creator.");
        }

        // Check if user has created courses
        $coursesCount = $user->createdCourses()->count();
        if ($coursesCount > 0 && !$request->boolean('confirm')) {
            return back()->with('warning', 
                "{$user->full_name} has created {$coursesCount} course(s). " .
                "Are you sure you want to revoke their course creator status? " .
                "This will not delete their existing courses."
            )->with('confirm_revoke', $user->id);
        }

        // Revoke course creator status
        $user->revokeCourseCreatorStatus();

        // Log the action
        ActivityLogger::logSystem('course_creator_revoked',
            "Course creator status revoked from {$user->full_name}",
            [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_type' => $user->user_type,
                'revoked_by' => auth()->user()->email,
                'had_courses' => $coursesCount,
            ]
        );

        Log::info('Course creator status revoked', [
            'user_id' => $user->id,
            'revoked_by' => auth()->id(),
        ]);

        return back()->with('success', "Course creator status revoked from {$user->full_name}.");
    }

    /**
     * Bulk grant course creator status
     */
    public function bulkGrant(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $granted = 0;
        $skipped = 0;

        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            
            if (!$user || $user->is_course_creator) {
                $skipped++;
                continue;
            }

            $user->grantCourseCreatorStatus();
            $granted++;

            ActivityLogger::logSystem('course_creator_granted',
                "Course creator status granted to {$user->full_name} (bulk)",
                [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'granted_by' => auth()->user()->email,
                ]
            );
        }

        return back()->with('success', "Course creator status granted to {$granted} user(s). {$skipped} skipped.");
    }

    /**
     * Bulk revoke course creator status
     */
    public function bulkRevoke(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $revoked = 0;
        $skipped = 0;

        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            
            if (!$user || !$user->is_course_creator) {
                $skipped++;
                continue;
            }

            $user->revokeCourseCreatorStatus();
            $revoked++;

            ActivityLogger::logSystem('course_creator_revoked',
                "Course creator status revoked from {$user->full_name} (bulk)",
                [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'revoked_by' => auth()->user()->email,
                ]
            );
        }

        return back()->with('success', "Course creator status revoked from {$revoked} user(s). {$skipped} skipped.");
    }

    /**
     * Show course creator statistics and activity
     */
    public function statistics()
    {
        // Get course creation statistics
        $stats = [
            'by_type' => [
                'internal' => [
                    'creators' => User::internal()->courseCreators()->count(),
                    'courses' => \App\Models\Course::whereHas('creator', function ($q) {
                        $q->where('user_type', 'internal');
                    })->count(),
                ],
                'external' => [
                    'creators' => User::external()->courseCreators()->count(),
                    'courses' => \App\Models\Course::whereHas('creator', function ($q) {
                        $q->where('user_type', 'external');
                    })->count(),
                ],
            ],
            'recent_courses' => \App\Models\Course::with('creator')
                ->whereNotNull('creator_id')
                ->latest()
                ->take(10)
                ->get(),
            'top_creators' => User::courseCreators()
                ->withCount('createdCourses')
                ->orderByDesc('created_courses_count')
                ->take(10)
                ->get(),
        ];

        return view('admin.course-creators.statistics', compact('stats'));
    }
}
