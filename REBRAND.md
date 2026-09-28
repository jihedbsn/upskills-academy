# Rebrand: SAST → Upskills Academy

> **Date:** 2026-07-18 · Converts the sport-coaching platform (Swedish Academy of Sport Training)
> into **Upskills Academy — an English-teaching platform**, applying the navy/teal design system
> derived from the new logo. Companion docs: [PROJECT-ANALYSIS.md](PROJECT-ANALYSIS.md) (pre-rebrand audit),
> [design-system/](design-system/) (tokens + component recipes).

## Rollback

```bash
set -a; . ./.env; set +a     # MYSQL_ROOT_PASSWORD etc. — root .env is gitignored
gunzip -c backup_sast_pre_rebrand.sql.gz | docker exec -i sast-mysql mysql -uroot -p"$MYSQL_ROOT_PASSWORD" sast
```
The backup dump is not in git (it contains customer data) — ask the maintainer for it.
Code changes are file edits in `front/` and `admin-panel/` (no schema changes were made).

## Brand

| | Old | New |
|---|---|---|
| Name | Swedish Academy of Sport Training (SAST) | **Upskills Academy** |
| Domain | swedish-academy.se | upskillsacademy.com (placeholder) |
| Primary | `#0071ae` blue / `#6440FB` Educrat purple / `#7367f0` Vuexy purple | **`#093162` navy** |
| Accent | `#ffcb05` yellow | **`#3cb4b0` teal** |
| Logo | logo-sast.png | `logo.svg` (workspace root) → `logo-upskills.svg` + white variant + favicon mark |
| Fonts | Work Sans / Montserrat | Libre Franklin (UI) + Newsreader (display) |

## Database (seeded demo content)

Seed script: **`generate_seed.py` → `seed_upskills_demo.sql`** (idempotent: truncates + reinserts).
Run: `docker exec -i sast-mysql mysql -uroot -p"$MYSQL_ROOT_PASSWORD" --default-character-set=utf8mb4 sast < seed_upskills_demo.sql`

- **12 courses** (AR+EN variations): General English A1/A2/B1/B2, Business English, IELTS, TOEFL,
  free Conversation Club, Grammar Masterclass, Academic Writing, Kids & Teens, TESOL teacher training.
- **72 lessons** (6/course, bilingual), **3 quizzes** (15 questions / 60 answers, bilingual), linked via `quiz_products`.
- **8 teachers** (bilingual names, password `DEMO_TEACHER_PASSWORD` in root `.env`), **1 demo student** (`demo@upskillsacademy.com` / `DEMO_STUDENT_PASSWORD` in root `.env`).
- **6 books**, **3 packages** (+ product links & discounts), **6 testimonials**, **2 hero carousels**,
  4 partners, 6 featured courses, 8 ratings, 6 speaking-exam topics (`sujets`), 2 IELTS live sessions,
  1 blog + 2 articles; `information` (contact) updated; brand strings replaced inside `pages` and `email_templates`.
- **Semantic codes preserved:** `type_course` values `fa`/`fi`/`pt` kept (app logic switches on them) —
  re-labelled Foundation English / Fluency & Interaction / Proficiency Track.
- **Categories kept as delivery modes** (Zoom/Online/Classroom/Free/Blended/CPD/PCE) with polished titles.
- **PII/junk purged:** email_logs, notifications, chats, contact messages, reset codes, user_activities,
  failed_jobs, carts, newsletter subscriptions, `anis_test`, old orphaned certif/success/rating rows.
- **Admins table untouched** (existing local admin logins keep working).

### Demo images
DB rows reference generated placeholder SVGs served by the **admin app** (`FILE_URL=http://127.0.0.1:8000/`):
`admin-panel/public/uploads/{products/images,package,books/images,books,teachers,avis,carousels,partners,blogs,articles}`
plus `demo-sample.pdf` for book downloads. Swap for real media later; filenames are referenced in the DB.

## Front app changes (`front/`)

- `.env`: `APP_NAME="Upskills Academy"`, `FILE_URL="http://127.0.0.1:8000/"`.
- `routes/web.php`: `/` now redirects to `/en` (was `/ar`).
- **Lang files** (`resources/lang/{en,ar,fr}/`): all brand strings replaced; sport vocabulary re-mapped
  (coaching→General English, nutrition→Business English, rehabilitation→Exam Preparation, coach→Instructor/معلم,
  specialty list → IELTS/TOEFL, Business English, FA/FI/PT levels, Academic Writing, Young Learners, TESOL);
  partnership copy rewritten (CEFR/TESOL instead of REPS/GCSS); AR "مدرب"→"معلم" across all inflections.
- **Views**: logos → `assets/img/general/logo-upskills.svg` (+ white variant in footer), SVG favicon,
  testimonial roles, "Sports Training" badge → "English Courses", email templates' brand/header/footer,
  certificate emblem colors, analytics IDs neutralized (`G-XXXXXXXXXX`).
- **`public/assets/css/upskills-theme.css`** (new, loaded after `main.css`): re-points Educrat's CSS variables
  (`--color-purple-1` → navy etc.), overrides the 9 hardcoded purple rules, brand gradient, fonts
  (Newsreader display / Libre Franklin UI, RTL-safe), teal focus rings, radii.

## Admin app changes (`admin-panel/`)

- `.env`: same APP_NAME/FILE_URL updates.
- **Text**: login page ("English Teaching Management System"), page `<title>`s, footer copyright,
  all email templates & services (`EmailTemplateService`, `QuizService` URL, Mail classes, test commands),
  `sujets`/`products` type labels (FA/FI/PT → Foundation/Fluency/Proficiency), `Partnership` specialty labels.
- **`public/css/upskills-admin.css`** (new, loaded after `vuexy-custom.css`): Bootstrap/Vuexy primary → navy,
  sidebar dark-navy with navy/teal active gradients, teal form focus, table/badge/tab polish.
- Login page palette swapped inline (`#0071ae→#093162`, `#ffcb05→#3cb4b0`) and the shield icon replaced
  with the white logo. Top navbar gradient → navy→teal.
- New assets: `public/admin/app-assets/images/logo/logo-upskills{,-white}.svg`, `public/favicon.svg`.

## How to run

```bash
./run.sh          # MySQL container + both apps
# Front → http://localhost:8001/en   (AR: /ar, FR partial: /fr)
# Admin → http://localhost:8000/     (existing admin credentials unchanged)
# Demo student: demo@upskillsacademy.com / $DEMO_STUDENT_PASSWORD (root .env)
```

## Verified

- `/en`, `/ar`, `/en/courses` (12 cards), `/en/courses/6` (IELTS detail EN+AR with lessons/instructor/image),
  `/en/packages`, `/en/books` — all 200, zero old-brand strings (only "Swedish Krona" currency remains, correctly).
- Admin login page 200 with new branding; placeholder images served via admin (`/uploads/...` 200).
- Laravel logs clean (no errors/exceptions).

## Homepage redesign (2026-07-18, second pass)

All in `front/resources/views/accueil/components/` unless noted:

- **Hero** (`carousel.blade.php`) — replaced the broken Swiper (double text: headlines were painted inside the placeholder images AND rendered from DB) with a static, DB-driven hero: navy gradient + grid texture, serif headline, teal CTA, trust row, floating course-card composition in pure CSS. Wave separator removed. Staggered entrance animation (ease-out-quint, respects `prefers-reduced-motion`). Hero text comes from the first active `carousels` row.
- **Stats** (`stats.blade.php`) — teal/navy restyle (was yellow/purple); numbers updated in `achievements` (40/2500/30/15); labels re-mapped (English Courses / Learning Resources / Certified Teachers).
- **Why Choose** (`welcome.blade.php`) — full rewrite: dark-navy section, glass cards, teal icons (was bright blue bg + yellow title).
- **Learn new skills** (`learn-skills.blade.php`) — gym photo replaced with `public/images/learning-illustration.svg` (branded SVG scene); greens/blues → design system; signup link made locale-aware.
- **Testimonials** (`avis.blade.php`) — full rewrite: light section, static 3-col grid (no more slider clipping), quote cards with avatars + stars.
- **Partners** (`nos-partenaires.blade.php`) — full rewrite: quiet grayscale logo strip (hover = color), no carousel, no clipping.
- **Global** — legacy blues (`#0071ae`, `#005a8d`) and yellow (`#ffcb05`) purged from `head/footer/currency-switcher/chatbot-widget` blades; fixed header + preloader forced to brand navy via `upskills-theme.css`; footer solid navy-900 with white logo; newsletter buttons teal; Graduates removed from the header nav (route + footer link still exist).
- **Screenshot/QA aid** — `?noanim=1` query param (in `layouts/head.blade.php`) hides the preloader and forces reveal-animated elements visible; used for headless Chrome verification. Harmless in production.

### Homepage round 2 (same day)
- Hero: real student photo (WebP, 57 KB) with leaf-cut corner, gradient melt, dot-grid/ring/spark shapes, glassy chips, LIVE badge (localized), A1→C1 pill.
- Nav: Home + Graduates removed from header menu.
- Partners: uncontained icon+name SVG logos (6), infinite CSS marquee (4 duplicated groups, hover-pause, RTL-aware, reduced-motion fallback).
- Testimonials: native scroll-snap carousel (arrows, dots, gentle auto-advance) — no clipping.
- New CTA band between testimonials and partners (localized EN/AR/FR).
- Breadcrumbs on CMS pages fixed (missing `.breadcrumbs__content` wrapper + invisible last-crumb color).
- Deep sweep round 2: chatbot system prompt rewritten (was full SAST knowledge base), mail subjects, service fallbacks, `.env` MAIL_FROM_NAME/ADMIN_EMAIL, DB pages ("educational sports institution", "Kingdom of Sweden", "athletes", accreditation logo table removed), email templates.

### Round 3 — course page, footer, emails, auth (2026-07-18)
- **Course detail**: purchase sidebar rebuilt as an in-flow sticky column (was absolute + GSAP pin → footer overlapped it); recovered the card markup from `~/sast-elearn-backup/backup/front.zip` after a bad edit. RTL now mirrors (sidebar left on /ar via scoped `us-course-row` flip), header un-forced from LTR, `[dir=rtl] .row` blanket hack removed, duration localized (`3 months` → `3 أشهر` via `Product::getDurationAttribute`), tabs get a full-bleed divider + navy/teal states, empty flash-section top gap removed, breadcrumbs fixed.
- **Footer**: rebuilt columns — About/contact (from `information`) + 3 link columns + inline newsletter (flex, logical properties); legacy `[dir=rtl]` patch block removed (its `gap:8px` on the row wrapped the newsletter column in AR). AR/EN now identical.
- **Emails**: Upskills logo (PNG rasterized from the SVG) added to both base templates; old SAST logo banner removed; palette swept to navy/teal across front + admin email views AND `email_templates` rows; welcome email no longer attaches `student-guide.pdf`; subject in English.
- **Auth**: Google sign-in hidden on login + signup (blade-commented for easy restore).
- **Partnership pages**: broken `backgrounds/1.png` masthead replaced with reusable `us-pagehead` gradient band (title-only); descriptions moved below on white.

## Known limitations / next steps

- FR locale remains partial (5/13 lang files) — untouched scope, sport terms in FR were fixed.
- Placeholder SVG images & `demo-sample.pdf` should be replaced with real media for production.
- The security backlog from [PROJECT-ANALYSIS.md](PROJECT-ANALYSIS.md) §1 (rotate live keys, auth the
  referrals API, register `CheckPermission`, remove debug routes) is **still open** — rebrand did not fix code security.
- `certifs` table left empty (courses have `certif_id = NULL`); create certificate templates in the admin when needed.
- Old-brand Google/Meta analytics IDs were neutralized, not replaced — set real IDs before launch.
