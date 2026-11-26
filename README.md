# Mehfil Event Planner

**Live site:** 
https://mehfil-event-planner.onrender.com

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

## Performance log
Measured with Chrome DevTools (Network tab, Disable cache, Fast 4G throttling, hard reload, `APP_DEBUG=false` with config/route/view cache).

| Run | Load | Finish |
|---|---|---|
| 1 | 999 ms | 1.20 s |
| 2 | 784 ms | 976 ms |
| 3 | 822 ms | 1.03 s |
| Avg | ~868 ms | ~1.07 s |

`kpi/data` request time: 206 ms (Chrome DevTools, Fast 4G, cache disabled)

Result: page load stays under 2 s in the local test. Free host test: pending (will be measured after deployment).
Screenshot: `docs/performance-kpi.png`
Optimizations: single aggregate query, index on `event_date`, 60 s response cache, Chart.js via CDN.

## CSV export
- Button: "Download CSV" in `resources/views/admin/kpi.blade.php`
- Route: `GET /admin/kpi/export` (admin only), defined in `routes/web.php`
- Code: `KpiController::export()` in `app/Http/Controllers/Admin/KpiController.php` uses `response()->streamDownload()` (Laravel StreamedResponse) with `fputcsv()`
- Format: UTF-8 with BOM (opens correctly in Excel). Part 1: summary (`Metric,Value`). Part 2: monthly table (`Month,Bookings,Revenue (PKR)`)
- Real sample file downloaded from the app: `docs/sample-kpi-export.csv`

Note: totals exclude cancelled bookings, so they are lower than the total count on the Bookings page.


## Handover Guide


(Free tier: the first request after 15 minutes of inactivity can take about a minute while the service wakes up.)

### 1. What this is
Laravel 10 online booking system with a customer booking form and an admin dashboard (bookings management, KPI charts with Chart.js, CSV export).

### 2. Repository structure
| Path | Purpose |
|---|---|
| `app/Http/Controllers/` | Controllers (`BookingController`, `ProfileController`, `Admin/...`, `Admin/KpiController`) |
| `app/Http/Middleware/` | Auth and role middleware (`role:admin`) |
| `resources/views/` | Blade views (`admin/`, `layouts/admin.blade.php`, `bookings/`) |
| `routes/web.php` | Web routes (admin routes use `auth` + `role:admin`) |
| `database/migrations/`, `database/seeders/` | Schema and demo data (`DatabaseSeeder`, `DemoBookingSeeder`) |
| `tests/Feature/` | Feature tests (including `KpiAccessTest`) |
| `Dockerfile`, `docker/start.sh` | Container build and startup (migrations, optional seeding, Apache) |
| `.github/workflows/deploy.yml` | CI/CD pipeline |
| `docs/` | Screenshots and sample CSV export |

### 3. Run locally
1. Requirements: PHP 8.5 (8.4.1 or newer works with `composer.lock`), Composer, Node 20, MySQL.
2. `composer install` and `npm install`
3. `copy .env.example .env`, then `php artisan key:generate`
4. Create a MySQL database and set `DB_*` values in `.env`
5. `php artisan migrate --seed` (local demo users come from `DatabaseSeeder`)
6. In two terminals: `npm run dev` and `php artisan serve`
7. Open http://127.0.0.1:8000

Run tests (never against your real MySQL database, `RefreshDatabase` wipes tables):
```
set DB_CONNECTION=sqlite
set DB_DATABASE=:memory:
php artisan test
```

### 4. CI/CD pipeline (GitHub Actions)
File: `.github/workflows/deploy.yml`. Runs on every push to `main` (tests also run on pull requests).

1. **test**: sets up PHP 8.5 and Node 20, builds Vite assets, installs Composer dependencies, runs `php artisan test` on SQLite.
2. **build**: `npm ci` and `npm run build`.
3. **deploy**: only on push to `main`, after test and build pass. Calls the Render deploy hook (`curl -X POST` with the secret `RENDER_DEPLOY_HOOK_URL`).

Render Auto-Deploy is turned off so a deploy only happens after the tests pass.

### 5. Deployment on Render (free tier)
Render has no native PHP runtime, so the app runs from the `Dockerfile` (PHP 8.5 + Apache).

First-time setup:
1. Create a **PostgreSQL** instance (Free). Copy its **Internal Database URL**.
2. Create a **Web Service** from this GitHub repo: Language **Docker**, Instance **Free**, same region as the database.
3. Add the environment variables from section 6.
4. In the service settings turn **Auto-Deploy** off and copy the **Deploy Hook** URL.
5. In GitHub: Settings, Secrets and variables, Actions, add the secret `RENDER_DEPLOY_HOOK_URL` with that URL.
6. Deploy once (Manual Deploy). On start, `docker/start.sh` runs `php artisan migrate --force`, seeds if `RUN_SEED=true`, then caches config and views and starts Apache.
7. After the first successful login set `RUN_SEED` back to `false`.

Redeploy: push to `main` and the pipeline deploys by itself. Manual redeploy: Render, Manual Deploy, Deploy latest commit.

### 6. Environment variables (set in Render, never commit real values)
| Variable | Purpose |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Output of `php artisan key:generate --show` |
| `APP_URL` | https://mehfil-event-planner.onrender.com |
| `DB_CONNECTION` | `pgsql` |
| `DATABASE_URL` | Internal Database URL of the Render Postgres instance |
| `LOG_CHANNEL` | `stderr` (logs appear in the Render dashboard) |
| `MAIL_MAILER` | `log` (SMTP ports are blocked on the free tier) |
| `SESSION_DRIVER` / `CACHE_DRIVER` | `file` |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | Demo admin account created by the seeder |
| `CUSTOMER_EMAIL` / `CUSTOMER_PASSWORD` | Demo customer account |
| `RUN_SEED` | `true` only for the first deploy, then `false` |



### 7. Maintenance
- Free Postgres on Render expires about 30 days after creation. Before that, create a new database (or upgrade), update `DATABASE_URL`, and redeploy with `RUN_SEED=true` once.
- The filesystem is ephemeral: sessions and cache are lost on restart (users get logged out). Do not store uploads on disk.
- Email is written to the log only. For real emails use an HTTP-based mail provider.
- Keep `composer.lock` PHP requirement and the PHP version in `Dockerfile` and `deploy.yml` in sync.

### 8. Troubleshooting
| Symptom | Cause and fix |
|---|---|
| First page load takes about a minute | Free instance was asleep. Wait and reload. |
| Deploy fails with `Class "Faker\Factory" not found` | Faker must be in `require` (not `require-dev`) because production seeding uses factories. |
| Deploy fails with `Class "Database\Seeders\X" does not exist` | The seeder file is not committed, or its file name case differs (Linux is case sensitive). |
| `Vite manifest not found` (CI or production) | Assets were not built. Run `npm run build` before tests (CI) or check the Docker build stage. |
| `composer install` fails in CI with PHP version error | PHP version in `deploy.yml` or `Dockerfile` is older than `composer.lock` needs. |
| Admin or customer user not created in production | `ADMIN_PASSWORD` or `CUSTOMER_PASSWORD` is empty. Set it and redeploy with `RUN_SEED=true`. |
| `bad interpreter` when the container starts | `docker/start.sh` has Windows line endings (CRLF). Save it with LF. |
| CSS or links use `http://` | Set `APP_URL` to the https URL and keep `TrustProxies` set to `*`. |
| GitHub `deploy` job fails with `curl: blank argument` | The secret `RENDER_DEPLOY_HOOK_URL` is missing or empty. |
| Charts empty on KPI page | No bookings with amount. Redeploy with `RUN_SEED=true` once. |