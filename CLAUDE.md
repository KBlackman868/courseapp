# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Course management application for the Trinidad & Tobago Ministry of Health (MOH). Built with Laravel 11 (PHP 8.2+) backend, React 18 frontend via Inertia.js, styled with Tailwind CSS + DaisyUI. Integrates with Moodle LMS for course delivery and SSO.

## Common Commands

```bash
# Development (starts server, queue worker, log streaming, and Vite concurrently)
composer dev

# Individual dev services
php artisan serve              # Laravel dev server
npm run dev                    # Vite HMR dev server
php artisan queue:listen --tries=1  # Process background jobs
php artisan pail --timeout=0   # Stream logs

# Build
npm run build                  # Production frontend build

# Testing (Pest)
./vendor/bin/pest                              # All tests
./vendor/bin/pest --filter=TestClassName        # Single test class
./vendor/bin/pest tests/Feature/SomeTest.php   # Single test file

# Linting (Laravel Pint)
./vendor/bin/pint              # Fix PHP code style

# Database
php artisan migrate            # Run migrations
php artisan migrate:fresh --seed  # Reset and seed
php artisan db:seed --class=RolesAndPermissionsSeeder  # Seed roles

# Cache management
php artisan optimize:clear     # Clear all caches
```

## Architecture

### Backend (Laravel)

- **Controllers** are organized by domain: `Http/Controllers/Admin/`, `Http/Controllers/Auth/`, `Http/Controllers/API/V1/`
- **Services** contain business logic: `MoodleService`, `MoodleClient`, `MoodleCourseSync`, `OtpService`, `LdapService`, `ActivityLogger`, `EmailNotificationService`
- **Jobs** handle async Moodle operations: `CreateMoodleUserWithPassword`, `EnrollUserIntoMoodleCourse`, `DeleteMoodleUser`, `CreateOrLinkMoodleUser`
- **Policies** control authorization: `CoursePolicy`, `UserPolicy`, `AccountRequestPolicy`, `CourseAccessRequestPolicy`
- **Middleware** chain: `Authenticate` → `CheckAccountStatus` → `CheckSuspended` → `EnsureOtpVerified` → `LogActivity`

### Frontend (React + Inertia)

- Pages in `resources/js/Pages/` mirror backend routes
- Reusable components in `resources/js/Components/`
- Layouts: auth, admin, learner screens in `resources/js/Layouts/`
- Forms use Inertia's `useForm()` hook; navigation uses Inertia `Link`

### Rendering

Inertia.js bridges Laravel and React — controllers return `Inertia::render('PageName', $props)` and React page components receive props directly.

## Key Domain Concepts

### Roles (5 fixed, via spatie/laravel-permission)
`SuperAdmin`, `Admin`, `Course_Admin`, `MOH_Staff`, `External_User`

- `is_course_admin` is a special permission flag granted by SuperAdmin to Admin users
- Authorization gates: `manage-courses`, `approve-accounts`, `approve-course-access`, `view-pending-approvals`, `assign-course-admin` (defined in `AppServiceProvider`)

### User Types
- **Internal**: MOH staff with `@health.gov.tt` email → auto-assigned `MOH_Staff` role
- **External**: Register via `/register/external` → assigned `External_User` role
- Account statuses: `pending`, `active`, `inactive`

### Course Enrollment Flow
- **audience_type**: `MOH_ONLY`, `EXTERNAL_ONLY`, `BOTH` — controls course visibility
- **enrollment_type**: `OPEN_ENROLLMENT` (immediate) or `APPROVAL_REQUIRED` (needs admin approval)
- MOH staff enroll directly via `Enrollment` model
- External users request access via `CourseAccessRequest` model (pending → approved/denied)

### Database Gotcha
The `Enrollment` model maps to the `registrations` database table (`protected $table = 'registrations'`).

## Moodle Integration

Configured in `config/moodle.php` with env vars `MOODLE_BASE_URL`, `MOODLE_TOKEN`, etc. `MoodleClient` handles API calls with retry logic. SSO uses `auth_userkey` plugin. Webhooks at `/api/v1/moodle/*` sync events bidirectionally.

### Moodle Instance
- **URL**: `https://learnabouthealth.hin.gov.tt` (Moodle 4.5.4+)
- **mohlearn URL**: `https://mohlearn.hin.gov.tt`
- **Moodle install path**: `C:\inetpub\wwwroot\` (same server, no subfolder)
- **Moodle data path**: `C:\inetpub\moodledata`

### SSO Architecture (Desktop + Mobile App)

All access to Moodle is forced through mohlearn — no direct login on learnabouthealth.

**Moodle-side config** (`config.php`):
- `$CFG->alternateloginurl` → `https://mohlearn.hin.gov.tt/moodle/sso/login` (redirects login page to mohlearn)
- `$CFG->logouturl` → `https://mohlearn.hin.gov.tt/logout` (logs out of both systems)
- `$CFG->forcelogin = true` (no guest access)
- Moodle `index.php` has a custom redirect for unauthenticated users to mohlearn (after `require_once('config.php')`)
- Mobile authentication set to **"Via a browser window"** in Moodle admin

**mohlearn-side routes**:
- `/moodle/sso/login` — Entry point for Moodle's `alternateloginurl`. Handles both desktop and mobile app SSO. Controller: `MoodleSSOController`. No auth middleware — redirects to login if unauthenticated, then back to SSO flow after auth.
- `/moodle/sso` — Legacy route for in-app "Go to Moodle" nav link (requires auth).
- `/courses/{id}/access-moodle` — Course-specific SSO with enrollment verification (requires auth).

**Desktop flow**: learnabouthealth → redirected to mohlearn login → authenticate → SSO back to Moodle
**Mobile app flow**: Moodle app → opens browser → mohlearn login → authenticate → SSO → app receives token
**In-app flow**: mohlearn dashboard → "Access Course" → enrollment check → SSO into specific course

All Moodle users must have `auth=userkey`. The `CreateOrLinkMoodleUser` job enforces this automatically. Keep 1-2 admin accounts as `auth=manual` for emergency Moodle access via `learnabouthealth.hin.gov.tt/login/index.php?noredirect=1`.

## Environment

Default database is SQLite. Session, cache, and queue all use database driver. See `.env.example` for full config. Docker deployment config in `docker-compose.yml` with MySQL, Redis, Nginx.
`