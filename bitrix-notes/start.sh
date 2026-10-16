#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")" && pwd)"
DOCK="$(cd "$ROOT/../bitrix-bus-demo/bitrixdock" && pwd)"
WWW="$DOCK/www"
MODULES="$WWW/local/modules"
DUMP="$ROOT/docker/db/bitrix.sql"

if ! docker info >/dev/null 2>&1; then
  echo "Docker is not running"
  exit 1
fi

mkdir -p "$MODULES" "$WWW/local/api"

if [[ ! -d "$WWW/bitrix/modules/main" && ! -f "$WWW/bitrixsetup.php" ]]; then
  echo "Downloading bitrixsetup.php..."
  curl -fsSL "https://www.1c-bitrix.ru/download/scripts/bitrixsetup.php" -o "$WWW/bitrixsetup.php"
fi

echo "==> docker compose up --build"
cd "$ROOT"
docker compose up -d --build

echo "==> wait db"
ok=0
for i in $(seq 1 60); do
  status="$(docker inspect -f '{{.State.Health.Status}}' bitrix-notes-db-1 2>/dev/null || true)"
  if [[ "$status" == "healthy" ]]; then
    ok=1
    break
  fi
  sleep 2
done

if [[ "$ok" -ne 1 ]]; then
  echo "DB is not ready. Check: docker compose logs db"
  exit 1
fi

tables="$(docker compose exec -T db mysql -ubitrix -p123 -N -e 'SHOW TABLES FROM bitrix' 2>/dev/null || true)"
if [[ -z "$tables" && -f "$DUMP" ]]; then
  echo "==> import docker/db/bitrix.sql"
  docker compose exec -T db mysql -ubitrix -p123 bitrix < "$DUMP"
fi

echo ""
echo "Done."
echo "BUS:     http://localhost/"
echo "UI:      http://localhost:8081"
echo "Swagger: http://localhost:8081/docs"
echo "API:     http://localhost/api/notes"
echo "DB: host=db  db=bitrix  user=bitrix  pass=123"
