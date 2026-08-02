# Fignoc Technologies

Marketing site for **Fignoc Technologies** — a Harare software & growth studio (Laravel + Vite + Tailwind + Filament).

## Stack

- Laravel (PHP)
- Vite, Tailwind CSS v4, Alpine.js, GSAP
- Filament admin at `/admin`
- SQLite locally (swap to MySQL/Postgres in production)

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
composer run dev
```

App: `http://127.0.0.1:8000` · Admin: `/admin` (`php artisan make:filament-user`)

## Production notes

Set `APP_URL=https://www.fignoc.co.zw` and fill analytics / Search Console hooks in `config/fignoc.php` when ready.
