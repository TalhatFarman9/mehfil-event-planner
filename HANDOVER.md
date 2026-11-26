# Mehfil Event Planners: Handover Guide

Live site: https://mehfil-event-planner.onrender.com
(Free tier: the first request after 15 minutes of inactivity can take about a minute while the service wakes up.)

## 1. What this is
Laravel 10 online booking system with a customer booking form and an admin dashboard (bookings management, KPI charts with Chart.js, CSV export).

## 2. Repository structure
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

## 3. Run locally
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

## 4. CI/CD pipeline (GitHub Actions)
File: `.github/workflows/deploy.yml`. Runs on every push to `main` (tests also run on pull requests).

1. **test**: sets up PHP 8.5 and Node 20, builds Vite assets, installs Composer dependencies, runs `php artisan test` on SQLite.
2. **build**: `npm ci` and `npm run build`.
3. **deploy**: only on push to `main`, after test and build pass. Calls the Render deploy hook (`curl -X POST` with the secret `RENDER_DEPLOY_HOOK_URL`).

Render Auto-Deploy is turned off so a deploy only happens after the tests pass.

## 5. Deployment on Render (free tier)
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

## 6. Environment variables (set in Render, never commit real values)
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



## 7. Maintenance
- Free Postgres on Render expires about 30 days after creation. Before that, create a new database (or upgrade), update `DATABASE_URL`, and redeploy with `RUN_SEED=true` once.
- The filesystem is ephemeral: sessions and cache are lost on restart (users get logged out). Do not store uploads on disk.
- Email is written to the log only. For real emails use an HTTP-based mail provider.
- Keep `composer.lock` PHP requirement and the PHP version in `Dockerfile` and `deploy.yml` in sync.

## 8. Troubleshooting
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