<?php
/**
 * Inicio del sitio institucional (Figma «Sitio institucional • Desktop/Mobile • Light • Inicio»).
 *
 *   Página de inicio › Portada            encabezado, tarjeta destacada y franja de Transparencia
 *   Página de inicio › Cursos destacados  las 4 tarjetas de curso (los cursos viven en la plataforma)
 *   Página de inicio › Noticias y eventos títulos y enlaces; el contenido sale de Noticias y Eventos
 *   Página de inicio › Fortalecimiento    banda azul con sus 3 servicios
 *   Página de inicio › Recursos           3 tarjetas
 *   Noticias                              las entradas de WordPress, con otro nombre
 *   Eventos                               tipo de contenido propio, con fecha, hora y lugar
 *
 * Todo lo del inicio se edita aquí; front-page.php solo lo muestra.
 */

defined('ABSPATH') || exit;

const INAP_EVENTO = 'inap_evento';
const INAP_PLATAFORMA = 'https://sigo.inap.gob.gt/frontend/webinap';

/* ── Campos de las páginas del inicio ─────────────────────────────────────── */

/** Campo «Mostrar en el inicio» de una sección o tarjeta. */
function inap_inicio_mostrar(string $que = 'esta sección'): array
{
    return ['mostrar', 'Mostrar', 'checkbox', '1', "Quita la marca para ocultar {$que} sin borrar sus textos."];
}

/** Campos de una tarjeta numerada: cada clave lleva el número (curso2_titulo…). */
function inap_inicio_tarjeta(string $prefijo, int $n, array $campos): array
{
    return array_map(fn ($c) => array_replace($c, [0 => "{$prefijo}{$n}_{$c[0]}"]), $campos);
}

add_filter('inap_ajustes_grupos', function (array $grupos) {
    $cursos = [
        ['Con certificación', 'Curso y Certificación en Gestión Financiera — Nivel Intermedio', 'Finanzas públicas y calidad del gasto', '8 semanas', 'ejemplo/foto-1.jpg'],
        ['Con certificación', 'Curso y Certificación para Profesionales de Recursos Humanos', 'Gestión administrativa y organizacional', '10 semanas', 'ejemplo/foto-2.png'],
        ['Con certificación', 'Certificación de Conocimientos en Administración Pública — Nivel Básico', 'Modernización del Estado', '6 semanas', 'ejemplo/foto-3.png'],
        ['Curso', 'Introducción al Derecho Administrativo', 'Ética, transparencia y rendición de cuentas', '4 semanas', 'ejemplo/foto-4.png'],
    ];
    $secciones_cursos = [
        'Encabezado' => [
            'campos' => [
                inap_inicio_mostrar('toda la sección'),
                ['titulo', 'Título', 'text', 'Cursos destacados', ''],
                ['descripcion', 'Descripción', 'textarea', 'Inscripción abierta en la Agenda académica. Los cursos se toman en la plataforma de formación del INAP.', ''],
                ['boton_texto', 'Botón: texto', 'text', 'Ver agenda académica', ''],
                ['boton_url', 'Botón: enlace', 'url', INAP_PLATAFORMA, ''],
            ],
        ],
    ];
    foreach ($cursos as $i => [$tipo, $titulo, $area, $duracion, $foto]) {
        $n = $i + 1;
        $secciones_cursos["Curso {$n}"] = [
            'ayuda' => $n === 1 ? 'Copia los datos del curso tal como están en la Agenda académica. El enlace lleva a la página del curso en la plataforma.' : '',
            'campos' => inap_inicio_tarjeta('curso', $n, [
                inap_inicio_mostrar('este curso'),
                ['titulo', 'Nombre del curso', 'text', $titulo, ''],
                ['tipo', 'Tipo (etiqueta sobre la foto)', 'text', $tipo, 'Con certificación, Curso, Diplomado o Posgrado.'],
                ['area', 'Área', 'text', $area, ''],
                ['duracion', 'Duración', 'text', $duracion, ''],
                ['modalidad', 'Modalidad', 'text', 'Virtual', 'Virtual, Presencial o Híbrido.'],
                ['imagen', 'Foto', 'imagen', $foto, 'Horizontal, de al menos 600 × 340 px. Es decorativa: el nombre del curso ya la describe.'],
                ['url', 'Enlace al curso', 'url', INAP_PLATAFORMA, ''],
            ]),
        ];
    }

    $servicios = [
        ['users', 'Programas a la medida', 'Contenidos diseñados según las funciones y metas de tus equipos.'],
        ['clipboard-check', 'Inscripción grupal', 'Inscribe a tus colaboradores en un solo proceso y da seguimiento a su avance.'],
        ['file-search', 'Investigación y asistencia técnica', 'Acompañamiento especializado para fortalecer procesos institucionales.'],
    ];
    $secciones_fortalecimiento = [
        'Texto y botones' => [
            'campos' => [
                inap_inicio_mostrar('la banda'),
                ['titulo', 'Título', 'text', 'Fortalece las capacidades de tu institución', ''],
                ['texto', 'Texto', 'textarea', 'Diseñamos programas de formación, investigación y asistencia técnica a la medida de cada entidad del Estado.', ''],
                ['boton_texto', 'Botón: texto', 'text', 'Ver agenda de servicios', ''],
                ['boton_url', 'Botón: enlace', 'url', '#', ''],
                ['enlace_texto', 'Enlace: texto', 'text', 'Casos de fortalecimiento', ''],
                ['enlace_url', 'Enlace: enlace', 'url', '#', ''],
            ],
        ],
    ];
    foreach ($servicios as $i => [$icono, $titulo, $texto]) {
        $n = $i + 1;
        $secciones_fortalecimiento["Servicio {$n}"] = [
            'campos' => inap_inicio_tarjeta('servicio', $n, [
                inap_inicio_mostrar('este servicio'),
                ['icono', 'Ícono', 'icono', $icono, ''],
                ['titulo', 'Título', 'text', $titulo, ''],
                ['texto', 'Texto', 'textarea', $texto, ''],
            ]),
        ];
    }

    $recursos = [
        ['book-open', 'Revista INAP', 'Análisis y experiencias para una gestión pública basada en evidencia.', '7ma. edición'],
        ['landmark', 'Centro de Consulta Digital (CECODINAP)', 'Boletines, bibliografías y documentos de consulta en línea.', 'Sitio externo'],
        ['scale', 'Legislación', 'Código Municipal, Ley del IUSI, Código Tributario y otras normas de consulta.', '3 documentos'],
    ];
    $secciones_recursos = [
        'Encabezado' => [
            'campos' => [
                inap_inicio_mostrar('toda la sección'),
                ['titulo', 'Título', 'text', 'Recursos', ''],
                ['descripcion', 'Descripción', 'textarea', 'Publicaciones, legislación y el centro de consulta del INAP, abiertos para todos.', ''],
            ],
        ],
    ];
    foreach ($recursos as $i => [$icono, $titulo, $texto, $meta]) {
        $n = $i + 1;
        $secciones_recursos["Tarjeta {$n}"] = [
            'campos' => inap_inicio_tarjeta('recurso', $n, [
                inap_inicio_mostrar('esta tarjeta'),
                ['icono', 'Ícono', 'icono', $icono, ''],
                ['titulo', 'Título', 'text', $titulo, ''],
                ['texto', 'Texto', 'textarea', $texto, ''],
                ['meta', 'Línea inferior', 'text', $meta, 'Por ejemplo, la edición, «Sitio externo» o cuántos documentos hay.'],
                ['url', 'Enlace', 'url', '#', ''],
            ]),
        ];
    }

    return $grupos + [
        'inap_inicio_portada' => [
            'pagina' => 'Página de inicio: portada',
            'intro' => 'Lo primero que se ve en el inicio. Cada sección de la página de inicio tiene su propia pantalla en este menú, en el mismo orden que en el sitio.',
            'ver' => '/',
            'secciones' => [
                'Encabezado' => [
                    'campos' => [
                        ['antetitulo', 'Texto sobre el título', 'text', 'Instituto Nacional de Administración Pública', ''],
                        ['titulo', 'Título', 'textarea', 'Formamos y fortalecemos la administración pública de Guatemala', 'Es el título principal de la página. Corto: una idea, no una lista.'],
                        ['intro', 'Introducción', 'textarea', 'Formación para servidores públicos, apoyo técnico a las instituciones del Estado e investigación para una gestión pública eficiente y transparente.', ''],
                        ['buscador', 'Buscador: texto de ejemplo', 'text', 'Busca noticias, eventos, documentos o páginas', 'Se ve dentro del buscador mientras está vacío.'],
                        ['boton1_texto', 'Botón principal: texto', 'text', 'Ver agenda académica', ''],
                        ['boton1_url', 'Botón principal: enlace', 'url', INAP_PLATAFORMA, ''],
                        ['boton2_texto', 'Botón secundario: texto', 'text', 'Fortalece a tu institución', ''],
                        ['boton2_url', 'Botón secundario: enlace', 'text', '#fortalecimiento', '«#fortalecimiento» baja a la banda azul de esta misma página.'],
                    ],
                ],
                'Tarjeta destacada' => [
                    'ayuda' => 'Recuadro a la derecha del título (abajo en celular). Para un informe, una convocatoria o un anuncio importante.',
                    'campos' => [
                        inap_inicio_mostrar('la tarjeta'),
                        ['etiqueta', 'Etiqueta', 'text', 'Informe de gobierno 2025', ''],
                        ['destacado_titulo', 'Título', 'text', '«Segunda Cosecha»: lo que INAP logró en 2025', ''],
                        ['destacado_texto', 'Texto', 'textarea', 'Cursos impartidos, instituciones acompañadas y resultados del año, en un solo informe.', ''],
                        ['destacado_enlace', 'Enlace: texto', 'text', 'Leer el informe', ''],
                        ['destacado_url', 'Enlace: dirección', 'url', '#', 'Si es un PDF, súbelo a Medios y pega aquí su dirección.'],
                    ],
                ],
                'Franja de Transparencia' => [
                    'ayuda' => 'En computadora va debajo del encabezado; en celular, al final de la página.',
                    'campos' => [
                        ['tp_mostrar', 'Mostrar', 'checkbox', '1', 'Quita la marca para ocultar la franja sin borrar sus textos.'],
                        ['tp_titulo', 'Título', 'text', 'Transparencia e información pública', ''],
                        ['tp_texto', 'Texto', 'textarea', 'Consulta lo que INAP publica según el artículo 10 de la Ley de Acceso a la Información Pública, o pide lo que no esté publicado.', ''],
                        ['tp_boton1_texto', 'Primer botón: texto', 'text', 'Ver información pública', ''],
                        ['tp_boton1_url', 'Primer botón: enlace', 'text', '/transparencia/informacion-publica-de-oficio/', ''],
                        ['tp_boton2_texto', 'Segundo botón: texto', 'text', 'Solicitar información', ''],
                        ['tp_boton2_url', 'Segundo botón: enlace', 'text', '/transparencia/#solicitud', ''],
                    ],
                ],
            ],
        ],
        'inap_inicio_cursos' => [
            'pagina' => 'Página de inicio: cursos destacados',
            'intro' => 'Los cursos se publican en la plataforma de formación; aquí solo se eligen los 4 que aparecen en el inicio.',
            'ver' => '/',
            'secciones' => $secciones_cursos,
        ],
        'inap_inicio_noticias' => [
            'pagina' => 'Página de inicio: noticias y eventos',
            'intro' => 'Las noticias salen solas de Noticias (las 4 más recientes; una noticia fijada va primero) y los eventos de Eventos (los 3 próximos). Aquí solo se editan los títulos y enlaces de las dos secciones.',
            'ver' => '/',
            'secciones' => [
                'Noticias' => [
                    'campos' => [
                        inap_inicio_mostrar('las noticias'),
                        ['titulo', 'Título', 'text', 'Noticias', ''],
                        ['descripcion', 'Descripción', 'textarea', 'Lo más reciente del INAP: formación, fortalecimiento institucional y vida institucional.', ''],
                        ['enlace_texto', 'Enlace: texto', 'text', 'Ver todas las noticias', ''],
                        ['enlace_url', 'Enlace: dirección', 'text', '/noticias/', ''],
                    ],
                ],
                'Eventos' => [
                    'campos' => [
                        ['eventos_mostrar', 'Mostrar', 'checkbox', '1', 'Quita la marca para ocultar los eventos sin borrarlos.'],
                        ['eventos_titulo', 'Título', 'text', 'Próximos eventos', ''],
                        ['eventos_vacio', 'Texto cuando no hay eventos', 'text', 'No hay eventos programados por ahora.', ''],
                        ['eventos_enlace_texto', 'Enlace: texto', 'text', 'Ver calendario de eventos', ''],
                        ['eventos_enlace_url', 'Enlace: dirección', 'text', '/eventos/', ''],
                    ],
                ],
            ],
        ],
        'inap_inicio_fortalecimiento' => [
            'pagina' => 'Página de inicio: fortalecimiento',
            'intro' => 'Banda azul para instituciones. El botón secundario del encabezado baja hasta aquí.',
            'ver' => '/#fortalecimiento',
            'secciones' => $secciones_fortalecimiento,
        ],
        'inap_inicio_recursos' => [
            'pagina' => 'Página de inicio: recursos',
            'ver' => '/',
            'secciones' => $secciones_recursos,
        ],
    ];
});

add_action('admin_menu', function () {
    add_menu_page('Página de inicio', 'Página de inicio', INAP_CAP, 'inap_inicio_portada', fn () => inap_ajustes_pagina('inap_inicio_portada'), 'dashicons-admin-home', 3);
    $paginas = [
        'inap_inicio_portada' => 'Portada',
        'inap_inicio_cursos' => 'Cursos destacados',
        'inap_inicio_noticias' => 'Noticias y eventos',
        'inap_inicio_fortalecimiento' => 'Fortalecimiento',
        'inap_inicio_recursos' => 'Recursos',
    ];
    foreach ($paginas as $slug => $titulo) {
        add_submenu_page('inap_inicio_portada', "Página de inicio: {$titulo}", $titulo, INAP_CAP, $slug, fn () => inap_ajustes_pagina($slug));
    }
});

/* ── Ayudas para la plantilla ─────────────────────────────────────────────── */

/** Valor de un campo del inicio: inap_inicio('cursos', 'curso1_titulo'). */
function inap_inicio(string $pagina, string $clave): string
{
    return inap_ajuste("inap_inicio_{$pagina}", $clave);
}

function inap_inicio_visible(string $pagina, string $clave = 'mostrar'): bool
{
    return inap_inicio($pagina, $clave) === '1';
}

/** Enlace escrito en el administrador: «/ruta/» es del sitio, «#ancla» se queda igual, vacío = «#». */
function inap_url(string $v): string
{
    $v = trim($v);
    return match (true) {
        $v === '' => '#',
        str_starts_with($v, '#') => $v,
        str_starts_with($v, '/') && !str_starts_with($v, '//') => home_url($v),
        default => $v,
    };
}

/** Foto de un campo de imagen del inicio, con la foto de ejemplo si no se eligió otra. */
function inap_inicio_imagen(string $pagina, string $clave): string
{
    foreach (inap_ajustes_grupos()["inap_inicio_{$pagina}"]['secciones'] as $seccion) {
        foreach ($seccion['campos'] as [$k, , , $defecto]) {
            if ($k === $clave) {
                return inap_imagen_url((string) (((array) get_option("inap_inicio_{$pagina}", []))[$clave] ?? ''), $defecto);
            }
        }
    }
    return '';
}

const INAP_MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

/** Las noticias del inicio: las fijadas primero, luego las más recientes. */
function inap_inicio_noticias(int $cuantas = 4): array
{
    $fijadas = array_map('intval', (array) get_option('sticky_posts', []));
    $posts = $fijadas ? get_posts(['post__in' => $fijadas, 'numberposts' => $cuantas, 'orderby' => 'date', 'order' => 'DESC']) : [];
    if (count($posts) < $cuantas) {
        $posts = array_merge($posts, get_posts(['numberposts' => $cuantas - count($posts), 'post__not_in' => $fijadas, 'ignore_sticky_posts' => true]));
    }
    return array_map(function (WP_Post $p) {
        $tema = get_the_category($p->ID)[0]->name ?? '';
        $tema = $tema === 'Sin categoría' || $tema === 'Uncategorized' ? '' : $tema;
        $imagen = (int) get_post_thumbnail_id($p);
        return [
            'titulo' => get_the_title($p),
            'url' => get_permalink($p),
            'fecha' => inap_tp_fecha(get_the_date('Y-m-d', $p)),
            'iso' => get_the_date('Y-m-d', $p),
            'tema' => $tema,
            'resumen' => has_excerpt($p) ? get_the_excerpt($p) : '',
            'imagen' => $imagen ? (string) wp_get_attachment_image_url($imagen, 'large') : '',
            'alt' => $imagen ? (string) get_post_meta($imagen, '_wp_attachment_image_alt', true) : '',
        ];
    }, $posts);
}

/** Los próximos eventos (desde hoy), del más cercano al más lejano. */
function inap_inicio_eventos(int $cuantos = 3): array
{
    $posts = get_posts([
        'post_type' => INAP_EVENTO,
        'numberposts' => $cuantos,
        'meta_key' => '_inap_fecha',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => [['key' => '_inap_fecha', 'value' => wp_date('Y-m-d'), 'compare' => '>=', 'type' => 'DATE']],
    ]);
    return array_map('inap_evento_datos', $posts);
}

function inap_evento_datos(WP_Post $p): array
{
    $iso = (string) get_post_meta($p->ID, '_inap_fecha', true);
    [$a, $m, $d] = array_map('intval', explode('-', $iso . '--')) + [0, 0, 0];
    $hora = (string) get_post_meta($p->ID, '_inap_hora', true);
    $lugar = (string) get_post_meta($p->ID, '_inap_lugar', true);
    return [
        'titulo' => get_the_title($p),
        'url' => get_permalink($p),
        'iso' => $iso,
        'dia' => $d ? sprintf('%02d', $d) : '',
        'mes' => $m ? ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'][$m - 1] : '',
        'fecha_larga' => $m ? sprintf('%d de %s de %d', $d, INAP_MESES[$m - 1], $a) : '',
        'meta' => implode(' · ', array_filter([$hora !== '' ? ltrim($hora, '0') : '', $lugar])),
    ];
}

/* ── Noticias = entradas de WordPress ─────────────────────────────────────── */

add_filter('post_type_labels_post', function ($l) {
    foreach ([
        'name' => 'Noticias', 'singular_name' => 'Noticia', 'menu_name' => 'Noticias', 'name_admin_bar' => 'Noticia',
        'all_items' => 'Todas las noticias', 'add_new' => 'Agregar noticia', 'add_new_item' => 'Agregar noticia',
        'edit_item' => 'Editar noticia', 'new_item' => 'Nueva noticia', 'view_item' => 'Ver noticia',
        'search_items' => 'Buscar noticias', 'not_found' => 'No hay noticias.', 'item_published' => 'Noticia publicada.',
        'item_updated' => 'Noticia actualizada.', 'featured_image' => 'Foto de la noticia', 'set_featured_image' => 'Elegir foto',
        'remove_featured_image' => 'Quitar foto', 'use_featured_image' => 'Usar como foto de la noticia',
    ] as $k => $v) {
        $l->$k = $v;
    }
    return $l;
});

add_filter('taxonomy_labels_category', function ($l) {
    foreach ([
        'name' => 'Temas', 'singular_name' => 'Tema', 'menu_name' => 'Temas', 'all_items' => 'Todos los temas',
        'edit_item' => 'Editar tema', 'add_new_item' => 'Agregar tema', 'search_items' => 'Buscar temas', 'not_found' => 'No hay temas.',
    ] as $k => $v) {
        $l->$k = $v;
    }
    return $l;
});

add_action('after_setup_theme', fn () => add_theme_support('post-thumbnails'));

/* ── Eventos ──────────────────────────────────────────────────────────────── */

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
});

add_filter('enter_title_here', fn ($t, $post) => $post->post_type === INAP_EVENTO ? 'Nombre del evento' : $t, 10, 2);

add_action('add_meta_boxes_' . INAP_EVENTO, function () {
    add_meta_box('inap-evento', 'Fecha y lugar', function (WP_Post $post) {
        wp_nonce_field('inap_evento', '_inap_evento');
        $v = fn ($k) => esc_attr((string) get_post_meta($post->ID, "_inap_{$k}", true));
        ?>
        <p><label for="inap-fecha"><strong>Fecha</strong> (obligatoria)</label><br />
          <input id="inap-fecha" name="inap_fecha" type="date" value="<?php echo $v('fecha'); ?>" required /></p>
        <p><label for="inap-hora"><strong>Hora de inicio</strong></label><br />
          <input id="inap-hora" name="inap_hora" type="time" value="<?php echo $v('hora'); ?>" /></p>
        <p><label for="inap-lugar"><strong>Lugar</strong></label><br />
          <input id="inap-lugar" name="inap_lugar" type="text" class="widefat" value="<?php echo $v('lugar'); ?>" aria-describedby="inap-lugar-ayuda" />
          <span id="inap-lugar-ayuda" class="description">Por ejemplo: Sede central, zona 10 · Transmisión en vivo.</span></p>
        <p class="description">El inicio muestra los 3 eventos más cercanos desde hoy. Cuando la fecha pasa, el evento deja de aparecer solo.</p>
        <?php
    }, INAP_EVENTO, 'side', 'high');
});

add_action('save_post_' . INAP_EVENTO, function ($post_id) {
    if (!wp_verify_nonce($_POST['_inap_evento'] ?? '', 'inap_evento') || !current_user_can('edit_post', $post_id) || wp_is_post_autosave($post_id)) {
        return;
    }
    $fecha = sanitize_text_field(wp_unslash($_POST['inap_fecha'] ?? ''));
    $hora = sanitize_text_field(wp_unslash($_POST['inap_hora'] ?? ''));
    update_post_meta($post_id, '_inap_fecha', preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) ? $fecha : '');
    update_post_meta($post_id, '_inap_hora', preg_match('/^\d{2}:\d{2}$/', $hora) ? $hora : '');
    update_post_meta($post_id, '_inap_lugar', sanitize_text_field(wp_unslash($_POST['inap_lugar'] ?? '')));
});

add_filter('manage_' . INAP_EVENTO . '_posts_columns', fn ($cols) => array_slice($cols, 0, 2, true) + ['inap_fecha' => 'Fecha del evento', 'inap_lugar' => 'Lugar'] + array_slice($cols, 2, null, true));

add_action('manage_' . INAP_EVENTO . '_posts_custom_column', function ($col, $post_id) {
    $e = inap_evento_datos(get_post($post_id));
    echo esc_html(match ($col) {
        'inap_fecha' => $e['fecha_larga'] ? $e['fecha_larga'] . ($e['iso'] < wp_date('Y-m-d') ? ' (ya pasó)' : '') : '— sin fecha —',
        'inap_lugar' => $e['meta'],
        default => '',
    });
}, 10, 2);

/* Lista del administrador: por fecha del evento, el más lejano arriba. Archivo público: solo los próximos. */
add_action('pre_get_posts', function (WP_Query $q) {
    if (is_admin() && $q->is_main_query() && $q->get('post_type') === INAP_EVENTO && !$q->get('orderby')) {
        $q->set('meta_key', '_inap_fecha');
        $q->set('orderby', 'meta_value');
        $q->set('order', 'DESC');
    }
    if (!is_admin() && $q->is_main_query() && $q->is_post_type_archive(INAP_EVENTO)) {
        $q->set('meta_key', '_inap_fecha');
        $q->set('orderby', 'meta_value');
        $q->set('order', 'ASC');
        $q->set('meta_query', [['key' => '_inap_fecha', 'value' => wp_date('Y-m-d'), 'compare' => '>=', 'type' => 'DATE']]);
    }
});

/* ── Contenido inicial ────────────────────────────────────────────────────── */

/**
 * Una sola vez: páginas «Inicio» y «Noticias» (portada estática), noticias y eventos de ejemplo con sus fotos.
 * Todo lo de ejemplo lleva la marca _inap_ejemplo para encontrarlo y borrarlo.
 */
function inap_semilla_inicio(): void
{
    if (get_option('show_on_front') !== 'page' || !get_option('page_on_front')) {
        $pagina = fn ($titulo, $slug) => get_page_by_path($slug)?->ID
            ?: wp_insert_post(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $titulo, 'post_name' => $slug]);
        update_option('page_on_front', $pagina('Inicio', 'inicio'));
        update_option('page_for_posts', $pagina('Noticias', 'noticias'));
        update_option('show_on_front', 'page');
    }
    if (get_option('inap_semilla_inicio')) {
        return;
    }
    update_option('inap_semilla_inicio', INAP_VERSION, false);

    // «¡Hola, mundo!» de una instalación nueva, si nadie lo tocó, a la papelera.
    $hola = get_post(1);
    if ($hola && $hola->post_type === 'post' && $hola->post_modified === $hola->post_date && in_array($hola->post_name, ['hello-world', 'hola-mundo'], true)) {
        wp_trash_post(1);
    }

    $fotos = [];
    foreach (['foto-1.jpg', 'foto-2.png', 'foto-3.png', 'foto-4.png'] as $archivo) {
        $fotos[] = inap_semilla_foto($archivo);
    }

    $noticias = [
        ['2026-11-12 09:00:00', 'Formación', 'Más de 300 servidores públicos inician el programa de Transformación digital e IA en el gobierno', 'La cohorte reúne a equipos de 40 instituciones del Estado durante ocho semanas.'],
        ['2026-11-06 09:00:00', 'Eventos', 'El Foro de la Función Pública abre su registro para 2026', ''],
        ['2026-10-30 09:00:00', 'Recursos', 'Ya está disponible la Revista INAP, 7ma. edición', ''],
        ['2026-10-22 09:00:00', 'Fortalecimiento', 'INAP acompaña al Viceministerio del Agua en su modelo de gestión por procesos', ''],
    ];
    foreach ($noticias as $i => [$fecha, $tema, $titulo, $resumen]) {
        $cat = term_exists($tema, 'category') ?: wp_insert_term($tema, 'category');
        $cat = is_array($cat) ? (int) $cat['term_id'] : 0;
        $id = wp_insert_post([
            'post_type' => 'post', 'post_status' => 'publish', 'post_date' => $fecha, 'post_title' => $titulo,
            'post_excerpt' => $resumen, 'post_category' => $cat ? [$cat] : [],
            'post_content' => '<p>Noticia de ejemplo. Comunicación Social la reemplaza o la borra cuando publique las noticias reales.</p>',
        ]);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, '_inap_ejemplo', '1');
            $fotos[$i] && set_post_thumbnail($id, $fotos[$i]);
        }
    }

    $eventos = [
        [4, '08:30', 'Sede central, zona 10', 'Foro de la Función Pública 2026'],
        [12, '18:00', 'Transmisión en vivo', 'Con Enfoque Público: gestión por resultados'],
        [19, '10:00', 'Sede central, zona 10', 'Graduación del Diplomado en Gerencia Pública'],
    ];
    foreach ($eventos as [$dias, $hora, $lugar, $titulo]) {
        $id = wp_insert_post(['post_type' => INAP_EVENTO, 'post_status' => 'publish', 'post_title' => $titulo,
            'post_content' => '<p>Evento de ejemplo. Comunicación Social lo reemplaza o lo borra cuando publique los eventos reales.</p>']);
        if ($id && !is_wp_error($id)) {
            update_post_meta($id, '_inap_fecha', wp_date('Y-m-d', strtotime("+{$dias} days")));
            update_post_meta($id, '_inap_hora', $hora);
            update_post_meta($id, '_inap_lugar', $lugar);
            update_post_meta($id, '_inap_ejemplo', '1');
        }
    }
}

/** Copia una foto de ejemplo del tema a Medios y devuelve su ID (0 si falla). */
function inap_semilla_foto(string $archivo): int
{
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $origen = get_theme_file_path("assets/ejemplo/{$archivo}");
    $subida = wp_upload_bits("inap-ejemplo-{$archivo}", null, (string) @file_get_contents($origen));
    if (!empty($subida['error'])) {
        return 0;
    }
    $id = wp_insert_attachment([
        'post_mime_type' => wp_check_filetype($subida['file'])['type'],
        'post_title' => 'Foto de ejemplo',
        'post_status' => 'inherit',
    ], $subida['file']);
    if (!$id || is_wp_error($id)) {
        return 0;
    }
    wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $subida['file']));
    update_post_meta($id, '_wp_attachment_image_alt', 'Servidores públicos en una sesión de trabajo del INAP');
    update_post_meta($id, '_inap_ejemplo', '1');
    return $id;
}
