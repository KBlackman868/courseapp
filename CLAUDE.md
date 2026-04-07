# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with this repository.

## Project Overview

CourseApp is a Laravel 11 + React/Inertia application for the Trinidad and Tobago Ministry of Health (MOH). It serves as a course management portal that integrates with a Moodle LMS instance via SSO (auth_userkey plugin), handling enrollment workflows, account requests, and role-based access for both internal MOH staff and external users.

## Tech Stack

- **Backend:** Laravel 11.31, PHP 8.2
- **Frontend:** Inertia.js v2, React 18, Tailwind CSS, DaisyUI, Vite
- **Auth/RBAC:** Laravel Breeze, Spatie Laravel Permission, Laravel Socialite (Google), SAML2 (aacotroneo + codegreencreative IdP)
- **Database:** SQL Server (sqlsrv) — note CHECK constraints on enum-like columns
- **Testing:** Pest 3 + Pest Laravel plugin
- **Other:** Laravel Telescope, Bugsnag, Ziggy (named routes in JS), PhpSpreadsheet

## Common Commands

```bash
# Dev (runs server, queue, logs, vite concurrently)
composer dev

# Frontend
npm run dev          # Vite dev server
npm run build        # Production build

# Backend
php artisan serve
php artisan migrate
php artisan migrate:fresh --seed
php artisan queue:listen --tries=1
php artisan tinker

# Tests (Pest)
php artisan test
./vendor/bin/pest
./vendor/bin/pest --filter=SomeTest

# Seeders (run individually)
php artisan db:seed --class=AdminUsersSeeder
php artisan db:seed --class=MohTestAccountSeeder

# Code style
./vendor/bin/pint
```

## Architecture

### Roles (Spatie Permission, guard: web)
- `superadmin` — full access
- `admin` — administrative access
- `course_admin` — manages courses, approves enrollment requests
- `instructor`
- `moh_staff` — internal MOH user (email under `health.gov.tt`)
- `external_user` — external user (any other email)
- `user` — generic user

Roles are seeded by `database/seeders/AdminUsersSeeder.php`. A test MOH staff account can be created via `database/seeders/MohTestAccountSeeder.php` (`mohtestaccount@health.gov.tt` / `Ilovemoh2026!`).

### User Types
`users.user_type` is `'internal'` (MOH staff) or `'external'`. MOH detection is by `@health.gov.tt` email domain in the registration controllers.

### Account Request Workflow
External users go through `account_requests` with statuses: `pending_verification` → `email_verified` → `approved` (or `pending` for legacy). Admin/superadmin/course_admin can approve from `Admin/AccountRequests/Index.jsx` and `Show.jsx`. The "Pending" tab combines all three pre-approval states (`AccountRequestController@index`).

### Moodle SSO
`App\Services\MoodleService` calls Moodle's `auth_userkey_request_login_url` to mint a one-time login URL. The flow:

1. `CourseController@goToMoodle` (or similar) calls `buildMoodleSSOUrl()` → `MoodleService::generateCourseLoginUrl()`.
2. On failure, the service throws (no silent guest fallback). The `/moodle/sso` route in `routes/web.php` redirects with a flash error rather than guest-redirecting.
3. `determineAccessLevel()` in `CourseController` does NOT require `$user->moodle_user_id` for `can_access_moodle` — Moodle accounts are created lazily on first SSO.

If Moodle returns 500 from `auth_userkey_request_login_url`, the issue is on the Moodle side (auth_userkey plugin / `createorlinkmoodleuser` setting / `mapping field` mismatch), not in this codebase.

### Course Enrollment Model
- `courses.audience_type` — DB CHECK constraint allows ONLY lowercase `'moh'`, `'external'`, `'all'`. Any normalization in controllers MUST map to these values. Frontend forms in `Admin/Courses/Show.jsx` use these lowercase values directly.
- `courses.enrollment_type` — `'OPEN_ENROLLMENT'` or `'APPROVAL_REQUIRED'` (see migration `2026_01_30_000005_update_courses_enrollment_settings.php`). `OPEN_ENROLLMENT` enrolls immediately; `APPROVAL_REQUIRED` creates a `course_access_requests` row for course admins to approve.
- Deleting a course: must delete related `course_access_requests` first (FK has `onDelete('no action')`). See `CourseController@destroy`.

### Password Requirements
All registration / password change / reset forms use the shared `resources/js/Components/PasswordChecklist.jsx` for live requirement feedback (length ≥ 12, upper, lower, digit, symbol). Placeholder text: `"Min. 12 characters"`. Blade-rendered auth pages (`resources/views/pages/home_register.blade.php`, `auth/register-external.blade.php`, `auth/moh-request-account.blade.php`, `pages/reset_password.blade.php`) implement the same checklist in vanilla JS.

## Key Directories

- `app/Http/Controllers/` — `CourseController`, `Auth/*`, `Admin/AccountRequestController`
- `app/Services/MoodleService.php` — Moodle API client / SSO URL generation
- `app/Models/` — `User`, `Course`, `CourseAccessRequest`, `AccountRequest`
- `database/migrations/` — note SQL Server CHECK constraints on enum columns
- `database/seeders/` — `AdminUsersSeeder`, `MohTestAccountSeeder`
- `resources/js/Pages/` — Inertia pages (Admin, Auth, Courses, etc.)
- `resources/js/Components/PasswordChecklist.jsx` — shared password UX
- `resources/views/pages/` and `resources/views/auth/` — Blade-rendered public pages
- `routes/web.php` — main routes including `/moodle/sso`
- `docs/` — additional project documentation

## SQL Server Gotchas

- Booleans in raw SQL use `1`/`0`, not `true`/`false`.
- CHECK constraints on string enum columns will reject any value not in the original set — verify allowed values before normalizing in PHP. The `audience_type` constraint (`CK__courses__audienc__*`) only permits `'moh'`, `'external'`, `'all'`.
- FK constraints with `onDelete('no action')` require manual cleanup of dependents before parent deletion.
