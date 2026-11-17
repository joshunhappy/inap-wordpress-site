#!/usr/bin/env bash
# Instala WordPress en el contenedor, activa el tema «inap» y deja los enlaces permanentes listos.
# Requiere: docker compose up -d (y un .env con los valores de .env.example).
set -euo pipefail
cd "$(dirname "$0")/.."
set -a; source .env; set +a

wp() { docker compose run --rm cli wp "$@"; }

until docker compose exec -T db mariadb-admin ping -u"$DB_USER" -p"$DB_PASSWORD" --silent >/dev/null 2>&1; do sleep 2; done
until docker compose exec -T wordpress test -f /var/www/html/wp-config.php; do sleep 2; done

if ! wp core is-installed >/dev/null 2>&1; then
  wp core install --url="http://localhost:${WP_PORT:-8080}" --title="INAP" \
    --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="$WP_ADMIN_EMAIL" --skip-email
fi

wp language core install es_ES --activate >/dev/null 2>&1 || true
wp option update timezone_string "America/Guatemala"
wp option update date_format "j M. Y"
wp rewrite structure "/%postname%/" --hard
wp theme activate inap
wp rewrite flush --hard
wp eval 'inap_semilla();'   # menús y numerales de los diseños, solo si aún no existen
echo "Listo: http://localhost:${WP_PORT:-8080}/transparencia/"
