# Moduloom — Deployment (shared hosting)

Written for BlueHost/DreamHost-class shared hosting, per
`MASTER_ARCHITECTURE.md`'s deployment target. Read `SECURITY.md` first —
this application currently has no authentication at all; do not deploy
it anywhere reachable by the public internet until that's resolved.

## Requirements

- PHP 8.4+ with the extensions Laravel needs (`pdo_mysql`, `mbstring`,
  `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`) — most
  shared-hosting PHP builds already include these; check your host's
  PHP version selector before anything else, since Moduloom's locked
  dependencies genuinely require 8.4+ (not 8.3 — this was caught the
  hard way in Phase 0's CI).
- MariaDB 10.6+ (or MySQL 8.0+).
- Composer and SSH access. Moduloom's modules are local Composer path
  packages (`modules/*`), not packages pulled from Packagist — the
  repository must be deployed with the `modules/` directory intact and
  `composer install` run on the server itself, not just `vendor/`
  uploaded separately. Confirm your hosting plan includes SSH and
  Composer before committing to it; not every shared-hosting tier does.
- Node.js, for building frontend assets (`npm run build`) — either on
  the server if available, or built locally/in CI and the resulting
  `public/build/` directory deployed alongside the PHP code.

## Document root

Point the hosting account's document root at this project's `public/`
directory, not the project root — the same as any standard Laravel
app. Everything else (`app/`, `modules/`, `database/`, `.env`) must sit
outside the web-servable root.

## Environment

Copy `.env.example` to `.env` and set, at minimum:

```bash
APP_ENV=production
APP_DEBUG=false
APP_KEY=                    # generate with: php artisan key:generate
APP_URL=https://yourdomain.example

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1           # or your host's provided DB hostname
DB_PORT=3306
DB_DATABASE=your_real_database
DB_USERNAME=your_real_username
DB_PASSWORD=your_real_password
```

`APP_DEBUG=false` is not optional. With it `true`, an error page shows
full stack traces, file paths, environment variables, and recent SQL
queries to anyone who triggers an error — a direct information
disclosure vulnerability, not a cosmetic concern.

## Install

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

`php artisan migrate` only runs the **kernel's own** migrations
(`installed_modules`, `module_references`, `roles`, `capabilities`,
`role_capability_grants`, `profile_install_history`, `theme_selections`)
— by design, per `MASTER_ARCHITECTURE.md`'s non-destructive-removal and
additive-overlay rules. A module's own tables (`notes`, `crm_contacts`,
`forum_threads`, ...) are created on demand, exactly once, the first
time that specific module is actually installed through the kernel
(`ModuleInstaller`) — via the setup wizard, the module marketplace, or
`php artisan module:install {slug}`. There is nothing to pre-migrate for
modules nobody has installed yet, and nothing wrong if a fresh
production instance's schema looks mostly empty right after this step —
that's expected, not a failed deploy.

`php artisan storage:link` is required for the Files, Documents,
Personal databases' (via its own data, not uploads) and any future
upload-backed module to actually serve what gets uploaded — without it,
uploaded files save correctly but their public URLs 404.

## File permissions

`storage/` and `bootstrap/cache/` need to be writable by the web
server's process user. On most shared hosting this means `755` on the
directories and `644` on files is sufficient since PHP runs as your own
account; if you hit permission errors, `775` on those two directories
specifically (never the whole project) is the usual fix — never `777`.

## Cron / scheduler

Nothing in Moduloom currently needs Laravel's scheduler — no module
ships a scheduled job. Skip the usual cron entry unless a future module
adds one.

## After deploy

Visit `/setup` and install your first ecosystem. There is no seed data
and no default account — the instance starts genuinely empty, by
design, the same as any fresh Personal/Small Business/etc. install
proven in this project's own test suite.
