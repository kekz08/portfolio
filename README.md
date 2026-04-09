# Portify - Laravel + Vue Project Tracking

This project has been converted from a static portfolio export into a Laravel + Vue.js application with:

- Portfolio landing page with `Hire Me` CTA
- Registration flow with personal/project request details
- Authenticated user dashboard for project status tracking
- Admin/staff dashboard for status updates
- Activity log and email/database notifications on status changes
- Policy/Gate-based authorization with role-based access

## Stack

- Laravel 13
- Vue 3 + Inertia + Vite
- SQLite (default local setup)

## Status Workflow

- `pending_review`
- `approved`
- `in_progress`
- `completed`

## Local Setup

1. Install dependencies
   - `composer install`
   - `npm install`
2. Environment
   - `cp .env.example .env` (if needed)
   - `php artisan key:generate`
3. Database
   - Ensure `database/database.sqlite` exists
   - `php artisan migrate --seed`
4. Run app
   - `php artisan serve`
   - `npm run dev`

## Default Seeded Accounts

- Admin: `admin@example.com` / `password`
- Client: `client@example.com` / `password`

## Architecture Notes

- Validation: Form Requests
  - `App\Http\Requests\Auth\RegisterUserRequest`
  - `App\Http\Requests\Project\ProjectStatusUpdateRequest`
- Services
  - `App\Services\RegistrationService`
  - `App\Services\ProjectStatusService`
- Repository pattern
  - `App\Repositories\Contracts\ProjectRepositoryInterface`
  - `App\Repositories\Eloquent\EloquentProjectRepository`
- Authorization
  - Gate: `manage-projects`
  - Policy: `App\Policies\ProjectPolicy`

## Security Highlights

- Password hashing (`Hash::make`)
- Encrypted personal data (`users.phone` cast as `encrypted`)
- CSRF protection (Laravel default middleware)
- Rate limiting on registration (`throttle:10,1`)
- Session encryption enabled (`SESSION_ENCRYPT=true`)

## Legacy Static Export

Original static files are preserved in:

- `legacy-static/`

