#!/bin/bash
# Server details come from the gitignored root .env (template: .env.example).

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
[ -f "$ROOT/.env" ] || { echo "❌ Missing $ROOT/.env — copy .env.example to .env and fill it in."; exit 1; }
set -a; . "$ROOT/.env"; set +a
: "${VPS_HOST:?set VPS_HOST in .env}" "${VPS_USER:?set VPS_USER in .env}" "${VPS_SSH_KEY:?set VPS_SSH_KEY in .env}"
REMOTE_PATH="${ADMIN_REMOTE_PATH:-/var/www/admin}"

echo "🚀 Deploying admin..."

rsync -az --delete \
  --exclude /vendor --exclude /node_modules --exclude /.env --exclude "/.env.*" \
  --exclude /storage --exclude /bootstrap/cache \
  --exclude /tests --no-group \
  -e "ssh -i $VPS_SSH_KEY" \
  "$ROOT/admin-panel/" \
  "$VPS_USER@$VPS_HOST:$REMOTE_PATH/"

rc=$?
if [ $rc -ne 0 ] && [ $rc -ne 23 ]; then
  echo "❌ rsync failed"
  exit 1
fi

echo "🔄 Fixing permissions & rebuilding caches..."
ssh -i "$VPS_SSH_KEY" "$VPS_USER@$VPS_HOST" \
  "cd $REMOTE_PATH && php artisan storage:link --force && sudo chown -R www-data:www-data public/uploads && sudo chmod -R 775 public/uploads && sudo -u www-data php artisan config:cache && sudo -u www-data php artisan view:cache"

echo "✅ Admin deployed successfully!"
