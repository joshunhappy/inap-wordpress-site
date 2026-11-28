<?php
/**
 * Contacto: página /contacto/ (ruta propia, sin página en la base de datos) con datos de contacto,
 * tarjetas a Preguntas frecuentes y Delegaciones, y el formulario «Envíanos un mensaje».
 *
 * Cada mensaje se guarda en Contacto › Mensajes (aunque el correo falle) y se envía por correo al
 * buzón de su tema o al destinatario general. Textos y temas en Contacto › Página de contacto.
 */

defined('ABSPATH') || exit;

const INAP_MENSAJE = 'inap_mensaje';
/** Campos del formulario que se guardan con el mensaje (además de tema y mensaje). */
const INAP_CONTACTO_CAMPOS = ['nombre' => 'Nombre', 'apellidos' => 'Apellidos', 'correo' => 'Correo', 'telefono' => 'Teléfono', 'institucion' => 'Institución'];

/* ── Ruta ─────────────────────────────────────────────────────────────────── */

add_action('init', fn () => add_rewrite_rule('^contacto/?$', 'index.php?inap_contacto=1', 'top'));
add_filter('query_vars', fn ($v) => array_merge($v, ['inap_contacto']));
add_filter('template_include', fn ($t) => get_query_var('inap_contacto') ? get_theme_file_path('templates/contacto.php') : $t);
// Sin esto WordPress cree que /contacto/ es la portada y le pone su título.
add_filter('document_title_parts', function ($partes) {
    if (get_query_var('inap_contacto')) {
        $partes['title'] = inap_contacto_txt('titulo');
    }
    return $partes;
});

function inap_contacto_url(): string
{
    return home_url('/contacto/');
}

function inap_contacto_txt(string $clave): string
{
    return inap_ajuste('inap_contacto', $clave);
}

/**
 * Temas del formulario: [['tema' => …, 'correo' => …|''], …], de «Tema» o «Tema | correo» por renglón.
 */
function inap_contacto_temas(): array
{
    $temas = [];
    foreach (preg_split('/\R/', inap_contacto_txt('temas')) as $linea) {
        [$tema, $correo] = array_map('trim', explode('|', $linea, 2)) + [1 => ''];
        if ($tema !== '') {
            $temas[] = ['tema' => $tema, 'correo' => is_email($correo) ? $correo : ''];
        }
    }
    return $temas;
}

/* ── Mensajes recibidos (Contacto › Mensajes) ─────────────────────────────── */

add_action('init', function () {
    register_post_type(INAP_MENSAJE, [
        'labels' => [
            'name' => 'Mensajes', 'singular_name' => 'Mensaje', 'menu_name' => 'Contacto', 'all_items' => 'Mensajes',
            'edit_item' => 'Mensaje', 'search_items' => 'Buscar mensajes', 'not_found' => 'Aún no hay mensajes.',
            'not_found_in_trash' => 'No hay mensajes en la papelera.',
        ],
        'public' => false,
        'show_ui' => true,
        'menu_position' => 26,
        'menu_icon' => 'dashicons-email-alt',
        'supports' => false,
        'capability_type' => 'page',
        'capabilities' => ['create_posts' => 'do_not_allow'],
        'map_meta_cap' => true,
    ]);
});

add_filter('manage_' . INAP_MENSAJE . '_posts_columns', fn ($c) => [
    'cb' => $c['cb'], 'title' => 'Tema y nombre', 'inap_correo' => 'Correo de la persona', 'inap_enviado' => 'Aviso por correo', 'date' => 'Recibido',
]);
add_action('manage_' . INAP_MENSAJE . '_posts_custom_column', function ($col, $id) {
    if ($col === 'inap_correo') {
        $correo = (string) get_post_meta($id, '_inap_correo', true);
        echo '<a href="mailto:' . esc_attr($correo) . '">' . esc_html($correo) . '</a>';
    } elseif ($col === 'inap_enviado') {
        $a = (string) get_post_meta($id, '_inap_destinatario', true);
        echo get_post_meta($id, '_inap_enviado', true) === '1' ? esc_html("Enviado a {$a}") : '<strong>No se pudo enviar</strong> (léelo aquí)';
    }
}, 10, 2);

// El mensaje se lee, no se edita: una caja de solo lectura en lugar del editor.
add_action('add_meta_boxes_' . INAP_MENSAJE, function () {
    add_meta_box('inap_mensaje', 'Mensaje', function (WP_Post $post) {
        echo '<table class="form-table" role="presentation"><tbody>';
        $filas = INAP_CONTACTO_CAMPOS + ['tema' => 'Tema'];
        foreach ($filas as $k => $etiqueta) {
            $v = (string) get_post_meta($post->ID, "_inap_{$k}", true);
            $html = $k === 'correo' && $v !== '' ? '<a href="mailto:' . esc_attr($v) . '">' . esc_html($v) . '</a>' : esc_html($v ?: '—');
            echo '<tr><th scope="row">' . esc_html($etiqueta) . '</th><td>' . $html . '</td></tr>';
        }
        echo '<tr><th scope="row">Mensaje</th><td>' . nl2br(esc_html($post->post_content)) . '</td></tr>';
        $a = (string) get_post_meta($post->ID, '_inap_destinatario', true);
        $estado = get_post_meta($post->ID, '_inap_enviado', true) === '1'
            ? "Se envió una copia a {$a}."
            : "No se pudo enviar la copia por correo a {$a}. Responde desde tu correo a la persona.";
        echo '<tr><th scope="row">Aviso por correo</th><td>' . esc_html($estado) . '</td></tr>';
        echo '</tbody></table>';
        echo '<p>Para responder, escribe a la persona desde tu correo. Cuando ya no lo necesites, envía el mensaje a la papelera.</p>';
    }, INAP_MENSAJE, 'normal', 'high');
    remove_meta_box('submitdiv', INAP_MENSAJE, 'side');
    remove_meta_box('slugdiv', INAP_MENSAJE, 'normal');
});

/* ── Recibir el formulario ────────────────────────────────────────────────── */

/** Lo que la persona escribió antes de un error, una sola vez. */
function inap_contacto_previo(): array
{
    $clave = preg_replace('/[^A-Za-z0-9]/', '', (string) ($_GET['datos'] ?? ''));
    if ($clave === '') {
        return [];
    }
    $datos = get_transient("inap_contacto_{$clave}");
    delete_transient("inap_contacto_{$clave}");
    return is_array($datos) ? $datos : [];
}

add_action('admin_post_nopriv_inap_contacto', 'inap_contacto_recibir');
add_action('admin_post_inap_contacto', 'inap_contacto_recibir');

function inap_contacto_recibir(): void
{
    $volver = function (array $args): never {
        wp_safe_redirect(add_query_arg($args, inap_contacto_url()) . '#mensaje');
        exit;
    };

    if (!wp_verify_nonce($_POST['_inap'] ?? '', 'inap_contacto')) {
        $volver(['mensaje' => 'expirado']);
    }
    // Campo trampa, invisible para las personas: si viene lleno, es un robot. Se le responde como si nada.
    if (trim((string) ($_POST['sitio_web'] ?? '')) !== '') {
        $volver(['mensaje' => 'enviado']);
    }
    // Hasta 5 mensajes cada 10 minutos desde la misma conexión.
    $ip = 'inap_contacto_ip_' . md5((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
    $veces = (int) get_transient($ip);
    if ($veces >= 5) {
        $volver(['mensaje' => 'limite']);
    }

    $d = [];
    foreach (array_keys(INAP_CONTACTO_CAMPOS) as $k) {
        $d[$k] = mb_substr(sanitize_text_field(wp_unslash($_POST[$k] ?? '')), 0, 200);
    }
    $d['tema'] = sanitize_text_field(wp_unslash($_POST['tema'] ?? ''));
    $d['mensaje'] = mb_substr(trim(sanitize_textarea_field(wp_unslash($_POST['mensaje'] ?? ''))), 0, 5000);

    $temas = array_column(inap_contacto_temas(), 'correo', 'tema');
    $errores = array_keys(array_filter([
        'correo' => !is_email($d['correo']),
        'tema' => !array_key_exists($d['tema'], $temas),
        'mensaje' => $d['mensaje'] === '',
    ]));
    if ($errores) {
        $clave = wp_generate_password(20, false);
        set_transient("inap_contacto_{$clave}", $d, 10 * MINUTE_IN_SECONDS);
        $volver(['mensaje' => 'error', 'campos' => implode(',', $errores), 'datos' => $clave]);
    }
    set_transient($ip, $veces + 1, 10 * MINUTE_IN_SECONDS);

    $persona = trim("{$d['nombre']} {$d['apellidos']}") ?: $d['correo'];
    $para = $temas[$d['tema']] ?: inap_contacto_txt('destinatario');
    $id = wp_insert_post([
        'post_type' => INAP_MENSAJE,
        'post_status' => 'private',
        'post_title' => "{$d['tema']} — {$persona}",
        'post_content' => $d['mensaje'],
    ]);
    foreach ($d as $k => $v) {
        $k !== 'mensaje' && update_post_meta($id, "_inap_{$k}", $v);
    }
    update_post_meta($id, '_inap_destinatario', $para);

    $lineas = [];
    foreach (INAP_CONTACTO_CAMPOS + ['tema' => 'Tema'] as $k => $etiqueta) {
        $lineas[] = "{$etiqueta}: " . ($d[$k] !== '' ? $d[$k] : '—');
    }
    $cuerpo = implode("\n", $lineas) . "\n\nMensaje:\n{$d['mensaje']}\n\n—\nEnviado desde el formulario de contacto de " . home_url('/')
        . "\nTambién está guardado en el administrador del sitio: Contacto › Mensajes.";
    $enviado = wp_mail($para, "[Contacto INAP] {$d['tema']} — {$persona}", $cuerpo, ["Reply-To: {$d['correo']}"]);
    update_post_meta($id, '_inap_enviado', $enviado ? '1' : '0');

    $volver(['mensaje' => 'enviado']);
}

/* ── Textos editables (Contacto › Página de contacto) ─────────────────────── */

add_filter('inap_ajustes_grupos', function (array $grupos) {
    $grupos['inap_contacto'] = [
        'pagina' => 'Página de contacto',
        'intro' => 'Textos y temas de la página Contacto. El correo, teléfono y dirección salen de Ajustes › INAP. Los mensajes que llegan se leen en Contacto › Mensajes.',
        'ver' => '/contacto/',
        'secciones' => [
            'Encabezado' => [
                'campos' => [
                    ['titulo', 'Título', 'text', 'Contacto', 'También es el nombre en el breadcrumb.'],
                    ['intro', 'Descripción', 'textarea', 'Escríbenos si tienes dudas sobre cursos, servicios para tu institución o cualquier otro tema. Te respondemos al correo que indiques.', ''],
                    ['dato1', 'Dato 1', 'text', 'Respuesta por correo', 'Los datos salen con un ✓ debajo de la descripción. Vacío = no sale.'],
                    ['dato2', 'Dato 2', 'text', 'Sin registrarte', ''],
                    ['horario', 'Horario de atención', 'text', 'Lunes a viernes, de 8:00 a 16:30', 'Sale con un reloj, debajo de la descripción y en «Datos de contacto». Vacío = no sale.'],
                ],
            ],
            'Tarjeta «¿Buscas información pública?»' => [
                'intro' => 'Lleva a Transparencia, donde está el formulario de solicitud de información pública.',
                'campos' => [
                    ['ley_mostrar', 'Mostrar', 'checkbox', '1', ''],
                    ['ley_titulo', 'Título', 'text', '¿Buscas información pública?', ''],
                    ['ley_texto', 'Texto', 'textarea', 'Las solicitudes de información pública tienen su propio formulario y un plazo de respuesta de 10 días hábiles, según la ley.', ''],
                    ['ley_boton', 'Texto del botón', 'text', 'Ir a Transparencia', ''],
                ],
            ],
            'Datos de contacto' => [
                'campos' => [
                    ['datos_titulo', 'Título', 'text', 'Datos de contacto', 'El correo, teléfono y dirección se cambian en Ajustes › INAP.'],
                ],
            ],
            'Tarjetas de abajo' => [
                'intro' => 'Dos tarjetas debajo de los datos de contacto. Una tarjeta sin dirección no sale.',
                'campos' => [
                    ['faq_titulo', 'Tarjeta 1: título', 'text', 'Preguntas frecuentes', ''],
                    ['faq_texto', 'Tarjeta 1: texto', 'textarea', 'Respuestas a las dudas más comunes sobre inscripción, constancias y acceso al Aula virtual.', ''],
                    ['faq_meta', 'Tarjeta 1: línea de abajo', 'text', 'Revísalas antes de escribirnos', ''],
                    ['faq_url', 'Tarjeta 1: dirección', 'url', '#', 'Ej.: /preguntas-frecuentes/'],
                    ['delegaciones_titulo', 'Tarjeta 2: título', 'text', 'Nuestras delegaciones', ''],
                    ['delegaciones_texto', 'Tarjeta 2: texto', 'textarea', 'Direcciones, correos y teléfonos de las sedes del INAP en Petén, Cobán y Noroccidente.', ''],
                    ['delegaciones_meta', 'Tarjeta 2: línea de abajo', 'text', '3 delegaciones regionales', ''],
                    ['delegaciones_url', 'Tarjeta 2: dirección', 'url', '#', 'Ej.: /delegaciones/'],
                ],
            ],
            'Formulario' => [
                'campos' => [
                    ['form_titulo', 'Título', 'text', 'Envíanos un mensaje', ''],
                    ['form_intro', 'Texto', 'textarea', 'Completa el formulario y te responderemos al correo que indiques.', ''],
                    ['temas', 'Temas', 'textarea', "Cursos e inscripciones\nConstancias y certificados\nAula virtual\nServicios para instituciones\nOtro tema", 'Uno por renglón, en el orden de la lista. Para que un tema llegue a otro buzón, escríbelo así: «Aula virtual | aula@inap.gob.gt».'],
                    ['destinatario', 'Correo que recibe los mensajes', 'email', 'informacion@inap.gob.gt', 'Para los temas sin correo propio. Cada mensaje también se guarda en Contacto › Mensajes.'],
                    ['gracias', 'Aviso al enviar', 'textarea', 'Recibimos tu mensaje. Te responderemos al correo que indicaste.', ''],
                ],
            ],
        ],
    ];
    return $grupos;
});

add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=' . INAP_MENSAJE, 'Página de contacto', 'Página de contacto', INAP_CAP, 'inap_contacto', fn () => inap_ajustes_pagina('inap_contacto'));
});

/* ── Menús: «Contacto» y «Contáctanos» apuntaban a «#» ────────────────────── */

function inap_semilla_contacto(): void
{
    if (get_option('inap_semilla_contacto')) {
        return;
    }
    update_option('inap_semilla_contacto', INAP_VERSION, false);
    foreach (wp_get_nav_menus() as $menu) {
        foreach ((array) wp_get_nav_menu_items($menu) as $item) {
            if (in_array($item->title, ['Contacto', 'Contáctanos'], true) && in_array($item->url, ['#', ''], true)) {
                update_post_meta($item->ID, '_menu_item_url', inap_contacto_url());
            }
        }
    }
}
