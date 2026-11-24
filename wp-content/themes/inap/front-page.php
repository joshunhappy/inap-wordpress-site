<?php
/**
 * Inicio del sitio institucional (Figma «Sitio institucional • Desktop/Mobile • Light • Inicio»).
 * Los textos, fotos y enlaces salen de Página de inicio (inc/inicio.php); noticias y eventos, de sus listas.
 * En celular la franja de Transparencia pasa al final (order-last), como en el diseño.
 */
defined('ABSPATH') || exit;

$p = fn (string $clave) => inap_inicio('portada', $clave);
$flecha = fn (int $size = 18) => inap_icon('arrow-right', $size, 'shrink-0');
$enlace = 'inline-flex min-h-11 items-center gap-2 rounded-sm text-sm font-semibold lg:min-h-0 ' . INAP_FOCUS;

/** Encabezado de sección (componente Section Header). */
$encabezado = fn (string $id, string $titulo, string $texto) => sprintf(
    '<div class="flex max-w-180 flex-col gap-2 md:gap-3"><h2 id="%s" class="text-2xl font-semibold text-base-content md:text-4xl md:font-bold">%s</h2>%s</div>',
    esc_attr($id), esc_html($titulo), $texto !== '' ? '<p class="text-base text-neutral-text md:text-lg">' . esc_html($texto) . '</p>' : ''
);

get_header();
?>
<main id="contenido" tabindex="-1" class="flex flex-col">

  <section aria-labelledby="inicio-titulo" class="relative overflow-hidden">
    <img src="<?php echo esc_url(get_theme_file_uri('assets/brand/waves-lines.svg')); ?>" alt="" width="1440" height="411"
         class="pointer-events-none absolute top-1/2 left-1/2 h-[411px] w-[1440px] max-w-none -translate-x-1/2 -translate-y-1/2 lg:h-auto lg:w-full" />
    <div class="relative mx-auto flex max-w-360 flex-col gap-6 px-4 pt-8 pb-12 lg:flex-row lg:items-center lg:gap-12 lg:px-30 lg:pt-18 lg:pb-20">
      <div class="flex flex-col gap-6 lg:min-w-0 lg:flex-1">
        <div class="flex flex-col gap-4 lg:gap-6">
          <?php if ($p('antetitulo') !== '') : ?>
            <p class="text-sm font-semibold text-primary-text"><?php echo esc_html($p('antetitulo')); ?></p>
          <?php endif; ?>
          <h1 id="inicio-titulo" class="text-4xl font-bold text-base-content lg:text-[3.5rem] lg:leading-16 lg:font-medium lg:tracking-[-0.02em]"><?php echo esc_html($p('titulo')); ?></h1>
          <?php if ($p('intro') !== '') : ?>
            <p class="text-base text-neutral-text lg:text-lg"><?php echo esc_html($p('intro')); ?></p>
          <?php endif; ?>
        </div>
        <form role="search" action="<?php echo esc_url(home_url('/')); ?>" data-buscador-inicio
              class="flex h-13 w-full items-center gap-3 rounded-full border border-base-border bg-base-2 pr-1.5 pl-4.5 shadow-md has-[input:focus-visible]:outline-2 has-[input:focus-visible]:outline-offset-2 has-[input:focus-visible]:outline-primary-text lg:h-16 lg:pr-2 lg:pl-6 lg:shadow-lg">
          <?php echo inap_icon('search', 22, 'shrink-0 text-base-content'); ?>
          <label for="inicio-q" class="sr-only">Buscar en el sitio</label>
          <input id="inicio-q" name="s" type="search" enterkeyhint="search" autocomplete="off" placeholder="<?php echo esc_attr($p('buscador')); ?>"
                 class="min-w-0 flex-1 bg-transparent text-base text-base-content outline-none placeholder:text-neutral-text lg:text-lg" />
          <button type="submit" class="btn btn-primary btn-lg hidden rounded-full lg:inline-flex">Buscar</button>
        </form>
        <div class="flex flex-col gap-3 lg:flex-row">
          <?php if ($p('boton1_texto') !== '') : ?>
            <a href="<?php echo esc_url(inap_url($p('boton1_url'))); ?>" class="btn btn-primary btn-lg lg:btn-md"><?php echo esc_html($p('boton1_texto')); ?></a>
          <?php endif; ?>
          <?php if ($p('boton2_texto') !== '') : ?>
            <a href="<?php echo esc_url(inap_url($p('boton2_url'))); ?>" class="btn btn-outline btn-primary btn-lg lg:btn-md"><?php echo esc_html($p('boton2_texto')); ?></a>
          <?php endif; ?>
        </div>
      </div>

      <?php if (inap_inicio_visible('portada')) : ?>
        <section aria-labelledby="inicio-destacado" class="flex flex-col items-start gap-4 rounded-box border border-base-border bg-base-100 p-7 lg:w-96 lg:shrink-0">
          <?php if ($p('etiqueta') !== '') : ?>
            <span class="badge badge-soft badge-primary h-auto px-2.5 py-1 text-xs font-semibold text-primary-text"><?php echo esc_html($p('etiqueta')); ?></span>
          <?php endif; ?>
          <h2 id="inicio-destacado" class="text-xl font-semibold text-base-content"><?php echo esc_html($p('destacado_titulo')); ?></h2>
          <p class="text-sm text-neutral-text"><?php echo esc_html($p('destacado_texto')); ?></p>
          <a href="<?php echo esc_url(inap_url($p('destacado_url'))); ?>" aria-describedby="inicio-destacado" class="<?php echo $enlace; ?> text-primary-text"><?php echo esc_html($p('destacado_enlace')) . $flecha(); ?></a>
        </section>
      <?php endif; ?>
    </div>
  </section>

  <?php if (inap_inicio_visible('portada', 'tp_mostrar')) : ?>
    <section aria-labelledby="inicio-tp" class="order-last mx-auto w-full max-w-360 px-4 pb-12 lg:order-none lg:px-30 lg:pb-24">
      <div class="flex flex-col gap-4 rounded-box bg-surface-subtle p-5 lg:flex-row lg:items-center lg:gap-6 lg:p-8">
        <span class="flex size-14 shrink-0 items-center justify-center rounded-lg bg-base-100 text-base-content"><?php echo inap_icon('scale', 28); ?></span>
        <div class="flex flex-col gap-1 lg:min-w-0 lg:flex-1">
          <h2 id="inicio-tp" class="text-xl font-semibold text-base-content"><?php echo esc_html($p('tp_titulo')); ?></h2>
          <p class="text-sm text-neutral-text"><?php echo esc_html($p('tp_texto')); ?></p>
        </div>
        <div class="flex flex-col gap-3 lg:shrink-0 lg:flex-row">
          <a href="<?php echo esc_url(inap_url($p('tp_boton1_url'))); ?>" class="btn btn-outline btn-primary"><?php echo esc_html($p('tp_boton1_texto')); ?></a>
          <a href="<?php echo esc_url(inap_url($p('tp_boton2_url'))); ?>" class="btn btn-primary"><?php echo esc_html($p('tp_boton2_texto')); ?></a>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php
  $c = fn (string $clave) => inap_inicio('cursos', $clave);
  $cursos = array_filter([1, 2, 3, 4], fn ($n) => inap_inicio_visible('cursos', "curso{$n}_mostrar") && $c("curso{$n}_titulo") !== '');
  if (inap_inicio_visible('cursos') && $cursos) :
  ?>
    <section aria-labelledby="inicio-cursos" class="bg-surface-subtle">
      <div class="mx-auto flex max-w-360 flex-col gap-5 px-4 py-12 lg:grid lg:grid-cols-[1fr_auto] lg:items-end lg:gap-8 lg:px-30 lg:py-24">
        <?php echo $encabezado('inicio-cursos', $c('titulo'), $c('descripcion')); ?>
        <?php if ($c('boton_texto') !== '') : ?>
          <a href="<?php echo esc_url(inap_url($c('boton_url'))); ?>" class="btn btn-outline btn-primary btn-lg order-last lg:order-none lg:btn-md"><?php echo esc_html($c('boton_texto')); ?></a>
        <?php endif; ?>
        <ul class="-mx-4 flex snap-x snap-mandatory scroll-px-4 gap-4 overflow-x-auto px-4 pb-2 lg:col-span-2 lg:mx-0 lg:grid lg:grid-cols-4 lg:gap-6 lg:overflow-visible lg:px-0 lg:pb-0">
          <?php foreach ($cursos as $n) :
            $modalidad = $c("curso{$n}_modalidad");
          ?>
            <li class="w-70.5 shrink-0 snap-start lg:w-auto">
              <article class="group relative flex h-full flex-col overflow-hidden rounded-box border border-base-content/10 bg-base-2 shadow-sm transition hover:border-primary-text hover:shadow-lg has-[a:focus-visible]:outline-2 has-[a:focus-visible]:outline-offset-2 has-[a:focus-visible]:outline-primary-text">
                <div class="relative aspect-video overflow-hidden">
                  <img src="<?php echo esc_url(inap_inicio_imagen('cursos', "curso{$n}_imagen")); ?>" alt="" loading="lazy" class="size-full object-cover" />
                  <?php if ($c("curso{$n}_tipo") !== '') : ?>
                    <span class="absolute top-4 left-4 rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-inap-brand-navy"><?php echo esc_html($c("curso{$n}_tipo")); ?></span>
                  <?php endif; ?>
                </div>
                <div class="flex flex-1 flex-col gap-2.5 px-4 pt-4 pb-5">
                  <h3 class="line-clamp-3 text-base leading-6 font-semibold text-base-content group-hover:text-primary-text">
                    <a href="<?php echo esc_url(inap_url($c("curso{$n}_url"))); ?>" class="after:absolute after:inset-0 focus-visible:outline-none"><?php echo esc_html($c("curso{$n}_titulo")); ?></a>
                  </h3>
                  <p class="flex-1 text-sm text-neutral-text"><?php echo esc_html($c("curso{$n}_area")); ?></p>
                  <ul class="flex gap-4 border-t border-base-content/10 pt-2.5 text-xs text-neutral-text">
                    <?php if ($c("curso{$n}_duracion") !== '') : ?>
                      <li class="flex items-center gap-1.5"><?php echo inap_icon('clock', 16); ?><span class="sr-only">Duración:</span> <?php echo esc_html($c("curso{$n}_duracion")); ?></li>
                    <?php endif; ?>
                    <?php if ($modalidad !== '') : ?>
                      <li class="flex items-center gap-1.5"><?php echo inap_icon(stripos($modalidad, 'presencial') !== false ? 'map-pin' : 'monitor', 16); ?><span class="sr-only">Modalidad:</span> <?php echo esc_html($modalidad); ?></li>
                    <?php endif; ?>
                  </ul>
                </div>
              </article>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
  <?php endif; ?>

  <?php
  $nv = fn (string $clave) => inap_inicio('noticias', $clave);
  $noticias = inap_inicio_visible('noticias') ? inap_inicio_noticias() : [];
  $con_eventos = inap_inicio_visible('noticias', 'eventos_mostrar');
  $eventos = $con_eventos ? inap_inicio_eventos() : [];
  if ($noticias || $con_eventos) :
  ?>
    <div class="mx-auto flex w-full max-w-360 flex-col gap-12 px-4 py-12 lg:flex-row lg:items-start lg:px-30 lg:py-24">
      <?php if ($noticias) :
        $principal = array_shift($noticias);
      ?>
        <section aria-labelledby="inicio-noticias" class="flex flex-col gap-5 lg:grid lg:min-w-0 lg:flex-1 lg:grid-cols-[1fr_auto] lg:items-end lg:gap-x-4 lg:gap-y-6">
          <?php echo $encabezado('inicio-noticias', $nv('titulo'), $nv('descripcion')); ?>
          <?php if ($nv('enlace_texto') !== '') : ?>
            <a href="<?php echo esc_url(inap_url($nv('enlace_url'))); ?>" class="<?php echo $enlace; ?> order-last self-start text-primary-text lg:order-none lg:self-end"><?php echo esc_html($nv('enlace_texto')) . $flecha(); ?></a>
          <?php endif; ?>
          <div class="flex flex-col gap-5 lg:col-span-2 lg:flex-row lg:items-start lg:gap-6">
            <?php echo inap_noticia_tarjeta($principal, 'vertical', 'h3', 'lg:min-w-0 lg:flex-1'); ?>
            <?php if ($noticias) : ?>
              <ul class="flex flex-col gap-5 lg:min-w-0 lg:flex-1 lg:gap-6">
                <?php foreach ($noticias as $n) : ?>
                  <li>
                    <?php echo inap_noticia_tarjeta($n, 'horizontal'); ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </section>
      <?php endif; ?>

      <?php if ($con_eventos) : ?>
        <section aria-labelledby="inicio-eventos" class="flex flex-col gap-3 lg:w-96 lg:shrink-0 lg:gap-6">
          <h2 id="inicio-eventos" class="text-2xl font-semibold text-base-content"><?php echo esc_html($nv('eventos_titulo')); ?></h2>
          <?php if ($eventos) : ?>
            <ul class="flex flex-col">
              <?php foreach ($eventos as $e) : ?>
                <li class="group relative flex items-center gap-4 border-b border-base-border py-4 has-[a:focus-visible]:outline-2 has-[a:focus-visible]:outline-offset-2 has-[a:focus-visible]:outline-primary-text">
                  <span aria-hidden="true" class="flex h-15 w-14 shrink-0 flex-col items-center justify-center rounded-lg bg-surface-subtle py-2 text-center font-semibold text-primary-text">
                    <span class="text-2xl"><?php echo esc_html($e['dia']); ?></span>
                    <span class="text-xs"><?php echo esc_html($e['mes']); ?></span>
                  </span>
                  <div class="flex min-w-0 flex-1 flex-col gap-1">
                    <h3 class="text-base leading-6 font-semibold text-base-content group-hover:text-primary-text">
                      <a href="<?php echo esc_url($e['url']); ?>" class="after:absolute after:inset-0 focus-visible:outline-none"><?php echo esc_html($e['titulo']); ?></a>
                    </h3>
                    <p class="text-sm text-neutral-text"><time datetime="<?php echo esc_attr($e['iso']); ?>" class="sr-only"><?php echo esc_html($e['fecha_larga']); ?><?php echo $e['meta'] !== '' ? ', ' : ''; ?></time><?php echo esc_html($e['meta']); ?></p>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else : ?>
            <p class="text-sm text-neutral-text"><?php echo esc_html($nv('eventos_vacio')); ?></p>
          <?php endif; ?>
          <?php if ($nv('eventos_enlace_texto') !== '') : ?>
            <a href="<?php echo esc_url(inap_url($nv('eventos_enlace_url'))); ?>" class="<?php echo $enlace; ?> self-start text-primary-text"><?php echo esc_html($nv('eventos_enlace_texto')) . $flecha(); ?></a>
          <?php endif; ?>
        </section>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php
  $f = fn (string $clave) => inap_inicio('fortalecimiento', $clave);
  if (inap_inicio_visible('fortalecimiento')) :
    $servicios = array_filter([1, 2, 3], fn ($n) => inap_inicio_visible('fortalecimiento', "servicio{$n}_mostrar") && $f("servicio{$n}_titulo") !== '');
    $foco_oscuro = 'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-surface-content';
  ?>
    <section id="fortalecimiento" aria-labelledby="inicio-fortalecimiento" class="relative overflow-hidden bg-brand-surface">
      <img src="<?php echo esc_url(get_theme_file_uri('assets/inicio/ondas-banda.svg')); ?>" alt="" width="2241" height="639"
           class="pointer-events-none absolute top-1/2 left-1/2 h-[639px] w-[2241px] max-w-none -translate-x-1/2 -translate-y-1/2" />
      <div class="relative mx-auto flex max-w-360 flex-col gap-8 px-4 py-12 lg:flex-row lg:items-center lg:gap-20 lg:px-30 lg:py-24">
        <div class="flex flex-col gap-4 lg:min-w-0 lg:flex-1 lg:gap-5">
          <h2 id="inicio-fortalecimiento" class="text-[1.75rem] leading-[2.125rem] font-bold text-brand-surface-content lg:max-w-122 lg:text-4xl"><?php echo esc_html($f('titulo')); ?></h2>
          <p class="text-base text-brand-surface-muted lg:text-lg"><?php echo esc_html($f('texto')); ?></p>
          <div class="flex flex-col items-center gap-1 pt-2 lg:flex-row lg:gap-6 lg:pt-3">
            <?php if ($f('boton_texto') !== '') : ?>
              <a href="<?php echo esc_url(inap_url($f('boton_url'))); ?>" class="btn btn-accent btn-lg w-full lg:btn-md lg:w-auto <?php echo $foco_oscuro; ?>"><?php echo esc_html($f('boton_texto')); ?></a>
            <?php endif; ?>
            <?php if ($f('enlace_texto') !== '') : ?>
              <a href="<?php echo esc_url(inap_url($f('enlace_url'))); ?>" class="inline-flex min-h-11 items-center gap-2 rounded-sm text-sm font-semibold text-brand-surface-content lg:min-h-0 <?php echo $foco_oscuro; ?>"><?php echo esc_html($f('enlace_texto')) . $flecha(); ?></a>
            <?php endif; ?>
          </div>
        </div>
        <?php if ($servicios) : ?>
          <ul class="flex flex-col gap-3 lg:w-132 lg:shrink-0">
            <?php foreach ($servicios as $n) : ?>
              <li class="flex items-start gap-4 rounded-box bg-brand-surface-line p-4 lg:px-6 lg:py-5">
                <span class="flex size-11 shrink-0 items-center justify-center rounded-md bg-accent text-accent-content"><?php echo inap_icon($f("servicio{$n}_icono"), 22); ?></span>
                <div class="flex min-w-0 flex-1 flex-col gap-1">
                  <h3 class="text-base leading-6 font-semibold text-brand-surface-content"><?php echo esc_html($f("servicio{$n}_titulo")); ?></h3>
                  <p class="text-sm text-brand-surface-muted"><?php echo esc_html($f("servicio{$n}_texto")); ?></p>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php
  $r = fn (string $clave) => inap_inicio('recursos', $clave);
  $recursos = array_filter([1, 2, 3], fn ($n) => inap_inicio_visible('recursos', "recurso{$n}_mostrar") && $r("recurso{$n}_titulo") !== '');
  if (inap_inicio_visible('recursos') && $recursos) :
  ?>
    <section aria-labelledby="inicio-recursos" class="mx-auto flex w-full max-w-360 flex-col gap-5 px-4 py-12 lg:gap-8 lg:px-30 lg:py-24">
      <?php echo $encabezado('inicio-recursos', $r('titulo'), $r('descripcion')); ?>
      <ul class="grid gap-5 lg:grid-cols-3 lg:gap-6">
        <?php foreach ($recursos as $n) : ?>
          <li>
            <a href="<?php echo esc_url(inap_url($r("recurso{$n}_url"))); ?>" class="group flex h-full flex-col gap-4 rounded-box border border-base-border bg-base-100 p-6 hover:bg-surface-subtle <?php echo INAP_FOCUS; ?>">
              <span class="flex size-12 items-center justify-center rounded-sm bg-surface-subtle text-primary-text group-hover:bg-base-100"><?php echo inap_icon($r("recurso{$n}_icono"), 24); ?></span>
              <span class="flex flex-col gap-2">
                <span class="text-xl font-semibold text-base-content"><?php echo esc_html($r("recurso{$n}_titulo")); ?></span>
                <span class="text-sm text-neutral-text"><?php echo esc_html($r("recurso{$n}_texto")); ?></span>
              </span>
              <span class="mt-auto flex items-center gap-2 text-sm text-neutral-text"><span class="flex-1"><?php echo esc_html($r("recurso{$n}_meta")); ?></span><?php echo inap_icon('arrow-right', 20, 'shrink-0 text-primary-text'); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  <?php endif; ?>

</main>
<?php get_footer();
