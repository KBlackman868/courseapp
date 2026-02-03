# Laravel to Moodle SSO Integration Guide

## Using the auth_userkey Plugin

**Document Version:** 1.0
**Date:** February 2026
**Author:** Ministry of Health IT Department

---

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Moodle Configuration](#moodle-configuration)
4. [Laravel Configuration](#laravel-configuration)
5. [Testing the Integration](#testing-the-integration)
6. [Troubleshooting](#troubleshooting)
7. [Security Considerations](#security-considerations)

---

## Overview

This guide details how to implement Single Sign-On (SSO) between a Laravel application and Moodle LMS using the `auth_userkey` plugin. This allows users to click a link in the Laravel application and be automatically logged into Moodle without entering credentials.

### How It Works

1. User clicks "Open Moodle" in the Laravel application
2. Laravel calls Moodle's Web Service API to request a one-time login URL
3. Moodle generates a unique key and returns a login URL
4. User is redirected to Moodle with the key
5. Moodle validates the key and logs the user in automatically
6. User is redirected to the requested course/page

---

## Prerequisites

### Moodle Requirements
- Moodle 3.9 or higher
- Administrator access to Moodle
- Web Services enabled
- `auth_userkey` plugin installed

### Laravel Requirements
- Laravel 9.x or higher
- HTTP client (Guzzle or Laravel HTTP facade)
- User accounts with email addresses matching Moodle accounts

### Network Requirements
- Laravel server must be able to reach Moodle's Web Service endpoint
- Both servers should use HTTPS in production

---

## Moodle Configuration

### Step 1: Install the auth_userkey Plugin

1. Download the plugin from: https://moodle.org/plugins/auth_userkey
2. Extract to: `moodle/auth/userkey/`
3. Log into Moodle as administrator
4. Go to **Site Administration → Notifications**
5. Follow the prompts to complete installation

### Step 2: Enable the Plugin

1. Go to **Site Administration → Plugins → Authentication → Manage authentication**
2. Find "User key authentication" and click the **eye icon** to enable it
3. Click on **Settings** for User key authentication
4. Configure the following settings:

| Setting | Value | Description |
|---------|-------|-------------|
| Mapping field | `Username` or `Email` | Field used to identify users (must match what Laravel sends) |
| Key lifetime | `60` | Seconds the login key remains valid |
| IP restriction | Disabled | Whether to restrict login to the requesting IP |
| Logout redirect URL | `https://your-laravel-app.com/logout` | Where to redirect after Moodle logout |
| Create user | No | Whether to auto-create users (set Yes if needed) |

5. Click **Save changes**

### Step 3: Enable Web Services

1. Go to **Site Administration → Advanced features**
2. Enable **Web services** checkbox
3. Click **Save changes**

### Step 4: Enable REST Protocol

1. Go to **Site Administration → Plugins → Web services → Manage protocols**
2. Enable the **REST protocol** (click the eye icon)

### Step 5: Create a Web Service

1. Go to **Site Administration → Plugins → Web services → External services**
2. Click **Add** under "Custom services"
3. Fill in:
   - **Name:** Laravel SSO Service
   - **Short name:** laravel_sso
   - **Enabled:** Yes
   - **Authorised users only:** Yes
4. Click **Add service**

### Step 6: Add Functions to the Service

1. Click on **Functions** for the newly created service
2. Click **Add functions**
3. Search for and add these functions:
   - `auth_userkey_request_login_url` (for SSO login)
   - `core_user_get_users` (optional, for user lookup)
   - `enrol_manual_enrol_users` (if enrolling users via API)
4. Click **Add functions**

### Step 7: Create a Service User

1. Go to **Site Administration → Users → Add a new user**
2. Create a user with:
   - **Username:** `webservice_user`
   - **Password:** (set a strong password)
   - **Email:** `webservice@yourdomain.com`
3. Assign the user the **Manager** role at system level:
   - Go to **Site Administration → Users → Permissions → Assign system roles**
   - Select **Manager** and add the webservice user

### Step 8: Authorize the Service User

1. Go to **Site Administration → Plugins → Web services → External services**
2. Click on **Authorised users** for your service
3. Add the `webservice_user`

### Step 9: Create a Token

1. Go to **Site Administration → Plugins → Web services → Manage tokens**
2. Click **Create token**
3. Select the `webservice_user`
4. Select your service (Laravel SSO Service)
5. Click **Save changes**
6. **IMPORTANT:** Copy and securely store the generated token

### Step 10: Configure User Authentication Method

For each user who will use SSO:

1. Go to **Site Administration → Users → Browse list of users**
2. Find the user and click **Edit**
3. Change **Choose an authentication method** to **User key authentication**
4. Click **Update profile**

**Note:** Users with "User key authentication" can ONLY log in via SSO. Keep at least one admin account with "Manual accounts" authentication for backup access.

---

## Laravel Configuration

### Step 1: Environment Configuration

Add to your `.env` file:

```env
MOODLE_BASE_URL=https://your-moodle-site.com
MOODLE_TOKEN=your_webservice_token_here
MOODLE_VERIFY_SSL=true
MOODLE_SSO_ENABLED=true
```

### Step 2: Create Configuration File

Create `config/moodle.php`:

```php
<?php

return [
    'base_url' => env('MOODLE_BASE_URL'),
    'token' => env('MOODLE_TOKEN'),
    'verify_ssl' => env('MOODLE_VERIFY_SSL', true),
    'sso_enabled' => env('MOODLE_SSO_ENABLED', true),
    'sso_logout_redirect' => env('APP_URL') . '/logout',
];
```

### Step 3: Create MoodleService Class

Create `app/Services/MoodleService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('moodle.base_url'), '/');
        $this->token = config('moodle.token');
    }

    /**
     * Make a call to Moodle Web Service API
     */
    public function call(string $function, array $params = [])
    {
        $url = $this->baseUrl . '/webservice/rest/server.php';

        $requestParams = array_merge($params, [
            'wstoken' => $this->token,
            'wsfunction' => $function,
            'moodlewsrestformat' => 'json'
        ]);

        $client = Http::asForm();

        if (!config('moodle.verify_ssl')) {
            $client = $client->withoutVerifying();
        }

        $response = $client->post($url, $requestParams);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['exception'])) {
                throw new \Exception("Moodle error: " . ($data['message'] ?? 'Unknown'));
            }

            return $data;
        }

        throw new \Exception("HTTP request failed: " . $response->status());
    }

    /**
     * Generate SSO login URL for a user
     */
    public function generateLoginUrl(array $userData, ?string $redirectUrl = null): ?string
    {
        try {
            $params = ['user' => $userData];
            $response = $this->call('auth_userkey_request_login_url', $params);

            if (isset($response['loginurl'])) {
                $loginUrl = $response['loginurl'];

                if ($redirectUrl) {
                    $separator = (strpos($loginUrl, '?') !== false) ? '&' : '?';
                    $loginUrl .= $separator . 'wantsurl=' . urlencode($redirectUrl);
                }

                return $loginUrl;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('SSO generation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate SSO URL for accessing a specific course
     */
    public function generateCourseLoginUrl($user, int $moodleCourseId): string
    {
        $courseUrl = $this->baseUrl . '/course/view.php?id=' . $moodleCourseId;

        // Build user data - username is typically email prefix
        $username = $user->username ?? explode('@', $user->email)[0];

        $userData = [
            'username' => $username,
            'email' => $user->email,
            'firstname' => $user->first_name ?? 'User',
            'lastname' => $user->last_name ?? 'User',
        ];

        $loginUrl = $this->generateLoginUrl($userData, $courseUrl);

        return $loginUrl ?? $courseUrl;
    }
}
```

### Step 4: Create Controller Method

In your CourseController or relevant controller:

```php
public function accessMoodle($courseId)
{
    $course = Course::findOrFail($courseId);
    $user = auth()->user();

    // Verify enrollment
    $enrollment = Enrollment::where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->where('status', 'approved')
        ->first();

    if (!$enrollment) {
        return redirect()->back()->with('error', 'You must be enrolled to access this course.');
    }

    if (!$course->moodle_course_id) {
        return redirect()->back()->with('error', 'This course is not available in Moodle.');
    }

    $moodleService = app(MoodleService::class);
    $moodleUrl = $moodleService->generateCourseLoginUrl($user, $course->moodle_course_id);

    return redirect()->away($moodleUrl);
}
```

### Step 5: Create Route

In `routes/web.php`:

```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/courses/{course}/access-moodle', [CourseController::class, 'accessMoodle'])
        ->name('courses.access-moodle');
});
```

### Step 6: Create Button in View

In your course view blade file:

```blade
@if($enrollment && $enrollment->status === 'approved' && $course->moodle_course_id)
    <a href="{{ route('courses.access-moodle', $course) }}"
       class="btn btn-primary"
       target="_blank">
        Open in Moodle
    </a>
@endif
```

---

## Testing the Integration

### Test 1: API Connection

Create a test route to verify the connection:

```php
Route::get('/test-moodle-connection', function () {
    $moodleService = app(MoodleService::class);

    try {
        $response = $moodleService->call('core_webservice_get_site_info');
        return response()->json([
            'status' => 'success',
            'site_name' => $response['sitename'] ?? 'Unknown'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
```

### Test 2: SSO Login

1. Log into your Laravel application
2. Navigate to a course page
3. Click "Open in Moodle"
4. You should be automatically logged into Moodle

### Test 3: Verify User Matching

If SSO fails, check:
- User exists in Moodle with matching username/email
- User's authentication method is set to "User key authentication"
- The mapping field in auth_userkey settings matches what you're sending

---

## Troubleshooting

### Error: "HTTP request failed with status: 500"

**Causes:**
- User not found in Moodle
- User's auth method not set to "userkey"
- Mapping field mismatch (sending email but configured for username)

**Solution:**
1. Verify user exists in Moodle
2. Check user's authentication method
3. Ensure mapping field in Moodle matches your Laravel code

### Error: "Invalid token"

**Causes:**
- Token expired or invalid
- Token not authorized for the service

**Solution:**
1. Generate a new token in Moodle
2. Verify token is assigned to the correct service
3. Update `.env` with new token

### Error: "Access control exception"

**Causes:**
- Web service user lacks permissions
- Function not added to service

**Solution:**
1. Assign Manager role to webservice user
2. Verify function is added to the external service

### User lands on Moodle login page instead of being logged in

**Causes:**
- Key expired (default 60 seconds)
- User's auth method is not "userkey"

**Solution:**
1. Increase key lifetime in auth_userkey settings
2. Change user's authentication method

---

## Security Considerations

### Token Security
- Never commit tokens to version control
- Store tokens in environment variables
- Rotate tokens periodically

### HTTPS
- Always use HTTPS in production
- Enable SSL verification in Laravel

### User Validation
- Always verify user enrollment before generating SSO URL
- Log all SSO access attempts

### Moodle Hardening
- Restrict webservice user permissions to minimum needed
- Enable IP restrictions if possible
- Monitor webservice logs

---

## Appendix: Quick Reference

### Moodle Web Service Functions

| Function | Purpose |
|----------|---------|
| `auth_userkey_request_login_url` | Generate SSO login URL |
| `core_user_get_users` | Look up users |
| `core_webservice_get_site_info` | Test connection |
| `enrol_manual_enrol_users` | Enroll users in courses |

### Required Moodle Settings

| Setting | Location |
|---------|----------|
| Enable Web services | Site Admin → Advanced features |
| Enable REST protocol | Site Admin → Plugins → Web services → Manage protocols |
| auth_userkey settings | Site Admin → Plugins → Authentication → User key |
| External service | Site Admin → Plugins → Web services → External services |

### Laravel Environment Variables

```env
MOODLE_BASE_URL=https://moodle.example.com
MOODLE_TOKEN=your_token_here
MOODLE_VERIFY_SSL=true
MOODLE_SSO_ENABLED=true
```

---

**End of Document**
