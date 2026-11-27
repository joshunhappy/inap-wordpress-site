<?php
/**
 * Un evento: columna de 720 px. Tipo + modalidad, título, entradilla, tarjeta de datos (fecha, hora, lugar,
 * transmisión, costo) con «Inscribirme» y «Agregar al calendario», foto con pie, cuerpo, compartir
 * y otros próximos eventos. Textos en Eventos › Página de eventos.
 */
get_header();
the_post();

$t = 'inap_eventos_txt';
$e = inap_evento_datos(get_post());
$lista = get_post_type_archive_link(INAP_EVENTO);
$pie = $e['imagen_id'] ? (string) wp_get_attachment_caption($e['imagen_id']) : '';
$otros = $t('otros_mostrar') === '1' ? inap_eventos_proximos(3, false, $e['id']) : [];
$datos = array_filter([
    ['calendar', 'Fecha', $e['fecha_completa']],
    ['clock', 'Hora', $e['horario']],
    ['map-pin', 'Lugar', $e['lugar_completo']],
    ['monitor', 'Transmisión', $e['transmision']],
    ['ticket', 'Costo', $e['costo']],
], fn ($d) => $d[2] !== '');

inap_breadcrumb([['Inicio', home_url('/')], [$t('titulo'), $lista], [$e['titulo'], null]]);
inap_back_link($t('titulo'), $lista);
?>
<main id="contenido" tabindex="-1" class="px-4 pt-6 pb-16 lg:pt-12 lg:pb-24">
  <article class="mx-auto flex max-w-180 flex-col gap-8">
    <header class="flex flex-col gap-4">
      <?php if ($e['tipo'] !== '' || $e['modalidad_texto'] !== '') : ?>
        <p class="flex flex-wrap items-center gap-3">
          <?php if ($e['tipo'] !== '') : ?>
            <a href="<?php echo esc_url(add_query_arg('tipo', $e['tipo_slug'], $lista) . '#proximos'); ?>" class="badge badge-soft badge-primary text-xs font-semibold text-primary-text hover:underline <?php echo INAP_FOCUS; ?>"><?php echo esc_html($e['tipo']); ?></a>
          <?php endif; ?>
          <?php if ($e['modalidad_texto'] !== '') : ?>
            <span class="text-sm text-neutral-text"><?php echo esc_html($e['modalidad_texto']); ?></span>
          <?php endif; ?>
        </p>
      <?php endif; ?>
      <h1 class="text-3xl font-bold text-base-content lg:text-4xl"><?php echo esc_html($e['titulo']); ?></h1>
      <?php if ($e['resumen'] !== '') : ?>
        <p class="text-lg text-neutral-text"><?php echo esc_html($e['resumen']); ?></p>
      <?php endif; ?>
    </header>

    <section aria-label="Datos del evento" class="flex flex-col gap-5 rounded-xl bg-surface-muted p-6">
      <?php if ($datos) : ?>
        <dl class="flex flex-col gap-4">
          <?php foreach ($datos as [$icono, $etiqueta, $valor]) : ?>
            <div class="flex items-start gap-3">
              <?php echo inap_icon($icono, 20, 'shrink-0 text-primary-text'); ?>
              <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                <dt class="text-sm text-neutral-text"><?php echo esc_html($etiqueta); ?></dt>
                <dd class="text-base text-base-content">
                  <?php echo $etiqueta === 'Fecha' ? '<time datetime="' . esc_attr($e['iso']) . '">' . esc_html($valor) . '</time>' : esc_html($valor); ?>
                </dd>
              </div>
            </div>
          <?php endforeach; ?>
        </dl>
      <?php endif; ?>
      <?php if ($e['pasado']) : ?>
        <p class="text-sm font-semibold text-base-content"><?php echo esc_html($t('pasado_texto')); ?></p>
      <?php elseif ($e['iso'] !== '') : ?>
        <div class="flex flex-wrap gap-3">
          <?php if ($e['inscripcion'] !== '') : ?>
            <a href="<?php echo esc_url($e['inscripcion']); ?>" class="btn btn-primary"><?php echo esc_html($t('inscribirme_texto')); ?></a>
          <?php endif; ?>
          <a href="<?php echo esc_url(inap_evento_ics_url($e['id'])); ?>" download class="btn btn-outline btn-primary"><?php echo esc_html($t('calendario_texto')); ?></a>
        </div>
      <?php endif; ?>
    </section>

    <?php if ($e['imagen_id']) : ?>
      <figure class="flex flex-col gap-2">
        <?php echo wp_get_attachment_image($e['imagen_id'], 'large', false, ['class' => 'aspect-video w-full rounded-xl object-cover']); ?>
        <?php if ($pie !== '') : ?>
          <figcaption class="text-sm text-neutral-text"><?php echo esc_html($pie); ?></figcaption>
        <?php endif; ?>
      </figure>
    <?php endif; ?>

    <div class="inap-texto"><?php the_content(); ?></div>

    <?php echo inap_compartir(); ?>

    <?php if ($otros) : ?>
      <section aria-labelledby="otros-eventos" class="flex flex-col gap-2">
        <h2 id="otros-eventos" class="text-xl font-semibold text-base-content"><?php echo esc_html($t('otros_titulo')); ?></h2>
        <ul>
          <?php foreach ($otros as $o) {
              echo inap_evento_fila($o, 'h3', false);
          } ?>
        </ul>
        <?php if ($t('todos_texto') !== '') : ?>
          <a href="<?php echo esc_url($lista); ?>" class="self-start rounded-sm pt-2 text-sm font-semibold text-primary-text hover:underline <?php echo INAP_FOCUS; ?>"><?php echo esc_html($t('todos_texto')); ?></a>
        <?php endif; ?>
      </section>
    <?php endif; ?>
  </article>
</main>
<?php get_footer();
