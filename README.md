# INAP · sitio institucional (WordPress)

Tema `inap` del sitio institucional del Instituto Nacional de Administración Pública (Guatemala) y su entorno local.
Primer módulo: **Transparencia** (artículo 10 de la Ley de Acceso a la Información Pública, Decreto 57-2008).

## Entorno local

Requiere Docker. El sitio queda en `http://localhost:8080` (solo en esta computadora).

```bash
cp .env.example .env          # cambia las contraseñas: son solo locales
docker compose up -d
./tools/install.sh            # instala WordPress, activa el tema, crea los menús y los 29 numerales
```

Páginas:

| Ruta | Pantalla en Figma |
| --- | --- |
| `/` | Sitio institucional · Inicio (`front-page.php`) |
| `/noticias/` (`?tema=`, `?anio=`, `?q=`) | Sitio institucional · Noticias (`home.php`) |
| `/{noticia}/` | Sitio institucional · Noticia (`single-post.php`) |
| `/eventos/` (`?tipo=`, `?modalidad=`, `?q=`) | Sitio institucional · Eventos (`archive-inap_evento.php`) |
| `/eventos/{evento}/` (`?calendario=1` = archivo .ics) | Sitio institucional · Evento (`single-inap_evento.php`) |
| `/contacto/` | Sitio institucional · Contacto (`templates/contacto.php`, `inc/contacto.php`; mensajes en Contacto › Mensajes + wp_mail) |
| Dirección que no existe (404) | Sitio institucional · 404 (`404.php`; textos en Ajustes › INAP, enlaces en el menú «Enlaces útiles (página 404)») |
| `/?s=…` | Sin diseño aún: lista provisional (`index.php`) |
| `/transparencia/` | Transparencia · Inicio · Formulario |
| `/transparencia/informacion-publica-de-oficio/` | Información pública de oficio |
| `/transparencia/articulo-10/8-informes-de-ejecucion-presupuestaria/` | Numeral (meses; `?meses` lo abre) |
| `/transparencia/articulo-10/4-personal-y-remuneraciones/` | Numeral · Apartados |
| `/transparencia/articulo-10/21-fideicomisos-con-fondos-publicos/` | Numeral · Sin información |

## Instalar en un hosting

```bash
./tools/package.sh            # → dist/inap-<versión>.zip
```

En el WordPress del hosting: **Apariencia › Temas › Añadir nuevo › Subir tema**, elegir el .zip y **Activar**.
Al activarlo, el tema crea lo que falte: menús, los 29 numerales, las páginas Inicio y Noticias (portada estática),
13 noticias y 9 eventos de ejemplo (con 9 tipos de evento) con sus fotos; y activa los enlaces permanentes. Al reemplazar el tema con un .zip
de otra versión («Reemplazar el actual») lo vuelve a revisar, porque WordPress no lo reactiva.
Para una demo, escribir el aviso en Ajustes › INAP.

## Qué se edita en el administrador

Guía paso a paso para quien administra el contenido: [docs/manual-de-uso.md](docs/manual-de-uso.md).

Pensado para Comunicación Social con el rol **Editor** (sin plugins):

| Qué | Dónde |
| --- | --- |
| Textos, botones, cursos destacados, servicios y tarjetas del inicio | Página de inicio › Portada, Cursos destacados, Noticias y eventos, Fortalecimiento, Recursos (`inc/inicio.php`) |
| Noticias (entradas con otro nombre; temas = categorías) | Noticias |
| Textos de /noticias/ y de cada noticia (pestañas, destacadas, compartir) | Noticias › Página de noticias (`inc/noticias.php`) |
| Mensajes del formulario de contacto; textos, temas y destinatario de /contacto/ | Contacto › Mensajes / Contacto › Página de contacto (`inc/contacto.php`) |
| Eventos (tipo `inap_evento`: fecha, horas, modalidad, lugar, transmisión, costo, inscripción; `inc/eventos.php`) | Eventos |
| Tipos de evento (taxonomía `inap_tipo_evento`) | Eventos › Tipos |
| Textos de /eventos/ y de cada evento | Eventos › Página de eventos (`inc/eventos.php`) |
| Barra superior, mega menús, menú de celular, pie y redes | Apariencia › Menús (5 menús, incluido «Enlaces útiles (página 404)»; la estructura está al inicio de `inc/menus.php`) |
| Teléfono, correo y dirección generales; enlaces a la plataforma de cursos y al Aula virtual; búsquedas populares del buscador | Ajustes › INAP |
| Numerales del artículo 10: meses, apartados, enlaces de OneDrive, avisos | Transparencia › Numerales |
| Textos de las páginas de Transparencia, año, datos de la Unidad de Información Pública | Transparencia › Textos y ajustes |

Las etiquetas del formulario de solicitud y los textos de accesibilidad quedan en el código.
Mientras el tema está activo, el Editor también puede editar menús (`edit_theme_options`); no se cambian los roles guardados.

## CSS y piezas compartidas

El CSS no se escribe aquí: sale del sistema de diseño en `../angular/inap` (`npm run build:css`), que también
lee las plantillas de este tema para generar sus clases. La barra, los mega menús, el menú de celular y el pie
usan el mismo HTML que los ejemplos del sitio de referencia: `wordpressTemplates()` genera plantillas con
huecos (`{{titulo}}`, `{{url}}`…) que el tema llena con los menús y ajustes; `wordpressMenus()` da el contenido
inicial de los menús (`parts/menus.json`).

```bash
./tools/sync-from-angular.sh  # después de cambiar plantillas del tema o componentes del sitio de referencia
```

Copia en el tema: `assets/css/inap.css`, `parts/*`, el sprite de Lucide, logos y la fuente Montserrat.
`assets/css/tema.css`, `assets/js/sitio.js`, `assets/js/buscador.js` (sugerencias del buscador; datos de `inc/buscador.php` y de `GET /wp-json/inap/v1/sugerencias?q=`), `assets/js/admin-numeral.js` y `assets/js/admin-ajustes.js` (selector de fotos de Medios) son propios del tema.

## Datos de Transparencia

Al instalar, los numerales se crean con los **datos de ejemplo** de Figma (`inc/transparencia-ejemplo.php`:
fechas inventadas, enlaces vacíos). Los documentos se quedan en el OneDrive de la Unidad de Información Pública:
cada fila abre una carpeta. Las fechas de «Actualizado el…» y los resúmenes se calculan con las filas.
Pendiente de definir con la Unidad quién pega cada mes los enlaces y quién los valida.

El formulario de solicitud valida los datos pero **no envía ni guarda nada**: falta definir cómo se reciben.
