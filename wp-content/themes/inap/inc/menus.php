<?php
/**
 * Barra, mega menús, menú de celular y pie, armados con los menús de Apariencia › Menús y las
 * plantillas de parts/ (mismo HTML que el sitio de referencia; tools/sync-from-angular.sh).
 *
 * Estructura de los menús:
 *   Barra superior    enlaces sueltos (Noticias, Eventos…); también van en la tarjeta azul del celular.
 *   Menú principal    1.er nivel = botón del mega menú; 2.º nivel = columnas; 3.er nivel = enlaces.
 *                     Un elemento de 1.er nivel sin hijos es un enlace directo. Los campos extra del
 *                     1.er nivel (enlace del pie del panel, columna lateral, nombre en celular) están
 *                     al abrir el elemento.
 *   Pie de página     1.er nivel = columnas; 2.º nivel = enlaces.
 *   Redes sociales    enlaces sueltos.
 * La descripción de un enlace sale debajo del título en el mega menú (no en celular).
 * «Abrir en una pestaña nueva» le pone el ícono ↗ y el aviso para lectores de pantalla.
 */

defined('ABSPATH') || exit;

const INAP_MENUS = [
    'utilidad' => 'Barra superior',
    'principal' => 'Menú principal (mega menús)',
    'pie' => 'Pie de página',
    'redes' => 'Redes sociales',
];

add_action('after_setup_theme', fn () => register_nav_menus(INAP_MENUS));

/* ── Plantillas ───────────────────────────────────────────────────────────── */

/** Plantilla de parts/ con sus {{huecos}} llenos. Los valores ya van escapados. */
function inap_tpl(string $name, array $vars = []): string
{
    static $cache = [];
    if (!isset($cache[$name])) {
        $file = get_theme_file_path("parts/{$name}.html");
        $cache[$name] = is_readable($file)
            ? str_replace(['{{ASSETS}}', 'href="/"', 'action="/"'], [esc_url(get_theme_file_uri('assets')), 'href="' . esc_url(home_url('/')) . '"', 'action="' . esc_url(home_url('/')) . '"'], file_get_contents($file))
            : "<!-- falta parts/{$name}.html: correr tools/sync-from-angular.sh -->";
    }
    $pares = [];
    foreach ($vars as $k => $v) {
        $pares['{{' . $k . '}}'] = (string) $v;
    }
    return strtr($cache[$name], $pares);
}

/** Lista HTML ya armada: se quita el salto final para no dejar líneas vacías. */
function inap_tpl_lista(array $piezas): string
{
    return implode("\n", array_map('rtrim', $piezas));
}

/* ── Elementos de menú ────────────────────────────────────────────────────── */

/**
 * Elementos de un menú como árbol: [['item' => WP_Post, 'hijos' => […]], …].
 * Vacío si la ubicación no tiene menú asignado.
 */
function inap_menu(string $ubicacion): array
{
    static $cache = [];
    if (isset($cache[$ubicacion])) {
        return $cache[$ubicacion];
    }
    $ids = get_nav_menu_locations();
    $items = !empty($ids[$ubicacion]) ? (wp_get_nav_menu_items($ids[$ubicacion]) ?: []) : [];
    $nodos = [];
    foreach ($items as $item) {
        $nodos[$item->ID] = ['item' => $item, 'hijos' => []];
    }
    $raiz = [];
    foreach ($items as $item) {
        $padre = (int) $item->menu_item_parent;
        if ($padre && isset($nodos[$padre])) {
            $nodos[$padre]['hijos'][] = &$nodos[$item->ID];
        } else {
            $raiz[] = &$nodos[$item->ID];
        }
    }
    return $cache[$ubicacion] = $raiz;
}

function inap_menu_vars(WP_Post $item): array
{
    return ['titulo' => esc_html($item->title), 'url' => esc_url($item->url ?: '#')];
}

function inap_menu_externo(WP_Post $item): bool
{
    return $item->target === '_blank';
}

/** Enlace del mega menú: con o sin descripción, interno o externo. */
function inap_mega_enlace(WP_Post $item, bool $con_descripcion = true): string
{
    $desc = $con_descripcion ? trim((string) $item->description) : '';
    $tpl = 'mega-enlace' . (inap_menu_externo($item) ? '-ext' : '') . ($desc !== '' ? '-desc' : '');
    return inap_tpl($tpl, inap_menu_vars($item) + ['descripcion' => esc_html($desc)]);
}

/* ── Contacto y enlaces de Ajustes › INAP ─────────────────────────────────── */

function inap_contacto_vars(bool $en_lineas): array
{
    $telefono = inap_ajuste('inap_ajustes', 'telefono');
    $digitos = preg_replace('/\D+/', '', $telefono);
    $lineas = array_filter(array_map('trim', explode("\n", inap_ajuste('inap_ajustes', 'direccion'))));
    return [
        'telefono' => esc_html($telefono),
        'tel' => esc_attr('+' . (str_starts_with($digitos, '502') ? '' : '502') . $digitos),
        'correo' => esc_html(inap_ajuste('inap_ajustes', 'correo')),
        'direccion' => $en_lineas ? implode('<br />', array_map('esc_html', $lineas)) : esc_html(implode(', ', $lineas)),
    ];
}

function inap_enlaces_vars(): array
{
    $plataforma = untrailingslashit(inap_ajuste('inap_ajustes', 'plataforma_url'));
    return [
        'aula_url' => esc_url(inap_ajuste('inap_ajustes', 'aula_url') ?: '#'),
        'login_url' => esc_url($plataforma . '/login'),
        'registro_url' => esc_url($plataforma . '/registro'),
    ];
}

/* ── Barra, mega menús y menú de celular ──────────────────────────────────── */

/** Barra de escritorio con sus mega menús, y barra + menú de celular. */
function inap_cabecera(): string
{
    $principal = inap_menu('principal');
    $utilidad = inap_menu('utilidad');

    $items = $paneles = $secciones = [];
    foreach ($principal as $i => $nodo) {
        $item = $nodo['item'];
        if (!$nodo['hijos']) {
            $items[] = inap_tpl('nav-enlace', inap_menu_vars($item));
            $movil = get_post_meta($item->ID, '_inap_movil', true) ?: $item->title;
            $secciones[] = inap_tpl('movil-enlace', ['titulo' => esc_html($movil)] + inap_menu_vars($item));
            continue;
        }
        $items[] = inap_tpl('nav-mega', ['i' => $i] + inap_menu_vars($item));

        $columnas = $grupos = [];
        foreach ($nodo['hijos'] as $c => $col) {
            $columnas[] = inap_tpl('mega-columna', [
                'id' => "nav-mega-{$i}-h{$c}",
                'titulo' => esc_html($col['item']->title),
                'enlaces' => inap_tpl_lista(array_map(fn ($l) => inap_mega_enlace($l['item']), $col['hijos'])),
            ]);
            $grupos[] = inap_tpl('movil-grupo', [
                'id' => "nav-sec-{$i}-g{$c}",
                'titulo' => esc_html($col['item']->title),
                'enlaces' => inap_tpl_lista(array_map(fn ($l) => inap_mega_enlace($l['item'], false), $col['hijos'])),
            ]);
        }
        $panel = inap_tpl('mega-panel', [
            'i' => $i,
            'columnas' => inap_tpl_lista($columnas),
            'hub' => esc_html(get_post_meta($item->ID, '_inap_hub', true) ?: 'Ver ' . mb_strtolower($item->title)),
            'hub_url' => esc_url($item->url ?: '#'),
            'lateral' => inap_mega_lateral($item),
        ]);
        $paneles[] = str_replace('<div id="nav-mega-' . $i . '" class="', '<div id="nav-mega-' . $i . '" hidden class="', $panel);

        $movil = get_post_meta($item->ID, '_inap_movil', true) ?: $item->title;
        $secciones[] = inap_tpl('movil-seccion', ['id' => "nav-sec-{$i}", 'titulo' => esc_html($movil), 'grupos' => inap_tpl_lista($grupos)]);
    }

    $enlaces = inap_enlaces_vars();
    $ultimo = count($utilidad) - 1;
    $escritorio = inap_tpl('header-desktop', $enlaces + [
        'utilidad' => inap_tpl_lista(array_map(fn ($n) => inap_tpl('utilidad', inap_menu_vars($n['item'])), $utilidad)),
        'principal' => inap_tpl_lista($items),
    ]);
    $menu_movil = inap_tpl('menu-mobile', $enlaces + [
        'secciones' => inap_tpl_lista($secciones),
        'movil_utilidad' => inap_tpl_lista(array_map(fn ($n, $k) => inap_tpl($k === $ultimo ? 'movil-utilidad-ultimo' : 'movil-utilidad', inap_menu_vars($n['item'])), $utilidad, array_keys($utilidad))),
    ]);

    return '<div class="relative z-40 hidden lg:block" data-barra>' . $escritorio
        . '<div class="absolute inset-x-0 top-full"><div class="mx-auto mt-2 max-w-360 px-20">' . implode('', $paneles) . '</div></div></div>'
        . '<div class="relative z-30 lg:hidden">' . inap_tpl('header-mobile', $enlaces) . $menu_movil . '</div>';
}

/** Columna lateral del mega menú: tarjeta destacada, contacto o nada. */
function inap_mega_lateral(WP_Post $item): string
{
    $tipo = get_post_meta($item->ID, '_inap_lateral', true);
    if ($tipo === 'contacto') {
        return inap_tpl('mega-contacto', inap_contacto_vars(false));
    }
    if ($tipo !== 'destacado') {
        return '';
    }
    $m = fn (string $k) => (string) get_post_meta($item->ID, "_inap_dest_{$k}", true);
    return inap_tpl('mega-destacado', [
        'icono' => esc_attr($m('icono') ?: 'file-text'),
        'titulo' => esc_html($m('titulo')),
        'texto' => esc_html($m('texto')),
        'boton' => esc_html($m('boton') ?: 'Ver más'),
        'url' => esc_url($m('url') ?: '#'),
    ]);
}

/* ── Pie ──────────────────────────────────────────────────────────────────── */

function inap_pie(): string
{
    $columnas = array_map(fn ($nodo, $i) => inap_tpl('pie-columna', [
        'id' => 'pie-col' . ($i + 1),
        'titulo' => esc_html($nodo['item']->title),
        'enlaces' => inap_tpl_lista(array_map(fn ($l) => inap_tpl('pie-enlace', inap_menu_vars($l['item'])), $nodo['hijos'])),
    ]), inap_menu('pie'), array_keys(inap_menu('pie')));

    return inap_tpl('footer', inap_contacto_vars(true) + [
        'columnas' => inap_tpl_lista($columnas),
        'redes' => inap_tpl_lista(array_map(fn ($n) => inap_tpl('red', inap_menu_vars($n['item'])), inap_menu('redes'))),
        'anio' => esc_html(wp_date('Y')),
    ]);
}

/* ── Campos extra del 1.er nivel del menú principal ───────────────────────── */

const INAP_MENU_CAMPOS = [
    '_inap_movil' => ['Nombre en el menú de celular', 'text', 'Opcional. Por ejemplo «Fortalecimiento institucional».'],
    '_inap_hub' => ['Texto del enlace al pie del panel', 'text', 'Lleva a la dirección (URL) de este elemento. Por ejemplo «Ver todos los recursos».'],
    '_inap_lateral' => ['Columna lateral', 'select', ''],
    '_inap_dest_titulo' => ['Tarjeta: título', 'text', ''],
    '_inap_dest_texto' => ['Tarjeta: texto', 'textarea', ''],
    '_inap_dest_boton' => ['Tarjeta: texto del enlace', 'text', 'Por ejemplo «Descargar PDF».'],
    '_inap_dest_url' => ['Tarjeta: dirección del enlace', 'url', ''],
    '_inap_dest_icono' => ['Tarjeta: ícono', 'text', 'Nombre de un ícono de lucide.dev, por ejemplo file-text, building-2 o book-open.'],
];

/** Solo los elementos de 1.er nivel del menú asignado a «Menú principal». */
function inap_es_item_principal(int $item_id): bool
{
    $menu = get_nav_menu_locations()['principal'] ?? 0;
    return $menu && has_term($menu, 'nav_menu', $item_id) && !(int) get_post_meta($item_id, '_menu_item_menu_item_parent', true);
}

add_action('wp_nav_menu_item_custom_fields', function ($item_id) {
    if (!inap_es_item_principal((int) $item_id)) {
        return;
    }
    echo '<fieldset class="inap-mega description-wide" style="margin:8px 0;padding:8px 12px;border:1px solid #dcdcde"><legend style="font-weight:600;padding:0 4px">Mega menú</legend>';
    foreach (INAP_MENU_CAMPOS as $clave => [$etiqueta, $tipo, $ayuda]) {
        $id = "{$clave}-{$item_id}";
        $name = "inap_menu[{$item_id}][{$clave}]";
        $valor = (string) get_post_meta($item_id, $clave, true);
        echo '<p class="description description-wide"><label for="' . esc_attr($id) . '">' . esc_html($etiqueta) . '<br />';
        if ($tipo === 'select') {
            echo '<select id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" class="widefat">';
            foreach (['' => 'Ninguna', 'destacado' => 'Tarjeta destacada (campos de abajo)', 'contacto' => 'Contacto (de Ajustes › INAP)'] as $v => $t) {
                echo '<option value="' . esc_attr($v) . '"' . selected($valor, $v, false) . '>' . esc_html($t) . '</option>';
            }
            echo '</select>';
        } elseif ($tipo === 'textarea') {
            echo '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" rows="2" class="widefat">' . esc_textarea($valor) . '</textarea>';
        } else {
            echo '<input id="' . esc_attr($id) . '" name="' . esc_attr($name) . '" type="' . esc_attr($tipo) . '" value="' . esc_attr($valor) . '" class="widefat" />';
        }
        echo '</label>' . ($ayuda ? '<span class="description">' . esc_html($ayuda) . '</span>' : '') . '</p>';
    }
    echo '</fieldset>';
});

add_action('wp_update_nav_menu_item', function ($menu_id, $item_id) {
    if (!isset($_POST['inap_menu'][$item_id]) || !current_user_can('edit_theme_options')) {
        return;
    }
    $datos = wp_unslash($_POST['inap_menu'][$item_id]);
    foreach (INAP_MENU_CAMPOS as $clave => [, $tipo]) {
        $v = (string) ($datos[$clave] ?? '');
        $v = match ($tipo) {
            'url' => esc_url_raw(trim($v)),
            'textarea' => sanitize_textarea_field($v),
            default => sanitize_text_field($v),
        };
        $v === '' ? delete_post_meta($item_id, $clave) : update_post_meta($item_id, $clave, $v);
    }
}, 10, 2);

/* Descripción y «Abrir en una pestaña nueva» vienen ocultos en Apariencia › Menús; aquí se usan. */
add_filter('get_user_option_managenav-menuscolumnshidden', function ($ocultas) {
    return is_array($ocultas) ? array_values(array_diff($ocultas, ['description', 'link-target'])) : ['title-attribute', 'css-classes', 'xfn'];
});

/* ── Contenido inicial ────────────────────────────────────────────────────── */

/**
 * Crea los cuatro menús con el contenido de los diseños (parts/menus.json) si aún no tienen menú.
 * No toca los menús que ya existen.
 */
function inap_semilla_menus(): void
{
    $json = get_theme_file_path('parts/menus.json');
    $datos = is_readable($json) ? json_decode(file_get_contents($json), true) : null;
    if (!$datos) {
        return;
    }
    $ubicaciones = get_nav_menu_locations();
    $url = fn (string $titulo) => $titulo === 'Transparencia' ? home_url('/transparencia/') : '#';
    $agregar = function (int $menu, string $titulo, int $padre = 0, array $extra = []) use ($url): int {
        return (int) wp_update_nav_menu_item($menu, 0, $extra + [
            'menu-item-title' => $titulo,
            'menu-item-url' => $url($titulo),
            'menu-item-type' => 'custom',
            'menu-item-status' => 'publish',
            'menu-item-parent-id' => $padre,
        ]);
    };
    $crear = function (string $ubicacion) use (&$ubicaciones): int {
        if (!empty($ubicaciones[$ubicacion]) && wp_get_nav_menu_object($ubicaciones[$ubicacion])) {
            return 0;
        }
        $existente = wp_get_nav_menu_object(INAP_MENUS[$ubicacion]);
        $id = $existente ? (int) $existente->term_id : (int) wp_create_nav_menu(INAP_MENUS[$ubicacion]);
        $ubicaciones[$ubicacion] = $id;
        return $existente ? 0 : $id;
    };

    if ($m = $crear('utilidad')) {
        foreach ($datos['utilidad'] as $t) {
            $agregar($m, $t);
        }
    }
    if ($m = $crear('redes')) {
        foreach ($datos['redes'] as $t) {
            $agregar($m, $t);
        }
    }
    if ($m = $crear('pie')) {
        foreach ($datos['pie'] as $col) {
            $padre = $agregar($m, $col['titulo']);
            foreach ($col['enlaces'] as $t) {
                $agregar($m, $t, $padre);
            }
        }
    }
    if ($m = $crear('principal')) {
        foreach ($datos['principal'] as $sec) {
            $padre = $agregar($m, $sec['titulo']);
            $meta = ['_inap_movil' => $sec['movil'], '_inap_hub' => $sec['hub']];
            if (is_array($sec['lateral'])) {
                $meta += ['_inap_lateral' => 'destacado'];
                foreach (['icono', 'titulo', 'texto', 'boton'] as $k) {
                    $meta["_inap_dest_{$k}"] = $sec['lateral'][$k];
                }
            } else {
                $meta['_inap_lateral'] = 'contacto';
            }
            foreach (array_filter($meta) as $k => $v) {
                update_post_meta($padre, $k, $v);
            }
            foreach ($sec['columnas'] as $col) {
                $columna = $agregar($m, $col['titulo'], $padre);
                foreach ($col['enlaces'] as $l) {
                    $agregar($m, $l['titulo'], $columna, array_filter([
                        'menu-item-description' => $l['descripcion'],
                        'menu-item-target' => $l['externo'] ? '_blank' : '',
                    ]));
                }
            }
        }
    }
    set_theme_mod('nav_menu_locations', $ubicaciones);
}
