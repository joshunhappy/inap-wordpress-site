#!/usr/bin/env bash
# Empaqueta el tema en dist/inap-<versión>.zip para subirlo en Apariencia › Temas › Añadir nuevo › Subir tema.
# Al activarlo crea los menús y los 29 numerales (si no existen) y activa los enlaces permanentes.
# Correr tools/sync-from-angular.sh antes si cambió el sitio de referencia.
set -euo pipefail
cd "$(dirname "$0")/.."
VERSION=$(sed -n 's/^Version: //p' wp-content/themes/inap/style.css)
mkdir -p dist
ZIP="$PWD/dist/inap-$VERSION.zip"
rm -f "$ZIP"
(cd wp-content/themes && zip -qr "$ZIP" inap -x '*.DS_Store')
echo "$ZIP ($(du -h "$ZIP" | cut -f1))"
