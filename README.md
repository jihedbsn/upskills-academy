# Upskills Academy

An English-teaching e-learning platform: course catalogue, bilingual lessons (EN/AR, partial FR), quizzes, live Zoom sessions, certificates, Stripe and bank-transfer checkout, installments, referrals and an AI chatbot.

The repo holds **two Laravel 12 apps** that share one MySQL database:

| App | Path | Local URL | Purpose |
|---|---|---|---|
| **Front** | [`front/`](front/) | http://localhost:8001/en | Student-facing site: catalogue, checkout, student dashboard |
| **Admin panel** | [`admin-panel/`](admin-panel/) | http://localhost:8000/login | Back office: content, students, orders, exams, emails. Also serves uploaded media (`FILE_URL`) |

## Repository layout

```
.
├── front/                  Laravel app — student site (Educrat template + upskills-theme.css)
├── admin-panel/            Laravel app — admin back office (Vuexy template + upskills-admin.css)
├── design-system/          Brand tokens & component recipes (navy #093162 / teal #3cb4b0)
├── deploy/                 Production .env templates (*.example)
├── run.sh                  Start MySQL (Docker) + both apps locally
├── deploy-front.sh         rsync front/ to the VPS and rebuild caches
├── deploy-admin.sh         rsync admin-panel/ to the VPS and rebuild caches
├── generate_seed.py        Generates seed_upskills_demo.sql
├── seed_upskills_demo.sql  Demo content (12 courses, lessons, quizzes, teachers, books…)
├── DEPLOY.md               Full VPS deployment guide (nginx, PHP-FPM, certbot, queues, cron)
├── REBRAND.md              What changed in the SAST → Upskills rebrand
└── PROJECT-ANALYSIS.md     Code/security audit and prioritized fix list
```

## Tech stack

- PHP 8.2+ (8.3 recommended), Laravel 12, Composer
- MySQL 8 (runs in Docker locally)
- Stripe, Zoom (Server-to-Server OAuth), Brevo SMTP, OpenAI, Google/Facebook login
- Database queue and scheduler: both apps need `queue:work` and `schedule:run` in production

## Getting started (local)

**Prerequisites:** PHP 8.2+ with `mbstring xml curl zip gd bcmath intl mysql` extensions, Composer, Docker.

```bash
git clone git@github.com:jihedbsn/upskills-academy.git
cd upskills-academy

# 1. Secrets & settings — every .env is gitignored; fill in the copies
cp .env.example .env                              # used by run.sh and deploy-*.sh
cp front/.env.example front/.env
cp admin-panel/.env.example admin-panel/.env
# Set DB_PASSWORD in both app .env files to the same value as DB_PASSWORD in the root .env

# 2. Dependencies + app keys
for app in front admin-panel; do
  (cd $app && composer install && php artisan key:generate)
done

# 3. Database: put the SQL dump (ask the maintainer, not in git) at ./sast_database.sql, then
./run.sh              # creates the MySQL container, imports the dump on first run, starts both apps
./run.sh stop         # stop the two web servers

# 4. (optional) reload the demo content
set -a; . ./.env; set +a
docker exec -i "$MYSQL_CONTAINER" mysql -uroot -p"$MYSQL_ROOT_PASSWORD" \
  --default-character-set=utf8mb4 "$DB_DATABASE" < seed_upskills_demo.sql
```

The demo seed creates the student account `demo@upskillsacademy.com`. Its password, and the teacher accounts' password, are in `DEMO_STUDENT_PASSWORD` / `DEMO_TEACHER_PASSWORD` in the root `.env`.

Logs: `/tmp/sast-front.log`, `/tmp/sast-admin.log`, plus each app's `storage/logs/laravel.log`.

## Configuration

All credentials live in **untracked `.env` files**. Only `*.example` templates are committed.

| File | Used by | Holds |
|---|---|---|
| `.env` | `run.sh`, `deploy-*.sh` | Local MySQL passwords, VPS host/user/SSH key, demo account passwords |
| `front/.env` | Front app | App key, DB, SMTP, Stripe, Zoom, OpenAI, Google/Facebook OAuth, bank-transfer details |
| `admin-panel/.env` | Admin app | App key, DB, SMTP, IMAP inbox, Stripe, Zoom, Facebook/WhatsApp tokens |
| `deploy/env.*.production` | Copied to the server as `.env` | Production values for each app |

Key setting: `FILE_URL` in **both** apps must point at the admin app, because it serves all course images and uploads.

## What is intentionally *not* in git

| Item | Why | Where to get it |
|---|---|---|
| `.env`, `.env.*` (except `.env.example`) | Secrets | Ask the maintainer, or fill from the templates |
| `*.sql`, `*.sql.gz` (except the demo seed) | Contain real customer data | Ask the maintainer |
| `vendor/`, `node_modules/` | Dependencies | `composer install` |
| `storage/` contents (logs, cache, sessions, private email attachments) | Runtime data | Created automatically |
| `admin-panel/public/uploads/`, `front/public/uploads/`, other user-uploaded folders | Media library / user uploads | rsync from the server or the maintainer. See [DEPLOY.md §4](DEPLOY.md#4-upload-the-code) |

## Deployment

See **[DEPLOY.md](DEPLOY.md)** for first-time server setup. For routine updates, run the deploy scripts. They read the server details from the root `.env`:

```bash
./deploy-front.sh
./deploy-admin.sh
```

## Useful artisan commands

```bash
php artisan test:all-student-emails [email]   # front — send every student email template
php artisan test:all-admin-emails [email]     # admin — send every admin email template
php artisan test:certificate-email [email]    # admin — certificate-ready email
```

Without an `[email]` argument these commands send to `MAIL_TEST_RECIPIENT` from the app's `.env`.

## Security notes

- Keys that were stored in older local `.env` files (Stripe live, OpenAI, Zoom, Google/Facebook, Brevo SMTP) should be treated as exposed and **rotated**. See [DEPLOY.md](DEPLOY.md) and [PROJECT-ANALYSIS.md](PROJECT-ANALYSIS.md).
- Never commit a filled-in `.env`. Check `git status` before every commit.
- Database seeders use development-only passwords (`password` / `password123`). Don't run them against production.
