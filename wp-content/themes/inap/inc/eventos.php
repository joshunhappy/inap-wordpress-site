<?php
/**
 * Eventos: tipo de contenido propio, /eventos/ (archive-inap_evento.php) y cada evento (single-inap_evento.php).
 *
 *   Eventos › Todos los eventos     cada evento: fecha, hora, modalidad, lugar, transmisión, costo, inscripción
 *   Eventos › Tipos                 Foro, Conversatorio, Taller… (filtro «Tipo de evento» y etiqueta de cada fila)
 *   Eventos › Página de eventos     textos de la página y de cada evento
 *
 * Diseño: Figma «Sitio institucional • Desktop/Mobile • Light • Eventos / Evento» (939:26935, 940:27236).
 */

defined('ABSPATH') || exit;

const INAP_EVENTO = 'inap_evento';
const INAP_EVENTO_TIPO = 'inap_tipo_evento';
const INAP_EVENTOS_POR_PAGINA = 10;
const INAP_DIAS = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
const INAP_MODALIDADES = ['presencial' => 'Presencial', 'en-linea' => 'En línea', 'hibrido' => 'Presencial y en línea'];

/* ── Tipo de contenido y tipos de evento ──────────────────────────────────── */

add_action('init', function () {
    register_post_type(INAP_EVENTO, [
        'labels' => [
            'name' => 'Eventos',
            'singular_name' => 'Evento',
            'menu_name' => 'Eventos',
            'all_items' => 'Todos los eventos',
            'add_new' => 'Agregar evento',
            'add_new_item' => 'Agregar evento',
            'edit_item' => 'Editar evento',
            'view_item' => 'Ver evento',
            'search_items' => 'Buscar eventos',
            'not_found' => 'No hay eventos.',
            'item_published' => 'Evento publicado.',
            'item_updated' => 'Evento actualizado.',
            'featured_image' => 'Foto del evento',
            'set_featured_image' => 'Elegir foto',
            'remove_featured_image' => 'Quitar foto',
            'use_featured_image' => 'Usar como foto del evento',
        ],
        'public' => true,
        'has_archive' => 'eventos',
        'rewrite' => ['slug' => 'eventos', 'with_front' => false],
        'show_in_rest' => false,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-calendar-alt',
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
    ]);

    // Con casillas (como los Temas de las noticias): se marca uno por evento.
    register_taxonomy(INAP_EVENTO_TIPO, INAP_EVENTO, [
        'labels' => [
            'name' => 'Tipos de evento',
            'singular_name' => 'Tipo de evento',
            'menu_name' => 'Tipos',
            'all_items' => 'Todos los tipos',
            'edit_item' => 'Editar tipo',
            'add_new_item' => 'Agregar tipo',
            'new_item_name' => 'Nombre del tipo',
            'search_items' => 'Buscar tipos',
            'not_found' => 'No hay tipos.',
            'back_to_items' => '← Volver a los tipos',
        ],
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_rest' => false,
        'rewrite' => false,
        'query_var' => false,
        'capabilities' => ['manage_terms' => INAP_CAP, 'edit_terms' => INAP_CAP, 'delete_terms' => INAP_CAP, 'assign_terms' => 'edit_posts'],
    ]);
});

add_filter('enter_title_here', fn ($t, $post) => $post->post_type === INAP_EVENTO ? 'Nombre del evento' : $t, 10, 2);

/* ── Datos del evento (caja en el editor) ─────────────────────────────────── */

/** Campos guardados como _inap_{clave}: [clave, etiqueta, tipo, ayuda]. */
const INAP_EVENTO_CAMPOS = [
    ['fecha', 'Fecha (obligatoria)', 'date', 'Cuando la fecha pasa, el evento se mueve solo a «Eventos pasados».'],
    ['hora', 'Hora de inicio', 'time', 'Vacía si el evento dura todo el día.'],
    ['hora_fin', 'Hora de fin', 'time', 'Opcional. Se lee «8:30 a 13:00».'],
    ['modalidad', 'Modalidad', 'modalidad', 'Para el filtro «Modalidad»: «Presencial y en línea» sale con los dos.'],
    ['lugar', 'Lugar (corto)', 'text', 'Se ve en las listas, junto a la hora. Por ejemplo: Sede central, zona 10 · En línea.'],
    ['direccion', 'Lugar (completo)', 'text', 'Opcional. Se ve en la página del evento en lugar del corto. Por ejemplo: Sede central del INAP, Boulevard Los Próceres 16-40, zona 10.'],
    ['transmision', 'Transmisión', 'text', 'Opcional. Por ejemplo: En vivo por Facebook y YouTube del INAP.'],
    ['costo', 'Costo', 'text', 'Opcional. Por ejemplo: Gratuito, con inscripción previa (cupo de 200 personas).'],
    ['inscripcion', 'Enlace de inscripción', 'url', 'Opcional. Dirección completa (https://…), normalmente la del curso o evento en la plataforma de cursos. Vacío = sin botón «Inscribirme».'],
];

add_action('add_meta_boxes_' . INAP_EVENTO, function () {
    add_meta_box('inap-evento', 'Datos del evento', function (WP_Post $post) {
        wp_nonce_field('inap_evento', '_inap_evento');
        echo '<table class="form-table" role="presentation"><tbody>';
        foreach (INAP_EVENTO_CAMPOS as [$k, $etiqueta, $tipo, $ayuda]) {
            $id = 'inap-' . $k;
            $v = (string) get_post_meta($post->ID, "_inap_{$k}", true);
            echo '<tr><th scope="row"><label for="' . $id . '">' . esc_html($etiqueta) . '</label></th><td>';
            if ($tipo === 'modalidad') {
                echo '<select id="' . $id . '" name="inap_' . $k . '" aria-describedby="' . $id . '-ayuda"><option value="">—</option>';
                foreach (INAP_MODALIDADES as $valor => $texto) {
                    echo '<option value="' . esc_attr($valor) . '"' . selected($v, $valor, false) . '>' . esc_html($texto) . '</option>';
                }
                echo '</select>';
            } else {
                $clase = in_array($tipo, ['text', 'url'], true) ? ' class="large-text"' : '';
                echo '<input id="' . $id . '" name="inap_' . $k . '" type="' . $tipo . '" value="' . esc_attr($v) . '"' . $clase
                    . ($k === 'fecha' ? ' required' : '') . ' aria-describedby="' . $id . '-ayuda" />';
            }
            echo '<p id="' . $id . '-ayuda" class="description">' . esc_html($ayuda) . '</p></td></tr>';
        }
        echo '</tbody></table>';
        echo '<p class="description">El <strong>Tipo de evento</strong> se marca en la caja «Tipos de evento»; la <strong>Foto del evento</strong>, en su caja; la entradilla, en <strong>Extracto</strong>.</p>';
    }, INAP_EVENTO, 'normal', 'high');
});

add_action('save_post_' . INAP_EVENTO, function ($post_id) {
    if (!wp_verify_nonce($_POST['_inap_evento'] ?? '', 'inap_evento') || !current_user_can('edit_post', $post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    foreach (INAP_EVENTO_CAMPOS as [$k, , $tipo]) {
        $v = trim(sanitize_text_field(wp_unslash($_POST["inap_{$k}"] ?? '')));
        $v = match ($tipo) {
            'date' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : '',
            'time' => preg_match('/^\d{2}:\d{2}$/', $v) ? $v : '',
            'modalidad' => isset(INAP_MODALIDADES[$v]) ? $v : '',
            'url' => esc_url_raw($v),
            default => $v,
        };
        update_post_meta($post_id, "_inap_{$k}", $v);
    }
});

add_filter('manage_' . INAP_EVENTO . '_posts_columns', fn ($cols) => array_slice($cols, 0, 2, true) + ['inap_fecha' => 'Fecha del evento', 'inap_lugar' => 'Lugar'] + array_slice($cols, 2, null, true));

add_action('manage_' . INAP_EVENTO . '_posts_custom_column', function ($col, $post_id) {
    $e = inap_evento_datos(get_post($post_id));
    echo esc_html(match ($col) {
        'inap_fecha' => $e['fecha_larga'] ? $e['fecha_larga'] . ($e['pasado'] ? ' (ya pasó)' : '') : '— sin fecha —',
        'inap_lugar' => $e['lugar'],
        default => '',
    });
}, 10, 2);

/* ── Datos para las plantillas ────────────────────────────────────────────── */

/** «08:30» → «8:30». */
function inap_hora(string $h): string
{
    return $h !== '' ? ltrim(substr($h, 0, 1), '0') . substr($h, 1) : '';
}

function inap_evento_datos(WP_Post $p): array
{
    $m = fn (string $k) => (string) get_post_meta($p->ID, "_inap_{$k}", true);
    $iso = $m('fecha');
    [$a, $mes, $d] = array_map('intval', explode('-', $iso . '--')) + [0, 0, 0];
    $ts = $mes ? mktime(12, 0, 0, $mes, $d, $a) : 0;
    $hora = inap_hora($m('hora'));
    $fin = inap_hora($m('hora_fin'));
    $lugar = $m('lugar');
    $modalidad = $m('modalidad');
    $transmision = $m('transmision');
    $tipo = get_the_terms($p, INAP_EVENTO_TIPO);
    $tipo = is_array($tipo) ? $tipo[0] : null;
    $imagen = (int) get_post_thumbnail_id($p);

    return [
        'id' => $p->ID,
        'titulo' => get_the_title($p),
        'url' => get_permalink($p),
        'iso' => $iso,
        'pasado' => $iso !== '' && $iso < wp_date('Y-m-d'),
        // Día sin cero inicial («7 DIC») en el inicio y en Eventos.
        'dia' => $d ? (string) $d : '',
        'mes' => $mes ? ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'][$mes - 1] : '',
        'mes_anio' => $mes ? ucfirst(INAP_MESES[$mes - 1]) . " {$a}" : '',
        'fecha_larga' => $mes ? sprintf('%d de %s de %d', $d, INAP_MESES[$mes - 1], $a) : '',
        'fecha_completa' => $mes ? ucfirst(INAP_DIAS[(int) date('w', $ts)]) . sprintf(' %d de %s de %d', $d, INAP_MESES[$mes - 1], $a) : '',
        'hora' => $hora,
        'horario' => $hora !== '' && $fin !== '' ? "{$hora} a {$fin}" : $hora,
        'lugar' => $lugar,
        'lugar_completo' => $m('direccion') ?: $lugar,
        'meta' => implode(' · ', array_filter([$hora, $lugar])),
        'modalidad' => $modalidad,
        'modalidad_texto' => match ($modalidad) {
            'en-linea' => 'Evento en línea',
            'hibrido' => 'Evento presencial y en línea',
            'presencial' => $transmision !== '' ? 'Evento presencial con transmisión en vivo' : 'Evento presencial',
            default => '',
        },
        'transmision' => $transmision,
        'costo' => $m('costo'),
        'inscripcion' => $m('inscripcion'),
        'tipo' => $tipo?->name ?? '',
        'tipo_slug' => $tipo?->slug ?? '',
        'resumen' => has_excerpt($p) ? get_the_excerpt($p) : '',
        'imagen_id' => $imagen,
    ];
}

/* ── Filtros y consultas ──────────────────────────────────────────────────── */

/** Filtros de la URL: ?tipo=slug&modalidad=presencial|en-linea&q=palabras. */
function inap_eventos_filtros(): array
{
    $modalidad = sanitize_key(wp_unslash($_GET['modalidad'] ?? ''));
    return [
        'tipo' => sanitize_title(wp_unslash($_GET['tipo'] ?? '')),
        'modalidad' => in_array($modalidad, ['presencial', 'en-linea'], true) ? $modalidad : '',
        'q' => trim(sanitize_text_field(wp_unslash($_GET['q'] ?? ''))),
    ];
}

function inap_eventos_con_filtros(): bool
{
    return (bool) array_filter(inap_eventos_filtros());
}

/** Argumentos de WP_Query: próximos (desde hoy, del más cercano) o pasados (del más reciente), con los filtros. */
function inap_eventos_args(bool $proximos, ?array $f = null): array
{
    $f ??= inap_eventos_filtros();
    $meta = [
        'relation' => 'AND',
        ['key' => '_inap_fecha', 'value' => wp_date('Y-m-d'), 'compare' => $proximos ? '>=' : '<', 'type' => 'DATE'],
    ];
    if ($f['modalidad'] !== '') {
        // «Presencial y en línea» sale con las dos opciones.
        $meta[] = ['key' => '_inap_modalidad', 'value' => [$f['modalidad'], 'hibrido'], 'compare' => 'IN'];
    }
    $args = [
        'post_type' => INAP_EVENTO,
        'post_status' => 'publish',
        'meta_key' => '_inap_fecha',
        'orderby' => ['meta_value' => $proximos ? 'ASC' : 'DESC', 'date' => 'ASC'],
        'meta_query' => $meta,
    ];
    if ($f['tipo'] !== '') {
        $args['tax_query'] = [['taxonomy' => INAP_EVENTO_TIPO, 'field' => 'slug', 'terms' => $f['tipo']]];
    }
    if ($f['q'] !== '') {
        $args['s'] = $f['q'];
    }
    return $args;
}

/** Los próximos eventos (desde hoy), del más cercano al más lejano. $excluir = id del evento abierto. */
function inap_eventos_proximos(int $cuantos = 100, bool $con_filtros = false, int $excluir = 0): array
{
    $args = inap_eventos_args(true, $con_filtros ? null : ['tipo' => '', 'modalidad' => '', 'q' => '']);
    $posts = get_posts($args + ['numberposts' => $cuantos, 'post__not_in' => $excluir ? [$excluir] : [], 'suppress_filters' => false]);
    return array_map('inap_evento_datos', $posts);
}

/** Tipos con al menos un evento publicado, para el filtro. */
function inap_eventos_tipos(): array
{
    $tipos = get_terms(['taxonomy' => INAP_EVENTO_TIPO, 'hide_empty' => true]);
    return is_array($tipos) ? $tipos : [];
}

/*
  Lista principal de /eventos/ = eventos pasados, con paginación. Los próximos van arriba, en su propia consulta.
  En el administrador: por fecha del evento, el más lejano arriba.
*/
add_action('pre_get_posts', function (WP_Query $q) {
    if (!$q->is_main_query()) {
        return;
    }
    if (is_admin() && $q->get('post_type') === INAP_EVENTO && !$q->get('orderby')) {
        $q->set('meta_key', '_inap_fecha');
        $q->set('orderby', 'meta_value');
        $q->set('order', 'DESC');
        return;
    }
    if (!is_admin() && $q->is_post_type_archive(INAP_EVENTO)) {
        foreach (inap_eventos_args(false) as $k => $v) {
            $q->set($k, $v);
        }
        $q->set('posts_per_page', INAP_EVENTOS_POR_PAGINA);
    }
});

/* ── Agregar al calendario (.ics) ─────────────────────────────────────────── */

/** Dirección del archivo .ics de un evento. */
function inap_evento_ics_url(int $id): string
{
    return add_query_arg('calendario', '1', get_permalink($id));
}

/** Archivo iCalendar (RFC 5545) del evento. Sin hora: evento de todo el día; sin hora de fin: dura una hora. */
function inap_evento_ics(WP_Post $p): string
{
    $e = inap_evento_datos($p);
    $tz = wp_timezone();
    $utc = new DateTimeZone('UTC');
    $esc = fn (string $s) => str_replace(["\\", ';', ',', "\r\n", "\n"], ["\\\\", '\;', '\,', '\n', '\n'], $s);
    $hora = (string) get_post_meta($p->ID, '_inap_hora', true);
    $fin = (string) get_post_meta($p->ID, '_inap_hora_fin', true);

    if ($hora !== '') {
        $ini = new DateTimeImmutable("{$e['iso']} {$hora}", $tz);
        $termina = $fin !== '' && $fin > $hora ? new DateTimeImmutable("{$e['iso']} {$fin}", $tz) : $ini->modify('+1 hour');
        $cuando = ['DTSTART:' . $ini->setTimezone($utc)->format('Ymd\THis\Z'), 'DTEND:' . $termina->setTimezone($utc)->format('Ymd\THis\Z')];
    } else {
        $dia = new DateTimeImmutable($e['iso'], $tz);
        $cuando = ['DTSTART;VALUE=DATE:' . $dia->format('Ymd'), 'DTEND;VALUE=DATE:' . $dia->modify('+1 day')->format('Ymd')];
    }

    $lineas = array_merge(
        ['BEGIN:VCALENDAR', 'VERSION:2.0', 'PRODID:-//INAP//Sitio institucional//ES', 'CALSCALE:GREGORIAN', 'METHOD:PUBLISH', 'BEGIN:VEVENT',
            'UID:evento-' . $p->ID . '@' . wp_parse_url(home_url(), PHP_URL_HOST),
            'DTSTAMP:' . gmdate('Ymd\THis\Z')],
        $cuando,
        ['SUMMARY:' . $esc(wp_strip_all_tags(html_entity_decode($e['titulo'], ENT_QUOTES, 'UTF-8')))],
        $e['lugar_completo'] !== '' ? ['LOCATION:' . $esc($e['lugar_completo'])] : [],
        ['DESCRIPTION:' . $esc(trim($e['resumen'] . "\n\n" . $e['url'])), 'URL:' . $e['url'], 'END:VEVENT', 'END:VCALENDAR']
    );

    // Líneas de máximo 75 bytes; las siguientes empiezan con un espacio.
    $plegar = function (string $l): string {
        $out = '';
        while (strlen($l) > 75) {
            $corte = 75;
            while ($corte > 0 && (ord($l[$corte]) & 0xC0) === 0x80) {
                $corte--; // no partir un carácter UTF-8
            }
            $out .= substr($l, 0, $corte) . "\r\n ";
            $l = substr($l, $corte);
        }
        return $out . $l;
    };
    return implode("\r\n", array_map($plegar, $lineas)) . "\r\n";
}

add_action('template_redirect', function () {
    if (!isset($_GET['calendario']) || !is_singular(INAP_EVENTO)) {
        return;
    }
    $p = get_queried_object();
    if (!$p instanceof WP_Post || get_post_meta($p->ID, '_inap_fecha', true) === '') {
        return;
    }
    nocache_headers();
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . sanitize_file_name($p->post_name ?: 'evento') . '.ics"');
    echo inap_evento_ics($p);
    exit;
});

/* ── Textos editables ─────────────────────────────────────────────────────── */

add_filter('inap_ajustes_grupos', function (array $grupos) {
    $grupos['inap_eventos'] = [
        'pagina' => 'Página de eventos',
        'intro' => 'Textos de la página Eventos y de cada evento. Los eventos se escriben en Eventos › Agregar evento; las pestañas (Noticias · Eventos · Multimedia) y «Compartir» se editan en Noticias › Página de noticias.',
        'ver' => '/eventos/',
        'secciones' => [
            'Encabezado' => [
                'campos' => [
                    ['titulo', 'Título', 'text', 'Eventos', 'También es el nombre en el breadcrumb.'],
                    ['descripcion', 'Descripción', 'textarea', 'Foros, conversatorios, talleres, graduaciones y otras actividades abiertas del INAP.', ''],
                ],
            ],
            'Próximos eventos' => [
                'intro' => 'Todos los eventos desde hoy, agrupados por mes, con los filtros «Tipo de evento», «Modalidad» y «Buscar en eventos».',
                'campos' => [
                    ['proximos_titulo', 'Título', 'text', 'Próximos eventos', ''],
                    ['proximos_orden', 'Texto junto al total', 'text', 'del más cercano al más lejano', 'Se lee «5 eventos · del más cercano al más lejano».'],
                    ['proximos_vacio', 'Texto cuando no hay eventos', 'text', 'No hay eventos programados por ahora. Vuelve pronto o revisa los eventos pasados.', ''],
                    ['filtros_vacio', 'Texto cuando los filtros no encuentran nada', 'text', 'No hay eventos con estos filtros. Prueba con otro tipo, otra modalidad u otras palabras.', ''],
                ],
            ],
            'Eventos pasados' => [
                'intro' => 'Los eventos cuya fecha ya pasó, del más reciente al más antiguo, ' . INAP_EVENTOS_POR_PAGINA . ' por página.',
                'campos' => [
                    ['pasados_mostrar', 'Mostrar', 'checkbox', '1', 'Quita la marca para mostrar solo los próximos.'],
                    ['pasados_titulo', 'Título', 'text', 'Eventos pasados', ''],
                    ['pasados_orden', 'Texto junto al total', 'text', 'del más reciente al más antiguo', ''],
                ],
            ],
            'Filas de la lista' => [
                'campos' => [
                    ['ver_texto', 'Texto a la derecha de cada fila', 'text', 'Ver evento', 'Solo en computadora. Toda la fila lleva al evento.'],
                ],
            ],
            'Cada evento' => [
                'campos' => [
                    ['inscribirme_texto', 'Botón de inscripción', 'text', 'Inscribirme', 'Solo sale si el evento tiene «Enlace de inscripción» y aún no pasó.'],
                    ['calendario_texto', 'Botón del calendario', 'text', 'Agregar al calendario', 'Descarga el evento para Google Calendar, Outlook o el calendario del celular.'],
                    ['pasado_texto', 'Aviso en un evento que ya pasó', 'text', 'Este evento ya se realizó.', 'En lugar de los botones.'],
                    ['otros_mostrar', 'Mostrar «Otros próximos eventos»', 'checkbox', '1', ''],
                    ['otros_titulo', 'Título de otros eventos', 'text', 'Otros próximos eventos', 'Los 3 próximos, sin el evento abierto.'],
                    ['todos_texto', 'Enlace a la lista', 'text', 'Ver todos los eventos', ''],
                ],
            ],
        ],
    ];
    return $grupos;
});

add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=' . INAP_EVENTO, 'Página de eventos', 'Página de eventos', INAP_CAP, 'inap_eventos', fn () => inap_ajustes_pagina('inap_eventos'));
});

function inap_eventos_txt(string $clave): string
{
    return inap_ajuste('inap_eventos', $clave);
}

/* ── Fila de evento (Event Row) ───────────────────────────────────────────── */

/**
 * Toda la fila es un enlace (el del título se estira sobre ella); la fecha también va en texto.
 * $columnas: tipo y «Ver evento» a la derecha (solo en computadora). $con_hora: los pasados muestran solo el lugar.
 */
function inap_evento_fila(array $e, string $h = 'h3', bool $columnas = true, bool $con_hora = true): string
{
    $meta = $con_hora ? $e['meta'] : $e['lugar'];
    $html = '<li class="group relative flex items-center gap-6 border-b border-base-border ' . ($columnas ? 'py-5' : 'py-4')
        . ' has-[a:focus-visible]:outline-2 has-[a:focus-visible]:outline-offset-2 has-[a:focus-visible]:outline-primary-text">'
        . '<div class="flex min-w-0 flex-1 items-center gap-4">'
        . '<span aria-hidden="true" class="flex h-15 w-14 shrink-0 flex-col items-center justify-center rounded-lg bg-surface-subtle py-2 text-center font-semibold text-primary-text">'
        . '<span class="text-2xl">' . esc_html($e['dia']) . '</span><span class="text-xs">' . esc_html($e['mes']) . '</span></span>'
        . '<div class="flex min-w-0 flex-1 flex-col gap-1">'
        . "<{$h} class=\"text-base leading-6 font-semibold text-base-content group-hover:text-primary-text\">"
        . '<a href="' . esc_url($e['url']) . '" class="after:absolute after:inset-0 focus-visible:outline-none">' . esc_html($e['titulo']) . '</a>'
        . "</{$h}>"
        . '<p class="text-sm text-neutral-text"><time datetime="' . esc_attr($e['iso']) . '" class="sr-only">' . esc_html($e['fecha_larga']) . ($meta !== '' ? ', ' : '') . '</time>' . esc_html($meta) . '</p>'
        . '</div></div>';
    if ($columnas) {
        $html .= '<div class="hidden w-45 shrink-0 lg:flex">'
            . ($e['tipo'] !== '' ? '<span class="badge badge-soft badge-primary text-xs font-semibold text-primary-text">' . esc_html($e['tipo']) . '</span>' : '')
            . '</div>'
            . '<span aria-hidden="true" class="hidden text-sm font-semibold text-primary-text group-hover:underline lg:block">' . esc_html(inap_eventos_txt('ver_texto')) . '</span>';
    }
    return $html . '</li>';
}

/* ── Contenido de ejemplo ─────────────────────────────────────────────────── */

/**
 * Una sola vez: tipos de evento, los datos nuevos (modalidad, transmisión, costo…) en los 3 eventos de ejemplo,
 * 2 próximos y 4 pasados más (los del diseño), y el texto completo del Foro.
 */
function inap_semilla_eventos(): void
{
    if (get_option('inap_semilla_eventos')) {
        return;
    }
    update_option('inap_semilla_eventos', INAP_VERSION, false);

    $tipo = function (string $nombre): int {
        $t = term_exists($nombre, INAP_EVENTO_TIPO) ?: wp_insert_term($nombre, INAP_EVENTO_TIPO);
        return is_array($t) ? (int) $t['term_id'] : 0;
    };
    foreach (['Foro', 'Conversatorio', 'Taller', 'Graduación', 'Presentación', 'Webinar', 'Lanzamiento', 'Encuentro', 'Transmisión'] as $nombre) {
        $tipo($nombre);
    }

    $plataforma = rtrim(inap_ajuste('inap_ajustes', 'plataforma_url'), '/');
    // [días desde hoy, hora, fin, modalidad, lugar, tipo, título, extras]
    $eventos = [
        [4, '08:30', '13:00', 'presencial', 'Sede central, zona 10', 'Foro', 'Foro de la Función Pública 2026', [
            'direccion' => 'Sede central del INAP, Boulevard Los Próceres 16-40, zona 10',
            'transmision' => 'En vivo por Facebook y YouTube del INAP',
            'costo' => 'Gratuito, con inscripción previa (cupo de 200 personas)',
            'inscripcion' => $plataforma,
        ]],
        [8, '15:00', '', 'en-linea', 'En línea', 'Conversatorio', 'Conversatorio: datos abiertos en las municipalidades', []],
        [12, '18:00', '', 'en-linea', 'Transmisión en vivo', 'Transmisión', 'Con Enfoque Público: gestión por resultados', []],
        [19, '10:00', '', 'presencial', 'Sede central, zona 10', 'Graduación', 'Graduación del Diplomado en Gerencia Pública', []],
        [25, '11:00', '', 'hibrido', 'Sede central y en línea', 'Presentación', 'Presentación de la Agenda de servicios 2027', []],
        [-9, '', '', 'en-linea', 'En línea', 'Webinar', 'Webinar: ética y transparencia en la función pública', []],
        [-15, '', '', 'presencial', 'Sede central, zona 10', 'Lanzamiento', 'Inauguración del programa Transformación digital e IA', []],
        [-22, '', '', 'presencial', 'Antigua Guatemala', 'Encuentro', 'Encuentro de enlaces institucionales de formación', []],
        [-29, '', '', 'en-linea', 'En línea', 'Conversatorio', 'Conversatorio: compras públicas transparentes', []],
    ];
    $ejemplo = '<!-- wp:paragraph --><p>Evento de ejemplo. Comunicación Social lo reemplaza o lo borra cuando publique los eventos reales.</p><!-- /wp:paragraph -->';

    foreach ($eventos as [$dias, $hora, $fin, $modalidad, $lugar, $nombre_tipo, $titulo, $extras]) {
        $existente = get_posts(['post_type' => INAP_EVENTO, 'title' => $titulo, 'post_status' => 'any', 'numberposts' => 1]);
        if ($existente) {
            $id = $existente[0]->ID;
            if (get_post_meta($id, '_inap_ejemplo', true) !== '1') {
                continue; // ya es un evento real: no se toca
            }
        } else {
            $id = wp_insert_post(['post_type' => INAP_EVENTO, 'post_status' => 'publish', 'post_title' => $titulo, 'post_content' => $ejemplo]);
            if (!$id || is_wp_error($id)) {
                continue;
            }
            update_post_meta($id, '_inap_fecha', wp_date('Y-m-d', strtotime("{$dias} days")));
            update_post_meta($id, '_inap_hora', $hora);
            update_post_meta($id, '_inap_lugar', $lugar);
            update_post_meta($id, '_inap_ejemplo', '1');
        }
        update_post_meta($id, '_inap_hora_fin', $fin);
        update_post_meta($id, '_inap_modalidad', $modalidad);
        foreach ($extras as $k => $v) {
            update_post_meta($id, "_inap_{$k}", $v);
        }
        wp_set_object_terms($id, $tipo($nombre_tipo), INAP_EVENTO_TIPO);
    }

    // El Foro con el texto completo del diseño, su entradilla y una foto con pie (solo si sigue siendo el de ejemplo).
    $foro = get_posts(['post_type' => INAP_EVENTO, 'title' => 'Foro de la Función Pública 2026', 'numberposts' => 1])[0] ?? null;
    if ($foro && get_post_meta($foro->ID, '_inap_ejemplo', true) === '1' && str_contains($foro->post_content, 'Evento de ejemplo.')) {
        $p = fn (string $t) => "<!-- wp:paragraph -->\n<p>{$t}</p>\n<!-- /wp:paragraph -->";
        $h2 = fn (string $t) => "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
        $li = fn (array $l) => "<!-- wp:list -->\n<ul class=\"wp-block-list\">" . implode('', array_map(fn ($x) => "<!-- wp:list-item --><li>{$x}</li><!-- /wp:list-item -->", $l)) . "</ul>\n<!-- /wp:list -->";
        wp_update_post([
            'ID' => $foro->ID,
            'post_excerpt' => 'Un encuentro para conversar sobre la profesionalización del servicio civil y la atención a la ciudadanía.',
            'post_content' => implode("\n\n", [
                $p('Texto de ejemplo. El Foro de la Función Pública reúne a autoridades, especialistas y servidores públicos para conversar sobre los retos del servicio civil en Guatemala.'),
                $p('Esta edición se centra en la profesionalización del servicio público y en el uso de herramientas digitales para mejorar la atención a la ciudadanía.'),
                $h2('Agenda'),
                $li(['9:00 Inauguración y bienvenida', '9:30 Panel: profesionalización del servicio civil', '11:00 Mesas de trabajo por región', '12:30 Conclusiones y cierre']),
                $h2('Dirigido a'),
                $p('Servidores públicos de todas las instituciones del Estado y público interesado. La transmisión en vivo es abierta y no requiere inscripción.'),
            ]),
        ]);
        $foto = get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_inap_ejemplo', 'meta_value' => '1', 'orderby' => 'ID', 'order' => 'ASC', 'numberposts' => 1])[0] ?? null;
        if ($foto && !has_post_thumbnail($foro->ID)) {
            set_post_thumbnail($foro->ID, $foto->ID);
        }
    }
}
