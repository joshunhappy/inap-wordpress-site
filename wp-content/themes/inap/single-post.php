<?php
/**
 * Una noticia: columna de lectura de 720 px. Tema + fecha, titular, extracto, foto con pie,
 * cuerpo, compartir y noticia anterior / siguiente. Textos en Noticias › Página de noticias.
 */
get_header();
the_post();

$t = 'inap_noticias_txt';
$n = inap_noticia_datos(get_post());
$lista = get_permalink((int) get_option('page_for_posts')) ?: home_url('/noticias/');
$pie = $n['imagen_id'] ? (string) wp_get_attachment_caption($n['imagen_id']) : '';
$anterior = get_previous_post();
$siguiente = get_next_post();
$url = rawurlencode(get_permalink());
$texto = rawurlencode(get_the_title());
$nueva = '<span class="sr-only"> (se abre en una pestaña nueva)</span>';
$compartir = [
    'Facebook' => "https://www.facebook.com/sharer/sharer.php?u={$url}",
    'LinkedIn' => "https://www.linkedin.com/sharing/share-offsite/?url={$url}",
    'X' => "https://x.com/intent/post?url={$url}&text={$texto}",
];
$enlace = 'rounded-sm text-primary-text hover:underline ' . INAP_FOCUS;

inap_breadcrumb([['Inicio', home_url('/')], [$t('titulo'), $lista], [$n['titulo'], null]]);
inap_back_link($t('titulo'), $lista);
?>
<main id="contenido" tabindex="-1" class="px-4 pt-6 pb-16 lg:pt-12 lg:pb-24">
  <article class="mx-auto flex max-w-180 flex-col gap-8">
    <header class="flex flex-col gap-4">
      <p class="flex flex-wrap items-center gap-3">
        <?php if ($n['tema'] !== '') : ?>
          <a href="<?php echo esc_url(add_query_arg('tema', $n['tema_slug'], $lista)); ?>" class="badge badge-soft badge-primary text-xs font-semibold text-primary-text hover:underline <?php echo INAP_FOCUS; ?>"><?php echo esc_html($n['tema']); ?></a>
        <?php endif; ?>
        <time datetime="<?php echo esc_attr($n['iso']); ?>" class="text-sm text-neutral-text"><?php echo esc_html($n['fecha_larga']); ?></time>
      </p>
      <h1 class="text-3xl font-bold text-base-content lg:text-4xl"><?php echo esc_html($n['titulo']); ?></h1>
      <?php if ($n['resumen'] !== '') : ?>
        <p class="text-lg text-neutral-text"><?php echo esc_html($n['resumen']); ?></p>
      <?php endif; ?>
    </header>

    <?php if ($n['imagen_id']) : ?>
      <figure class="flex flex-col gap-2">
        <?php echo wp_get_attachment_image($n['imagen_id'], 'large', false, ['class' => 'aspect-video w-full rounded-box object-cover', 'loading' => 'eager']); ?>
        <?php if ($pie !== '') : ?>
          <figcaption class="text-sm text-neutral-text"><?php echo esc_html($pie); ?></figcaption>
        <?php endif; ?>
      </figure>
    <?php endif; ?>

    <div class="inap-texto"><?php the_content(); ?></div>

    <?php if ($t('compartir_mostrar') === '1') : ?>
      <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-base-border pt-6 text-sm font-semibold">
        <p id="compartir" class="text-base-content"><?php echo esc_html($t('compartir_texto')); ?></p>
        <ul aria-labelledby="compartir" class="flex flex-wrap items-center gap-x-4 gap-y-2">
          <?php foreach ($compartir as $red => $href) : ?>
            <li><a href="<?php echo esc_url($href); ?>" target="_blank" rel="noopener" class="<?php echo $enlace; ?>"><?php echo esc_html($red) . $nueva; ?></a></li>
          <?php endforeach; ?>
          <li>
            <button type="button" data-copiar="<?php echo esc_url(get_permalink()); ?>" class="cursor-pointer <?php echo $enlace; ?>">Copiar enlace</button>
            <span role="status" class="ms-2 font-normal text-neutral-text" data-copiado></span>
          </li>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($anterior || $siguiente) : ?>
      <nav aria-label="Otras noticias" class="flex flex-col gap-4 pt-4 sm:flex-row sm:justify-between sm:gap-8">
        <?php foreach ([[$anterior, $t('anterior_texto'), ''], [$siguiente, $t('siguiente_texto'), 'sm:ms-auto sm:items-end sm:text-right']] as [$p, $kicker, $clase]) : ?>
          <?php if ($p) : ?>
            <a href="<?php echo esc_url(get_permalink($p)); ?>" class="group flex flex-col gap-1 rounded-sm sm:max-w-1/2 sm:flex-1 <?php echo $clase . ' ' . INAP_FOCUS; ?>">
              <span class="text-sm text-neutral-text"><?php echo esc_html($kicker); ?></span>
              <span class="line-clamp-2 text-sm font-semibold text-primary-text underline group-hover:no-underline"><?php echo esc_html(get_the_title($p)); ?></span>
            </a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>
  </article>
</main>
<?php get_footer();
