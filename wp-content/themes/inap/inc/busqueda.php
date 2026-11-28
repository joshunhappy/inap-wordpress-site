<?php
/**
 * Página de resultados de búsqueda (/?s=…; plantilla search.php).
 *
 * Busca en noticias, eventos y páginas (con la búsqueda de WordPress, por relevancia) y en los títulos de
 * los 29 numerales de Transparencia, que no son entradas. Pestañas por tipo (?tipo=post|inap_evento|page)
 * con su total; 10 resultados por página. Textos en Ajustes › INAP › Página de resultados de búsqueda.
 */

defined('ABSPATH') || exit;

const INAP_BUSQUEDA_POR_PAGINA = 10;
/** Pestañas: valor de ?tipo => [nombre de la pestaña, etiqueta de cada resultado]. */
const INAP_BUSQUEDA_TIPOS = ['post' => ['Noticias', 'Noticia'], 'inap_evento' => ['Eventos', 'Evento'], 'page' => ['Páginas', 'Página']];

add_filter('inap_ajustes_grupos', function (array $grupos) {
    $grupos['inap_ajustes']['secciones']['Página de resultados de búsqueda'] = [
        'ayuda' => 'La página que abre «Buscar» o «Ver todos los resultados». Los enlaces de «Enlaces útiles» son los del menú «Enlaces útiles (página 404)».',
        'campos' => [
            ['busqueda_titulo', 'Título', 'text', 'Resultados de búsqueda', 'También es el nombre en el breadcrumb.'],
            ['busqueda_sugerencias', 'Sugerencias cuando no hay resultados', 'textarea', "Revisa que las palabras estén bien escritas.\nUsa menos palabras o palabras más generales, por ejemplo «diplomado».\nSi buscas un curso o diplomado, búscalo en la plataforma de cursos.", 'Una por renglón. Vacío = no sale el bloque «Sugerencias».'],
            ['busqueda_cursos_titulo', 'Tarjeta de cursos: título', 'text', '¿Buscas cursos?', 'La tarjeta lleva a la búsqueda de la plataforma de cursos con las mismas palabras. Se oculta con «Mostrar ¿Buscas cursos?» (arriba).'],
            ['busqueda_cursos_texto', 'Tarjeta de cursos: texto', 'textarea', 'Los cursos, diplomados y programas de formación están en la plataforma de cursos, con fechas, cupos e inscripción.', ''],
        ],
    ];
    return $grupos;
});

// La plantilla hace su propia búsqueda; la consulta principal solo tiene que existir.
add_action('pre_get_posts', function (WP_Query $q) {
    if (!is_admin() && $q->is_main_query() && $q->is_search()) {
        $q->set('posts_per_page', 1);
        $q->set('no_found_rows', true);
        $q->set('post_type', array_keys(INAP_BUSQUEDA_TIPOS));
    }
});

/**
 * Todos los resultados de $q, ya ordenados: [['tipo' => post|inap_evento|page, 'titulo', 'url', 'meta', 'texto'], …].
 * Los numerales de Transparencia van al final, como páginas.
 */
function inap_busqueda_resultados(string $q): array
{
    $q = trim(mb_substr($q, 0, 100));
    if ($q === '') {
        return [];
    }
    $out = [];
    $ids = get_posts([
        'post_type' => array_keys(INAP_BUSQUEDA_TIPOS),
        'post_status' => 'publish',
        's' => $q,
        'posts_per_page' => 300,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);
    foreach ($ids as $id) {
        $p = get_post($id);
        $texto = has_excerpt($p) ? $p->post_excerpt : $p->post_content;
        $meta = match ($p->post_type) {
            'post' => get_the_date('j \\d\\e F \\d\\e Y', $p),
            INAP_EVENTO => (function () use ($p) {
                $e = inap_evento_datos($p);
                return implode(' · ', array_filter([$e['fecha_larga'], $e['hora']]));
            })(),
            default => wp_parse_url(get_permalink($p), PHP_URL_PATH) ?: '/',
        };
        $out[] = [
            'tipo' => $p->post_type,
            'titulo' => wp_strip_all_tags(html_entity_decode(get_the_title($p), ENT_QUOTES, 'UTF-8')),
            'url' => get_permalink($p),
            'meta' => $meta,
            'texto' => $texto,
        ];
    }

    $nq = inap_buscador_norm($q);
    foreach (inap_tp_numerales() as $n => $num) {
        if (str_contains(inap_buscador_norm($num['titulo']), $nq)) {
            $out[] = [
                'tipo' => 'page',
                'etiqueta' => 'Transparencia',
                'titulo' => $num['titulo'],
                'url' => inap_tp_url('numeral', $n),
                'meta' => "Numeral {$n} del artículo 10",
                'texto' => '',
            ];
        }
    }
    return $out;
}

/** Palabras buscadas (de 2 letras o más), sin acentos ni mayúsculas. */
function inap_busqueda_palabras(string $q): array
{
    $palabras = preg_split('/\s+/u', inap_buscador_norm(trim($q)), -1, PREG_SPLIT_NO_EMPTY);
    return array_values(array_unique(array_filter($palabras, fn ($p) => mb_strlen($p) >= 2)));
}

/**
 * Texto escapado con las palabras buscadas en negrita, sin importar acentos ni mayúsculas.
 * Si $largo > 0, recorta alrededor de la primera coincidencia («… texto …»).
 */
function inap_busqueda_resaltar(string $texto, array $palabras, int $largo = 0, string $clase = 'font-semibold'): string
{
    $texto = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags(strip_shortcodes(html_entity_decode($texto, ENT_QUOTES, 'UTF-8')))));
    if ($texto === '') {
        return '';
    }
    // remove_accents() cambia un carácter por uno: las posiciones del texto normalizado sirven en el original.
    $norm = inap_buscador_norm($texto);
    if (mb_strlen($norm) !== mb_strlen($texto)) {
        $norm = mb_strtolower($texto);
    }
    if ($largo > 0 && mb_strlen($texto) > $largo) {
        $pos = PHP_INT_MAX;
        foreach ($palabras as $p) {
            $i = mb_strpos($norm, $p);
            $i !== false && $pos = min($pos, $i);
        }
        $inicio = $pos === PHP_INT_MAX ? 0 : max(0, $pos - (int) ($largo / 3));
        if ($inicio > 0) {
            $espacio = mb_strpos($texto, ' ', $inicio);
            $inicio = $espacio !== false && $espacio < $inicio + 20 ? $espacio + 1 : $inicio;
        }
        $fin = min(mb_strlen($texto), $inicio + $largo);
        if ($fin < mb_strlen($texto)) {
            $espacio = mb_strrpos(mb_substr($texto, 0, $fin), ' ');
            $fin = $espacio !== false && $espacio > $inicio ? $espacio : $fin;
        }
        $antes = $inicio > 0 ? '… ' : '';
        $despues = $fin < mb_strlen($texto) ? ' …' : '';
        $texto = mb_substr($texto, $inicio, $fin - $inicio);
        $norm = mb_substr($norm, $inicio, $fin - $inicio);
    } else {
        $antes = $despues = '';
    }

    // Tramos que coinciden al inicio de una palabra («formación», no «Transformación»), sin solaparse.
    $tramos = [];
    foreach ($palabras as $p) {
        $desde = 0;
        while (($i = mb_strpos($norm, $p, $desde)) !== false) {
            if ($i === 0 || !preg_match('/[\p{L}\p{N}]/u', mb_substr($norm, $i - 1, 1))) {
                $tramos[] = [$i, $i + mb_strlen($p)];
            }
            $desde = $i + mb_strlen($p);
        }
    }
    usort($tramos, fn ($a, $b) => $a[0] <=> $b[0]);
    $html = '';
    $cursor = 0;
    foreach ($tramos as [$a, $b]) {
        if ($a < $cursor) {
            continue;
        }
        $html .= esc_html(mb_substr($texto, $cursor, $a - $cursor)) . '<strong class="' . esc_attr($clase) . '">' . esc_html(mb_substr($texto, $a, $b - $a)) . '</strong>';
        $cursor = $b;
    }
    return esc_html($antes) . $html . esc_html(mb_substr($texto, $cursor)) . esc_html($despues);
}

/** Enlace a la búsqueda de la plataforma de cursos con las mismas palabras, o '' si está apagado. */
function inap_busqueda_cursos_url(string $q): string
{
    $cursos = inap_buscador_config()['cursos'];
    return $cursos === '' ? '' : ($q !== '' ? add_query_arg('q', rawurlencode($q), $cursos) : $cursos);
}
