# Project Analysis — SAST E-Learning Platform

> **Date:** 2026-07-18 · **Analyzed:** `front/` (Laravel 12), `admin-panel/` (Laravel 12), MySQL db `sast` (105 tables)
> **Context:** snapshot taken *before* the rebrand to **Upskills Academy** (English-teaching platform).
> A pre-rebrand DB backup exists: `backup_sast_pre_rebrand.sql.gz`.

## Verdict at a glance

| Area | Grade | Summary |
|------|-------|---------|
| Functionality | **B** | Feature-rich: courses, quizzes (6.7k questions), installments, Stripe + manual receipts, Zoom, live chat, referrals, certificates, abandoned-cart recovery. Both apps boot and run. |
| Architecture | **C+** | Thin controllers → service layer (right instinct), but services are god-classes. |
| Security | **D** | Live secrets on disk, unauthenticated APIs, RBAC not enforced, debug routes. |
| Code hygiene | **D+** | Dev scripts in repo root, ~0 tests, log spam, FR/EN naming mix. |
| Database | **C** | Sound core schema; typo columns, orphaned tables, no soft deletes, residual PII. |

---

## 1. Critical findings (fix regardless of any rebrand)

1. **Live production secrets on disk.**
   - `front/.env`: **live Stripe** (`sk_live_…`, `pk_live_…`), **live OpenAI key**, Facebook/Google/Zoom secrets, AWS keys — with `APP_ENV=local`, `APP_DEBUG=true`.
   - Secret-bearing backups exist and are **not gitignored**: `front/.env.save`, `front/.env.bak.local`, `admin-panel/.env.bak.local` (contains real DB/Zoom/Stripe-test credentials).
   - **Action:** rotate every key above; delete `.env.*` backups; extend `.gitignore` to `.env*`.
2. **Unauthenticated API endpoints** — `admin-panel/routes/api.php:135-146`: the whole `referrals/*` group has no auth and takes `{studentId}` in the URL → IDOR on student stats/history/credit + state-changing actions (`generate-code`, `claim-reward`, `consume-credit`). Also public: `POST /abandoned-carts/mark-converted` (line 128), `POST /email-inbound` (line 148, no signature check).
3. **Permission system is decorative.** Custom RBAC (`Role`, `Permission`, `CheckPermission` middleware) exists but the middleware is **never registered** in `bootstrap/app.php` — only the blanket `AdminAuth` gate is enforced; every admin can do everything. A `webmaster` guard is referenced (`CheckPermission`) but never defined in `config/auth.php`. Dead duplicate auth stack: `Auth/AdminAuthController` + `Auth/WebMasterAuthController` reference routes/views that don't exist.
4. **Debug leftovers exposed as routes.**
   - Admin: `/anis_uploads` (`routes/web.php:57`) → `StudentController::test()` `dd()`s a student fetched by a hardcoded personal Gmail address.
   - Front: `/test-history` (`routes/web.php:89-119`) dumps student #1 data as JSON; `/mail-config-check` (`:121-149`) exposes mail configuration.

## 2. Architecture

### front/ (student site, port 8001)
- 182 web routes + 51 student routes, locale-prefixed (`en|ar|fr`); `/` hard-redirects to `/ar` while `.env` says `APP_LOCALE=en` and `config/app.php` says `ar` — three conflicting defaults.
- 50 thin controllers delegating to ~55 services (~17.6k lines): **`CheckoutService` 2,118 lines**, `StudentDashboardService` 1,717, `QuizService` 833. God-services are the main maintainability risk.
- 83 models; dual auth identity (`User` vs `Student`). **Correctness smell:** `Product::students()` maps `product_students.student_id` → `User`, while enrollment code treats the same column as `students.id`.
- Frontend: purchased **Educrat** template (Bootstrap-grid, 282 KB `main.css`, brand purple `#6440FB` hardcoded). Tailwind 4 + Vite configured but effectively unused.
- i18n: AR + EN complete; FR partial (5/13 files).

### admin-panel/ (port 8000)
- 284 web routes (41 resources) in one flat 514-line file; 74 controllers; services again god-sized (`ProductService` 1,319 lines — `store()` ~360, `update()` ~648; `CouponService` 966).
- **95 models — 77 filenames identical to front/**: two hand-maintained Eloquent layers over one DB, already drifting.
- Theme: purchased **Vuexy** (Bootstrap 5, `#7367f0`), with a second unused template kit vendored alongside (`public/admin/new-assets/`, `starter-kit/`, `public/assets/assets/`).
- No `lang/` directory — every string hardcoded; heavy FR/EN mix (243 French comment hits in `app/`).

### Shared / process
- **Effectively zero tests**: 2 real test files across both apps (~124 controllers, ~178 models).
- Migrations: admin has **two different `create_admins_table` migrations** (clean `migrate` breaks); front has 26/50 patch migrations (`fix_*`, 9 FK repairs); manual `pending_migrations.txt` tracking.
- Debug/log noise: 279 `Log::` calls in front (incl. per-request `SetLocale` info logging), 440 in admin; ~100 `console.log` per app in views.
- Stray files: front root `expire_course_test.php` (mutates DB!), `update_user_names_from_orders.php`, `update_public_controllers.php` (broken no-op), `sql.txt`; admin root `admin/` (compiled blade cache), `articles_bd/` (legacy SQL dumps), `pending_migrations.txt`, `sql.txt`, `app/Models/AnisTest.php`, `public/img/anis.jpg`; test blade views (`test-chatbot`, `test-debug`, `test-final`, `test-simple`).

## 3. Database (`sast`, 104 tables + 1 view)

- **Content (pre-rebrand):** 67 sport courses + 134 AR/EN variations, 508 lessons, 321 quizzes / 6,707 questions / 26,676 answers, 27 books, 12 packages, 63 certificate templates, 11 teachers. AR+EN both 100% populated; **no FR data** despite French column names.
- **Hierarchy:** `categories` (delivery modes: Zoom/Online/Classroom/Free/Blended/CPD/PCE) → `products` → `product_variations` (row per locale, `langue ∈ {ar,en}`) → `product_studies` (ordered, drip via `opens_after_purchase_days`) + `resources`; assessment via `quizzes → questions → reponse_questions`, linked by `quiz_products`.
- **Semantic codes:** `products.type_course ∈ {fa, fi, pt, autres}` (= fitness assistant/instructor/personal trainer) — referenced by `sujets.type` and `coupons.course_types`; app logic switches on these. *Rebrand note: keep codes, change labels.*
- **Quality issues:** typo columns (`breuillant`, `goverrnement`, `iscach`), misspelled tables (`*_acceuil`, `coupon_detailles`), free-text `period` ("30 days", "45  days", "3 months"), duplicate `lang`/`langue` columns, teacher #18 AR/EN names swapped, split collations (63 unicode_ci vs 41 general_ci), no soft deletes anywhere, `certif_students` fully orphaned (634/634 rows → deleted students, no FKs), `users` table dead (0 rows) but still an FK target.
- **PII retained after user purge** (~594 students & ~1,912 orders hard-deleted): 953 `email_logs` (real recipients + bodies), 2,549 `notifications`, 772 `chat_messages`, 48 `newsletter_subscriptions`, 108 `password_reset_codes`, 552 `user_activities` (IP + UA), 1,069 `failed_jobs` payloads.
- Images are **not stored locally** — served from production via `FILE_URL` (`https://admin.swedish-academy.se/uploads/...`).

## 4. What's genuinely good

- No SQL-injection patterns (parameterized everywhere sampled); no `$guarded = []`; bcrypt for all account passwords; thin controllers consistently applied; complete bilingual AR/EN content; FKs on 65 tables with sane cascade rules; Stripe + manual-receipt dual payment flow is a sensible fit for the market.

## 5. Recommended cleanup backlog (beyond the rebrand)

**P0 (security):** rotate all keys in §1; delete `.env` backups + widen `.gitignore`; add auth middleware to `referrals/*` + webhook signature check; remove debug routes (`/anis_uploads`, `/test-history`, `/mail-config-check`); register `CheckPermission` middleware and define/remove the `webmaster` guard.

**P1 (correctness):** resolve `product_students.student_id` → `User` vs `Student` mapping; dedupe `create_admins_table` migrations; fix teacher #18 swapped names; add FK or truncate orphaned `certif_students`.

**P2 (hygiene):** delete stray root scripts/dirs & test views; strip per-request info logging; extract god-services into focused actions; unify duplicated models into a shared package (or one modular monolith); pick one frontend pipeline (drop unused Tailwind or migrate to it); purge residual PII tables; normalize `period` to integer days; schedule collation unification.

**P3 (quality of life):** real test suite starting with checkout/quiz/enrollment happy paths; CI running `migrate:fresh` + tests; `lang/` extraction for the admin panel; French locale either completed or dropped.

---

*Full details (exact file paths + line numbers per finding) were produced during the audit conversation of 2026-07-18. The rebrand execution that followed this analysis is documented in `REBRAND.md`.*
