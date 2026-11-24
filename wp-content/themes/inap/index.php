<?php
/**
 * Plantilla general: una noticia, evento o página, o una lista (Noticias, Eventos, resultados de búsqueda).
 * Provisional hasta construir los diseños de Noticias y Eventos en Figma.
 */
get_header();

$lista = !is_singular();
$titulo = match (true) {
    is_search() => sprintf('Resultados para «%s»', get_search_query()),
    is_home() => get_the_title((int) get_option('page_for_posts')) ?: 'Noticias',
    is_post_type_archive(INAP_EVENTO) => 'Próximos eventos',
    is_category() => single_cat_title('', false),
    is_404() => 'No encontramos esta página',
    default => get_the_archive_title(),
};
?>
<main id="contenido" tabindex="-1" class="mx-auto w-full max-w-180 px-4 py-10 lg:py-16">
  <?php if ($lista) : ?>
    <h1 class="text-3xl font-bold text-base-content lg:text-4xl"><?php echo esc_html($titulo); ?></h1>
    <?php if (is_search() || is_404()) : ?>
      <form role="search" action="<?php echo esc_url(home_url('/')); ?>" class="mt-6 flex gap-2">
        <label for="buscar-q" class="sr-only">Buscar en el sitio</label>
        <input id="buscar-q" name="s" type="search" value="<?php echo esc_attr(get_search_query()); ?>" class="input min-w-0 flex-1" />
        <button type="submit" class="btn btn-primary">Buscar</button>
      </form>
    <?php endif; ?>
    <?php if (have_posts()) : ?>
      <ul class="mt-8 flex flex-col">
        <?php while (have_posts()) : the_post();
          $evento = get_post_type() === INAP_EVENTO ? inap_evento_datos(get_post()) : null;
        ?>
          <li class="flex flex-col gap-1 border-b border-base-border py-5">
            <p class="text-sm text-neutral-text">
              <?php if ($evento) : ?>
                <time datetime="<?php echo esc_attr($evento['iso']); ?>"><?php echo esc_html($evento['fecha_larga']); ?></time><?php echo $evento['meta'] !== '' ? ' · ' . esc_html($evento['meta']) : ''; ?>
              <?php elseif (get_post_type() === 'post') : ?>
                <time datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(inap_tp_fecha(get_the_date('Y-m-d'))); ?></time>
              <?php endif; ?>
            </p>
            <h2 class="text-xl font-semibold"><a href="<?php the_permalink(); ?>" class="rounded-sm text-base-content hover:text-primary-text hover:underline <?php echo INAP_FOCUS; ?>"><?php the_title(); ?></a></h2>
            <?php if (has_excerpt()) : ?>
              <p class="text-sm text-neutral-text"><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php endif; ?>
          </li>
        <?php endwhile; ?>
      </ul>
      <div class="mt-8"><?php the_posts_pagination(['prev_text' => 'Anteriores', 'next_text' => 'Siguientes']); ?></div>
    <?php elseif (is_post_type_archive(INAP_EVENTO)) : ?>
      <p class="mt-6 text-neutral-text"><?php echo esc_html(inap_inicio('noticias', 'eventos_vacio')); ?></p>
    <?php elseif (!is_404()) : ?>
      <p class="mt-6 text-neutral-text">No encontramos resultados. Prueba con otras palabras.</p>
    <?php else : ?>
      <p class="mt-6"><a class="rounded-sm text-primary-text underline <?php echo INAP_FOCUS; ?>" href="<?php echo esc_url(home_url('/')); ?>">Ir al inicio</a></p>
    <?php endif; ?>
  <?php else : while (have_posts()) : the_post();
    $evento = get_post_type() === INAP_EVENTO ? inap_evento_datos(get_post()) : null;
  ?>
    <article>
      <?php if ($evento) : ?>
        <p class="text-sm text-neutral-text"><time datetime="<?php echo esc_attr($evento['iso']); ?>"><?php echo esc_html($evento['fecha_larga']); ?></time><?php echo $evento['meta'] !== '' ? ' · ' . esc_html($evento['meta']) : ''; ?></p>
      <?php elseif (get_post_type() === 'post') : ?>
        <p class="text-sm text-neutral-text"><time datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(inap_tp_fecha(get_the_date('Y-m-d'))); ?></time></p>
      <?php endif; ?>
      <h1 class="mt-2 text-3xl font-bold text-base-content lg:text-4xl"><?php the_title(); ?></h1>
      <?php if (has_post_thumbnail()) : ?>
        <?php the_post_thumbnail('large', ['class' => 'mt-6 w-full rounded-box object-cover']); ?>
      <?php endif; ?>
      <div class="mt-6 flex flex-col gap-4 text-base text-base-content [&_a]:text-primary-text [&_a]:underline"><?php the_content(); ?></div>
    </article>
  <?php endwhile; endif; ?>
</main>
<?php get_footer();
