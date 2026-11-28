<?php
/**
 * Página no encontrada (404): antetítulo, título, texto, buscador y «Enlaces útiles».
 * Textos en Ajustes › INAP › Página no encontrada (404); enlaces en Apariencia › Menús,
 * ubicación «Enlaces útiles (página 404)».
 */
get_header();

$t = fn (string $clave) => inap_ajuste('inap_ajustes', "e404_{$clave}");
$enlaces = inap_menu('error404');
?>
<main id="contenido" tabindex="-1" class="relative">
  <?php /* Ondas en su propia capa recortada: la lista de sugerencias del buscador puede salir del main. */ ?>
  <div class="pointer-events-none absolute inset-0 overflow-hidden">
    <img src="<?php echo esc_url(get_theme_file_uri('assets/brand/waves-lines.svg')); ?>" alt="" width="1440" height="411"
         class="absolute top-0 left-1/2 h-[411px] w-[1440px] max-w-none -translate-x-1/2 lg:h-auto lg:w-full" />
  </div>
  <div class="relative mx-auto max-w-360 px-4 pt-10 pb-16 lg:px-30 lg:pt-22 lg:pb-48">
    <div class="flex max-w-180 flex-col gap-6">
      <?php if ($t('antetitulo') !== '') : ?>
        <p class="text-sm font-semibold text-base-content"><?php echo esc_html($t('antetitulo')); ?></p>
      <?php endif; ?>
      <h1 class="text-4xl font-bold text-base-content lg:text-5xl"><?php echo esc_html($t('titulo')); ?></h1>
      <?php if ($t('texto') !== '') : ?>
        <p class="text-lg text-neutral-text"><?php echo esc_html($t('texto')); ?></p>
      <?php endif; ?>

      <form role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get" class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="flex min-w-0 flex-1 flex-col gap-1.5">
          <label for="error-q" class="text-sm font-semibold text-base-content"><?php echo esc_html($t('buscar')); ?></label>
          <input id="error-q" name="s" type="search" class="input w-full" />
        </div>
        <button type="submit" class="btn btn-primary w-full sm:w-auto">Buscar</button>
      </form>

      <?php if ($enlaces) : ?>
        <nav aria-labelledby="error-enlaces" class="flex flex-col gap-6">
          <h2 id="error-enlaces" class="text-xl font-semibold text-base-content"><?php echo esc_html($t('enlaces')); ?></h2>
          <ul class="grid gap-x-8 gap-y-4 sm:grid-cols-2">
            <?php foreach ($enlaces as ['item' => $item]) :
                $externo = inap_menu_externo($item);
            ?>
              <li>
                <a href="<?php echo esc_url($item->url); ?>"<?php echo $externo ? ' target="_blank" rel="noopener"' : ''; ?>
                   class="inline-flex items-center gap-2 rounded-sm text-sm text-primary-text underline hover:no-underline <?php echo INAP_FOCUS; ?>">
                  <?php echo inap_icon('arrow-right', 20, 'shrink-0'); ?>
                  <?php echo esc_html($item->title); ?>
                  <?php if ($externo) : ?><span class="sr-only">(se abre en una pestaña nueva)</span><?php endif; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </nav>
      <?php endif; ?>
    </div>
  </div>
</main>
<?php get_footer();
