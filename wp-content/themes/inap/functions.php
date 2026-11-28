<?php
/**
 * Tema INAP: recursos, íconos, ajustes editables, menús, página de inicio y el módulo de Transparencia.
 */

defined('ABSPATH') || exit;

const INAP_VERSION = '0.9.0';

require_once __DIR__ . '/inc/ajustes.php';
require_once __DIR__ . '/inc/menus.php';
require_once __DIR__ . '/inc/eventos.php';
require_once __DIR__ . '/inc/inicio.php';
require_once __DIR__ . '/inc/noticias.php';
require_once __DIR__ . '/inc/buscador.php';
require_once __DIR__ . '/inc/busqueda.php';
require_once __DIR__ . '/inc/contacto.php';
require_once __DIR__ . '/inc/transparencia-admin.php';
require_once __DIR__ . '/inc/transparencia.php';

/** Contenido inicial (menús, numerales, inicio, noticias y eventos de los diseños). No pisa lo editado: solo crea lo que falta. */
function inap_semilla(): void
{
    inap_semilla_menus();
    inap_semilla_numerales();
    inap_semilla_inicio();
    inap_semilla_eventos();
    inap_semilla_noticias();
    inap_semilla_contacto();
}

/*
  Al activar el tema (también al subirlo como .zip en un hosting): enlaces permanentes con nombre,
  que las rutas de Transparencia necesitan, y el contenido inicial.
*/
add_action('after_switch_theme', function () {
    if (!get_option('permalink_structure')) {
        update_option('permalink_structure', '/%postname%/');
    }
    inap_semilla();
    flush_rewrite_rules();
});

/*
  Al reemplazar el tema con un .zip nuevo WordPress no lo vuelve a activar: con otra versión,
  se crea lo que falte y se rehacen las rutas.
*/
add_action('init', function () {
    if (get_option('inap_tema_version') !== INAP_VERSION) {
        update_option('inap_tema_version', INAP_VERSION);
        inap_semilla();
        flush_rewrite_rules();
    }
}, 99);

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('html5', ['search-form', 'script', 'style']);
});

/* ── Recursos ─────────────────────────────────────────────────────────────── */

add_action('wp_enqueue_scripts', function () {
    $ver = fn (string $rel) => (string) (@filemtime(get_theme_file_path($rel)) ?: INAP_VERSION);
    wp_enqueue_style('inap', get_theme_file_uri('assets/css/inap.css'), [], $ver('assets/css/inap.css'));
    wp_enqueue_style('inap-tema', get_theme_file_uri('assets/css/tema.css'), ['inap'], $ver('assets/css/tema.css'));
    wp_enqueue_script('inap', get_theme_file_uri('assets/js/sitio.js'), [], $ver('assets/js/sitio.js'), ['strategy' => 'defer', 'in_footer' => true]);
    // WordPress agrega estilos de bloques que este tema no usa.
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');
}, 20);

/** Tema claro u oscuro antes de pintar: mismo criterio que el sitio de referencia (clave «inap-tema»). */
add_action('wp_head', function () {
    echo "<script>(function(){try{var t=localStorage.getItem('inap-tema');}catch(e){}if(t!=='inap'&&t!=='dark'){t=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'inap';}document.documentElement.setAttribute('data-theme',t);})();</script>\n";
}, 1);

add_filter('language_attributes', fn ($out) => 'lang="es-GT" data-theme="inap"');

/* ── Íconos y piezas de página ────────────────────────────────────────────── */

/** Ícono Lucide del sprite del tema; decorativo (aria-hidden). */
function inap_icon(string $name, int $size = 20, string $class = ''): string
{
    $href = esc_url(get_theme_file_uri('assets/lucide/sprite.svg')) . '#' . esc_attr($name);
    $cls = $class !== '' ? ' class="' . esc_attr($class) . '"' : '';
    return sprintf(
        '<svg viewBox="0 0 24 24" width="%1$d" height="%1$d" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"%2$s><use href="%3$s" /></svg>',
        $size, $cls, $href
    );
}

/**
 * Campo de formulario (componente Field): etiqueta arriba, control, ayuda y error enlazados con
 * aria-describedby. $o: type (text, email, tel, textarea, select), required, autocomplete, placeholder,
 * ayuda, options (para select). $valor = lo escrito antes; $error = mensaje si el campo falló.
 */
function inap_campo(string $id, string $label, array $o = [], string $valor = '', string $error = ''): string
{
    $tipo = $o['type'] ?? 'text';
    $ayuda = $o['ayuda'] ?? '';
    $desc = trim(($ayuda ? "{$id}-ayuda " : '') . ($error ? "{$id}-error" : ''));
    $attrs = sprintf(
        'id="%1$s" name="%1$s"%2$s%3$s%4$s%5$s',
        esc_attr($id),
        !empty($o['required']) ? ' required' : '',
        !empty($o['autocomplete']) ? ' autocomplete="' . esc_attr($o['autocomplete']) . '"' : '',
        !empty($o['placeholder']) ? ' placeholder="' . esc_attr($o['placeholder']) . '"' : '',
        ($desc ? ' aria-describedby="' . esc_attr($desc) . '"' : '') . ($error ? ' aria-invalid="true"' : '')
    );
    $valor_attr = $valor !== '' ? ' value="' . esc_attr($valor) . '"' : '';
    $control = match ($tipo) {
        'textarea' => "<textarea {$attrs} rows=\"4\" class=\"textarea w-full" . ($error ? ' textarea-error' : '') . '">' . esc_textarea($valor) . '</textarea>',
        'select' => "<select {$attrs} class=\"select w-full" . ($error ? ' select-error' : '') . "\"><option value=\"\">Selecciona una opción</option>" . implode('', array_map(fn ($v) => '<option' . selected($v, $valor, false) . '>' . esc_html($v) . '</option>', $o['options'])) . '</select>',
        default => "<input type=\"{$tipo}\" {$attrs} class=\"input w-full" . ($error ? ' input-error' : '') . "\"{$valor_attr} />",
    };
    return '<fieldset class="fieldset min-w-0 flex-1 p-0"><label class="fieldset-legend" for="' . esc_attr($id) . '">' . esc_html($label) . '</label>' . $control
        . ($ayuda ? '<p id="' . esc_attr($id) . '-ayuda" class="label whitespace-normal text-base-content/60">' . esc_html($ayuda) . '</p>' : '')
        . ($error ? '<p id="' . esc_attr($id) . '-error" class="label whitespace-normal text-error-text">' . esc_html($error) . '</p>' : '')
        . '</fieldset>';
}

/** Breadcrumb de escritorio (barra de ancho completo). $items = [[texto, url|null], …]; el último es la página actual. */
function inap_breadcrumb(array $items): void
{
    echo '<div class="relative z-10 hidden bg-surface-muted lg:block"><nav aria-label="Breadcrumb" class="breadcrumbs mx-auto max-w-360 px-30 py-3 text-sm"><ul>';
    $last = count($items) - 1;
    foreach ($items as $i => [$label, $url]) {
        echo $i === $last || !$url
            ? '<li><span aria-current="page">' . esc_html($label) . '</span></li>'
            : '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
    }
    echo '</ul></nav></div>';
}

/** Enlace «Regresar» de celular, en lugar del breadcrumb. */
function inap_back_link(string $label, string $url): void
{
    printf(
        '<div class="relative z-10 flex h-11 items-center bg-surface-muted px-4 lg:hidden"><a href="%s" class="flex items-center gap-1 rounded-sm py-3 text-sm font-medium text-primary-text focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-text">%s%s</a></div>',
        esc_url($url), inap_icon('chevron-left', 16), esc_html($label)
    );
}
