<?php
/**
 * Ajustes editables desde el administrador, sin plugins.
 *
 *   Ajustes › INAP                         contacto general y enlaces a la plataforma de cursos
 *   Página de inicio › …                   textos, fotos y tarjetas del inicio (inc/inicio.php)
 *   Transparencia › Textos y ajustes       textos de las páginas y datos de la Unidad (inc/transparencia-admin.php)
 *
 * Cada página es una lista de campos con su valor por defecto: si nadie la ha guardado, el sitio
 * muestra los textos de los diseños. Comunicación Social (rol Editor) puede editarlas.
 */

defined('ABSPATH') || exit;

/** Capacidad para editar ajustes, menús y Transparencia: la tiene el rol Editor. */
const INAP_CAP = 'edit_pages';

/**
 * @var array<string, array{titulo: string, campos: array}> grupos de campos por opción.
 * Cada campo: [clave, etiqueta, tipo, valor por defecto, ayuda].
 * Tipos: text, textarea, url, email, number; checkbox ('1' o '0'); imagen (ID del archivo de Medios; por
 * defecto, una ruta dentro de assets/); icono (nombre de un ícono de INAP_ICONOS).
 */
function inap_ajustes_grupos(): array
{
    static $grupos = null;
    return $grupos ??= apply_filters('inap_ajustes_grupos', [
        'inap_ajustes' => [
            'titulo' => 'INAP',
            'secciones' => [
                'Contacto general' => [
                    'ayuda' => 'Se muestran en el pie de página y en el menú «Institución».',
                    'campos' => [
                        ['telefono', 'Teléfono', 'text', '(+502) 2419-8181', 'Tal como se lee. El enlace para llamar se arma solo.'],
                        ['correo', 'Correo', 'email', 'informacion@inap.gob.gt', ''],
                        ['direccion', 'Dirección', 'textarea', "Boulevard Los Próceres 16-40, zona 10\nCiudad de Guatemala", 'En el pie, cada línea va en un renglón.'],
                    ],
                ],
                'Plataforma de cursos y Aula virtual' => [
                    'ayuda' => 'La plataforma de cursos (Angular) tiene el inicio de sesión y el registro. Estos enlaces no se editan en los menús.',
                    'campos' => [
                        ['plataforma_url', 'Dirección de la plataforma de cursos', 'url', 'https://sigo.inap.gob.gt/frontend/webinap', '«Iniciar sesión» lleva a esta dirección + /login y «Regístrate» a /registro.'],
                        ['aula_url', 'Aula virtual (Moodle)', 'url', '', 'Se abre en una pestaña nueva.'],
                    ],
                ],
                'Aviso del sitio' => [
                    'ayuda' => 'Franja amarilla arriba de todas las páginas. Vacío = sin aviso.',
                    'campos' => [
                        ['aviso', 'Texto del aviso', 'text', '', 'Por ejemplo: «Versión de demostración: los datos de Transparencia son de ejemplo».'],
                    ],
                ],
            ],
        ],
    ]);
}

/** Valor de un ajuste, con el texto de los diseños si nadie lo ha cambiado. */
function inap_ajuste(string $opcion, string $clave): string
{
    static $cache = [];
    $guardado = $cache[$opcion] ??= (array) get_option($opcion, []);
    if (isset($guardado[$clave]) && $guardado[$clave] !== '') {
        return (string) $guardado[$clave];
    }
    foreach (inap_ajustes_grupos()[$opcion]['secciones'] ?? [] as $seccion) {
        foreach ($seccion['campos'] as [$k, , , $defecto]) {
            if ($k === $clave) {
                return (string) $defecto;
            }
        }
    }
    return '';
}

/* ── Páginas de ajustes ───────────────────────────────────────────────────── */

add_action('admin_menu', function () {
    add_options_page('Ajustes de INAP', 'INAP', INAP_CAP, 'inap_ajustes', fn () => inap_ajustes_pagina('inap_ajustes'));
});

/* Selector de Medios para los campos de imagen, solo en las páginas de ajustes del tema. */
add_action('admin_enqueue_scripts', function () {
    $pagina = sanitize_key($_GET['page'] ?? '');
    if (!isset(inap_ajustes_grupos()[$pagina])) {
        return;
    }
    wp_enqueue_media();
    $ver = (string) (@filemtime(get_theme_file_path('assets/js/admin-ajustes.js')) ?: INAP_VERSION);
    wp_enqueue_script('inap-ajustes', get_theme_file_uri('assets/js/admin-ajustes.js'), [], $ver, ['in_footer' => true]);
});

/** URL de una imagen guardada (ID de Medios) o, si no hay, de la foto de ejemplo del tema. */
function inap_imagen_url(string $valor, string $defecto, string $tamano = 'large'): string
{
    if ($valor !== '' && ($url = wp_get_attachment_image_url((int) $valor, $tamano))) {
        return $url;
    }
    return $defecto !== '' ? get_theme_file_uri('assets/' . $defecto) : '';
}

/** Íconos que se pueden elegir en los ajustes (nombre Lucide => nombre en el administrador). */
const INAP_ICONOS = [
    'users' => 'Personas',
    'user-check' => 'Persona con visto bueno',
    'graduation-cap' => 'Graduación',
    'school' => 'Escuela',
    'book-open' => 'Libro abierto',
    'library' => 'Biblioteca',
    'newspaper' => 'Periódico',
    'file-text' => 'Documento',
    'file-search' => 'Documento con lupa',
    'clipboard-check' => 'Lista revisada',
    'landmark' => 'Edificio público',
    'building-2' => 'Edificio',
    'scale' => 'Balanza',
    'gavel' => 'Mazo',
    'handshake' => 'Apretón de manos',
    'briefcase' => 'Maletín',
    'chart-column' => 'Gráfica',
    'lightbulb' => 'Idea',
    'target' => 'Objetivo',
    'award' => 'Premio',
    'calendar' => 'Calendario',
    'megaphone' => 'Megáfono',
    'monitor' => 'Computadora',
    'globe' => 'Mundo',
    'map-pin' => 'Ubicación',
    'mail' => 'Correo',
    'phone' => 'Teléfono',
    'circle-help' => 'Ayuda',
];

add_action('admin_init', function () {
    foreach (array_keys(inap_ajustes_grupos()) as $opcion) {
        register_setting($opcion, $opcion, ['type' => 'array', 'sanitize_callback' => fn ($v) => inap_ajustes_limpiar($opcion, (array) $v)]);
        // options.php pide manage_options; el Editor también puede guardar estas páginas.
        add_filter("option_page_capability_{$opcion}", fn () => INAP_CAP);
    }
});

function inap_ajustes_limpiar(string $opcion, array $valores): array
{
    $limpio = [];
    foreach (inap_ajustes_grupos()[$opcion]['secciones'] as $seccion) {
        foreach ($seccion['campos'] as [$clave, , $tipo]) {
            $v = wp_unslash($valores[$clave] ?? '');
            $limpio[$clave] = match ($tipo) {
                'textarea' => sanitize_textarea_field($v),
                'url' => esc_url_raw(trim($v)),
                'email' => sanitize_email($v),
                'number' => $v === '' ? '' : (string) absint($v),
                'checkbox' => $v === '1' ? '1' : '0',
                'imagen' => $v === '' ? '' : (string) absint($v),
                'icono' => isset(INAP_ICONOS[$v]) ? $v : '',
                default => sanitize_text_field($v),
            };
        }
    }
    return $limpio;
}

/** Formulario de una opción: una tabla por sección. Un campo vacío vuelve al texto de los diseños. */
function inap_ajustes_pagina(string $opcion): void
{
    $grupo = inap_ajustes_grupos()[$opcion];
    $guardado = (array) get_option($opcion, []);
    echo '<div class="wrap"><h1 class="wp-heading-inline">' . esc_html($grupo['pagina'] ?? 'Ajustes de INAP') . '</h1>';
    if (!empty($grupo['ver'])) {
        printf(' <a href="%s" class="page-title-action" target="_blank">Ver en el sitio<span class="screen-reader-text"> (se abre en una pestaña nueva)</span></a>', esc_url(home_url($grupo['ver'])));
    }
    echo '<hr class="wp-header-end" />';
    if (!empty($grupo['intro'])) {
        echo '<p>' . esc_html($grupo['intro']) . '</p>';
    }
    echo '<p>Si dejas un campo vacío, el sitio muestra el texto que aparece en gris.</p>';
    // Las páginas de Ajustes ya muestran «Ajustes guardados»; las demás lo necesitan aquí.
    if (($GLOBALS['parent_file'] ?? '') !== 'options-general.php') {
        settings_errors();
    }
    echo '<form method="post" action="options.php">';
    settings_fields($opcion);
    foreach ($grupo['secciones'] as $titulo => $seccion) {
        echo '<h2>' . esc_html($titulo) . '</h2>';
        if (!empty($seccion['ayuda'])) {
            echo '<p>' . esc_html($seccion['ayuda']) . '</p>';
        }
        echo '<table class="form-table" role="presentation"><tbody>';
        foreach ($seccion['campos'] as [$clave, $etiqueta, $tipo, $defecto, $ayuda]) {
            $id = "{$opcion}-{$clave}";
            $name = "{$opcion}[{$clave}]";
            $valor = (string) ($guardado[$clave] ?? '');
            $desc = $ayuda !== '' ? ' aria-describedby="' . esc_attr("{$id}-ayuda") . '"' : '';
            $etiqueta_html = $tipo === 'imagen'
                ? '<span id="' . esc_attr("{$id}-etiqueta") . '">' . esc_html($etiqueta) . '</span>'
                : '<label for="' . esc_attr($id) . '">' . esc_html($etiqueta) . '</label>';
            echo '<tr><th scope="row">' . $etiqueta_html . '</th><td>';
            if ($tipo === 'checkbox') {
                $marcado = ($valor !== '' ? $valor : $defecto) === '1';
                printf('<input type="hidden" name="%1$s" value="0" /><input id="%2$s" name="%1$s" type="checkbox" value="1"%3$s%4$s />', esc_attr($name), esc_attr($id), checked($marcado, true, false), $desc);
            } elseif ($tipo === 'icono') {
                $actual = $valor !== '' ? $valor : $defecto;
                printf('<select id="%s" name="%s"%s>', esc_attr($id), esc_attr($name), $desc);
                foreach (INAP_ICONOS as $icono => $nombre) {
                    printf('<option value="%s"%s>%s</option>', esc_attr($icono), selected($icono, $actual, false), esc_html($nombre));
                }
                echo '</select>';
            } elseif ($tipo === 'imagen') {
                $src = inap_imagen_url($valor, $defecto, 'medium');
                printf(
                    '<div class="inap-imagen" data-defecto="%1$s"><img src="%2$s" alt="" width="160" style="display:block;max-width:160px;height:auto;margin-bottom:8px;border:1px solid #dcdcde" />'
                    . '<input type="hidden" id="%3$s" name="%4$s" value="%5$s" />'
                    . '<button type="button" class="button" data-elegir aria-describedby="%3$s-etiqueta">Elegir imagen</button> '
                    . '<button type="button" class="button-link" data-quitar aria-describedby="%3$s-etiqueta"%6$s>Usar la foto de ejemplo</button></div>',
                    esc_url(inap_imagen_url('', $defecto, 'medium')), esc_url($src), esc_attr($id), esc_attr($name), esc_attr($valor), $valor === '' ? ' hidden' : ''
                );
            } elseif ($tipo === 'textarea') {
                printf('<textarea id="%s" name="%s" rows="3" class="large-text" placeholder="%s"%s>%s</textarea>', esc_attr($id), esc_attr($name), esc_attr($defecto), $desc, esc_textarea($valor));
            } else {
                printf('<input id="%s" name="%s" type="%s" value="%s" placeholder="%s" class="%s"%s />', esc_attr($id), esc_attr($name), esc_attr($tipo), esc_attr($valor), esc_attr($defecto), $tipo === 'number' ? 'small-text' : 'large-text', $desc);
            }
            if ($ayuda !== '') {
                echo '<p id="' . esc_attr("{$id}-ayuda") . '" class="description">' . esc_html($ayuda) . '</p>';
            }
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    }
    submit_button('Guardar cambios');
    echo '</form></div>';
}

/* ── Permisos ─────────────────────────────────────────────────────────────── */

/*
  Los menús (Apariencia › Menús) piden edit_theme_options, que el Editor no tiene. Mientras este tema
  esté activo, quien puede editar páginas también puede editar menús. No cambia los roles guardados.
*/
add_filter('user_has_cap', function (array $caps) {
    if (!empty($caps[INAP_CAP])) {
        $caps['edit_theme_options'] = true;
    }
    return $caps;
});

/* El Editor no debe entrar al Personalizador ni a Widgets: los textos del tema están en estas páginas. */
add_action('admin_menu', function () {
    if (!current_user_can('manage_options')) {
        remove_submenu_page('themes.php', 'themes.php');
        remove_submenu_page('themes.php', 'widgets.php');
        global $submenu;
        foreach ($submenu['themes.php'] ?? [] as $i => $item) {
            if (str_starts_with($item[2], 'customize.php')) {
                unset($submenu['themes.php'][$i]);
            }
        }
    }
}, 999);
