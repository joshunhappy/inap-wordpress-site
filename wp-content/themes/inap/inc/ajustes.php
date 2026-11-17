<?php
/**
 * Ajustes editables desde el administrador, sin plugins.
 *
 *   Ajustes › INAP                         contacto general y enlaces a la plataforma de cursos
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
 * Cada campo: [clave, etiqueta, tipo (text|textarea|url|email|number), valor por defecto, ayuda].
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
    echo '<div class="wrap"><h1>' . esc_html($grupo['pagina'] ?? 'Ajustes de INAP') . '</h1>';
    if (!empty($grupo['intro'])) {
        echo '<p>' . esc_html($grupo['intro']) . '</p>';
    }
    echo '<p>Si dejas un campo vacío, el sitio muestra el texto que aparece en gris.</p>';
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
            echo '<tr><th scope="row"><label for="' . esc_attr($id) . '">' . esc_html($etiqueta) . '</label></th><td>';
            if ($tipo === 'textarea') {
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
