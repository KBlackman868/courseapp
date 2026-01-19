# Learn About Health - Course Management System

A comprehensive Learning Management System (LMS) built for the Ministry of Health (MOH) to manage course creation, user enrollment, and learning delivery with deep integration to Moodle.

## Features

- **Course Management**: Create, update, and manage courses with categories
- **User Authentication**: Multiple auth methods (Email, LDAP, Google OAuth, SAML2)
- **Enrollment System**: Request-based enrollment with auto-approval for MOH staff
- **Moodle Integration**: Seamless sync of users, courses, and enrollments
- **Role-Based Access Control**: Granular permissions (superadmin, admin, instructor, student)
- **Activity Logging**: Complete audit trail for compliance
- **OTP Verification**: Email-based one-time password verification

## Technology Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: React 18 with Inertia.js
- **Styling**: Tailwind CSS 3 + DaisyUI
- **Database**: SQLite (default), MySQL/PostgreSQL supported
- **Queue**: Database-driven job queue

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite/MySQL/PostgreSQL

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd courseapp
```

### 2. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment Variables

Edit `.env` and configure:

```env
# Application
APP_NAME="Learn About Health"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
# DB_DATABASE=/path/to/database.sqlite

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@example.com

# Moodle Integration (optional)
MOODLE_BASE_URL=https://your-moodle.com
MOODLE_TOKEN=your-moodle-api-token

# LDAP Configuration (optional)
LDAP_HOST=ldap.example.com
LDAP_PORT=389
LDAP_BASE_DN=dc=example,dc=com

# Google OAuth (optional)
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 5. Database Setup

```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed initial data (optional)
php artisan db:seed
```

### 6. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start the Application

```bash
# Start the development server
php artisan serve

# Start the queue worker (in a separate terminal)
php artisan queue:work
```

Visit `http://localhost:8000` in your browser.

## Development

### Running Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/EnrollmentTest.php
```

### Code Quality

```bash
# Run PHP linting
./vendor/bin/pint

# Run static analysis (if installed)
./vendor/bin/phpstan analyse
```

### Queue Worker

For local development, run the queue worker:

```bash
php artisan queue:work --tries=3
```

For production, use a process manager like Supervisor.

## API Endpoints

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Landing page |
| POST | `/login` | User login |
| POST | `/register` | User registration |
| GET | `/health/ping` | Health check (load balancer) |
| GET | `/health` | Comprehensive health check |

### Authenticated Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard` | User dashboard |
| GET | `/courses` | Course listing |
| GET | `/courses/{id}` | Course details |
| POST | `/courses/{id}/enroll` | Request enrollment |
| GET | `/mycourses` | User's enrolled courses |

### Admin Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/dashboard` | Admin dashboard |
| GET | `/admin/courses` | Manage courses |
| GET | `/admin/enrollments` | Manage enrollments |
| POST | `/admin/enrollments/{id}/approve` | Approve enrollment |
| POST | `/admin/enrollments/{id}/deny` | Deny enrollment |
| GET | `/admin/users` | Manage users |

### REST API (v1)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/moodle/users` | Create Moodle user |
| PUT | `/api/v1/moodle/users/{id}` | Update Moodle user |
| POST | `/api/v1/moodle/enrolments` | Create enrollment |

## Architecture

```
app/
├── Console/Commands/     # CLI commands
├── Exceptions/           # Custom exceptions
├── Http/
│   ├── Controllers/      # Request handlers
│   ├── Middleware/       # HTTP middleware
│   └── Requests/         # Form request validation
├── Jobs/                 # Queued jobs
├── Mail/                 # Email templates
├── Models/               # Eloquent models
├── Notifications/        # User notifications
└── Services/             # Business logic
    ├── MoodleService     # Moodle API integration
    ├── LdapService       # LDAP authentication
    ├── OtpService        # OTP verification
    ├── CacheService      # Caching layer
    └── ActivityLogger    # Audit logging
```

## User Roles

| Role | Permissions |
|------|-------------|
| `superadmin` | Full system access, activity logs |
| `admin` | Course & user management |
| `course_admin` | Course management within assigned categories |
| `instructor` | Course content management |
| `student` | Course enrollment and access |

## Moodle Integration

The system integrates with Moodle for:

- **User Sync**: Automatically create/link users in Moodle
- **Course Sync**: Import courses from Moodle or push new courses
- **Enrollment Sync**: Sync approved enrollments to Moodle

### Configuration

Set the following in your `.env`:

```env
MOODLE_BASE_URL=https://your-moodle-instance.com
MOODLE_TOKEN=your-web-service-token
MOODLE_VERIFY_SSL=true
```

### Required Moodle Web Services

- `core_user_get_users`
- `core_user_create_users`
- `core_user_update_users`
- `enrol_manual_enrol_users`
- `enrol_manual_unenrol_users`
- `core_course_get_courses`

## Deployment

### Production Checklist

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure proper database (MySQL/PostgreSQL)
3. Set up Redis for caching and sessions
4. Configure queue worker with Supervisor
5. Set up SSL/HTTPS
6. Configure proper mail server
7. Run `php artisan config:cache`
8. Run `php artisan route:cache`
9. Run `php artisan view:cache`

## Troubleshooting

### Common Issues

**419 Page Expired Error**
- Ensure `TrustProxies` middleware is configured correctly
- Check CSRF token in forms

**Moodle Connection Failed**
- Verify `MOODLE_BASE_URL` and `MOODLE_TOKEN`
- Check network connectivity
- Ensure web services are enabled in Moodle

**Queue Jobs Not Processing**
- Ensure queue worker is running: `php artisan queue:work`
- Check `failed_jobs` table for errors

**Email Not Sending**
- Verify mail configuration in `.env`
- Check mail logs: `storage/logs/laravel.log`

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## License

This project is proprietary software for the Ministry of Health.

## Support

For support, contact the IT department or open an issue in the repository.
