#!/usr/bin/env bash
# Trae del sitio de referencia (angular/inap) todo lo que el tema comparte con él:
#   - CSS compilado (npm run build:css → dist/wordpress/inap.css), que también lee las plantillas de este tema
#   - plantillas de la barra, mega menús, menú de celular y pie (wordpressTemplates()) y el contenido
#     inicial de los menús (wordpressMenus()), con el mismo HTML de los ejemplos
#   - sprite de íconos Lucide, logos y ondas de marca, fuente Montserrat
# Correr después de cambiar plantillas del tema o componentes del sitio de referencia.
set -euo pipefail
cd "$(dirname "$0")/.."
ROOT="$PWD"
ANGULAR="${ANGULAR_DIR:-$ROOT/../angular/inap}"
THEME="$ROOT/wp-content/themes/inap"

[ -s "$HOME/.nvm/nvm.sh" ] && { source "$HOME/.nvm/nvm.sh"; nvm use 24 >/dev/null; }

mkdir -p "$THEME/assets/css" "$THEME/assets/lucide" "$THEME/assets/brand" "$THEME/assets/fonts" "$THEME/parts"

# Piezas de HTML compartidas
(cd "$ANGULAR" && npx esbuild "$ROOT/tools/render-partials.ts" --bundle --platform=node --format=esm \
  --log-level=warning --outfile="$ROOT/tools/.render-partials.mjs")
node "$ROOT/tools/.render-partials.mjs" "$THEME/parts"
rm -f "$ROOT/tools/.render-partials.mjs"

# CSS (después de las piezas, para que Tailwind vea sus clases)
(cd "$ANGULAR" && npm run --silent build:css)
cp "$ANGULAR/dist/wordpress/inap.css" "$THEME/assets/css/inap.css"

cp "$ANGULAR/node_modules/lucide-static/sprite.svg" "$THEME/assets/lucide/sprite.svg"
cp "$ANGULAR/public/brand/"*.svg "$THEME/assets/brand/"
cp "$ANGULAR/node_modules/@fontsource-variable/montserrat/files/montserrat-latin-wght-normal.woff2" \
   "$ANGULAR/node_modules/@fontsource-variable/montserrat/files/montserrat-latin-ext-wght-normal.woff2" \
   "$THEME/assets/fonts/"
echo "Tema sincronizado con $ANGULAR"
