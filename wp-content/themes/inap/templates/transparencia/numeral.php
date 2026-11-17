<?php
/**
 * Transparencia · un numeral del artículo 10 (Figma «Numeral», «Numeral · Meses anteriores abiertos»,
 * «Numeral · Apartados», «Numeral · Sin información» y sus versiones de celular).
 * En escritorio, menú lateral con los 29 numerales; en celular, select «Ir a otro numeral».
 */
defined('ABSPATH') || exit;

$n = (int) get_query_var('inap_n');
$num = inap_tp_numeral($n);
$todos = inap_tp_numerales();
$prev = $todos[$n - 1] ?? null;
$next = $todos[$n + 1] ?? null;
$abierto = isset($_GET['meses']);

get_header();
inap_breadcrumb([['Inicio', home_url('/')], ['Transparencia', inap_tp_url()], ['Información pública de oficio', inap_tp_url('ipo')], [$num['titulo'], null]]);
inap_back_link('Información pública de oficio', inap_tp_url('ipo'));
?>
<div class="mx-auto flex max-w-360 gap-12 px-4 pt-6 pb-12 lg:px-30 lg:pt-8 lg:pb-24">
  <nav aria-labelledby="art10-titulo" class="hidden w-80 shrink-0 flex-col gap-1 lg:flex">
    <h2 id="art10-titulo" class="px-3 text-sm font-semibold text-neutral-text">Artículo 10</h2>
    <ul class="flex flex-col gap-0.5">
      <?php foreach ($todos as $i => $item) : $actual = $i === $n; ?>
        <li>
          <a href="<?php echo esc_url(inap_tp_url('numeral', $i)); ?>"<?php echo $actual ? ' aria-current="page"' : ''; ?>
             class="flex items-center gap-2 rounded-field px-3 py-2 text-sm hover:bg-surface-subtle aria-[current=page]:bg-surface-subtle aria-[current=page]:font-semibold <?php echo INAP_FOCUS; ?>">
            <span class="w-5 shrink-0 <?php echo $actual ? 'text-primary-text' : 'text-neutral-text'; ?>"><?php printf('%02d', $i); ?></span>
            <span class="<?php echo $actual ? 'text-primary-text' : 'text-base-content'; ?>"><?php echo esc_html($item['titulo']); ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <main id="contenido" tabindex="-1" class="flex min-w-0 flex-1 flex-col gap-6 lg:gap-8">
    <header class="flex flex-col gap-2 lg:gap-3">
      <p class="text-sm font-semibold text-primary-text">Artículo 10 · numeral <?php echo (int) $n; ?></p>
      <h1 class="text-4xl font-bold text-base-content"><?php echo esc_html($num['titulo']); ?></h1>
    </header>

    <?php if (!empty($num['que_publica'])) : ?>
      <section aria-labelledby="que-publica" class="flex flex-col gap-2 rounded-box bg-surface-subtle p-4 lg:px-6 lg:py-5">
        <h2 id="que-publica" class="text-sm font-semibold text-base-content">Qué publica INAP en este numeral</h2>
        <p class="text-sm text-neutral-text lg:text-base"><?php echo esc_html($num['que_publica']); ?></p>
      </section>
    <?php endif; ?>

    <form method="get" data-ir class="flex items-end gap-2 lg:hidden">
      <fieldset class="fieldset min-w-0 flex-1 p-0">
        <label class="fieldset-legend" for="otro-numeral">Ir a otro numeral</label>
        <select id="otro-numeral" name="numeral" class="select w-full">
          <?php foreach ($todos as $i => $item) : ?>
            <option value="<?php echo esc_url(inap_tp_url('numeral', $i)); ?>"<?php selected($i, $n); ?>><?php echo (int) $i . '. ' . esc_html($item['titulo']); ?></option>
          <?php endforeach; ?>
        </select>
      </fieldset>
      <button type="submit" class="btn btn-outline btn-primary h-10">Ir</button>
    </form>

    <div class="flex items-end gap-4 lg:gap-6">
      <?php echo inap_tp_selector_anio('w-36 shrink-0 lg:w-50'); ?>
      <p class="pb-2.5 text-sm text-neutral-text"><?php echo esc_html($num['resumen'] ?? ''); ?></p>
    </div>

    <?php switch ($num['tipo'] ?? '') :
        case 'periodos': ?>
          <section aria-label="Documentos por mes">
            <?php echo inap_tp_periodos($num['periodos'], $num['titulo'], 'meses-anteriores', $abierto); ?>
          </section>
          <?php break;

        case 'apartados':
          $aps = $num['apartados']; ?>
          <nav aria-labelledby="en-esta-pagina" class="flex flex-col gap-3">
            <h2 id="en-esta-pagina" class="text-sm font-semibold text-base-content">En esta página</h2>
            <ol class="grid gap-x-8 lg:grid-flow-col lg:grid-cols-2 lg:grid-rows-4">
              <?php foreach ($aps as $i => $ap) : ?>
                <li><a href="#apartado-<?php echo $i + 1; ?>" class="flex min-h-11 items-center rounded-sm py-3 text-sm font-semibold text-primary-text hover:underline <?php echo INAP_FOCUS; ?>"><?php echo ($i + 1) . '. ' . esc_html($ap['titulo']); ?></a></li>
              <?php endforeach; ?>
            </ol>
          </nav>
          <div class="flex flex-col gap-10 lg:gap-12">
            <?php foreach ($aps as $i => $ap) : $id = 'apartado-' . ($i + 1); ?>
              <section id="<?php echo $id; ?>" aria-labelledby="<?php echo $id; ?>-t" class="flex flex-col <?php echo isset($ap['sub']) ? 'gap-6' : 'gap-4'; ?>">
                <h2 id="<?php echo $id; ?>-t" class="text-xl font-semibold text-base-content"><?php echo ($i + 1) . '. ' . esc_html($ap['titulo']); ?></h2>
                <?php if (isset($ap['sub'])) : foreach ($ap['sub'] as $j => $sub) : ?>
                  <section aria-labelledby="<?php echo "{$id}-{$j}"; ?>-t" class="flex flex-col gap-2">
                    <h3 id="<?php echo "{$id}-{$j}"; ?>-t" class="text-sm font-semibold text-base-content"><?php echo esc_html($sub['titulo']); ?></h3>
                    <?php echo inap_tp_periodos($sub['periodos'], $sub['titulo'], "{$id}-{$j}-meses"); ?>
                  </section>
                <?php endforeach; else :
                    echo inap_tp_periodos($ap['periodos'], $ap['titulo'], "{$id}-meses");
                endif; ?>
              </section>
            <?php endforeach; ?>
          </div>
          <?php break;

        case 'sin-info': ?>
          <div role="status" class="alert alert-soft alert-info items-start">
            <?php echo inap_icon('info'); ?>
            <div class="flex flex-col gap-1">
              <p class="font-semibold"><?php echo esc_html($num['aviso']['titulo']); ?></p>
              <p><?php echo esc_html($num['aviso']['texto']); ?></p>
            </div>
          </div>
          <?php break;

        default: ?>
          <div role="status" class="alert alert-soft alert-info"><?php echo inap_icon('info'); ?><span>Este numeral aún no tiene documentos publicados en <?php echo (int) inap_tp_anio(); ?>.</span></div>
    <?php endswitch; ?>

    <nav aria-label="Numerales anterior y siguiente" class="flex flex-col gap-4 pt-2 lg:flex-row lg:justify-between lg:gap-6 lg:pt-4">
      <?php if ($prev) : ?>
        <a href="<?php echo esc_url(inap_tp_url('numeral', $prev['n'])); ?>" class="flex flex-col gap-1 rounded-sm text-sm <?php echo INAP_FOCUS; ?>">
          <span class="text-neutral-text">Anterior</span>
          <span class="font-semibold text-primary-text underline lg:no-underline lg:hover:underline"><?php echo $prev['n'] . '. ' . esc_html($prev['titulo']); ?></span>
        </a>
      <?php else : ?><span></span><?php endif; ?>
      <?php if ($next) : ?>
        <a href="<?php echo esc_url(inap_tp_url('numeral', $next['n'])); ?>" class="flex flex-col gap-1 rounded-sm text-sm lg:items-end lg:text-end <?php echo INAP_FOCUS; ?>">
          <span class="text-neutral-text">Siguiente</span>
          <span class="font-semibold text-primary-text underline lg:no-underline lg:hover:underline"><?php echo $next['n'] . '. ' . esc_html($next['titulo']); ?></span>
        </a>
      <?php endif; ?>
    </nav>
  </main>
</div>
<?php get_footer();
