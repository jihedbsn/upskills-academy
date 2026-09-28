#!/bin/bash
# Run the Upskills Academy e-learning platform locally (MySQL in Docker + two Laravel apps).
# Usage: ./run.sh          # start everything
#        ./run.sh stop     # stop the two web servers (MySQL container keeps running)
# Credentials come from the gitignored root .env (template: .env.example).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FRONT="$ROOT/front"
ADMIN="$ROOT/admin-panel"

if [ "${1:-}" = "stop" ]; then
  pkill -f 'artisan serve --host=127.0.0.1 --port=8001' 2>/dev/null || true
  pkill -f 'artisan serve --host=127.0.0.1 --port=8000' 2>/dev/null || true
  echo "🛑 Stopped front (8001) and admin (8000). MySQL container left running."
  exit 0
fi

[ -f "$ROOT/.env" ] || { echo "❌ Missing $ROOT/.env — copy .env.example to .env and fill it in."; exit 1; }
set -a; . "$ROOT/.env"; set +a
: "${MYSQL_CONTAINER:=sast-mysql}" "${DB_DUMP:=sast_database.sql}"
: "${MYSQL_ROOT_PASSWORD:?set MYSQL_ROOT_PASSWORD in .env}" "${DB_DATABASE:?set DB_DATABASE in .env}"
: "${DB_USERNAME:?set DB_USERNAME in .env}" "${DB_PASSWORD:?set DB_PASSWORD in .env}"

wait_for_mysql() {
  echo "  waiting for MySQL…"
  until docker exec "$MYSQL_CONTAINER" mysqladmin ping -uroot -p"$MYSQL_ROOT_PASSWORD" --silent 2>/dev/null; do sleep 2; done
}

# 1. MySQL container — create on first run, otherwise just start it
if ! docker ps -a --format '{{.Names}}' | grep -q "^${MYSQL_CONTAINER}\$"; then
  [ -f "$ROOT/$DB_DUMP" ] || { echo "❌ First run needs the SQL dump at $ROOT/$DB_DUMP (not in git — ask the maintainer)."; exit 1; }
  echo "▶ Creating $MYSQL_CONTAINER container + importing dump (first run)…"
  docker run -d --name "$MYSQL_CONTAINER" \
    -e MYSQL_ROOT_PASSWORD="$MYSQL_ROOT_PASSWORD" \
    -e MYSQL_DATABASE="$DB_DATABASE" -e MYSQL_USER="$DB_USERNAME" -e MYSQL_PASSWORD="$DB_PASSWORD" \
    -p 127.0.0.1:3306:3306 -v sast-mysql-data:/var/lib/mysql mysql:8.0
  wait_for_mysql
  docker exec -i "$MYSQL_CONTAINER" mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$DB_DATABASE" < "$ROOT/$DB_DUMP"
  echo "  imported."
else
  docker start "$MYSQL_CONTAINER" >/dev/null
  wait_for_mysql
fi
echo "✅ MySQL up on 127.0.0.1:3306 (db: $DB_DATABASE)"

# 2. Web servers (nohup so they survive terminal close)
pkill -f 'artisan serve --host=127.0.0.1 --port=8001' 2>/dev/null || true
pkill -f 'artisan serve --host=127.0.0.1 --port=8000' 2>/dev/null || true
( cd "$FRONT" && nohup php artisan serve --host=127.0.0.1 --port=8001 > /tmp/sast-front.log 2>&1 & )
( cd "$ADMIN" && nohup php artisan serve --host=127.0.0.1 --port=8000 > /tmp/sast-admin.log 2>&1 & )
sleep 3
echo "✅ Front  → http://localhost:8001/en"
echo "✅ Admin  → http://localhost:8000/login"
echo "   logs: /tmp/sast-front.log  /tmp/sast-admin.log"
