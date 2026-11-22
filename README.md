# Mehfil Event Planner

Online booking system with an admin dashboard. Built with Laravel 10.

## Authentication choice

This project uses **Laravel Sanctum in session (cookie) mode**, with Breeze
login and registration. Roles are `customer` and `admin`, enforced by a
`role` middleware.

**Why session and not JWT:**
- One browser-based app on one domain, so cookies work naturally.
- Logout revokes access immediately, which matters for admin accounts.
- HttpOnly cookies and built-in CSRF protection are safer than keeping a JWT in localStorage.
- Sanctum can issue API tokens later if a mobile app is added.

Full comparison: [docs/auth-decision-log.pdf](docs/auth-decision-log.pdf)

## Setup

    composer install
    npm install && npm run build
    cp .env.example .env
    php artisan key:generate
    php artisan migrate --seed
    php artisan serve

## Test accounts

| Role     | Email                | Password       |
|----------|----------------------|----------------|
| Admin    | admin@mehfil.test    | Admin@12345    |
| Customer | customer@mehfil.test | Customer@12345 |

## Features (Task 1)

- Registration and login for customers (default role) and admins
- Role middleware: `/admin/*` routes are admin-only, others get 403
- Customer booking dashboard and admin sidebar dashboard
- Email verification stub (mail driver set to `log`)

  Admin registration at `/admin/register` needs the code from `ADMIN_REGISTRATION_CODE`
  in `.env` (local dev default: `Mehfil-Admin-2026`, change it in production).

  ## Task 2: Admin booking panel

- `/admin/bookings`: paginated table (10 per page) with edit and delete.
- Search by client name or event date, filter by status and exact date.
- Delete asks for confirmation. All routes are behind `auth:sanctum` and `role:admin`.
- Demo data: `php artisan migrate --seed` creates 35 sample bookings.

## KPI Dashboard (Task 4)

- URL: `/admin/kpi` (admin only: `auth` + `role:admin` middleware)
- Charts (Chart.js 4): bookings per month (bar), revenue trend (line), bookings vs revenue (combo)
- Data loaded via AJAX from `/admin/kpi/data` (aggregate query, cached 60 s)
- CSV export: `/admin/kpi/export` using Laravel `streamDownload` (StreamedResponse)
- Cancelled bookings are excluded from totals

### Performance log
Measured with Chrome DevTools (Network tab, Disable cache, Fast 4G throttling, hard reload, `APP_DEBUG=false` with config/route/view cache).

| Run | Load | Finish |
|---|---|---|
| 1 | 999 ms | 1.20 s |
| 2 | 784 ms | 976 ms |
| 3 | 822 ms | 1.03 s |
| Avg | ~868 ms | ~1.07 s |

`kpi/data` request time: _ ms (apni value likho)

Result: page load stays under 2 s in the local test. Free host test: pending (will be measured after deployment).
Screenshot: `docs/performance-kpi.png`
Optimizations: single aggregate query, index on `event_date`, 60 s response cache, Chart.js via CDN.