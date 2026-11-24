<?php
/**
 * Noticias: página /noticias/ (home.php) y cada noticia (single-post.php).
 * Textos de la página en Noticias › Página de noticias; las noticias son entradas de WordPress.
 */

const INAP_NOTICIAS_POR_PAGINA = 9;
const INAP_MESES_CORTOS = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];

/* ── Textos editables ─────────────────────────────────────────────────────── */

add_filter('inap_ajustes_grupos', function (array $grupos) {
    $grupos['inap_noticias'] = [
        'pagina' => 'Página de noticias',
        'intro' => 'Textos de la página Noticias y de cada noticia. Las noticias se escriben en Noticias › Agregar noticia.',
        'ver' => '/noticias/',
        'secciones' => [
            'Encabezado' => [
                'campos' => [
                    ['titulo', 'Título', 'text', 'Noticias', 'También es el nombre en el breadcrumb.'],
                    ['descripcion', 'Descripción', 'textarea', 'Lo más reciente del INAP en formación, fortalecimiento institucional y vida institucional.', ''],
                ],
            ],
            'Pestañas' => [
                'intro' => 'Enlaces a las secciones hermanas, debajo del encabezado. Una pestaña sin dirección no se muestra.',
                'campos' => [
                    ['pestanas_mostrar', 'Mostrar', 'checkbox', '1', 'Quita la marca para ocultar las pestañas.'],
                    ['eventos_texto', 'Eventos: texto', 'text', 'Eventos', ''],
                    ['eventos_url', 'Eventos: dirección', 'text', '/eventos/', ''],
                    ['multimedia_texto', 'Multimedia: texto', 'text', 'Multimedia', ''],
                    ['multimedia_url', 'Multimedia: dirección', 'text', '', 'Vacío mientras no exista la sección Multimedia.'],
                ],
            ],
            'Destacadas' => [
                'intro' => 'Las 4 noticias más recientes; las fijadas van primero. Solo se muestran en la primera página y no se repiten abajo.',
                'campos' => [
                    ['destacadas_mostrar', 'Mostrar', 'checkbox', '1', 'Quita la marca para que todas las noticias vayan en la lista.'],
                    ['destacadas_titulo', 'Título', 'text', 'Destacadas', ''],
                ],
            ],
            'Todas las noticias' => [
                'campos' => [
                    ['todas_titulo', 'Título', 'text', 'Todas las noticias', ''],
                    ['orden_texto', 'Texto junto al total', 'text', 'de la más reciente a la más antigua', 'Se lee «48 noticias · de la más reciente a la más antigua».'],
                    ['vacio', 'Texto cuando no hay resultados', 'text', 'No hay noticias con estos filtros. Prueba con otro tema, otro año u otras palabras.', ''],
                ],
            ],
            'Cada noticia' => [
                'campos' => [
                    ['compartir_mostrar', 'Mostrar «Compartir»', 'checkbox', '1', 'Enlaces para compartir en Facebook, LinkedIn y X, y para copiar el enlace.'],
                    ['compartir_texto', 'Texto antes de los enlaces', 'text', 'Compartir:', ''],
                    ['anterior_texto', 'Enlace a la noticia anterior', 'text', 'Noticia anterior', ''],
                    ['siguiente_texto', 'Enlace a la noticia siguiente', 'text', 'Noticia siguiente', ''],
                ],
            ],
        ],
    ];
    return $grupos;
});

add_action('admin_menu', function () {
    add_submenu_page('edit.php', 'Página de noticias', 'Página de noticias', INAP_CAP, 'inap_noticias', fn () => inap_ajustes_pagina('inap_noticias'));
});

function inap_noticias_txt(string $clave): string
{
    return inap_ajuste('inap_noticias', $clave);
}

/* ── Datos de una noticia ─────────────────────────────────────────────────── */

function inap_noticia_datos(WP_Post $p): array
{
    $cat = get_the_category($p->ID)[0] ?? null;
    $tema = $cat && !in_array($cat->slug, ['sin-categoria', 'uncategorized'], true) ? $cat : null;
    $imagen = (int) get_post_thumbnail_id($p);
    $iso = get_the_date('Y-m-d', $p);
    [$a, $m, $d] = array_map('intval', explode('-', $iso));
    return [
        'id' => $p->ID,
        'titulo' => get_the_title($p),
        'url' => get_permalink($p),
        'fecha' => "{$d} " . INAP_MESES_CORTOS[$m - 1] . " {$a}",
        'fecha_larga' => "{$d} de " . INAP_MESES[$m - 1] . " de {$a}",
        'iso' => $iso,
        'tema' => $tema?->name ?? '',
        'tema_slug' => $tema?->slug ?? '',
        'resumen' => has_excerpt($p) ? get_the_excerpt($p) : '',
        'imagen_id' => $imagen,
        'imagen' => $imagen ? (string) wp_get_attachment_image_url($imagen, 'large') : '',
        'alt' => $imagen ? (string) get_post_meta($imagen, '_wp_attachment_image_alt', true) : '',
    ];
}

/** Las destacadas: las fijadas primero, luego las más recientes. */
function inap_noticias_destacadas(int $cuantas = 4): array
{
    static $cache = [];
    if (isset($cache[$cuantas])) {
        return $cache[$cuantas];
    }
    $fijadas = array_map('intval', (array) get_option('sticky_posts', []));
    $posts = $fijadas ? get_posts(['post__in' => $fijadas, 'numberposts' => $cuantas, 'orderby' => 'date', 'order' => 'DESC']) : [];
    if (count($posts) < $cuantas) {
        $posts = array_merge($posts, get_posts(['numberposts' => $cuantas - count($posts), 'post__not_in' => $fijadas, 'ignore_sticky_posts' => true]));
    }
    return $cache[$cuantas] = array_map('inap_noticia_datos', $posts);
}

/* ── Piezas de la tarjeta (News Card) ─────────────────────────────────────── */

function inap_noticia_meta(array $n): string
{
    return '<p class="text-sm text-neutral-text"><time datetime="' . esc_attr($n['iso']) . '">' . esc_html($n['fecha']) . '</time>'
        . ($n['tema'] !== '' ? ' · ' . esc_html($n['tema']) : '') . '</p>';
}

function inap_noticia_foto(array $n, string $class): string
{
    return $n['imagen'] !== ''
        ? '<img src="' . esc_url($n['imagen']) . '" alt="' . esc_attr($n['alt']) . '" loading="lazy" class="' . $class . ' object-cover" />'
        : '<span class="' . $class . ' flex items-center justify-center bg-brand-surface text-brand-surface-content">' . inap_icon('newspaper', 32) . '</span>';
}

/**
 * News Card. Toda la tarjeta es un enlace (el del titular se estira sobre ella).
 * $tipo: 'vertical' (foto arriba, con resumen) u 'horizontal' (foto pequeña a la izquierda).
 */
function inap_noticia_tarjeta(array $n, string $tipo = 'vertical', string $h = 'h3', string $extra = ''): string
{
    $foco = 'has-[a:focus-visible]:outline-2 has-[a:focus-visible]:outline-offset-2 has-[a:focus-visible]:outline-primary-text';
    $enlace = '<a href="' . esc_url($n['url']) . '" class="after:absolute after:inset-0 focus-visible:outline-none">' . esc_html($n['titulo']) . '</a>';
    if ($tipo === 'horizontal') {
        return '<article class="group relative flex items-start gap-4 rounded-box ' . $foco . ' ' . $extra . '">'
            . inap_noticia_foto($n, 'h-24 w-32 shrink-0 rounded-lg')
            . '<div class="flex min-w-0 flex-1 flex-col gap-2">' . inap_noticia_meta($n)
            . "<{$h} class=\"text-base leading-6 font-semibold text-base-content group-hover:text-primary-text\">{$enlace}</{$h}>"
            . '</div></article>';
    }
    return '<article class="group relative flex flex-col overflow-hidden rounded-box border border-base-border bg-base-100 ' . $foco . ' ' . $extra . '">'
        . inap_noticia_foto($n, 'h-54 w-full shrink-0')
        . '<div class="flex flex-col gap-2 px-5 pt-5 pb-6">' . inap_noticia_meta($n)
        . "<{$h} class=\"text-xl font-semibold text-base-content group-hover:text-primary-text\">{$enlace}</{$h}>"
        . ($n['resumen'] !== '' ? '<p class="text-sm text-neutral-text">' . esc_html($n['resumen']) . '</p>' : '')
        . '</div></article>';
}

/* ── Filtros y consulta de /noticias/ ─────────────────────────────────────── */

/** Filtros de la URL: ?tema=slug&anio=2026&q=palabras. */
function inap_noticias_filtros(): array
{
    $anio = (int) ($_GET['anio'] ?? 0);
    return [
        'tema' => sanitize_title(wp_unslash($_GET['tema'] ?? '')),
        'anio' => $anio >= 1980 && $anio <= 2100 ? $anio : 0,
        'q' => trim(sanitize_text_field(wp_unslash($_GET['q'] ?? ''))),
    ];
}

function inap_noticias_con_filtros(): bool
{
    return (bool) array_filter(inap_noticias_filtros());
}

/** Las destacadas se muestran (y salen de la lista) solo sin filtros. */
function inap_noticias_muestra_destacadas(): bool
{
    return inap_noticias_txt('destacadas_mostrar') === '1' && !inap_noticias_con_filtros();
}

add_action('pre_get_posts', function (WP_Query $q) {
    if (is_admin() || !$q->is_main_query() || !$q->is_home()) {
        return;
    }
    $f = inap_noticias_filtros();
    $q->set('posts_per_page', INAP_NOTICIAS_POR_PAGINA);
    $q->set('ignore_sticky_posts', true);
    if ($f['tema'] !== '') {
        $q->set('category_name', $f['tema']);
    }
    if ($f['anio']) {
        $q->set('year', $f['anio']);
    }
    if ($f['q'] !== '') {
        $q->set('s', $f['q']);
    }
    if (inap_noticias_muestra_destacadas()) {
        $q->set('post__not_in', array_column(inap_noticias_destacadas(), 'id'));
    }
});

/** Los archivos de un tema o de un año llevan a /noticias/ con el filtro puesto. */
add_action('template_redirect', function () {
    $noticias = get_permalink((int) get_option('page_for_posts'));
    if (!$noticias) {
        return;
    }
    if (is_category()) {
        wp_safe_redirect(add_query_arg('tema', get_queried_object()->slug, $noticias), 301);
        exit;
    }
    if (is_year()) {
        wp_safe_redirect(add_query_arg('anio', (int) get_query_var('year'), $noticias), 301);
        exit;
    }
});

/** Temas con noticias publicadas, para el filtro. */
function inap_noticias_temas(): array
{
    return array_values(array_filter(
        get_categories(['hide_empty' => true]),
        fn ($t) => !in_array($t->slug, ['sin-categoria', 'uncategorized'], true)
    ));
}

/** Años con noticias publicadas, del más reciente al más antiguo. */
function inap_noticias_anios(): array
{
    global $wpdb;
    return array_map('intval', $wpdb->get_col(
        "SELECT DISTINCT YEAR(post_date) FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' ORDER BY 1 DESC"
    ));
}

/* ── Paginación (componente Pagination) ───────────────────────────────────── */

function inap_paginacion(int $total, int $actual, string $ancla = ''): string
{
    if ($total < 2) {
        return '';
    }
    $url = fn (int $n) => esc_url(get_pagenum_link($n) . $ancla);
    $paso = 'flex h-11 items-center gap-1 rounded-lg px-3 text-sm font-medium';
    $enlace = 'text-primary-text hover:bg-surface-subtle ' . INAP_FOCUS;
    $apagado = 'text-base-content/60';

    $prev = $actual > 1
        ? '<a href="' . $url($actual - 1) . '" class="' . $paso . ' ' . $enlace . '">' . inap_icon('chevron-left', 16) . 'Anterior</a>'
        : '<span class="' . $paso . ' ' . $apagado . '">' . inap_icon('chevron-left', 16) . 'Anterior</span>';
    $next = $actual < $total
        ? '<a href="' . $url($actual + 1) . '" class="' . $paso . ' ' . $enlace . '">Siguiente' . inap_icon('chevron-right', 16) . '</a>'
        : '<span class="' . $paso . ' ' . $apagado . '">Siguiente' . inap_icon('chevron-right', 16) . '</span>';

    // Primera, última, la actual y sus vecinas (y la 3 si estamos en la 1).
    $paginas = array_unique(array_filter([1, 2, $actual === 1 ? 3 : 0, $actual - 1, $actual, $actual + 1, $total], fn ($n) => $n >= 1 && $n <= $total));
    sort($paginas);
    $numeros = '';
    $anterior = 0;
    foreach ($paginas as $n) {
        if ($n - $anterior > 1) {
            $numeros .= '<li aria-hidden="true" class="flex size-11 items-center justify-center text-sm font-semibold text-neutral-text">…</li>';
        }
        $numeros .= $n === $actual
            ? '<li><a href="' . $url($n) . '" aria-current="page" class="flex size-11 items-center justify-center rounded-lg bg-primary text-sm font-semibold text-primary-content ' . INAP_FOCUS . '"><span class="sr-only">Página </span>' . $n . '</a></li>'
            : '<li><a href="' . $url($n) . '" class="flex size-11 items-center justify-center rounded-lg text-sm font-semibold text-base-content hover:bg-surface-subtle ' . INAP_FOCUS . '"><span class="sr-only">Página </span>' . $n . '</a></li>';
        $anterior = $n;
    }

    return '<nav aria-label="Paginación" class="flex items-center justify-between gap-1 lg:justify-center">'
        . $prev
        . '<ul class="hidden items-center gap-1 lg:flex">' . $numeros . '</ul>'
        . '<p class="text-sm text-neutral-text lg:hidden">Página ' . $actual . ' de ' . $total . '</p>'
        . $next . '</nav>';
}

/* ── Contenido de ejemplo de la página de noticias ────────────────────────── */

/**
 * Una sola vez: 9 noticias más (las de la lista del diseño), el texto completo de la primera
 * y pie de foto en las fotos de ejemplo. Arregla los enlaces «#» de Noticias y Eventos en los menús.
 */
function inap_semilla_noticias(): void
{
    if (get_option('inap_semilla_noticias')) {
        return;
    }
    update_option('inap_semilla_noticias', INAP_VERSION, false);

    $fotos = get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 4, 'orderby' => 'ID', 'order' => 'ASC',
        'meta_key' => '_inap_ejemplo', 'fields' => 'ids']);
    foreach ($fotos as $foto) {
        if (wp_get_attachment_caption($foto) === '') {
            wp_update_post(['ID' => $foto, 'post_excerpt' => 'Foto de ejemplo: servidores públicos en una sesión de trabajo del INAP.']);
        }
    }

    // Texto completo para la primera noticia de ejemplo, si nadie la cambió.
    $primera = get_posts(['post_type' => 'post', 'numberposts' => 1, 'meta_key' => '_inap_ejemplo', 'title' => 'Más de 300 servidores públicos inician el programa de Transformación digital e IA en el gobierno']);
    if ($primera && str_starts_with($primera[0]->post_content, '<p>Noticia de ejemplo.')) {
        wp_update_post(['ID' => $primera[0]->ID, 'post_content' => implode("\n\n", [
            '<!-- wp:paragraph --><p>Texto de ejemplo. El programa combina sesiones en línea con un proyecto que cada equipo desarrolla en su institución. Al finalizar, los participantes presentan sus resultados ante sus autoridades.</p><!-- /wp:paragraph -->',
            '<!-- wp:paragraph --><p>Durante ocho semanas, los equipos trabajarán temas de gobierno digital, uso responsable de la inteligencia artificial y mejora de trámites para la ciudadanía.</p><!-- /wp:paragraph -->',
            '<!-- wp:heading --><h2 class="wp-block-heading">Qué incluye el programa</h2><!-- /wp:heading -->',
            '<!-- wp:list --><ul class="wp-block-list"><!-- wp:list-item --><li>Cuatro módulos en línea, con material disponible en todo momento.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Acompañamiento de facilitadores del INAP.</li><!-- /wp:list-item --><!-- wp:list-item --><li>Certificado al aprobar el proyecto aplicado.</li><!-- /wp:list-item --></ul><!-- /wp:list -->',
            '<!-- wp:quote --><blockquote class="wp-block-quote"><!-- wp:paragraph --><p>«La transformación digital empieza por las personas que atienden a la ciudadanía.»</p><!-- /wp:paragraph --><cite>Nombre y cargo de la persona citada</cite></blockquote><!-- /wp:quote -->',
            '<!-- wp:paragraph --><p>Las inscripciones para la siguiente cohorte se publicarán en la Agenda académica del INAP.</p><!-- /wp:paragraph -->',
        ])]);
        if (!has_excerpt($primera[0])) {
            wp_update_post(['ID' => $primera[0]->ID, 'post_excerpt' => 'La cohorte reúne a equipos de 40 instituciones del Estado durante ocho semanas, con sesiones en línea y un proyecto aplicado en cada institución.']);
        }
    }

    $resumen = 'Resumen de ejemplo: una o dos oraciones sobre la noticia.';
    $noticias = [
        ['2026-11-05', 'Formación', 'Servidores municipales de Huehuetenango concluyen el curso de gestión de proyectos'],
        ['2026-10-28', 'Fortalecimiento', 'INAP y el INE firman convenio de cooperación técnica'],
        ['2026-10-20', 'Formación', 'Taller de facilitadores INAP–SESAN reúne a 60 participantes'],
        ['2026-10-18', 'Multimedia', 'Transmisión en vivo: ética y transparencia en la función pública'],
        ['2026-10-15', 'Formación', 'Delegación de Quetzaltenango gradúa a 45 servidores municipales'],
        ['2026-10-08', 'Multimedia', 'Con Enfoque Público: conversación sobre gestión por resultados'],
        ['2026-10-01', 'Fortalecimiento', 'INAP presenta la Agenda de servicios 2027 a instituciones del Estado'],
        ['2026-09-24', 'Formación', 'Nueva edición del Diplomado en Gerencia Pública'],
        ['2026-09-15', 'Institucional', 'INAP celebra 43 años al servicio de la administración pública'],
    ];
    foreach ($noticias as $i => [$fecha, $tema, $titulo]) {
        $cat = term_exists($tema, 'category') ?: wp_insert_term($tema, 'category');
        $cat = is_array($cat) ? (int) $cat['term_id'] : 0;
        $id = wp_insert_post([
            'post_type' => 'post', 'post_status' => 'publish', 'post_date' => "{$fecha} 09:00:00", 'post_title' => $titulo,
            'post_excerpt' => $resumen, 'post_category' => $cat ? [$cat] : [],
            'post_content' => '<!-- wp:paragraph --><p>Noticia de ejemplo. Comunicación Social la reemplaza o la borra cuando publique las noticias reales.</p><!-- /wp:paragraph -->',
        ]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, '_inap_ejemplo', '1');
            $fotos && set_post_thumbnail($id, $fotos[$i % count($fotos)]);
        }
    }

    // Menús sembrados antes de esta versión: Noticias y Eventos apuntaban a «#».
    $destinos = ['Noticias' => '/noticias/', 'Noticias INAP' => '/noticias/', 'Eventos' => '/eventos/'];
    foreach (wp_get_nav_menus() as $menu) {
        foreach ((array) wp_get_nav_menu_items($menu) as $item) {
            if (isset($destinos[$item->title]) && in_array($item->url, ['#', ''], true)) {
                update_post_meta($item->ID, '_menu_item_url', home_url($destinos[$item->title]));
            }
        }
    }
}
