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