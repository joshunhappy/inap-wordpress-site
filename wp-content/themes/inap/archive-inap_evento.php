<?php
/**
 * /eventos/: próximos (agrupados por mes, solo en la página 1) y pasados (consulta principal, con paginación).
 * Los filtros Tipo / Modalidad / Buscar valen para las dos listas. Textos en Eventos › Página de eventos.
 */
get_header();

global $wp_query;
$t = 'inap_eventos_txt';
$titulo = $t('titulo');
$base = get_post_type_archive_link(INAP_EVENTO);
$f = inap_eventos_filtros();
$con_filtros = inap_eventos_con_filtros();
$pagina = max(1, (int) get_query_var('paged'));
$proximos = $pagina === 1 ? inap_eventos_proximos(100, true) : [];
$pasados_total = (int) $wp_query->found_posts;
$con_pasados = $t('pasados_mostrar') === '1' && ($pasados_total > 0 || $con_filtros);
$cuenta = fn (int $n, string $orden) => esc_html(sprintf(_n('%s evento', '%s eventos', $n), number_format_i18n($n)) . ($orden !== '' ? ' · ' . $orden : ''));

// Próximos agrupados por mes: «Noviembre 2026» => [eventos].
$meses = [];
foreach ($proximos as $e) {
    $meses[$e['mes_anio']][] = $e;
}

inap_breadcrumb([['Inicio', home_url('/')], [$titulo, null]]);
inap_back_link('Inicio', home_url('/'));
?>
<main id="contenido" tabindex="-1" class="mx-auto flex w-full max-w-360 flex-col gap-10 px-4 pt-6 pb-16 lg:gap-14 lg:px-30 lg:pt-10 lg:pb-24">
  <header class="flex flex-col gap-4">
    <h1 class="text-4xl font-bold text-base-content lg:text-5xl"><?php echo esc_html($titulo); ?></h1>
    <?php if ($t('descripcion') !== '') : ?>
      <p class="text-lg text-neutral-text"><?php echo esc_html($t('descripcion')); ?></p>
    <?php endif; ?>
  </header>

  <?php echo inap_pestanas('eventos'); ?>

  <?php if ($pagina === 1) : ?>
    <section id="proximos" aria-labelledby="eventos-proximos" class="flex flex-col gap-6">
      <div class="flex flex-col gap-1">
        <h2 id="eventos-proximos" class="text-2xl font-semibold text-base-content lg:text-3xl"><?php echo esc_html($t('proximos_titulo')); ?></h2>
        <p class="text-sm text-neutral-text"><?php echo $cuenta(count($proximos), $t('proximos_orden')); ?></p>
      </div>

      <form role="search" aria-label="Filtrar eventos" action="<?php echo esc_url($base . '#proximos'); ?>" method="get" class="flex flex-col gap-4 lg:flex-row lg:items-end">
        <div class="flex flex-col gap-1.5 lg:w-70">
          <label for="eventos-tipo" class="text-sm font-semibold text-base-content">Tipo de evento</label>
          <select id="eventos-tipo" name="tipo" class="select w-full">
            <option value="">Todos los tipos</option>
            <?php foreach (inap_eventos_tipos() as $tipo) : ?>
              <option value="<?php echo esc_attr($tipo->slug); ?>" <?php selected($f['tipo'], $tipo->slug); ?>><?php echo esc_html($tipo->name); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="flex flex-col gap-1.5 lg:w-40">
          <label for="eventos-modalidad" class="text-sm font-semibold text-base-content">Modalidad</label>
          <select id="eventos-modalidad" name="modalidad" class="select w-full">
            <option value="">Todas</option>
            <option value="presencial" <?php selected($f['modalidad'], 'presencial'); ?>>Presencial</option>
            <option value="en-linea" <?php selected($f['modalidad'], 'en-linea'); ?>>En línea</option>
          </select>
        </div>
        <div class="flex flex-col gap-1.5 lg:min-w-0 lg:flex-1">
          <label for="eventos-q" class="text-sm font-semibold text-base-content">Buscar en eventos</label>
          <input id="eventos-q" name="q" type="search" value="<?php echo esc_attr($f['q']); ?>" class="input w-full" />
        </div>
        <div class="flex items-center gap-4">
          <button type="submit" class="btn btn-primary">Filtrar</button>
          <?php if ($con_filtros) : ?>
            <a href="<?php echo esc_url($base . '#proximos'); ?>" class="rounded-sm text-sm font-medium text-primary-text underline <?php echo INAP_FOCUS; ?>">Quitar filtros</a>
          <?php endif; ?>
        </div>
      </form>

      <?php if ($meses) : ?>
        <div class="flex flex-col gap-8">
          <?php foreach ($meses as $mes => $eventos) :
            $id = 'mes-' . sanitize_title($mes);
          ?>
            <section aria-labelledby="<?php echo esc_attr($id); ?>">
              <h3 id="<?php echo esc_attr($id); ?>" class="text-xl font-semibold text-base-content"><?php echo esc_html($mes); ?></h3>
              <ul>
                <?php foreach ($eventos as $e) {
                    echo inap_evento_fila($e, 'h4');
                } ?>
              </ul>
            </section>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <p class="text-neutral-text"><?php echo esc_html($con_filtros ? $t('filtros_vacio') : $t('proximos_vacio')); ?></p>
      <?php endif; ?>
    </section>
  <?php endif; ?>

  <?php if ($con_pasados) : ?>
    <section id="pasados" aria-labelledby="eventos-pasados" class="flex flex-col gap-6">
      <div class="flex flex-col gap-1">
        <h2 id="eventos-pasados" class="text-2xl font-semibold text-base-content lg:text-3xl"><?php echo esc_html($t('pasados_titulo')); ?></h2>
        <p class="text-sm text-neutral-text"><?php echo $cuenta($pasados_total, $t('pasados_orden')); ?></p>
      </div>
      <?php if (have_posts()) : ?>
        <ul>
          <?php while (have_posts()) : the_post();
              echo inap_evento_fila(inap_evento_datos(get_post()), 'h3', true, false);
          endwhile; ?>
        </ul>
        <?php echo inap_paginacion((int) $wp_query->max_num_pages, $pagina, '#pasados'); ?>
      <?php else : ?>
        <p class="text-neutral-text"><?php echo esc_html($t('filtros_vacio')); ?></p>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</main>
<?php get_footer();
