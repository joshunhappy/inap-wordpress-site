<?php
/**
 * Tema INAP: recursos, íconos, ajustes editables, menús y el módulo de Transparencia.
 */

defined('ABSPATH') || exit;

const INAP_VERSION = '0.2.0';

require_once __DIR__ . '/inc/ajustes.php';
require_once __DIR__ . '/inc/menus.php';
require_once __DIR__ . '/inc/transparencia-admin.php';
require_once __DIR__ . '/inc/transparencia.php';

/** Contenido inicial (menús y numerales de los diseños). No pisa lo editado: solo crea lo que falta. */
function inap_semilla(): void
{
    inap_semilla_menus();
    inap_semilla_numerales();
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

/* Mientras no exista la portada del sitio institucional, el inicio lleva a Transparencia. */
add_action('template_redirect', function () {
    if (is_front_page() && !get_query_var('inap_tp')) {
        wp_safe_redirect(home_url('/transparencia/'), 302);
        exit;
    }
});

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
