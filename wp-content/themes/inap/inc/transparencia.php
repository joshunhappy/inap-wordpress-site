<?php
/**
 * Módulo de Transparencia (art. 10 de la Ley de Acceso a la Información Pública, Decreto 57-2008).
 *
 * Rutas propias (sin páginas en la base de datos):
 *   /transparencia/                                  inicio con el formulario de solicitud
 *   /transparencia/informacion-publica-de-oficio/    los 29 numerales
 *   /transparencia/articulo-10/{n}-{slug}/           un numeral
 *
 * Los datos salen de Transparencia › Numerales y los textos de Transparencia › Textos y ajustes
 * (inc/transparencia-admin.php). Al instalar, los numerales se crean con el contenido de ejemplo de Figma.
 */

defined('ABSPATH') || exit;

const INAP_TP_BASE = 'transparencia';

/* ── Rutas ────────────────────────────────────────────────────────────────── */

add_action('init', function () {
    add_rewrite_rule('^transparencia/?$', 'index.php?inap_tp=inicio', 'top');
    add_rewrite_rule('^transparencia/informacion-publica-de-oficio/?$', 'index.php?inap_tp=ipo', 'top');
    add_rewrite_rule('^transparencia/articulo-10/([0-9]{1,2})-[^/]+/?$', 'index.php?inap_tp=numeral&inap_n=$matches[1]', 'top');
});

add_filter('query_vars', fn ($v) => array_merge($v, ['inap_tp', 'inap_n']));

add_action('after_switch_theme', 'flush_rewrite_rules');

add_filter('template_include', function ($template) {
    $view = get_query_var('inap_tp');
    if (!$view) {
        return $template;
    }
    if ($view === 'numeral') {
        $n = (int) get_query_var('inap_n');
        if (!inap_tp_numeral($n)) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            return get_404_template() ?: $template;
        }
        // Una sola dirección por numeral: si el nombre cambió o el enlace está mal escrito, se redirige.
        $canonica = inap_tp_url('numeral', $n);
        $pedida = home_url(strtok($_SERVER['REQUEST_URI'] ?? '', '?'));
        if (untrailingslashit($pedida) !== untrailingslashit($canonica)) {
            $query = $_SERVER['QUERY_STRING'] ?? '';
            wp_safe_redirect($canonica . ($query !== '' ? '?' . $query : ''), 301);
            exit;
        }
    }
    status_header(200);
    return get_theme_file_path("templates/transparencia/{$view}.php");
});

add_filter('pre_get_document_title', function ($title) {
    $view = get_query_var('inap_tp');
    if (!$view) {
        return $title;
    }
    $page = match ($view) {
        'inicio' => 'Transparencia e información pública',
        'ipo' => 'Información pública de oficio',
        'numeral' => (inap_tp_numeral((int) get_query_var('inap_n'))['titulo'] ?? '') . ' · Artículo 10',
        default => $title,
    };
    return $page . ' · INAP';
});

/* ── Datos ────────────────────────────────────────────────────────────────── */

/** Texto de Transparencia › Textos y ajustes (o el de los diseños). */
function inap_tp_texto(string $clave): string
{
    return inap_ajuste('inap_transparencia', $clave);
}

function inap_tp_anio(): int
{
    return (int) inap_tp_texto('anio') ?: (int) wp_date('Y');
}

/** «2026-10-06» → «6 oct. 2026», como en los diseños. */
function inap_tp_fecha(string $iso): string
{
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $iso, $m)) {
        return '';
    }
    $meses = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sept.', 'oct.', 'nov.', 'dic.'];
    return (int) $m[3] . ' ' . $meses[(int) $m[2] - 1] . ' ' . $m[1];
}

/** Fila guardada → documento para las plantillas (componente Document Row). */
function inap_tp_doc(array $f): array
{
    return array_filter([
        'tipo' => $f['estado'],
        'titulo' => $f['titulo'],
        'fecha' => inap_tp_fecha($f['fecha'] ?? ''),
        'url' => $f['url'] ?: '#',
        'meta' => $f['nota'] ?? '',
    ], fn ($v) => $v !== '');
}

/** Fecha ISO más reciente de un grupo de filas. */
function inap_tp_ultima(array $filas): string
{
    $fechas = array_filter(array_column($filas, 'fecha'));
    return $fechas ? max($fechas) : '';
}

/** Un numeral guardado, con lo que muestran las plantillas: meta de la lista, resumen, documentos. */
function inap_tp_numeral_desde_post(WP_Post $post): array
{
    $m = fn (string $k) => get_post_meta($post->ID, "_inap_{$k}", true);
    $tipo = (string) $m('tipo');
    $filas = (array) ($m('filas') ?: []);
    $apartados = (array) ($m('apartados') ?: []);
    $anio = inap_tp_anio();

    $todas = $tipo === 'apartados' ? array_merge([], ...array_column($apartados, 'filas')) : ($tipo === 'periodos' ? $filas : []);
    $ultima = inap_tp_ultima($todas);
    $cuando = $ultima ? ' · última actualización el ' . inap_tp_fecha($ultima) : '';

    $num = [
        'n' => (int) $m('n'),
        'titulo' => get_the_title($post),
        'tipo' => $tipo,
        'que_publica' => (string) $m('que_publica'),
        'ultima' => $ultima,
        'meta' => match (true) {
            $tipo === 'sin-info' => "Sin información en {$anio}",
            $ultima !== '' => 'Actualizado el ' . inap_tp_fecha($ultima),
            default => 'Aún sin documentos publicados',
        },
    ];
    if ($tipo === 'periodos') {
        $publicados = count(array_filter($filas, fn ($f) => $f['estado'] === 'enlace'));
        $num['resumen'] = sprintf('%d %s en OneDrive%s', $publicados, $publicados === 1 ? 'publicado' : 'publicados', $cuando);
        $num['periodos'] = array_map('inap_tp_doc', $filas);
    } elseif ($tipo === 'apartados') {
        // Apartados seguidos con el mismo grupo van juntos bajo el título del grupo.
        $num['apartados'] = [];
        foreach ($apartados as $a) {
            $item = ['titulo' => $a['titulo'], 'periodos' => array_map('inap_tp_doc', $a['filas'])];
            $k = array_key_last($num['apartados']);
            if ($a['grupo'] !== '' && $k !== null && isset($num['apartados'][$k]['sub']) && $num['apartados'][$k]['titulo'] === $a['grupo']) {
                $num['apartados'][$k]['sub'][] = $item;
            } elseif ($a['grupo'] !== '') {
                $num['apartados'][] = ['titulo' => $a['grupo'], 'sub' => [$item]];
            } else {
                $num['apartados'][] = $item;
            }
        }
        $num['resumen'] = count($num['apartados']) . ' apartados' . $cuando;
    } elseif ($tipo === 'sin-info') {
        $num['resumen'] = "Sin documentos en {$anio}";
        $num['aviso'] = ['titulo' => (string) $m('aviso_titulo'), 'texto' => (string) $m('aviso_texto')];
    }
    return $num;
}

function inap_tp_datos(): array
{
    static $datos = null;
    if ($datos !== null) {
        return $datos;
    }
    $numerales = [];
    foreach (get_posts(['post_type' => INAP_TP_TIPO, 'post_status' => 'publish', 'numberposts' => -1]) as $post) {
        $num = inap_tp_numeral_desde_post($post);
        $numerales[$num['n']] ??= $num;
    }
    ksort($numerales);
    $ultima = $numerales ? max(array_column($numerales, 'ultima')) : '';
    return $datos = [
        'anio' => inap_tp_anio(),
        'actualizado' => inap_tp_fecha($ultima),
        'numerales' => $numerales,
    ];
}

/** @return array<int, array> numerales indexados por número (1–29) */
function inap_tp_numerales(): array
{
    return inap_tp_datos()['numerales'];
}

function inap_tp_numeral(int $n): ?array
{
    return inap_tp_numerales()[$n] ?? null;
}

/** Opciones del selector «Año»: el año actual y los anteriores con su enlace de OneDrive. */
function inap_tp_anios(): array
{
    $anios = [['anio' => inap_tp_anio(), 'url' => '']];
    foreach (explode("\n", inap_tp_texto('anios_anteriores')) as $linea) {
        if (preg_match('/^\s*(\d{4})\s*\|\s*(\S+)/', $linea, $m)) {
            $anios[] = ['anio' => (int) $m[1], 'url' => esc_url_raw($m[2])];
        }
    }
    return $anios;
}

/** Selector «Año»: con años anteriores lleva botón «Ir» (no navega al cambiar, WCAG 3.2.2). */
function inap_tp_selector_anio(string $class = ''): string
{
    $anios = inap_tp_anios();
    $opciones = implode('', array_map(fn ($a) => sprintf('<option value="%s"%s>%d</option>', esc_attr($a['url']), $a['url'] === '' ? ' selected' : '', $a['anio']), $anios));
    $boton = count($anios) > 1 ? '<button type="submit" class="btn btn-outline btn-primary h-10">Ir</button>' : '';
    return sprintf(
        '<form method="get" data-ir class="flex items-end gap-2 %s"><fieldset class="fieldset min-w-0 flex-1 p-0"><label class="fieldset-legend" for="anio">Año</label><select id="anio" class="select w-full">%s</select></fieldset>%s</form>',
        esc_attr($class), $opciones, $boton
    );
}

/** Contacto de la Unidad de Información Pública (Textos y ajustes). */
function inap_tp_unidad(): array
{
    $tel = inap_tp_texto('telefono');
    $dias = (int) inap_tp_texto('dias') ?: 10;
    return [
        'correo' => inap_tp_texto('correo'),
        'telefono' => $tel,
        'tel' => '+502' . preg_replace('/\D+/', '', preg_replace('/^\s*\(?\+?502\)?/', '', $tel)),
        'direccion' => inap_tp_texto('direccion'),
        'plazo' => "Respuesta en un máximo de {$dias} días hábiles",
        'plazo_corto' => "Respuesta en {$dias} días hábiles",
    ];
}

function inap_tp_url(string $view = 'inicio', ?int $n = null): string
{
    return match ($view) {
        'ipo' => home_url('/transparencia/informacion-publica-de-oficio/'),
        'numeral' => home_url(sprintf('/transparencia/articulo-10/%d-%s/', $n, sanitize_title(inap_tp_numeral($n)['titulo']))),
        default => home_url('/transparencia/'),
    };
}

/* ── Piezas ───────────────────────────────────────────────────────────────── */

const INAP_FOCUS = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-text';

/**
 * Fila de un período (componente Document Row).
 * $doc = ['tipo' => enlace|pendiente|sin-informacion, 'titulo' => 'Septiembre 2026', 'meta' => …, 'url' => …]
 * $contexto = nombre del numeral o apartado, para el aria-label del botón.
 */
function inap_tp_documento(array $doc, string $contexto): string
{
    $icono = ['enlace' => 'external-link', 'pendiente' => 'clock', 'sin-informacion' => 'minus'][$doc['tipo']];
    $color = $doc['tipo'] === 'enlace' ? 'text-primary-text' : 'text-neutral-text';
    $meta = $doc['meta'] ?? match ($doc['tipo']) {
        'pendiente' => 'Aún no se ha publicado.',
        'sin-informacion' => 'Sin movimiento en este período.',
        default => 'Documentos en OneDrive · actualizado el ' . $doc['fecha'],
    };
    $accion = '';
    if ($doc['tipo'] === 'enlace') {
        $accion = sprintf(
            '<a href="%s" target="_blank" rel="noopener" aria-label="%s" class="flex h-11 shrink-0 items-center gap-2 rounded-field border border-base-border px-3 text-sm font-semibold text-primary-text hover:bg-surface-subtle %s">%s<span aria-hidden="true">Abrir</span></a>',
            esc_url($doc['url'] ?? '#'),
            esc_attr(sprintf('Abrir %s, %s, en OneDrive (se abre en una pestaña nueva)', mb_strtolower($contexto), mb_strtolower($doc['titulo']))),
            INAP_FOCUS,
            inap_icon('external-link', 16)
        );
    }
    return sprintf(
        '<li class="flex items-center gap-4 border-b border-base-border py-4"><span class="flex size-10 shrink-0 items-center justify-center rounded-field bg-surface-subtle %s">%s</span><span class="flex min-w-0 flex-1 flex-col gap-0.5 text-sm"><span class="font-semibold text-base-content">%s</span><span class="text-neutral-text">%s</span></span>%s</li>',
        $color, inap_icon($icono, 20), esc_html($doc['titulo']), esc_html($meta), $accion
    );
}

/**
 * Lista de períodos: los dos más recientes a la vista y los anteriores tras «Meses anteriores (N)».
 * $abierto muestra la lista completa (para enlazar el estado abierto).
 */
function inap_tp_periodos(array $docs, string $contexto, string $id, bool $abierto = false): string
{
    $visibles = array_slice($docs, 0, 2);
    $resto = array_slice($docs, 2);
    $html = '<ul class="flex flex-col">' . implode('', array_map(fn ($d) => inap_tp_documento($d, $contexto), $visibles)) . '</ul>';
    if ($resto) {
        $n = count($resto);
        $html .= sprintf('<ul id="%s" class="flex flex-col"%s>%s</ul>', esc_attr($id), $abierto ? '' : ' hidden', implode('', array_map(fn ($d) => inap_tp_documento($d, $contexto), $resto)));
        $html .= sprintf(
            '<button type="button" data-meses="%1$d" aria-expanded="%2$s" aria-controls="%3$s" class="group mt-4 flex h-11 items-center gap-2 self-start rounded-field px-3 text-sm font-semibold text-primary-text hover:bg-surface-subtle %4$s"><span data-label>%5$s</span>%6$s</button>',
            $n, $abierto ? 'true' : 'false', esc_attr($id), INAP_FOCUS,
            $abierto ? "Colapsar meses ({$n})" : "Meses anteriores ({$n})",
            inap_icon('chevron-down', 16, 'transition-transform group-aria-expanded:rotate-180')
        );
    }
    return '<div class="flex flex-col">' . $html . '</div>';
}

/** Ícono + texto de una fila de contacto. */
function inap_tp_contacto(string $icono, string $html, string $icon_color = 'text-base-content'): string
{
    return sprintf('<li class="flex items-start gap-2 text-sm">%s<span>%s</span></li>', inap_icon($icono, 20, "shrink-0 {$icon_color}"), $html);
}

/* ── Formulario de solicitud ──────────────────────────────────────────────── */

/*
  Entorno de prueba: valida y vuelve a la página, pero no envía ni guarda la solicitud.
  Falta definir con la Unidad de Información Pública cómo se reciben (correo, sistema propio).
*/
add_action('admin_post_nopriv_inap_solicitud', 'inap_tp_recibir_solicitud');
add_action('admin_post_inap_solicitud', 'inap_tp_recibir_solicitud');

function inap_tp_recibir_solicitud(): void
{
    $volver = inap_tp_url() . '#solicitud';
    if (!wp_verify_nonce($_POST['_inap'] ?? '', 'inap_solicitud')) {
        wp_safe_redirect(add_query_arg('solicitud', 'expirada', $volver));
        exit;
    }
    $correo = sanitize_email(wp_unslash($_POST['correo'] ?? ''));
    $pedido = trim(sanitize_textarea_field(wp_unslash($_POST['pedido'] ?? '')));
    $errores = [];
    if (!is_email($correo)) {
        $errores[] = 'correo';
    }
    if ($pedido === '') {
        $errores[] = 'pedido';
    }
    $args = $errores ? ['solicitud' => 'error', 'campos' => implode(',', $errores)] : ['solicitud' => 'prueba'];
    wp_safe_redirect(add_query_arg($args, inap_tp_url()) . '#solicitud');
    exit;
}
