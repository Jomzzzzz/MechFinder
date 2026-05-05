# MechFinder

A Laravel 12 PWA-based motorcycle repair dispatch system connecting motorists with nearby auto repair shops.

> For full system documentation, ERD, and DFD diagrams see [DOCUMENTATION.md](DOCUMENTATION.md).

---

## What It Does

- **Motorists** (no account required) open the PWA, find nearby shops on a map, submit a dispatch request with vehicle info, track the job status in real-time, message the shop, and leave a review.
- **Shop owners** log in via Gmail/Google OAuth, manage incoming requests from a dashboard, accept/decline jobs, update job status, and reply to motorists.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS, Vite |
| Database | MySQL |
| Auth | Session + Google OAuth 2.0 (Gmail-only) |
| PWA | Service Worker + Web App Manifest |
| Deployment | Railway (production), Herd (local) |

---

## Local Setup

### Requirements
- PHP 8.2+
- Composer
- MySQL
- Node.js + npm
- [Laravel Herd](https://herd.laravel.com)

### Steps

```bash
# 1. Install PHP dependencies
composer install

# 2. Install JS dependencies
npm install

# 3. Copy environment file
cp .env.example .env

# 4. Set your DB credentials in .env
#    DB_DATABASE=mechfinder_db
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Generate app key
php artisan key:generate

# 6. Create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS mechfinder_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Run migrations
php artisan migrate

# 8. Link storage
php artisan storage:link

# 9. Build assets
npm run dev
```

Then visit `http://mechfinder.test` in your browser.

---

## Entry Points

| URL | Description |
|---|---|
| `http://mechfinder.test/` | Welcome / landing page |
| `http://mechfinder.test/motorist` | Motorist PWA (installable) |
| `http://mechfinder.test/login` | Shop owner login |
| `http://mechfinder.test/signup` | Shop owner registration |
| `http://mechfinder.test/shop/dashboard` | Shop dashboard (auth required) |

---

## Key Directories

```
app/Http/Controllers/     — AuthController, ShopController, MotoristController
resources/views/motorist/ — Motorist PWA views
resources/views/shop/     — Shop portal views
resources/views/auth/     — Login / signup views
database/migrations/      — All database migrations
public/sw-motorist.js     — PWA service worker
public/manifest-motorist.json — PWA manifest
```

---

## Google OAuth (optional)

To enable Google Sign-In, add credentials to `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URL=http://mechfinder.test/auth/google/callback
```

Register `http://mechfinder.test/auth/google/callback` as an authorized redirect URI in [Google Cloud Console](https://console.cloud.google.com).
