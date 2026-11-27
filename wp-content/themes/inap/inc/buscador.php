<?php
/**
 * Buscador del sitio con sugerencias (patrón combobox; comportamiento en assets/js/buscador.js).
 *
 *   Campo vacío     búsquedas recientes (guardadas en el dispositivo), populares y «Explora por sección».
 *   Al escribir     GET /wp-json/inap/v1/sugerencias?q=… → sugerencias de texto, noticias y eventos, páginas.
 *
 * Diseño: «Sugerencias de búsqueda» del sitio de referencia (Figma Search Suggest 86:528, variante WordPress).
 * Populares y el enlace a la plataforma de cursos se editan en Ajustes › INAP › Buscador del sitio.
 */

defined('ABSPATH') || exit;

const INAP_BUSCADOR_POPULARES = 8;

add_filter('inap_ajustes_grupos', function (array $grupos) {
    $grupos['inap_ajustes']['secciones']['Buscador del sitio'] = [
        'ayuda' => 'Lista que se abre bajo el buscador de la barra y el del inicio. Las búsquedas recientes las guarda el navegador de cada persona.',
        'campos' => [
            ['buscador_populares', 'Búsquedas populares', 'textarea', "Convocatorias\nAcceso a la información\nInformes\nEventos 2026\nDirectorio institucional", 'Una por renglón, hasta ' . INAP_BUSCADOR_POPULARES . '. Se muestran como botones cuando el campo está vacío; cada una busca ese texto.'],
            ['buscador_cursos', 'Mostrar «¿Buscas cursos?»', 'checkbox', '1', 'Enlace al final de la lista que lleva a la búsqueda de la plataforma de cursos (dirección de arriba + /busqueda).'],
        ],
    ];
    return $grupos;
});

/** Sin mayúsculas ni acentos: «Ética» y «etica» coinciden. */
function inap_buscador_norm(string $s): string
{
    return remove_accents(mb_strtolower($s));
}

function inap_buscador_populares(): array
{
    $lineas = array_filter(array_map('trim', explode("\n", inap_ajuste('inap_ajustes', 'buscador_populares'))));
    return array_slice(array_values($lineas), 0, INAP_BUSCADOR_POPULARES);
}

/** Datos del panel con el campo vacío y direcciones que usa el JS. */
function inap_buscador_config(): array
{
    $noticias = (int) wp_count_posts('post')->publish;
    $plataforma = rtrim(inap_ajuste('inap_ajustes', 'plataforma_url'), '/');
    return [
        'api' => rest_url('inap/v1/sugerencias'),
        'resultados' => home_url('/'),
        'sprite' => get_theme_file_uri('assets/lucide/sprite.svg'),
        'cursos' => inap_ajuste('inap_ajustes', 'buscador_cursos') === '1' && $plataforma !== '' ? $plataforma . '/busqueda' : '',
        'populares' => inap_buscador_populares(),
        'secciones' => [
            [
                'titulo' => 'Noticias',
                'url' => get_permalink((int) get_option('page_for_posts')) ?: home_url('/noticias/'),
                'meta' => sprintf(_n('%s publicación', '%s publicaciones', $noticias), number_format_i18n($noticias)),
                'icono' => 'newspaper',
            ],
            [
                'titulo' => 'Transparencia',
                'url' => inap_tp_url(),
                'meta' => 'Información pública de oficio',
                'icono' => 'scale',
            ],
        ],
    ];
}

add_action('wp_enqueue_scripts', function () {
    $ver = (string) (@filemtime(get_theme_file_path('assets/js/buscador.js')) ?: INAP_VERSION);
    wp_enqueue_script('inap-buscador', get_theme_file_uri('assets/js/buscador.js'), [], $ver, ['strategy' => 'defer', 'in_footer' => true]);
    wp_add_inline_script('inap-buscador', 'window.INAP_BUSCADOR = ' . wp_json_encode(inap_buscador_config()) . ';', 'before');
}, 21);

/* ── Sugerencias al escribir ──────────────────────────────────────────────── */

add_action('rest_api_init', function () {
    register_rest_route('inap/v1', '/sugerencias', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'args' => ['q' => ['type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field']],
        'callback' => function (WP_REST_Request $r) {
            $res = new WP_REST_Response(inap_buscador_sugerencias((string) $r['q']));
            $res->header('Cache-Control', 'public, max-age=300');
            return $res;
        },
    ]);
});

/**
 * Hasta 3 sugerencias de texto, 2 noticias o eventos y 2 páginas (incluye los numerales de Transparencia).
 * @return array{sugerencias: string[], contenido: array, paginas: array}
 */
function inap_buscador_sugerencias(string $q): array
{
    $q = trim(mb_substr($q, 0, 80));
    $nq = inap_buscador_norm($q);
    $vacio = ['sugerencias' => [], 'contenido' => [], 'paginas' => []];
    if (mb_strlen($nq) < 2) {
        return $vacio;
    }

    $titulo = fn (WP_Post $p) => wp_strip_all_tags(html_entity_decode(get_the_title($p), ENT_QUOTES, 'UTF-8'));
    $posts = get_posts([
        'post_type' => ['post', INAP_EVENTO, 'page'],
        'post_status' => 'publish',
        's' => $q,
        'posts_per_page' => 20,
        'no_found_rows' => true,
    ]);

    $contenido = [];
    $paginas = [];
    $textos = [];
    foreach ($posts as $p) {
        $textos[] = $titulo($p);
        if ($p->post_type === 'page') {
            $paginas[] = ['titulo' => $titulo($p), 'url' => get_permalink($p), 'meta' => 'Página', 'icono' => 'file-text'];
        } elseif ($p->post_type === INAP_EVENTO) {
            $e = inap_evento_datos($p);
            $contenido[] = ['titulo' => $titulo($p), 'url' => get_permalink($p), 'meta' => trim('Evento · ' . $e['fecha_larga'], ' ·'), 'icono' => 'calendar'];
        } else {
            $contenido[] = ['titulo' => $titulo($p), 'url' => get_permalink($p), 'meta' => 'Noticia · ' . inap_tp_fecha(get_the_date('Y-m-d', $p)), 'icono' => 'newspaper'];
        }
    }

    foreach (inap_tp_numerales() as $n => $num) {
        // Al inicio de una palabra: «eti» no debe encontrar «objetivos».
        if (preg_match('/(^|[^\p{L}\p{N}])' . preg_quote($nq, '/') . '/u', inap_buscador_norm($num['titulo']))) {
            $textos[] = $num['titulo'];
            $paginas[] = ['titulo' => $num['titulo'], 'url' => inap_tp_url('numeral', $n), 'meta' => "Transparencia · Numeral {$n}", 'icono' => 'scale'];
        }
    }

    $cortos = array_merge(inap_buscador_populares(), get_terms(['taxonomy' => ['category', 'post_tag'], 'hide_empty' => true, 'fields' => 'names']) ?: []);

    return [
        'sugerencias' => inap_buscador_frases($nq, $cortos, $textos),
        'contenido' => array_slice($contenido, 0, 2),
        'paginas' => array_slice($paginas, 0, 2),
    ];
}

/**
 * Frases que contienen lo escrito, a partir de populares y temas (completos) y de los títulos encontrados
 * (lo escrito y hasta dos palabras con contenido más). Primero las que empiezan con lo escrito.
 */
function inap_buscador_frases(string $nq, array $cortos, array $titulos): array
{
    $vacias = ['de', 'del', 'la', 'las', 'el', 'los', 'y', 'e', 'o', 'u', 'en', 'a', 'al', 'para', 'por', 'con', 'un', 'una', 'su', 'sus', 'que', 'se'];
    $k = count(explode(' ', $nq));
    $frases = [];

    foreach ($cortos as $t) {
        if (str_contains(inap_buscador_norm($t), $nq)) {
            $frases[] = mb_strtolower(trim($t));
        }
    }

    foreach ($titulos as $t) {
        $palabras = preg_split('/\s+/u', trim(preg_replace('/[^\p{L}\p{N}\s-]+/u', ' ', $t)), -1, PREG_SPLIT_NO_EMPTY);
        $normales = array_map('inap_buscador_norm', $palabras);
        foreach ($normales as $i => $_) {
            if (!str_starts_with(implode(' ', array_slice($normales, $i, $k)), $nq)) {
                continue;
            }
            // Lo escrito y lo que sigue hasta dos palabras con contenido: «foro de la función pública».
            $tramo = array_slice($palabras, $i, $k);
            for ($j = $i + $k, $llenas = 0; $j < count($palabras) && $llenas < 2 && count($tramo) < $k + 4; $j++) {
                $tramo[] = $palabras[$j];
                $llenas += (int) !in_array($normales[$j], $vacias, true);
            }
            while (count($tramo) > $k && in_array(inap_buscador_norm(end($tramo)), $vacias, true)) {
                array_pop($tramo);
            }
            if (count($tramo) === $k && $i > 0) {
                // Lo escrito va al final del título: «comisión de ética».
                $tramo = array_slice($palabras, max(0, $i - 2), min(2, $i) + $k);
                while (count($tramo) > $k && in_array(inap_buscador_norm($tramo[0]), $vacias, true)) {
                    array_shift($tramo);
                }
            }
            $frases[] = mb_strtolower(implode(' ', $tramo));
            break;
        }
    }

    $unicas = [];
    foreach ($frases as $f) {
        $n = inap_buscador_norm($f);
        if ($n !== $nq && !isset($unicas[$n])) {
            $unicas[$n] = $f;
        }
    }
    uksort($unicas, fn ($a, $b) => (int) !str_starts_with($a, $nq) <=> (int) !str_starts_with($b, $nq));
    return array_slice(array_values($unicas), 0, 3);
}
