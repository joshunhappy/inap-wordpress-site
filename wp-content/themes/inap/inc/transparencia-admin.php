<?php
/**
 * Transparencia en el administrador:
 *   Transparencia › Numerales           un elemento por numeral del artículo 10, con sus filas (meses o documentos)
 *   Transparencia › Textos y ajustes    textos de las páginas, año, enlaces y datos de la Unidad de Información Pública
 *
 * Cada fila es un enlace a una carpeta de OneDrive, «pendiente» o «sin información». Las páginas
 * públicas (inc/transparencia.php) leen estos datos; las plantillas no cambian.
 */

defined('ABSPATH') || exit;

const INAP_TP_TIPO = 'inap_numeral';

const INAP_TP_ESTADOS = [
    'enlace' => 'Publicado (enlace)',
    'pendiente' => 'Pendiente',
    'sin-informacion' => 'Sin información',
];

/* ── Tipo de contenido ────────────────────────────────────────────────────── */

add_action('init', function () {
    register_post_type(INAP_TP_TIPO, [
        'labels' => [
            'name' => 'Numerales',
            'singular_name' => 'Numeral',
            'menu_name' => 'Transparencia',
            'all_items' => 'Numerales',
            'edit_item' => 'Editar numeral',
            'add_new' => 'Agregar numeral',
            'add_new_item' => 'Agregar numeral',
            'search_items' => 'Buscar numerales',
            'not_found' => 'No hay numerales.',
            'item_updated' => 'Numeral actualizado.',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => false,
        'menu_position' => 21,
        'menu_icon' => 'dashicons-visibility',
        'capability_type' => 'page',
        'map_meta_cap' => true,
        'supports' => ['title'],
    ]);
});

add_filter('enter_title_here', fn ($t, $post) => $post->post_type === INAP_TP_TIPO ? 'Nombre del numeral' : $t, 10, 2);

/* ── Lista: ordenada por número, con columna «Actualizado» ────────────────── */

add_action('pre_get_posts', function (WP_Query $q) {
    if (is_admin() && $q->is_main_query() && $q->get('post_type') === INAP_TP_TIPO && !$q->get('orderby')) {
        $q->set('meta_key', '_inap_n');
        $q->set('orderby', 'meta_value_num');
        $q->set('order', 'ASC');
        $q->set('posts_per_page', 50);
    }
});

add_filter('manage_' . INAP_TP_TIPO . '_posts_columns', fn ($cols) => [
    'cb' => $cols['cb'],
    'inap_n' => 'N.º',
    'title' => 'Numeral',
    'inap_resumen' => 'En la página',
]);

add_action('manage_' . INAP_TP_TIPO . '_posts_custom_column', function ($col, $post_id) {
    if ($col === 'inap_n') {
        echo (int) get_post_meta($post_id, '_inap_n', true);
    } elseif ($col === 'inap_resumen') {
        echo esc_html(inap_tp_numeral_desde_post(get_post($post_id))['meta']);
    }
}, 10, 2);

/* ── Pantalla de edición ──────────────────────────────────────────────────── */

add_action('add_meta_boxes_' . INAP_TP_TIPO, function () {
    add_meta_box('inap-numeral', 'Contenido del numeral', 'inap_tp_caja', INAP_TP_TIPO, 'normal', 'high');
    add_meta_box('inap-numeral-ayuda', 'Cómo publicar un mes', fn () => print('<ol style="margin-left:1.2em"><li>En OneDrive, comparte la carpeta del mes con «Cualquier persona con el vínculo» y copia el vínculo.</li><li>Aquí, en la fila del mes: elige «Publicado (enlace)», pega el vínculo y pon la fecha de hoy.</li><li>Para el mes que sigue, usa «Agregar fila arriba» con «Pendiente».</li><li>Pulsa «Actualizar».</li></ol><p>Las filas se muestran en el orden de esta pantalla: la más reciente arriba.</p>'), INAP_TP_TIPO, 'side');
});

/** Una fila del editor. $name = prefijo del campo, por ejemplo «inap[filas][3]». */
function inap_tp_fila_editor(string $name, array $f = []): string
{
    $f += ['titulo' => '', 'estado' => 'pendiente', 'url' => '', 'fecha' => '', 'nota' => ''];
    $opciones = '';
    foreach (INAP_TP_ESTADOS as $v => $t) {
        $opciones .= '<option value="' . esc_attr($v) . '"' . selected($f['estado'], $v, false) . '>' . esc_html($t) . '</option>';
    }
    return '<tr class="inap-fila">'
        . '<td><input type="text" name="' . esc_attr("{$name}[titulo]") . '" value="' . esc_attr($f['titulo']) . '" placeholder="Septiembre 2026" aria-label="Título" class="widefat" /></td>'
        . '<td><select name="' . esc_attr("{$name}[estado]") . '" aria-label="Estado">' . $opciones . '</select></td>'
        . '<td><input type="url" name="' . esc_attr("{$name}[url]") . '" value="' . esc_attr($f['url']) . '" placeholder="https://inap-my.sharepoint.com/…" aria-label="Enlace de OneDrive" class="widefat" /></td>'
        . '<td><input type="date" name="' . esc_attr("{$name}[fecha]") . '" value="' . esc_attr($f['fecha']) . '" aria-label="Fecha de actualización" /></td>'
        . '<td><input type="text" name="' . esc_attr("{$name}[nota]") . '" value="' . esc_attr($f['nota']) . '" aria-label="Nota (opcional)" class="widefat" /></td>'
        . '<td class="inap-acciones"><button type="button" class="button-link" data-inap="subir" aria-label="Subir fila">↑</button> <button type="button" class="button-link" data-inap="bajar" aria-label="Bajar fila">↓</button> <button type="button" class="button-link button-link-delete" data-inap="quitar">Quitar</button></td>'
        . '</tr>';
}

/** Tabla de filas con sus botones. $name = prefijo («inap[filas]»); __i__ se reemplaza al agregar filas. */
function inap_tp_filas_editor(string $name, array $filas): string
{
    $html = '<div class="inap-filas" data-name="' . esc_attr($name) . '">'
        . '<p><button type="button" class="button" data-inap="agregar-arriba">Agregar fila arriba</button></p>'
        . '<table class="widefat striped"><thead><tr><th>Título</th><th>Estado</th><th>Enlace de OneDrive</th><th>Actualizado el</th><th>Nota (opcional)</th><th><span class="screen-reader-text">Acciones</span></th></tr></thead><tbody>';
    foreach (array_values($filas) as $i => $f) {
        $html .= inap_tp_fila_editor("{$name}[{$i}]", $f);
    }
    return $html . '</tbody></table>'
        . '<p><button type="button" class="button" data-inap="agregar-abajo">Agregar fila abajo</button></p>'
        . '<template>' . inap_tp_fila_editor("{$name}[__i__]") . '</template></div>';
}

function inap_tp_apartado_editor(string $name, array $a = []): string
{
    $a += ['grupo' => '', 'titulo' => '', 'filas' => []];
    return '<fieldset class="inap-apartado"><legend class="screen-reader-text">Apartado</legend>'
        . '<p class="inap-apartado-campos"><label>Título del apartado<br /><input type="text" name="' . esc_attr("{$name}[titulo]") . '" value="' . esc_attr($a['titulo']) . '" class="regular-text" /></label> '
        . '<label>Grupo (opcional)<br /><input type="text" name="' . esc_attr("{$name}[grupo]") . '" value="' . esc_attr($a['grupo']) . '" class="regular-text" /></label> '
        . '<span class="inap-acciones"><button type="button" class="button-link" data-inap="subir-apartado">↑ Subir</button> <button type="button" class="button-link" data-inap="bajar-apartado">↓ Bajar</button> <button type="button" class="button-link button-link-delete" data-inap="quitar-apartado">Quitar apartado</button></span></p>'
        . inap_tp_filas_editor("{$name}[filas]", $a['filas'])
        . '</fieldset>';
}

function inap_tp_caja(WP_Post $post): void
{
    $m = fn (string $k, $def = '') => get_post_meta($post->ID, "_inap_{$k}", true) ?: $def;
    $tipo = $m('tipo');
    wp_nonce_field('inap_numeral', '_inap_numeral');
    ?>
    <table class="form-table" role="presentation"><tbody>
      <tr><th scope="row"><label for="inap-n">Número</label></th>
        <td><input id="inap-n" type="number" min="1" max="29" name="inap[n]" value="<?php echo esc_attr($m('n')); ?>" class="small-text" required />
          <p class="description">Del 1 al 29, según el artículo 10. Define el orden y la dirección de la página.</p></td></tr>
      <tr><th scope="row"><label for="inap-que">Qué publica INAP en este numeral</label></th>
        <td><textarea id="inap-que" name="inap[que_publica]" rows="3" class="large-text"><?php echo esc_textarea($m('que_publica')); ?></textarea></td></tr>
      <tr><th scope="row">Cómo se muestra</th>
        <td><fieldset><legend class="screen-reader-text">Cómo se muestra</legend>
          <?php foreach (['periodos' => 'Una lista de meses o documentos', 'apartados' => 'Varios apartados, cada uno con su lista (ej. numeral 4)', 'sin-info' => 'Sin información este año (aviso)', '' => 'Todavía sin documentos'] as $v => $t) : ?>
            <label style="display:block;margin-bottom:4px"><input type="radio" name="inap[tipo]" value="<?php echo esc_attr($v); ?>" <?php checked($tipo, $v); ?> data-inap-tipo /> <?php echo esc_html($t); ?></label>
          <?php endforeach; ?>
        </fieldset></td></tr>
    </tbody></table>

    <div data-inap-panel="periodos">
      <h3>Lista</h3>
      <?php echo inap_tp_filas_editor('inap[filas]', (array) $m('filas', [])); ?>
    </div>

    <div data-inap-panel="apartados">
      <h3>Apartados</h3>
      <p class="description">Los apartados con el mismo «Grupo» van juntos bajo ese título (por ejemplo, Viáticos al exterior y al interior, en el grupo Viáticos).</p>
      <div class="inap-apartados" data-name="inap[apartados]">
        <?php foreach (array_values((array) $m('apartados', [])) as $i => $a) {
            echo inap_tp_apartado_editor("inap[apartados][{$i}]", $a);
        } ?>
        <template><?php echo inap_tp_apartado_editor('inap[apartados][__a__]'); ?></template>
      </div>
      <p><button type="button" class="button" data-inap="agregar-apartado">Agregar apartado</button></p>
    </div>

    <div data-inap-panel="sin-info">
      <table class="form-table" role="presentation"><tbody>
        <tr><th scope="row"><label for="inap-aviso-t">Título del aviso</label></th>
          <td><input id="inap-aviso-t" type="text" name="inap[aviso_titulo]" value="<?php echo esc_attr($m('aviso_titulo')); ?>" placeholder="INAP no tiene fideicomisos en <?php echo esc_attr(inap_tp_anio()); ?>" class="large-text" /></td></tr>
        <tr><th scope="row"><label for="inap-aviso-x">Texto del aviso</label></th>
          <td><textarea id="inap-aviso-x" name="inap[aviso_texto]" rows="3" class="large-text"><?php echo esc_textarea($m('aviso_texto')); ?></textarea></td></tr>
      </tbody></table>
    </div>
    <?php
}

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true) || get_current_screen()?->post_type !== INAP_TP_TIPO) {
        return;
    }
    $ver = (string) (@filemtime(get_theme_file_path('assets/js/admin-numeral.js')) ?: INAP_VERSION);
    wp_enqueue_script('inap-numeral', get_theme_file_uri('assets/js/admin-numeral.js'), [], $ver, ['in_footer' => true]);
    wp_add_inline_style('wp-admin', '.inap-filas table input[type=date]{width:10.5em}.inap-filas td{vertical-align:middle}.inap-acciones{white-space:nowrap}.inap-apartado{border:1px solid #dcdcde;padding:12px 16px;margin:0 0 16px;background:#fff}.inap-apartado-campos{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end}');
});

/* ── Guardar ──────────────────────────────────────────────────────────────── */

function inap_tp_limpiar_filas($filas): array
{
    $limpias = [];
    foreach ((array) $filas as $f) {
        $f = (array) $f;
        $fila = [
            'titulo' => sanitize_text_field($f['titulo'] ?? ''),
            'estado' => array_key_exists($f['estado'] ?? '', INAP_TP_ESTADOS) ? $f['estado'] : 'pendiente',
            'url' => esc_url_raw(trim($f['url'] ?? '')),
            'fecha' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['fecha'] ?? '') ? $f['fecha'] : '',
            'nota' => sanitize_text_field($f['nota'] ?? ''),
        ];
        if ($fila['titulo'] !== '' || $fila['url'] !== '') {
            $limpias[] = $fila;
        }
    }
    return $limpias;
}

add_action('save_post_' . INAP_TP_TIPO, function ($post_id) {
    if (!isset($_POST['_inap_numeral']) || !wp_verify_nonce($_POST['_inap_numeral'], 'inap_numeral')
        || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
        return;
    }
    $d = wp_unslash($_POST['inap'] ?? []);
    $apartados = [];
    foreach ((array) ($d['apartados'] ?? []) as $a) {
        $titulo = sanitize_text_field($a['titulo'] ?? '');
        if ($titulo !== '') {
            $apartados[] = ['grupo' => sanitize_text_field($a['grupo'] ?? ''), 'titulo' => $titulo, 'filas' => inap_tp_limpiar_filas($a['filas'] ?? [])];
        }
    }
    inap_tp_guardar($post_id, [
        'n' => min(29, max(1, absint($d['n'] ?? 1))),
        'tipo' => in_array($d['tipo'] ?? '', ['periodos', 'apartados', 'sin-info'], true) ? $d['tipo'] : '',
        'que_publica' => sanitize_textarea_field($d['que_publica'] ?? ''),
        'aviso_titulo' => sanitize_text_field($d['aviso_titulo'] ?? ''),
        'aviso_texto' => sanitize_textarea_field($d['aviso_texto'] ?? ''),
        'filas' => inap_tp_limpiar_filas($d['filas'] ?? []),
        'apartados' => $apartados,
    ]);
});

function inap_tp_guardar(int $post_id, array $datos): void
{
    foreach ($datos as $k => $v) {
        update_post_meta($post_id, "_inap_{$k}", $v);
    }
}

/* ── Textos y ajustes ─────────────────────────────────────────────────────── */

add_filter('inap_ajustes_grupos', function (array $grupos) {
    $grupos['inap_transparencia'] = [
        'pagina' => 'Transparencia: textos y ajustes',
        'intro' => 'Textos de las páginas de Transparencia y datos de la Unidad de Información Pública. Los numerales se editan en Transparencia › Numerales.',
        'secciones' => [
            'Año y enlaces' => [
                'campos' => [
                    ['anio', 'Año que se muestra', 'number', (string) wp_date('Y'), 'Cambiarlo al empezar el año, junto con las filas de cada numeral.'],
                    ['anios_anteriores', 'Años anteriores', 'textarea', '', 'Un año por línea con su enlace de OneDrive, así: 2025 | https://…  Aparecen en el selector «Año».'],
                    ['ley_url', 'Ley de Acceso a la Información Pública (PDF)', 'url', '', ''],
                    ['presupuestaria_url', 'Página de Transparencia presupuestaria', 'url', '', 'Mientras esté vacío, la tarjeta no lleva a ningún lado.'],
                    ['rendicion_url', 'Página de Rendición de cuentas', 'url', '', ''],
                ],
            ],
            'Unidad de Información Pública' => [
                'ayuda' => 'Aparece en el inicio de Transparencia, en Información pública de oficio y en el aviso de plazo.',
                'campos' => [
                    ['correo', 'Correo', 'email', 'transparencia@inap.gob.gt', ''],
                    ['telefono', 'Teléfono', 'text', '2419-8181', ''],
                    ['direccion', 'Dirección', 'text', 'Boulevard Los Próceres 16-40, zona 10, Ciudad de Guatemala', ''],
                    ['dias', 'Días hábiles para responder', 'number', '10', 'La ley da 10 días hábiles.'],
                ],
            ],
            'Inicio de Transparencia' => [
                'campos' => [
                    ['inicio_titulo', 'Título', 'text', 'Transparencia e información pública', ''],
                    ['inicio_intro', 'Introducción', 'textarea', 'La Ley de Acceso a la Información Pública garantiza que cualquier persona pueda conocer la información que generan las instituciones públicas. Aquí encuentras lo que INAP publica y cómo pedir lo que no esté publicado.', ''],
                    ['inicio_dato1', 'Dato 1', 'text', 'Consulta gratuita', ''],
                    ['inicio_dato2', 'Dato 2', 'text', 'Sin registrarte', 'El tercer dato es el plazo de respuesta.'],
                    ['ley_titulo', 'Recuadro de la ley: título', 'text', 'Ley de Acceso a la Información Pública', ''],
                    ['ley_texto', 'Recuadro de la ley: texto', 'textarea', 'Decreto 57-2008 del Congreso de la República. Define qué información deben publicar las instituciones y cómo pedirla.', ''],
                    ['ipo_descripcion', 'Tarjeta «Información pública de oficio»: texto', 'textarea', 'Los 29 numerales del artículo 10: estructura, presupuesto, compras, contrataciones, viáticos y más.', 'La fecha de actualización se calcula con los numerales.'],
                    ['presupuestaria_descripcion', 'Tarjeta «Transparencia presupuestaria»: texto', 'textarea', 'Informes mensuales de asesorías contratadas, ingresos propios y contrataciones por renglón.', ''],
                    ['presupuestaria_meta', 'Tarjeta «Transparencia presupuestaria»: línea inferior', 'text', 'Actualizado el 5 nov. 2026', ''],
                    ['rendicion_descripcion', 'Tarjeta «Rendición de cuentas»: texto', 'textarea', 'Informes cada cuatro meses sobre cómo INAP usa su presupuesto y qué resultados obtiene.', ''],
                    ['rendicion_meta', 'Tarjeta «Rendición de cuentas»: línea inferior', 'text', 'Informes desde 2021', ''],
                    ['solicitud_intro', 'Formulario: introducción', 'textarea', 'Pide la información pública que no encuentres en esta página. La respuesta llega al correo que indiques.', ''],
                ],
            ],
            'Información pública de oficio' => [
                'campos' => [
                    ['ipo_intro', 'Introducción', 'textarea', 'Artículo 10 de la Ley de Acceso a la Información Pública. Los numerales con información mensual se actualizan en los primeros 5 días hábiles de cada mes.', ''],
                    ['ipo_solicitud', 'Recuadro «Solicita información pública»: texto', 'textarea', 'Puedes pedir cualquier información que no esté publicada. No necesitas explicar para qué la quieres.', ''],
                ],
            ],
        ],
    ];
    return $grupos;
});

add_action('admin_menu', function () {
    add_submenu_page('edit.php?post_type=' . INAP_TP_TIPO, 'Transparencia: textos y ajustes', 'Textos y ajustes', INAP_CAP, 'inap_transparencia', fn () => inap_ajustes_pagina('inap_transparencia'));
});

/* ── Contenido inicial ────────────────────────────────────────────────────── */

/** Crea los 29 numerales con los datos de ejemplo de Figma si aún no existe ninguno. */
function inap_semilla_numerales(): void
{
    if (get_posts(['post_type' => INAP_TP_TIPO, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids'])) {
        return;
    }
    foreach (require __DIR__ . '/transparencia-ejemplo.php' as $n => $num) {
        $id = wp_insert_post(['post_type' => INAP_TP_TIPO, 'post_status' => 'publish', 'post_title' => $num['titulo'], 'menu_order' => $n]);
        if ($id && !is_wp_error($id)) {
            inap_tp_guardar($id, ['n' => $n] + array_diff_key($num, ['titulo' => 1]) + ['tipo' => '', 'filas' => [], 'apartados' => []]);
        }
    }
}
