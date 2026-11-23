#!/bin/sh
set -e
cd /var/www

chown -R app:app runtime web/assets console/runtime 2>/dev/null || true

if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
  echo "Running migrations..."
  php yii migrate --interactive=0 || true
fi

exec "$@"
