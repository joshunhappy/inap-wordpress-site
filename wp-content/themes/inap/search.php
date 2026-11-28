<?php
/**
 * Resultados de búsqueda (Figma «Sitio institucional • Desktop/Mobile • Light • Búsqueda • Resultados / Sin resultados»).
 * Pestañas por tipo con su total, 10 resultados por página, tarjeta «¿Buscas cursos?».
 * Sin resultados: sugerencias y «Enlaces útiles» (menú de la página 404). Lógica en inc/busqueda.php.
 */
get_header();

$a = fn (string $clave) => inap_ajuste('inap_ajustes', $clave);
$q = trim(get_search_query(false));
$tipo = sanitize_key($_GET['tipo'] ?? '');
$tipo = isset(INAP_BUSQUEDA_TIPOS[$tipo]) ? $tipo : '';
$todos = inap_busqueda_resultados($q);
$cuentas = array_count_values(array_column($todos, 'tipo'));
$lista = $tipo === '' ? $todos : array_values(array_filter($todos, fn ($r) => $r['tipo'] === $tipo));
$paginas = max(1, (int) ceil(count($lista) / INAP_BUSQUEDA_POR_PAGINA));
$pagina = min(max(1, (int) get_query_var('paged')), $paginas);
$visibles = array_slice($lista, ($pagina - 1) * INAP_BUSQUEDA_POR_PAGINA, INAP_BUSQUEDA_POR_PAGINA);
$palabras = inap_busqueda_palabras($q);
$cursos = inap_busqueda_cursos_url($q);
$titulo = $a('busqueda_titulo');
$total = count($todos);
$donde = 'en noticias, eventos y páginas del sitio.';
$intro = match (true) {
    $q === '' => 'Escribe qué buscas: noticias, eventos, páginas o numerales de Transparencia.',
    $total === 0 => "No encontramos resultados para «{$q}» {$donde}",
    default => sprintf(_n('%1$s resultado para «%2$s» %3$s', '%1$s resultados para «%2$s» %3$s', $total), number_format_i18n($total), $q, $donde),
};
$url_tipo = fn (string $t) => add_query_arg(array_filter(['s' => $q, 'tipo' => $t]), home_url('/'));
$sugerencias = array_filter(array_map('trim', explode("\n", $a('busqueda_sugerencias'))));
$enlaces = inap_menu('error404');

inap_breadcrumb([['Inicio', home_url('/')], [$titulo, null]]);
inap_back_link('Inicio', home_url('/'));
?>
<main id="contenido" tabindex="-1" class="mx-auto flex w-full max-w-360 flex-col gap-10 px-4 pt-6 pb-16 lg:gap-14 lg:px-30 lg:pt-10 lg:pb-24">
  <header class="flex flex-col gap-4">
    <h1 class="text-4xl font-bold text-base-content lg:text-5xl"><?php echo esc_html($titulo); ?></h1>
    <p class="text-lg text-neutral-text" <?php echo $q !== '' ? 'role="status"' : ''; ?>><?php echo esc_html($intro); ?></p>
  </header>

  <form role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get" class="flex flex-col gap-3 lg:max-w-198 lg:flex-row lg:items-end lg:gap-4">
    <div class="flex min-w-0 flex-1 flex-col gap-1.5">
      <label for="busqueda-q" class="text-sm font-semibold text-base-content">Buscar en el sitio</label>
      <input id="busqueda-q" name="s" type="search" value="<?php echo esc_attr($q); ?>" class="input w-full" />
    </div>
    <?php if ($tipo !== '') : ?><input type="hidden" name="tipo" value="<?php echo esc_attr($tipo); ?>" /><?php endif; ?>
    <button type="submit" class="btn btn-primary self-start lg:self-auto">Buscar</button>
  </form>

  <?php if ($q !== '') : ?>
    <div class="flex flex-col gap-2">
      <?php if ($total > 0) : ?>
        <nav aria-label="Tipo de resultado" class="overflow-x-auto border-b border-base-border">
          <ul class="flex gap-6 lg:gap-8">
            <?php foreach (['' => 'Todo'] + array_map(fn ($t) => $t[0], INAP_BUSQUEDA_TIPOS) as $t => $nombre) :
                $n = $t === '' ? $total : ($cuentas[$t] ?? 0);
                $actual = $t === $tipo;
            ?>
              <li class="shrink-0">
                <a href="<?php echo esc_url($url_tipo($t)); ?>"<?php echo $actual ? ' aria-current="page"' : ''; ?>
                   class="-mb-px flex border-b-3 py-3 text-sm whitespace-nowrap <?php echo $actual ? 'border-primary font-semibold text-primary-text' : 'border-transparent font-medium text-neutral-text hover:text-base-content'; ?> <?php echo INAP_FOCUS; ?>"><?php echo esc_html("{$nombre} ({$n})"); ?></a>
              </li>
            <?php endforeach; ?>
          </ul>
        </nav>
      <?php endif; ?>

      <div class="grid gap-10 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] lg:gap-x-6 lg:gap-y-8">
        <?php if ($visibles) : ?>
          <section aria-labelledby="busqueda-lista" class="lg:col-start-1 lg:row-start-1">
            <h2 id="busqueda-lista" class="sr-only">Resultados</h2>
            <ul>
              <?php foreach ($visibles as $r) : ?>
                <li class="flex flex-col gap-1.5 border-b border-base-border py-5">
                  <p class="text-sm font-medium text-neutral-text"><span class="font-semibold text-primary-text"><?php echo esc_html($r['etiqueta'] ?? INAP_BUSQUEDA_TIPOS[$r['tipo']][1]); ?></span><?php echo $r['meta'] !== '' ? ' · ' . esc_html($r['meta']) : ''; ?></p>
                  <h3 class="text-xl leading-7 font-semibold text-base-content">
                    <a href="<?php echo esc_url($r['url']); ?>" class="rounded-sm hover:text-primary-text hover:underline <?php echo INAP_FOCUS; ?>"><?php echo inap_busqueda_resaltar($r['titulo'], $palabras, 0, 'font-bold'); ?></a>
                  </h3>
                  <?php $texto = inap_busqueda_resaltar($r['texto'], $palabras, 200); ?>
                  <?php if ($texto !== '') : ?>
                    <p class="text-sm text-neutral-text"><?php echo $texto; ?></p>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php elseif ($total > 0) : ?>
          <p class="pt-5 text-neutral-text lg:col-start-1 lg:row-start-1"><?php echo esc_html(sprintf('No hay %s con «%s». Revisa las otras pestañas.', mb_strtolower(INAP_BUSQUEDA_TIPOS[$tipo][0]), $q)); ?></p>
        <?php else : ?>
          <div class="flex flex-col gap-10 lg:col-start-1 lg:row-start-1">
            <?php if ($sugerencias) : ?>
              <section aria-labelledby="busqueda-sugerencias" class="flex flex-col gap-4">
                <h2 id="busqueda-sugerencias" class="text-2xl font-bold text-base-content">Sugerencias</h2>
                <ul class="list-disc ps-6 text-base text-base-content">
                  <?php foreach ($sugerencias as $s) : ?><li><?php echo esc_html($s); ?></li><?php endforeach; ?>
                </ul>
              </section>
            <?php endif; ?>
            <?php if ($enlaces) : ?>
              <nav aria-labelledby="busqueda-enlaces" class="flex flex-col gap-4">
                <h2 id="busqueda-enlaces" class="text-2xl font-bold text-base-content"><?php echo esc_html(inap_ajuste('inap_ajustes', 'e404_enlaces')); ?></h2>
                <ul class="grid gap-x-8 gap-y-4 sm:grid-cols-2">
                  <?php foreach ($enlaces as ['item' => $item]) :
                      $externo = inap_menu_externo($item);
                  ?>
                    <li>
                      <a href="<?php echo esc_url($item->url); ?>"<?php echo $externo ? ' target="_blank" rel="noopener"' : ''; ?> class="inline-flex items-center gap-2 rounded-sm text-sm text-primary-text underline hover:no-underline <?php echo INAP_FOCUS; ?>"><?php echo inap_icon('arrow-right', 20, 'shrink-0') . esc_html($item->title); ?><?php if ($externo) : ?><span class="sr-only"> (se abre en una pestaña nueva)</span><?php endif; ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </nav>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if ($cursos !== '') : ?>
          <aside aria-labelledby="busqueda-cursos" class="flex flex-col gap-3 self-start rounded-box bg-surface-subtle p-6 lg:col-start-2 lg:row-span-2 lg:row-start-1">
            <h2 id="busqueda-cursos" class="text-xl font-bold text-base-content"><?php echo esc_html($a('busqueda_cursos_titulo')); ?></h2>
            <?php if ($a('busqueda_cursos_texto') !== '') : ?>
              <p class="text-base text-base-content"><?php echo esc_html($a('busqueda_cursos_texto')); ?></p>
            <?php endif; ?>
            <a href="<?php echo esc_url($cursos); ?>" class="self-start rounded-sm pt-2 text-sm font-semibold text-primary-text hover:underline <?php echo INAP_FOCUS; ?>"><?php echo esc_html("Buscar «{$q}» en cursos"); ?></a>
          </aside>
        <?php endif; ?>

        <?php if ($visibles && $paginas > 1) : ?>
          <div class="lg:col-start-1 lg:row-start-2"><?php echo inap_paginacion($paginas, $pagina); ?></div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</main>
<?php get_footer();
