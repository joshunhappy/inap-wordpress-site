<?php
/** Plantilla general: título y contenido de la entrada o página. */
get_header(); ?>
<main id="contenido" tabindex="-1" class="mx-auto max-w-360 px-4 py-10 lg:px-30">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <h1 class="text-3xl font-bold"><?php the_title(); ?></h1>
    <div class="mt-6"><?php the_content(); ?></div>
  <?php endwhile; else : ?>
    <h1 class="text-3xl font-bold">No encontramos esta página</h1>
    <p class="mt-4"><a class="text-primary-text underline" href="<?php echo esc_url(home_url('/transparencia/')); ?>">Ir a Transparencia</a></p>
  <?php endif; ?>
</main>
<?php get_footer();
