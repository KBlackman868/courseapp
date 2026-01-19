# CourseApp Technical Documentation

> A beginner-friendly guide to understanding how this Learning Management System works

---

## Table of Contents

1. [What Is This Project?](#what-is-this-project)
2. [How the Pieces Fit Together](#how-the-pieces-fit-together)
3. [The Technology Stack (What Tools We Use)](#the-technology-stack)
4. [Project Folder Structure](#project-folder-structure)
5. [The Backend (Server-Side)](#the-backend)
6. [The Frontend (What Users See)](#the-frontend)
7. [The Database (Where Data Lives)](#the-database)
8. [Authentication (How Users Log In)](#authentication)
9. [How Features Work](#how-features-work)
10. [External Integrations](#external-integrations)
11. [Configuration Files](#configuration-files)
12. [Common Tasks](#common-tasks)

---

## What Is This Project?

CourseApp is a **Learning Management System (LMS)** built for the Trinidad and Tobago Ministry of Health. Think of it like a school portal where:

- **Users** can browse available courses and request to enroll
- **Admins** can manage courses, approve enrollments, and manage users
- **The system** connects to **Moodle** (another LMS platform) to sync courses and users

### Why Was It Built This Way?

The Ministry needed a custom portal that:
1. Works with their existing Moodle system (they didn't want to replace it)
2. Integrates with their employee directory (LDAP/Active Directory)
3. Allows external users (not employees) to also take courses
4. Has proper approval workflows for course enrollments

---

## How the Pieces Fit Together

Here's a simple diagram of how everything connects:

```
┌─────────────────────────────────────────────────────────┐
│                   USER'S WEB BROWSER                     │
│         (Where people interact with the app)             │
└─────────────────────────────────────────────────────────┘
                            ↓
                     Internet Request
                            ↓
┌─────────────────────────────────────────────────────────┐
│                    LARAVEL BACKEND                       │
│                  (The "brain" of the app)                │
│                                                          │
│  • Receives requests from the browser                    │
│  • Checks if the user is allowed to do things            │
│  • Gets/saves data from the database                     │
│  • Talks to external systems (Moodle, LDAP, Google)      │
│  • Sends back web pages or data                          │
└─────────────────────────────────────────────────────────┘
           ↓                    ↓                    ↓
    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
    │   DATABASE   │    │    MOODLE    │    │    LDAP      │
    │   (SQLite)   │    │    (LMS)     │    │  (Employee   │
    │              │    │              │    │   Directory) │
    │ Stores:      │    │ Stores:      │    │ Stores:      │
    │ • Users      │    │ • Courses    │    │ • Employee   │
    │ • Courses    │    │ • Grades     │    │   accounts   │
    │ • Enroll-    │    │ • Content    │    │ • Passwords  │
    │   ments      │    │              │    │              │
    └──────────────┘    └──────────────┘    └──────────────┘
```

### Why This Architecture?

**Separation of Concerns**: Each part has one job. The database stores data, Moodle handles actual course content, and our app handles the enrollment workflow. This makes the system easier to maintain and update.

---

## The Technology Stack

Here's what tools and technologies the project uses:

### Backend (Server-Side)

| Technology | What It Does | Why We Use It |
|------------|--------------|---------------|
| **PHP 8.2** | The programming language | It's what Laravel requires and is very common for web development |
| **Laravel 11** | The web framework | It provides structure and tools so we don't have to write everything from scratch |
| **SQLite/MySQL** | The database | SQLite is simple for development; MySQL is used in production |

### Frontend (What Users See)

| Technology | What It Does | Why We Use It |
|------------|--------------|---------------|
| **React 18** | JavaScript library for building user interfaces | Makes the app feel fast and responsive |
| **Inertia.js** | Connects React to Laravel | Lets us build single-page apps without writing a separate API |
| **Tailwind CSS** | Styling framework | Makes it easy to design consistent, good-looking pages |
| **DaisyUI** | Pre-made components for Tailwind | Provides buttons, forms, modals, etc. so we don't design from scratch |

### Build Tools

| Technology | What It Does | Why We Use It |
|------------|--------------|---------------|
| **Vite** | Bundles and compiles our JavaScript/CSS | It's fast and makes development smooth |
| **Composer** | Manages PHP packages | Automatically downloads and updates PHP libraries |
| **NPM** | Manages JavaScript packages | Automatically downloads and updates JavaScript libraries |

---

## Project Folder Structure

Here's what each folder in the project does:

```
courseapp/
│
├── app/                    # THE BACKEND CODE LIVES HERE
│   ├── Http/
│   │   ├── Controllers/    # Handle web requests (like clicking a button)
│   │   ├── Middleware/     # Check things before requests are processed
│   │   └── Requests/       # Validate form data
│   ├── Models/             # Define what our data looks like
│   ├── Services/           # Complex business logic
│   ├── Jobs/               # Background tasks (like sending emails)
│   └── Mail/               # Email templates
│
├── config/                 # SETTINGS FILES
│   ├── app.php             # General app settings
│   ├── database.php        # Database connection settings
│   ├── moodle.php          # Moodle integration settings
│   └── ldap.php            # Employee directory settings
│
├── database/
│   ├── migrations/         # Instructions for creating database tables
│   └── seeders/            # Sample data for testing
│
├── resources/              # FRONTEND CODE LIVES HERE
│   ├── js/
│   │   ├── Pages/          # React page components
│   │   ├── Components/     # Reusable UI pieces
│   │   └── Layouts/        # Page templates
│   └── views/              # Some pages still use Blade templates
│
├── routes/
│   ├── web.php             # Defines all the URLs in the app
│   └── api.php             # URLs for API access
│
├── public/                 # Files accessible to the internet
│   └── index.php           # The entry point for all requests
│
└── storage/                # Uploaded files, logs, cached data
```

### Why This Structure?

Laravel uses the **MVC pattern** (Model-View-Controller):
- **Models** (`app/Models/`) = Your data
- **Views** (`resources/`) = What users see
- **Controllers** (`app/Http/Controllers/`) = The logic that connects them

This separation makes code organized and easier to work with as the project grows.

---

## The Backend

### Controllers - Handling User Actions

Controllers are like **waiters in a restaurant**. When a customer (user) makes a request (clicks a button), the waiter (controller) takes the order, gets what's needed from the kitchen (database/services), and brings back the result.

#### Main Controllers

**`CourseController.php`** - Manages courses
```
What it does:
├── index()   → Shows the list of all courses
├── show()    → Shows one specific course
├── store()   → Creates a new course
├── update()  → Edits an existing course
└── delete()  → Removes a course
```

**`EnrollmentController.php`** - Manages who's enrolled in what
```
What it does:
├── store()    → When a user requests to enroll
├── approve()  → When an admin approves an enrollment
├── reject()   → When an admin rejects an enrollment
└── index()    → Shows all enrollment requests
```

**`UserManagementController.php`** - Admin user management
```
What it does:
├── index()    → Shows list of all users
├── update()   → Edit a user
├── suspend()  → Disable a user's account
└── delete()   → Remove a user
```

### Why Controllers Exist

Without controllers, you'd have messy code everywhere. Controllers give you **one place** to handle each type of action, making bugs easier to find and fix.

---

### Models - Your Data Structures

Models represent the **things** in your application. Think of them as blueprints for your data.

#### The User Model

Located at: `app/Models/User.php`

```php
// A User has these properties:
- id                  // Unique identifier
- first_name          // Their first name
- last_name           // Their last name
- email               // Their email address
- password            // Their encrypted password
- user_type           // "internal" (employee) or "external" (public)
- moodle_user_id      // Their ID in the Moodle system
- google_id           // If they logged in with Google
- is_suspended        // Whether they're blocked
```

**Why track `user_type`?** Internal users (Ministry employees) get auto-approved for courses. External users need admin approval. This field lets us handle them differently.

**Why track `moodle_user_id`?** When we enroll someone in a course, we need to tell Moodle which user to enroll. This links our user to their Moodle account.

#### The Course Model

Located at: `app/Models/Course.php`

```php
// A Course has these properties:
- id                    // Unique identifier
- title                 // Course name
- description           // What the course is about
- status                // "draft", "published", etc.
- moodle_course_id      // Links to the actual course in Moodle
- category_id           // Which category it belongs to
- creator_id            // Who created it
```

**Why link to Moodle?** Our app is just the "front door" - the actual course content lives in Moodle. We need this link to redirect users to the right place.

#### The Enrollment Model

Located at: `app/Models/Enrollment.php`

```php
// An Enrollment connects a User to a Course:
- id           // Unique identifier
- user_id      // Which user
- course_id    // Which course
- status       // "pending", "approved", or "denied"
```

**Why have statuses?** Not everyone should be auto-enrolled. The status lets admins review and approve requests before people get access.

---

### Services - Complex Business Logic

Services contain code that's too complex for controllers. They're like **specialized workers** that handle specific tasks.

#### MoodleService

Located at: `app/Services/MoodleService.php`

**What it does:** Talks to the Moodle system via its API

```
Key functions:
├── createUser()     → Creates a user account in Moodle
├── enrollUser()     → Enrolls a user in a Moodle course
├── getCourses()     → Gets list of courses from Moodle
└── syncCourse()     → Updates our database with Moodle course info
```

**Why it's separate:** Moodle communication is complex. By putting it in a service, controllers stay clean and Moodle logic is in one place.

#### LdapService

Located at: `app/Services/LdapService.php`

**What it does:** Checks employee credentials against the Ministry's directory

```
Key functions:
├── authenticate()   → Checks if username/password is correct
├── getUser()        → Gets employee information
└── isInternal()     → Checks if email is from Ministry domain
```

**Why use LDAP?** Ministry employees shouldn't need a separate password. LDAP lets them log in with their existing work credentials.

#### OtpService

Located at: `app/Services/OtpService.php`

**What it does:** Manages one-time passwords for verification

```
Key functions:
├── generateOtp()    → Creates a 6-digit code
├── verifyOtp()      → Checks if the code is correct
└── resendOtp()      → Sends a new code if needed
```

**Why OTP?** It's an extra security layer. Even if someone's password is stolen, they'd also need access to the email to get the OTP code.

---

### Middleware - Security Checkpoints

Middleware runs **before** your controller code. Think of them as **security guards** checking badges before letting someone through.

```
User Request
     ↓
┌─────────────────────────────────┐
│ Middleware 1: Is user logged in?│ → No? → Redirect to login
└─────────────────────────────────┘
     ↓ Yes
┌─────────────────────────────────┐
│ Middleware 2: Is email verified?│ → No? → Show verification page
└─────────────────────────────────┘
     ↓ Yes
┌─────────────────────────────────┐
│ Middleware 3: Is user an admin? │ → No? → Show "Access Denied"
└─────────────────────────────────┘
     ↓ Yes
┌─────────────────────────────────┐
│      Your Controller Code       │
└─────────────────────────────────┘
```

#### Key Middleware Files

| File | What It Checks |
|------|----------------|
| `Authenticate.php` | Is the user logged in? |
| `EnsureEmailVerified.php` | Has the user verified their email? |
| `AdminMiddleware.php` | Is the user an admin? |
| `CheckSuspended.php` | Is the user's account suspended? |
| `CheckRole.php` | Does the user have a specific role? |
| `LogActivity.php` | Records what the user did (for auditing) |

**Why middleware?** Without it, you'd have to write the same security checks in every controller. Middleware lets you write it once and apply it to many routes.

---

### Jobs - Background Tasks

Jobs are tasks that run **in the background** so users don't have to wait.

**Example:** When a user enrolls in a course, we need to:
1. Save the enrollment to our database (fast)
2. Create a Moodle account for them (slow - needs to call external API)
3. Enroll them in Moodle (slow - another API call)
4. Send them an email (slow - needs to connect to mail server)

**Without jobs:** User clicks "Enroll" → Waits 10+ seconds → Finally sees success page

**With jobs:** User clicks "Enroll" → Instantly sees success page → Background worker handles slow stuff

#### Key Job Files

| File | What It Does |
|------|--------------|
| `CreateMoodleUserWithPassword.php` | Creates a Moodle account and emails credentials |
| `EnrollUserIntoMoodleCourse.php` | Enrolls user in the Moodle course |
| `DeleteMoodleUser.php` | Removes a user from Moodle |

---

## The Frontend

### React Pages

React pages are located in `resources/js/Pages/`. Each file is a full page in the app.

```
resources/js/Pages/
├── Dashboard.jsx        # User's home page after login
├── Profile/
│   └── Edit.jsx         # Edit your profile
├── Auth/
│   ├── Login.jsx        # Login form
│   ├── Register.jsx     # Registration form
│   └── VerifyEmail.jsx  # Email verification page
└── Welcome.jsx          # Public landing page
```

### How Inertia.js Works

Inertia.js is the **glue** between Laravel and React. Here's how it works:

```
1. User clicks a link to "/courses"

2. Laravel receives the request:
   - Runs CourseController@index
   - Gets courses from database
   - Returns: Inertia::render('Courses/Index', ['courses' => $courses])

3. Inertia.js:
   - Doesn't reload the whole page
   - Just swaps the React component
   - Passes the 'courses' data as props

4. React component receives data:
   function Index({ courses }) {
     return courses.map(course => <CourseCard course={course} />)
   }
```

**Why Inertia?** It gives you the **feel** of a single-page app (fast, no full page reloads) without having to build a separate API.

### Components

Reusable UI pieces live in `resources/js/Components/`:

```
Components/
├── Button.jsx           # Styled buttons
├── Input.jsx            # Form input fields
├── Modal.jsx            # Popup dialogs
├── Dropdown.jsx         # Dropdown menus
└── ApplicationLogo.jsx  # The app logo
```

**Why components?** If you have a button style, you write it once in `Button.jsx` and reuse it everywhere. Change it once, it updates everywhere.

### Layouts

Layouts wrap your pages with common elements (navigation, footer, etc.):

```
Layouts/
├── AuthenticatedLayout.jsx   # For logged-in users (has navbar)
└── GuestLayout.jsx           # For visitors (minimal)
```

---

## The Database

### How Migrations Work

Migrations are **instructions** for creating database tables. They're like recipes that can be run in order.

**Why migrations?** Instead of manually creating tables, you write code. This means:
- Everyone on the team gets the same database structure
- Changes are tracked in version control
- You can set up a new database with one command

#### Example Migration

File: `database/migrations/2024_01_01_000000_create_users_table.php`

```php
// This tells Laravel: "Create a table called 'users' with these columns"
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();                    // Auto-incrementing ID
        $table->string('first_name');    // Text field
        $table->string('email')->unique(); // Must be unique
        $table->timestamp('created_at'); // When created
    });
}
```

### Database Tables Overview

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `users` | Stores user accounts | email, password, user_type, moodle_user_id |
| `courses` | Stores course information | title, description, moodle_course_id |
| `registrations` | Tracks who's enrolled in what | user_id, course_id, status |
| `categories` | Course categories | name, parent_id, moodle_category_id |
| `activity_logs` | Audit trail of all actions | user_id, action, description, ip_address |
| `roles` | User roles (admin, user, etc.) | name, display_name |
| `permissions` | What each role can do | name, description |

### Relationships

```
USER ─────────────┬──────────────────┐
                  │                  │
                  ▼                  ▼
          REGISTRATIONS          ACTIVITY_LOGS
          (enrollments)          (audit trail)
                  │
                  ▼
              COURSES ──────────► CATEGORIES
```

- One User can have many Registrations (they can enroll in multiple courses)
- One Course can have many Registrations (many users can enroll)
- Each Course belongs to one Category
- Every action creates an Activity Log entry

---

## Authentication

The app supports **four ways to log in**:

### 1. Email & Password (Traditional)

```
User enters email/password
        ↓
Laravel checks the database
        ↓
If correct → Log in
If wrong → Show error
```

**Why offer this?** It's the fallback for external users who don't have Ministry accounts or Google.

### 2. Google OAuth

```
User clicks "Sign in with Google"
        ↓
Redirected to Google's login page
        ↓
User logs into Google
        ↓
Google sends back user info
        ↓
App creates/finds account → Log in
```

**Why offer this?** Ministry uses Google Workspace. Employees can log in with one click using their work Google account.

**Domain restriction:** Only `@health.gov.tt` emails can use Google login. This prevents random people from accessing.

### 3. LDAP (Active Directory)

```
User enters work username/password
        ↓
App asks LDAP server: "Is this correct?"
        ↓
LDAP says yes/no
        ↓
If yes → Log in (auto-create account if needed)
```

**Why offer this?** Employees already have work credentials. They shouldn't need to create yet another password.

### 4. OTP Verification (Extra Security)

After logging in, some users must also enter a one-time code:

```
User logs in successfully
        ↓
App generates 6-digit code
        ↓
Code is emailed to user
        ↓
User enters code
        ↓
If correct → Full access granted
```

**Why OTP?** Adds an extra layer of security. Even if someone steals a password, they can't get in without also having access to the email.

---

## How Features Work

### Feature: Enrolling in a Course

Here's what happens step-by-step when someone clicks "Enroll":

```
1. USER CLICKS "ENROLL" BUTTON
   └─► Browser sends POST request to /courses/{id}/enroll

2. MIDDLEWARE CHECKS
   ├─► Is user logged in? ✓
   ├─► Is email verified? ✓
   └─► Is account suspended? ✓ (not suspended)

3. CONTROLLER RECEIVES REQUEST
   └─► EnrollmentController@store runs

4. CONTROLLER LOGIC
   ├─► Check: Already enrolled? → Show error
   ├─► Check: User type?
   │   ├─► Internal (employee) → Auto-approve
   │   └─► External (public) → Set as "pending"
   ├─► Create enrollment record in database
   └─► If approved:
       ├─► Dispatch job: Create Moodle account (if needed)
       ├─► Dispatch job: Enroll in Moodle course
       └─► Dispatch job: Send confirmation email

5. BACKGROUND JOBS RUN
   ├─► MoodleService creates user in Moodle
   ├─► MoodleService enrolls user in course
   └─► Email is sent to user

6. USER SEES SUCCESS MESSAGE
   └─► Redirected back to course page with "Enrolled!" message
```

### Feature: Admin Approving an Enrollment

```
1. ADMIN CLICKS "APPROVE" BUTTON
   └─► Browser sends POST request to /enrollments/{id}/approve

2. MIDDLEWARE CHECKS
   ├─► Is user logged in? ✓
   ├─► Is user an admin? ✓
   └─► etc.

3. CONTROLLER LOGIC
   ├─► Find the enrollment record
   ├─► Change status from "pending" to "approved"
   ├─► Dispatch job: Enroll in Moodle course
   └─► Dispatch job: Send "You're approved!" email

4. BACKGROUND JOBS RUN
   ├─► MoodleService enrolls user
   └─► Email is sent

5. ADMIN SEES SUCCESS MESSAGE
   └─► "Enrollment approved!"
```

### Feature: Syncing Courses from Moodle

```
1. ADMIN CLICKS "SYNC FROM MOODLE"
   └─► POST request to /admin/moodle/sync

2. CONTROLLER CALLS MOODLESERVICE
   └─► MoodleService->getCourses()
       └─► HTTP request to Moodle's API
       └─► Moodle returns list of courses as JSON

3. FOR EACH MOODLE COURSE
   ├─► Does it exist in our database?
   │   ├─► Yes → Update the record
   │   └─► No → Create new record
   └─► Link to correct category

4. ACTIVITY LOG RECORDED
   └─► "Admin synced 45 courses from Moodle"

5. ADMIN SEES RESULTS
   └─► "Synced 45 courses. 3 new, 42 updated."
```

---

## External Integrations

### Moodle Integration

**What is Moodle?** A popular open-source Learning Management System that hosts actual course content.

**How we integrate:**
- Moodle exposes a **Web Service API** (like a remote control)
- We call this API using HTTP requests
- We send: "Create user with this email"
- Moodle responds: "Done, their user ID is 12345"

**Configuration (`config/moodle.php`):**
```php
'base_url' => env('MOODLE_BASE_URL'),  // e.g., https://moodle.health.gov.tt
'token' => env('MOODLE_TOKEN'),         // Secret API key
'verify_ssl' => env('MOODLE_VERIFY_SSL', true),
```

### LDAP Integration

**What is LDAP?** A protocol for accessing employee directories. Think of it as a company phone book that also stores passwords.

**How we integrate:**
- User enters their work credentials
- We ask the LDAP server: "Is this username/password valid?"
- If yes, we also get their info (name, email, department)

**Configuration (`config/ldap.php`):**
```php
'host' => env('LDAP_HOST'),        // LDAP server address
'port' => env('LDAP_PORT', 389),   // Connection port
'base_dn' => env('LDAP_BASE_DN'),  // Where to search for users
```

### Google OAuth Integration

**What is OAuth?** A way to let users log in using another service (Google) without sharing their password with us.

**How it works:**
1. We redirect user to Google
2. User logs into Google directly
3. Google asks: "Allow CourseApp to access your info?"
4. User says yes
5. Google sends us a token
6. We use the token to get user's email and name

**Configuration (`config/services.php`):**
```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => '/auth/google/callback',
],
```

---

## Configuration Files

### The `.env` File

The `.env` file stores **environment-specific settings**. It's not committed to Git because:
- It contains secrets (passwords, API keys)
- Settings differ between development and production

**Key settings:**

```bash
# Application
APP_NAME=CourseApp          # Shown in emails and page titles
APP_ENV=local               # "local" for development, "production" for live
APP_DEBUG=true              # Show detailed errors (false in production!)
APP_URL=http://localhost    # Base URL of the app

# Database
DB_CONNECTION=sqlite        # Database type
DB_DATABASE=/path/to/db     # Where the database file is

# Moodle
MOODLE_BASE_URL=https://...   # Moodle server address
MOODLE_TOKEN=xxxx            # API authentication key

# LDAP
LDAP_ENABLED=true            # Turn LDAP on/off
LDAP_HOST=ldap.example.com   # LDAP server address

# Google OAuth
GOOGLE_CLIENT_ID=xxxx        # From Google Cloud Console
GOOGLE_CLIENT_SECRET=xxxx    # Keep this secret!

# Email
MAIL_MAILER=smtp             # How to send emails
MAIL_HOST=smtp.example.com   # Mail server address
```

### Config Files in `/config/`

| File | What It Controls |
|------|-----------------|
| `app.php` | App name, timezone, language |
| `auth.php` | How authentication works |
| `database.php` | Database connection settings |
| `moodle.php` | Moodle integration settings |
| `ldap.php` | LDAP connection settings |
| `mail.php` | Email sending settings |
| `queue.php` | Background job settings |

---

## Common Tasks

### How to Add a New Page

1. **Create the React component:**
```jsx
// resources/js/Pages/MyNewPage.jsx
export default function MyNewPage({ data }) {
    return <div>Hello, {data}!</div>;
}
```

2. **Create the route:**
```php
// routes/web.php
Route::get('/my-new-page', function () {
    return Inertia::render('MyNewPage', [
        'data' => 'World'
    ]);
});
```

3. **Visit `/my-new-page` in your browser**

### How to Add a Database Column

1. **Create a migration:**
```bash
php artisan make:migration add_phone_to_users_table
```

2. **Edit the migration file:**
```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->nullable();
    });
}
```

3. **Run the migration:**
```bash
php artisan migrate
```

### How to Create a Background Job

1. **Create the job:**
```bash
php artisan make:job SendWelcomeEmail
```

2. **Edit the job file:**
```php
// app/Jobs/SendWelcomeEmail.php
public function handle()
{
    // Your email sending logic here
}
```

3. **Dispatch the job:**
```php
// In your controller
SendWelcomeEmail::dispatch($user);
```

### Useful Commands

```bash
# Start the development server
php artisan serve

# Run database migrations
php artisan migrate

# Create a new controller
php artisan make:controller MyController

# Create a new model
php artisan make:model MyModel

# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test

# Watch for frontend changes
npm run dev

# Build for production
npm run build
```

---

## Security Considerations

### What We Protect Against

| Threat | How We Protect |
|--------|----------------|
| **SQL Injection** | Laravel's query builder escapes all inputs automatically |
| **Cross-Site Scripting (XSS)** | React escapes output by default |
| **Cross-Site Request Forgery (CSRF)** | Laravel includes CSRF tokens in all forms |
| **Password Theft** | Passwords are hashed with bcrypt (one-way encryption) |
| **Unauthorized Access** | Middleware checks permissions before every action |

### Audit Logging

Every important action is logged to the `activity_logs` table:

```
- Who did it (user ID, email)
- What they did (action type)
- When (timestamp)
- From where (IP address)
- Details (what was changed)
```

This creates an **audit trail** for compliance and troubleshooting.

---

## Glossary

| Term | Simple Definition |
|------|------------------|
| **API** | A way for programs to talk to each other |
| **Controller** | Code that handles a specific user action |
| **Migration** | Instructions for creating/changing database tables |
| **Middleware** | Code that runs before your main code (like security checks) |
| **Model** | A blueprint for your data |
| **OAuth** | A way to log in using another service |
| **OTP** | One-Time Password - a code sent to verify identity |
| **Queue/Job** | A task that runs in the background |
| **Route** | A URL pattern that points to specific code |
| **Seeder** | Code that fills your database with sample data |

---

## Getting Help

- **Laravel Documentation:** https://laravel.com/docs
- **React Documentation:** https://react.dev
- **Inertia.js Documentation:** https://inertiajs.com
- **Tailwind CSS Documentation:** https://tailwindcss.com

---

*This documentation was generated to help new developers understand the CourseApp codebase. Last updated: January 2026*
