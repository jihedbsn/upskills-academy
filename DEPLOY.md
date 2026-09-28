# Deploying Upskills Academy to the VPS

> **Targets:** front → `https://up-skills.academy` · admin → `https://admin.up-skills.academy`
> DNS A records for `@` and `admin` already point at the VPS. Assumes Ubuntu 22.04/24.04 with root/sudo SSH.
>
> **Kit in this repo:** [deploy/env.front.production.example](deploy/env.front.production.example) · [deploy/env.admin.production.example](deploy/env.admin.production.example) — copy each to `deploy/env.*.production` (gitignored) and fill in real values.
> **Not in git (get them from the maintainer):** the filled-in `deploy/env.*.production` files and `deploy/upskills_production_db.sql.gz` (current DB with demo content).

## ⚠️ Before anything else (non-negotiable)

1. **Rotate every key that lived in the old `.env`** — the local file held **live** Stripe (`sk_live…`), OpenAI, Google/Facebook/Zoom, AWS keys. Treat them all as leaked: create new ones, and use the *new* values on the server only.
2. Debug routes (`/test-history`, `/mail-config-check`, `/anis_uploads`) were removed on 2026-07-19 — do not restore them.
3. **Mail is already handled** — the apps send through Brevo (`smtp-relay.brevo.com`); carry the existing `MAIL_*` credentials into the server `.env`s (templates note this). The visible sender stays `noreply@…brevosend.com` until you validate `up-skills.academy` inside Brevo — accepted for now. Email logos are CID-embedded, so they render regardless of domain.

---

## 1. Server preparation

```bash
ssh root@YOUR_VPS_IP

apt update && apt upgrade -y
adduser deploy && usermod -aG sudo deploy        # work as non-root
ufw allow OpenSSH && ufw allow 80 && ufw allow 443 && ufw enable
```

## 2. Install the stack

```bash
apt install -y nginx mysql-server \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl php8.3-imagick \
  unzip curl git certbot python3-certbot-nginx

# Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

`php8.3-gd` + `imagick` cover intervention/image and the QR-code packages.

## 3. MySQL

```bash
mysql_secure_installation           # set root password, remove test users

mysql -u root -p <<'SQL'
CREATE DATABASE upskills CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'upskills'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON upskills.* TO 'upskills'@'localhost';
FLUSH PRIVILEGES;
SQL
```

Import the database (contains the seeded demo content, categories, pages, email templates):

```bash
# from your LOCAL machine
scp deploy/upskills_production_db.sql.gz deploy@YOUR_VPS_IP:/tmp/
# on the VPS
gunzip -c /tmp/upskills_production_db.sql.gz | mysql -u upskills -p upskills && rm /tmp/upskills_production_db.sql.gz
```

## 4. Upload the code

Uploads, dumps and `.env` files are not in git, so rsync from your machine. For routine redeploys, `./deploy-front.sh` / `./deploy-admin.sh` do this for you and read `VPS_HOST`, `VPS_USER`, `VPS_SSH_KEY` from the root `.env` (template: [.env.example](.env.example)).

```bash
# from the repo root on your LOCAL machine
RSYNC_EXCLUDES='--exclude node_modules --exclude vendor --exclude .env --exclude ".env.*" \
  --exclude storage/logs --exclude storage/framework/cache --exclude storage/framework/sessions \
  --exclude storage/framework/views --exclude tests'

rsync -az --delete $RSYNC_EXCLUDES front/        deploy@YOUR_VPS_IP:/var/www/front/
rsync -az --delete $RSYNC_EXCLUDES admin-panel/  deploy@YOUR_VPS_IP:/var/www/admin/
```

Notes:
- `admin-panel/public/uploads/**` (course covers, avatars, partner logos, `demo-sample.pdf` — 47 files) **must** ship — it's the media library both apps display. **The rsync above includes it, but it is `.gitignore`d**, so a git-based deploy will NOT carry it. If you deploy via git, transfer this folder separately:
  ```bash
  rsync -az admin-panel/public/uploads/ deploy@YOUR_VPS_IP:/var/www/admin/public/uploads/
  ```
  Verify afterwards: `curl -I https://admin.up-skills.academy/uploads/products/images/course-a1.svg` → must return `200`.
- `front/public/storage/payment_receipts` is a real directory (not a symlink) — keep it; don't run `php artisan storage:link`.

## 5. Environment files

```bash
# on the VPS
cp /var/www/front/.env.example /var/www/front/.env    # then overwrite with the template
```

Copy [deploy/env.front.production.example](deploy/env.front.production.example) → `deploy/env.front.production` and [deploy/env.admin.production.example](deploy/env.admin.production.example) → `deploy/env.admin.production` (both gitignored), fill them in locally (APP_KEYs copied from the current local `.env` files, new rotated secrets, DB password, SMTP), then:

```bash
scp deploy/env.front.production deploy@YOUR_VPS_IP:/var/www/front/.env
scp deploy/env.admin.production deploy@YOUR_VPS_IP:/var/www/admin/.env
```

The two settings people forget, both already in the templates:
- front `FILE_URL="https://admin.up-skills.academy/"` — course images live on the admin host; **https or you get mixed-content blocks**
- `APP_DEBUG=false` on both

## 6. Install dependencies & permissions

```bash
for APP in /var/www/front /var/www/admin; do
  cd $APP
  composer install --no-dev --optimize-autoloader
  chown -R deploy:www-data .
  chmod -R 775 storage bootstrap/cache
  chmod 640 .env
  php artisan config:cache
  php artisan view:cache
  php artisan event:cache
  # DO NOT run `php artisan route:cache` — both apps still register closure routes; it will error.
  php artisan migrate --force   # no-op against the imported DB, but validates the connection
done
```

## 7. Nginx

`/etc/nginx/sites-available/front`:

```nginx
server {
    listen 80;
    server_name up-skills.academy www.up-skills.academy;
    root /var/www/front/public;
    index index.php;

    client_max_body_size 64M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~* \.(css|js|svg|webp|png|jpg|jpeg|gif|ico|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    location ~ /\.(?!well-known) { deny all; }
}
```

`/etc/nginx/sites-available/admin` — same file with:
- `server_name admin.up-skills.academy;`
- `root /var/www/admin/public;`
- `client_max_body_size 256M;` (course video/PDF uploads go through the admin)

```bash
ln -s /etc/nginx/sites-available/front /etc/nginx/sites-enabled/
ln -s /etc/nginx/sites-available/admin /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
```

## 8. HTTPS

```bash
certbot --nginx -d up-skills.academy -d www.up-skills.academy
certbot --nginx -d admin.up-skills.academy
# auto-renewal is installed by the package; verify:
certbot renew --dry-run
```

Pick "redirect HTTP → HTTPS" when asked.

## 9. Queue worker + scheduler (both apps use them)

`QUEUE_CONNECTION=database` — emails/jobs need a worker. `/etc/systemd/system/upskills-queue@.service`:

```ini
[Unit]
Description=Upskills queue worker (%i)
After=network.target mysql.service

[Service]
User=www-data
Group=www-data
Restart=always
RestartSec=3
ExecStart=/usr/bin/php /var/www/%i/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
systemctl daemon-reload
systemctl enable --now upskills-queue@front upskills-queue@admin
```

Scheduler (front has 6 scheduled commands; admin has reminder/Zoom commands) — `sudo crontab -u www-data -e`:

```cron
* * * * * cd /var/www/front && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd /var/www/admin && php artisan schedule:run >> /dev/null 2>&1
```

## 10. Post-deploy checklist

- [ ] `https://up-skills.academy` → homepage with hero photo, courses, marquee (hard-refresh; check no mixed-content warnings in DevTools)
- [ ] `https://up-skills.academy/ar` → RTL renders, course images load (they come from the admin domain)
- [ ] `https://admin.up-skills.academy` → login page (navy/teal, Upskills logo)
- [ ] Sign up a test account → welcome email arrives via Brevo (embedded logo renders, navy/teal buttons, **no attachment**)
- [ ] Test checkout with Stripe **test keys first**, then swap to the new live keys + create the webhook endpoint (`https://up-skills.academy/stripe/webhook`) and set `STRIPE_WEBHOOK_SECRET`
- [ ] Chatbot answers as Upskills (new OpenAI key active)
- [ ] `php artisan config:cache` re-run after ANY .env edit

## 11. Backups (set up on day one)

```bash
# /etc/cron.daily/upskills-backup  (chmod +x)
#!/bin/sh
mysqldump -u upskills -pCHANGE_ME upskills | gzip > /var/backups/upskills-$(date +%F).sql.gz
tar -czf /var/backups/uploads-$(date +%F).tar.gz -C /var/www/admin/public uploads
find /var/backups -name "*.gz" -mtime +14 -delete
```

Ship them off-box (rclone to object storage) when you can.

## 12. Redeploying changes later

```bash
# local
rsync -az --delete $RSYNC_EXCLUDES front/ deploy@YOUR_VPS_IP:/var/www/front/
# vps
cd /var/www/front && composer install --no-dev --optimize-autoloader \
  && php artisan config:cache && php artisan view:cache && php artisan migrate --force \
  && sudo systemctl restart upskills-queue@front
```

(Long-term: deploy via `git pull` or CI from the GitHub repo — also gives you rollback. Remember `admin-panel/public/uploads/` and the `.env` files are not in git.)

## Troubleshooting

| Symptom | Likely cause |
|---|---|
| 500 on everything | `.env` missing/bad `APP_KEY`, or `storage/` not writable by `www-data` — check `storage/logs/laravel.log` |
| Course images broken on front (gray `410×330` placeholders) | Three possible causes, check in order: (1) `FILE_URL` wrong or `http://` (mixed content) — must be `https://admin.up-skills.academy/`; (2) the `uploads/` folder isn't on the admin server (it's gitignored — see §4) — `curl -I https://admin.up-skills.academy/uploads/products/images/course-a1.svg` should be `200`; (3) stale `config:cache` — `FILE_URL` now flows through `config('app.file_url')`, so re-run `php artisan config:cache` after deploying the fix. |
| Styles look stale | `php artisan view:cache` + hard refresh; nginx static cache is 30d — bump a query string if needed |
| Emails never arrive | SMTP creds wrong or worker down — `systemctl status upskills-queue@front`, check `jobs`/`failed_jobs` tables |
| "Please provide a valid cache path" | `storage/framework/{cache,sessions,views}` dirs missing (excluded from rsync) — `mkdir -p` them + chmod 775 |
