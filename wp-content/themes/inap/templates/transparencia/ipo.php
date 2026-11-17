<?php
/**
 * Transparencia · información pública de oficio: los 29 numerales del artículo 10
 * (Figma «Transparencia • Desktop • Light • Información pública de oficio» y celular).
 */
defined('ABSPATH') || exit;

$unidad = inap_tp_unidad();
get_header();
inap_breadcrumb([['Inicio', home_url('/')], ['Transparencia', inap_tp_url()], ['Información pública de oficio', null]]);
inap_back_link('Transparencia', inap_tp_url());
?>
<main id="contenido" tabindex="-1" class="mx-auto flex max-w-360 flex-col gap-10 px-4 pt-6 pb-12 lg:flex-row lg:items-start lg:gap-6 lg:px-30 lg:pt-8 lg:pb-24">
  <div class="flex min-w-0 flex-col gap-10 lg:flex-1">
    <header class="flex flex-col gap-4">
      <h1 class="text-4xl font-bold text-base-content">Información pública de oficio</h1>
      <p class="text-base text-neutral-text lg:text-lg lg:leading-7"><?php echo esc_html(inap_tp_texto('ipo_intro')); ?></p>
    </header>

    <section aria-labelledby="articulo-10" class="flex flex-col gap-6">
      <h2 id="articulo-10" class="sr-only">Numerales del artículo 10</h2>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
        <?php echo inap_tp_selector_anio('lg:w-50'); ?>
        <fieldset class="fieldset min-w-0 flex-1 p-0">
          <label class="fieldset-legend" for="buscar-numeral">Buscar un numeral</label>
          <label class="input w-full">
            <?php echo inap_icon('search', 18, 'shrink-0 text-neutral-text'); ?>
            <input id="buscar-numeral" type="search" autocomplete="off" placeholder="Por ejemplo: viáticos o 12" class="grow" />
          </label>
        </fieldset>
      </div>
      <p id="numerales-estado" class="sr-only" aria-live="polite"></p>
      <ul class="flex flex-col border-t border-base-border lg:border-t-0">
        <?php foreach (inap_tp_numerales() as $n => $num) : ?>
          <li data-numeral="<?php echo esc_attr($num['titulo']); ?>" data-n="<?php echo (int) $n; ?>">
            <a href="<?php echo esc_url(inap_tp_url('numeral', $n)); ?>" class="group flex items-center gap-4 border-b border-base-border p-3 hover:bg-surface-subtle <?php echo INAP_FOCUS; ?> focus-visible:-outline-offset-2">
              <span class="flex size-10 shrink-0 items-center justify-center rounded-field bg-surface-subtle text-sm font-semibold text-primary-text group-hover:bg-base-100"><?php printf('%02d', $n); ?></span>
              <span class="flex min-w-0 flex-1 flex-col gap-0.5 text-sm">
                <span class="font-semibold text-base-content"><?php echo esc_html($num['titulo']); ?></span>
                <span class="text-neutral-text"><?php echo esc_html($num['meta']); ?></span>
              </span>
              <?php echo inap_icon('chevron-right', 20, 'shrink-0 text-neutral-text'); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
      <div id="numerales-vacio" class="flex flex-col items-start gap-3 rounded-box bg-surface-subtle p-6" hidden>
        <p class="text-base font-semibold text-base-content">No hay numerales con «<span data-termino></span>»</p>
        <p class="text-sm text-neutral-text">Prueba con otra palabra o con el número del numeral, del 1 al 29.</p>
        <button id="numerales-ver-todos" type="button" class="btn btn-outline btn-primary">Ver los 29 numerales</button>
      </div>
    </section>
  </div>

  <aside class="flex flex-col gap-6 lg:w-96 lg:shrink-0">
    <section aria-labelledby="pedir-titulo" class="flex flex-col gap-4 rounded-box border border-base-border bg-base-100 p-6">
      <h2 id="pedir-titulo" class="text-xl font-semibold text-base-content">Solicita información pública</h2>
      <p class="text-sm text-neutral-text"><?php echo esc_html(inap_tp_texto('ipo_solicitud')); ?></p>
      <address class="not-italic">
        <ul class="flex flex-col gap-3">
          <?php
          echo inap_tp_contacto('mail', '<a href="mailto:' . esc_attr($unidad['correo']) . '" class="rounded-sm text-primary-text underline ' . INAP_FOCUS . '">' . esc_html($unidad['correo']) . '</a>', 'text-primary-text');
          echo inap_tp_contacto('phone', '<a href="tel:' . esc_attr($unidad['tel']) . '" class="rounded-sm ' . INAP_FOCUS . '">' . esc_html($unidad['telefono']) . '</a>', 'text-primary-text');
          echo inap_tp_contacto('clock', esc_html($unidad['plazo']), 'text-primary-text');
          echo inap_tp_contacto('map-pin', esc_html('Unidad de Información Pública, ' . $unidad['direccion']), 'text-primary-text');
          ?>
        </ul>
      </address>
      <a href="<?php echo esc_url(inap_tp_url() . '#solicitud'); ?>" class="btn btn-primary w-full">Llenar el formulario</a>
    </section>
    <nav aria-labelledby="otras-titulo" class="flex flex-col gap-2 rounded-box border border-base-border p-6">
      <h2 id="otras-titulo" class="text-xl font-semibold text-base-content">Otras secciones de Transparencia</h2>
      <ul>
        <?php foreach (['Transparencia presupuestaria' => 'presupuestaria_url', 'Rendición de cuentas' => 'rendicion_url', 'Ley de Acceso a la Información Pública (PDF)' => 'ley_url'] as $l => $clave) : ?>
          <li><a href="<?php echo esc_url(inap_tp_texto($clave) ?: '#'); ?>" class="flex min-h-11 items-center gap-2 rounded-sm text-sm font-semibold text-primary-text hover:underline <?php echo INAP_FOCUS; ?>"><?php echo esc_html($l); ?><?php echo inap_icon('arrow-right', 16, 'shrink-0'); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>
</main>
<?php get_footer();
