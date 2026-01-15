# Performance Analysis Report

**Date:** 2026-01-15
**Codebase:** Course Management / LMS Application (Laravel 11 + React)

---

## Executive Summary

This analysis identified **25+ performance anti-patterns** across the codebase, categorized by severity:

| Severity | Count | Primary Issues |
|----------|-------|----------------|
| **Critical** | 3 | N+1 queries in bulk operations, synchronous API calls in loops |
| **High** | 8 | Synchronous mail, unbounded queries, inefficient loops |
| **Medium** | 9 | Duplicate queries, missing eager loading, queue saturation |
| **Low** | 5 | Missing React optimizations, DOM manipulation |

---

## Critical Issues

### 1. N+1 Query in Bulk Course Delete
**File:** `app/Http/Controllers/CourseController.php:334-372`

```php
// PROBLEM: Individual query per course
foreach ($request->course_ids as $courseId) {
    $course = Course::find($courseId);  // N+1!
    if ($course->enrollments()->exists()) {  // N+2!
        ActivityLogger::logCourse('delete_skipped', $course, "...", [
            'enrollment_count' => $course->enrollments()->count(),  // N+3!
        ]);
    }
}
```

**Impact:** 3N database queries for N courses
**Fix:**
```php
$courses = Course::whereIn('id', $request->course_ids)
    ->withCount('enrollments')
    ->get();

foreach ($courses as $course) {
    if ($course->enrollments_count > 0) {
        // Use $course->enrollments_count directly
    }
}
```

---

### 2. N+1 Query in Bulk User Delete
**File:** `app/Http/Controllers/UserManagementController.php:178-213`

```php
// PROBLEM: Individual query per user
foreach ($request->user_ids as $userId) {
    $user = User::find($userId);  // N+1!
    Enrollment::where('user_id', $user->id)->delete();  // N+2!
}
```

**Impact:** 2N queries for N users
**Fix:**
```php
$users = User::whereIn('id', $request->user_ids)->get();
$userIds = $users->pluck('id');
Enrollment::whereIn('user_id', $userIds)->delete();  // Single batch delete
```

---

### 3. N+1 Query in Bulk Course Sync
**File:** `app/Http/Controllers/CourseController.php:500-557`

```php
// PROBLEM: Individual query + API call per course
foreach ($request->course_ids as $courseId) {
    $course = Course::find($courseId);  // N+1!
    $this->updateMoodleCourse($course);  // Synchronous HTTP!
}
```

**Impact:** N database queries + N synchronous HTTP requests
**Fix:** Use batch loading + Laravel queues

---

## High Severity Issues

### 4. Synchronous Email in Loop
**File:** `app/Http/Controllers/EnrollmentController.php:157-160`

```php
// PROBLEM: Synchronous mail sending
$superadmins = User::role('superadmin')->get();
foreach ($superadmins as $admin) {
    Mail::to($admin->email)->send(new NewCourseEnrollmentEmail($enrollment));
}
```

**Impact:** Request blocked while each email sends
**Fix:**
```php
Mail::to($admin->email)->queue(new NewCourseEnrollmentEmail($enrollment));
```

---

### 5. Unbounded Query in Export
**File:** `app/Http/Controllers/Admin/ActivityLogController.php:146`

```php
// PROBLEM: No limit on export
$logs = $query->get();  // Could be 100,000+ rows!
```

**Impact:** Memory exhaustion with large datasets
**Fix:**
```php
$query->lazy()->each(function ($log) use ($handle) {
    fputcsv($handle, [...]);
});
```

---

### 6. N+1 in Moodle Course Sync Service
**File:** `app/Services/MoodleCourseSync.php:118-143`

```php
// PROBLEM: Database check in loop
foreach ($moodleCourses as $moodleCourse) {
    $existingCourse = Course::where('moodle_course_id', $moodleCourse['id'])->exists();  // N+1!
}
```

**Fix:**
```php
$existingIds = Course::whereNotNull('moodle_course_id')
    ->pluck('moodle_course_id')
    ->flip()
    ->toArray();  // O(1) lookup with isset()
```

---

### 7. External API Call in Loop
**File:** `app/Services/MoodleCourseSync.php:219-234`

```php
// PROBLEM: HTTP request per course
foreach ($courses as $course) {
    $enrollments = $this->fetchCourseEnrollments($course['id']);  // HTTP call!
}
```

**Impact:** N HTTP requests to Moodle API
**Fix:** Batch API calls or cache results

---

### 8. User Refresh in Loop
**File:** `app/Http/Controllers/EnrollmentController.php:549-575`

```php
foreach ($approvedEnrollments as $enrollment) {
    if (!$enrollment->user->moodle_user_id) {
        CreateOrLinkMoodleUser::dispatchSync($enrollment->user);
        $enrollment->user->refresh();  // Extra query per user!
    }
}
```

---

### 9. count() in Loop Condition
**File:** `app/Http/Controllers/Admin/MoodleCourseImportController.php:398-403`

```php
// PROBLEM: count() evaluated each iteration
for ($i = 1; $i < count($rows); $i++) {
    if (count($rows[$i]) === count($headers)) {
```

**Fix:**
```php
$rowCount = count($rows);
$headerCount = count($headers);
for ($i = 1; $i < $rowCount; $i++) {
```

---

### 10. Inefficient Array Lookup
**File:** `app/Http/Controllers/Admin/MoodleCourseImportController.php:150-194`

```php
// PROBLEM: in_array is O(n)
foreach ($moodleCourses as $moodleCourse) {
    if (!in_array($moodleCourse['id'], $localMoodleIds)) {  // O(n) per iteration!
```

**Fix:**
```php
$localMoodleIds = Course::whereNotNull('moodle_course_id')
    ->pluck('moodle_course_id')
    ->flip()
    ->toArray();

if (!isset($localMoodleIds[$moodleCourse['id']])) {  // O(1)
```

---

### 11. Fetch All Then Search
**File:** `app/Http/Controllers/Admin/MoodleCourseImportController.php:285-320`

```php
// PROBLEM: Fetches all courses to find one
$moodleCourses = $this->syncService->fetchMoodleCourses();  // ALL courses
foreach ($moodleCourses as $mc) {
    if ($mc['id'] == $course->moodle_course_id) {
        $moodleCourse = $mc;
        break;
    }
}
```

**Fix:** Create method to fetch single course by ID

---

## Medium Severity Issues

### 12. Multiple Count Queries for Statistics
**File:** `app/Http/Controllers/CourseController.php:627-633`

```php
// PROBLEM: 5 separate queries
$stats = [
    'total' => Course::count(),
    'active' => Course::where('status', 'active')->count(),
    'inactive' => Course::where('status', 'inactive')->count(),
    'synced' => Course::whereNotNull('moodle_course_id')->count(),
    'not_synced' => Course::whereNull('moodle_course_id')->count(),
];
```

**Fix:**
```php
$stats = DB::table('courses')->select(
    DB::raw('COUNT(*) as total'),
    DB::raw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active"),
    DB::raw("SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive"),
    DB::raw('SUM(CASE WHEN moodle_course_id IS NOT NULL THEN 1 ELSE 0 END) as synced'),
    DB::raw('SUM(CASE WHEN moodle_course_id IS NULL THEN 1 ELSE 0 END) as not_synced')
)->first();
```

---

### 13. Duplicate Statistics Queries
**File:** `app/Http/Controllers/Admin/ActivityLogController.php:79-84`

```php
// PROBLEM: 4 queries with same date filter
$stats = [
    'total_today' => ActivityLog::whereDate('created_at', today())->count(),
    'failed_today' => ActivityLog::whereDate('created_at', today())->where('status', 'failed')->count(),
    'unique_users_today' => ActivityLog::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
    'critical_events' => ActivityLog::whereDate('created_at', today())->where('severity', 'critical')->count(),
];
```

---

### 14. Loading All Users Without Pagination
**File:** `app/Http/Controllers/Admin/ActivityLogController.php:76`

```php
$users = User::select('id', 'first_name', 'last_name', 'email')->get();  // No limit!
```

---

### 15. Role Loading Without Limit
**File:** `app/Http/Controllers/Admin/RoleManagementController.php:16`

```php
$roles = Role::all();  // Loads all roles into memory
```

---

## React/Frontend Issues

### 16. Inline Function Handlers (Multiple Files)
**Files:** All form components in `resources/js/Pages/Auth/` and `resources/js/Pages/Profile/`

```jsx
// PROBLEM: New function created on every render
<TextInput onChange={(e) => setData('email', e.target.value)} />
```

**Fix:**
```jsx
const handleEmailChange = useCallback(
    (e) => setData('email', e.target.value),
    [setData]
);
<TextInput onChange={handleEmailChange} />
```

**Affected Files:**
- `resources/js/Pages/Auth/Login.jsx:46,62,73-74`
- `resources/js/Pages/Auth/Register.jsx:39,56,73,93-94`
- `resources/js/Pages/Auth/ResetPassword.jsx:39,56,75-76`
- `resources/js/Pages/Profile/Partials/UpdatePasswordForm.jsx:71-72,92,110-111`
- `resources/js/Pages/Profile/Partials/UpdateProfileInformationForm.jsx:47,64`
- `resources/js/Pages/Profile/Partials/DeleteUserForm.jsx:93-94`

---

### 17. Direct DOM Manipulation
**File:** `resources/js/Pages/Welcome.jsx:5-12`

```jsx
// PROBLEM: Bypasses React's virtual DOM
const handleImageError = () => {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
};
```

**Fix:** Use React state for conditional rendering

---

### 18. Direct DOM Manipulation in App Entry
**File:** `resources/js/app.jsx:12,16-27`

```jsx
document.body.classList.add('loaded');
document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('a[href^="/"]');
    links.forEach(link => {
        link.addEventListener('click', (e) => {
            document.body.classList.add('page-exit');
        });
    });
});
```

**Fix:** Use Inertia.js event handlers or React context

---

### 19. No useMemo/useCallback Usage
**Impact:** All form onChange handlers recreated on every render
**Fix:** Add useCallback hooks to event handlers

---

### 20. Large Component File
**File:** `resources/js/Pages/Welcome.jsx` - 361 lines

**Fix:** Split into:
- `WelcomeHeader.jsx`
- `WelcomeCard.jsx`
- `WelcomeMain.jsx`

---

## Missing Database Indexes

Based on query patterns, add indexes for:

```php
// Migration suggestions
Schema::table('activity_logs', function (Blueprint $table) {
    $table->index('created_at');
    $table->index(['user_id', 'created_at']);
    $table->index(['status', 'created_at']);
    $table->index(['severity', 'created_at']);
});

Schema::table('courses', function (Blueprint $table) {
    $table->index('moodle_course_id');
    $table->index(['status', 'created_at']);
});

Schema::table('enrollments', function (Blueprint $table) {
    $table->index(['course_id', 'status']);
    $table->index(['user_id', 'status']);
});

Schema::table('users', function (Blueprint $table) {
    $table->index('moodle_user_id');
    $table->index('google_id');
    $table->index('ldap_guid');
});
```

---

## Priority Action Items

### Immediate (Critical)
1. Fix N+1 queries in `CourseController::bulkDelete()`
2. Fix N+1 queries in `UserManagementController::bulkDelete()`
3. Fix N+1 queries in `CourseController::bulkSync()`

### Short-term (High)
4. Queue email sending in `EnrollmentController`
5. Add streaming/chunking for exports in `ActivityLogController`
6. Optimize `MoodleCourseSync::syncAllCourses()`
7. Cache or batch Moodle API calls

### Medium-term (Medium)
8. Consolidate statistics queries
9. Add database indexes
10. Add pagination to unbounded queries
11. Add useCallback to React form handlers

---

## Estimated Performance Impact

| Fix | Queries Reduced | Response Time Impact |
|-----|-----------------|---------------------|
| Bulk delete optimization | 3N → 2 | 90%+ faster |
| Email queueing | N blocking → async | 95%+ faster |
| Statistics consolidation | 5 → 1 | 80% faster |
| Array lookup optimization | O(n²) → O(n) | 50-90% faster |
| Database indexes | varies | 50-90% faster queries |
