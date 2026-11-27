<?php
/**
 * /noticias/ (página de entradas). Destacadas (sin filtros, solo en la página 1) y todas las demás, con filtros.
 * Textos en Noticias › Página de noticias (inc/noticias.php).
 */
get_header();

global $wp_query;
$t = 'inap_noticias_txt';
$titulo = $t('titulo');
$base = get_permalink((int) get_option('page_for_posts')) ?: home_url('/noticias/');
$f = inap_noticias_filtros();
$pagina = max(1, (int) get_query_var('paged'));
$destacadas = inap_noticias_muestra_destacadas() && $pagina === 1 ? inap_noticias_destacadas() : [];
$total = (int) $wp_query->found_posts;
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

  <?php echo inap_pestanas('noticias'); ?>

  <?php if ($destacadas) :
    $principal = array_shift($destacadas);
  ?>
    <section aria-labelledby="noticias-destacadas" class="flex flex-col gap-6">
      <h2 id="noticias-destacadas" class="text-2xl font-semibold text-base-content lg:text-3xl"><?php echo esc_html($t('destacadas_titulo')); ?></h2>
      <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-6">
        <?php echo inap_noticia_tarjeta($principal, 'vertical', 'h3', 'lg:min-w-0 lg:flex-1'); ?>
        <?php if ($destacadas) : ?>
          <ul class="flex flex-col gap-5 lg:min-w-0 lg:flex-1 lg:gap-6">
            <?php foreach ($destacadas as $n) : ?>
              <li><?php echo inap_noticia_tarjeta($n, 'horizontal'); ?></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

  <section id="todas" aria-labelledby="noticias-todas" class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h2 id="noticias-todas" class="text-2xl font-semibold text-base-content lg:text-3xl"><?php echo esc_html($t('todas_titulo')); ?></h2>
      <p class="text-sm text-neutral-text" aria-live="polite">
        <?php
        echo esc_html(sprintf(_n('%s noticia', '%s noticias', $total), number_format_i18n($total)));
        echo $t('orden_texto') !== '' ? ' · ' . esc_html($t('orden_texto')) : '';
        ?>
      </p>
    </div>

    <form role="search" aria-label="Filtrar noticias" action="<?php echo esc_url($base . '#todas'); ?>" method="get" class="flex flex-col gap-4 lg:flex-row lg:items-end">
      <div class="flex flex-col gap-1.5 lg:w-70">
        <label for="noticias-tema" class="text-sm font-semibold text-base-content">Tema</label>
        <select id="noticias-tema" name="tema" class="select w-full">
          <option value="">Todos los temas</option>
          <?php foreach (inap_noticias_temas() as $tema) : ?>
            <option value="<?php echo esc_attr($tema->slug); ?>" <?php selected($f['tema'], $tema->slug); ?>><?php echo esc_html($tema->name); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex flex-col gap-1.5 lg:w-40">
        <label for="noticias-anio" class="text-sm font-semibold text-base-content">Año</label>
        <select id="noticias-anio" name="anio" class="select w-full">
          <option value="">Todos los años</option>
          <?php foreach (inap_noticias_anios() as $anio) : ?>
            <option value="<?php echo $anio; ?>" <?php selected($f['anio'], $anio); ?>><?php echo $anio; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="flex flex-col gap-1.5 lg:min-w-0 lg:flex-1">
        <label for="noticias-q" class="text-sm font-semibold text-base-content">Buscar en noticias</label>
        <input id="noticias-q" name="q" type="search" value="<?php echo esc_attr($f['q']); ?>" class="input w-full" />
      </div>
      <div class="flex items-center gap-4">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <?php if (inap_noticias_con_filtros()) : ?>
          <a href="<?php echo esc_url($base . '#todas'); ?>" class="rounded-sm text-sm font-medium text-primary-text underline <?php echo INAP_FOCUS; ?>">Quitar filtros</a>
        <?php endif; ?>
      </div>
    </form>

    <?php if (have_posts()) : ?>
      <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php while (have_posts()) : the_post(); ?>
          <li class="flex"><?php echo inap_noticia_tarjeta(inap_noticia_datos(get_post()), 'vertical', 'h3', 'w-full'); ?></li>
        <?php endwhile; ?>
      </ul>
      <?php echo inap_paginacion((int) $wp_query->max_num_pages, $pagina, '#todas'); ?>
    <?php else : ?>
      <p class="text-neutral-text"><?php echo esc_html($t('vacio')); ?></p>
    <?php endif; ?>
  </section>
</main>
<?php get_footer();
