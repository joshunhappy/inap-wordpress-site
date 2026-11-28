<?php
/**
 * Contacto (Figma «Sitio institucional • Desktop/Mobile • Light • Contacto»).
 * Computadora: encabezado | tarjeta de información pública; datos + tarjetas | formulario.
 * Celular, en orden de lectura: encabezado, datos, formulario, tarjeta de información pública, tarjetas.
 */
defined('ABSPATH') || exit;

$t = 'inap_contacto_txt';
$estado = sanitize_key($_GET['mensaje'] ?? '');
$campos_error = $estado === 'error' ? explode(',', sanitize_text_field(wp_unslash($_GET['campos'] ?? ''))) : [];
$previo = $estado === 'error' ? inap_contacto_previo() : [];
$campo = fn (string $id, string $label, array $o = []) => inap_campo($id, $label, $o, (string) ($previo[$id] ?? ''), in_array($id, $campos_error, true) ? ($o['error'] ?? '') : '');
$c = inap_contacto_vars(false);
$correo = inap_ajuste('inap_ajustes', 'correo');
$datos = array_filter([$t('dato1'), $t('dato2')]);
$tarjetas = array_filter([
    [$t('faq_titulo'), $t('faq_texto'), $t('faq_meta'), $t('faq_url')],
    [$t('delegaciones_titulo'), $t('delegaciones_texto'), $t('delegaciones_meta'), $t('delegaciones_url')],
], fn ($k) => $k[0] !== '' && $k[3] !== '');
$enlace = 'rounded-sm text-primary-text underline hover:no-underline ' . INAP_FOCUS;
$fila = fn (string $icono, string $html) => sprintf('<li class="flex items-start gap-2 text-sm text-base-content">%s<span>%s</span></li>', inap_icon($icono, 20, 'shrink-0'), $html);

get_header();
inap_breadcrumb([['Inicio', home_url('/')], [$t('titulo'), null]]);
inap_back_link('Inicio', home_url('/'));
?>
<main id="contenido" tabindex="-1" class="relative">
  <div class="pointer-events-none absolute inset-0 overflow-hidden">
    <img src="<?php echo esc_url(get_theme_file_uri('assets/brand/waves-lines.svg')); ?>" alt="" width="1440" height="411"
         class="absolute top-0 left-1/2 h-[411px] w-[1440px] max-w-none -translate-x-1/2 lg:h-auto lg:w-full" />
  </div>
  <div class="relative mx-auto grid max-w-360 gap-10 px-4 pt-6 pb-16 lg:grid-cols-2 lg:grid-rows-[auto_auto_1fr] lg:gap-x-6 lg:gap-y-6 lg:px-30 lg:pt-10 lg:pb-24">

    <header class="flex flex-col gap-4 lg:col-start-1 lg:row-start-1 lg:gap-5 lg:pb-8">
      <h1 class="text-4xl font-bold text-base-content lg:text-5xl lg:leading-[1]"><?php echo esc_html($t('titulo')); ?></h1>
      <?php if ($t('intro') !== '') : ?>
        <p class="text-base text-neutral-text lg:text-lg lg:leading-7"><?php echo esc_html($t('intro')); ?></p>
      <?php endif; ?>
      <?php if ($datos || $t('horario') !== '') : ?>
        <ul class="flex flex-col gap-3 text-sm font-semibold text-base-content lg:flex-row lg:flex-wrap lg:gap-x-6">
          <?php foreach ($datos as $dato) : ?>
            <li class="flex items-center gap-2"><?php echo inap_icon('circle-check', 20, 'shrink-0') . esc_html($dato); ?></li>
          <?php endforeach; ?>
          <?php if ($t('horario') !== '') : ?>
            <li class="flex items-center gap-2"><?php echo inap_icon('clock', 20, 'shrink-0') . esc_html($t('horario')); ?></li>
          <?php endif; ?>
        </ul>
      <?php endif; ?>
    </header>

    <section aria-labelledby="datos-titulo" class="flex flex-col gap-4 lg:col-start-1 lg:row-start-2 lg:pt-6">
      <h2 id="datos-titulo" class="text-xl font-semibold text-base-content"><?php echo esc_html($t('datos_titulo')); ?></h2>
      <address class="not-italic">
        <ul class="flex flex-col gap-3">
          <?php
          echo $fila('mail', '<a href="mailto:' . esc_attr($correo) . '" class="' . $enlace . '">' . $c['correo'] . '</a>');
          echo $fila('phone', '<a href="tel:' . $c['tel'] . '" class="' . $enlace . '">' . $c['telefono'] . '</a>');
          echo $fila('map-pin', $c['direccion']);
          if ($t('horario') !== '') {
              echo $fila('clock', esc_html($t('horario')));
          }
          ?>
        </ul>
      </address>
    </section>

    <section id="mensaje" aria-labelledby="mensaje-titulo" class="flex scroll-mt-24 flex-col gap-7 self-start rounded-box border border-base-border bg-base-100 p-5 lg:col-start-2 lg:row-span-2 lg:row-start-2 lg:gap-8 lg:p-8">
      <div class="flex flex-col gap-2">
        <h2 id="mensaje-titulo" class="text-2xl font-semibold text-base-content"><?php echo esc_html($t('form_titulo')); ?></h2>
        <?php if ($t('form_intro') !== '') : ?>
          <p class="text-lg leading-7 text-neutral-text"><?php echo esc_html($t('form_intro')); ?></p>
        <?php endif; ?>
      </div>

      <?php if ($estado === 'enviado') : ?>
        <div role="status" class="alert alert-soft alert-success"><?php echo inap_icon('circle-check'); ?><span><?php echo esc_html($t('gracias')); ?></span></div>
      <?php elseif ($estado === 'error') : ?>
        <div role="alert" class="alert alert-soft alert-error"><?php echo inap_icon('circle-x'); ?><span>Revisa los campos marcados para enviar el mensaje.</span></div>
      <?php elseif ($estado === 'expirado') : ?>
        <div role="alert" class="alert alert-soft alert-warning"><?php echo inap_icon('triangle-alert'); ?><span>La página estuvo abierta mucho tiempo. Vuelve a enviar el mensaje.</span></div>
      <?php elseif ($estado === 'limite') : ?>
        <div role="alert" class="alert alert-soft alert-warning"><?php echo inap_icon('triangle-alert'); ?><span>Recibimos varios mensajes seguidos desde tu conexión. Espera unos minutos y vuelve a intentarlo, o escríbenos a <?php echo $c['correo']; ?>.</span></div>
      <?php endif; ?>

      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="flex flex-col gap-7 lg:gap-8" novalidate>
        <input type="hidden" name="action" value="inap_contacto" />
        <?php wp_nonce_field('inap_contacto', '_inap'); ?>
        <div class="hidden" aria-hidden="true">
          <label for="sitio_web">No llenes este campo</label>
          <input id="sitio_web" name="sitio_web" type="text" tabindex="-1" autocomplete="off" />
        </div>
        <fieldset class="flex flex-col gap-5">
          <legend class="mb-5 text-xl font-semibold text-base-content">Tus datos</legend>
          <div class="flex flex-col gap-5 lg:flex-row lg:gap-6">
            <?php echo $campo('nombre', 'Nombre', ['autocomplete' => 'given-name']); ?>
            <?php echo $campo('apellidos', 'Apellidos', ['autocomplete' => 'family-name']); ?>
          </div>
          <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-6">
            <?php echo $campo('correo', 'Correo electrónico (obligatorio)', ['type' => 'email', 'required' => true, 'autocomplete' => 'email', 'placeholder' => 'nombre@correo.com', 'ayuda' => 'Aquí te enviaremos la respuesta.', 'error' => 'Escribe un correo válido, por ejemplo nombre@correo.com.']); ?>
            <?php echo $campo('telefono', 'Teléfono', ['type' => 'tel', 'autocomplete' => 'tel', 'placeholder' => '0000-0000']); ?>
          </div>
          <?php echo $campo('institucion', 'Institución donde trabajas (opcional)', ['autocomplete' => 'organization', 'placeholder' => 'Por ejemplo: Ministerio de Salud']); ?>
        </fieldset>
        <fieldset class="flex flex-col gap-5">
          <legend class="mb-5 text-xl font-semibold text-base-content">Tu mensaje</legend>
          <?php echo $campo('tema', 'Tema (obligatorio)', ['type' => 'select', 'required' => true, 'options' => array_column(inap_contacto_temas(), 'tema'), 'error' => 'Elige el tema de tu consulta.']); ?>
          <?php echo $campo('mensaje', 'Mensaje (obligatorio)', ['type' => 'textarea', 'required' => true, 'placeholder' => 'Escribe tu consulta', 'ayuda' => 'Si tu consulta es sobre un curso o trámite, incluye su nombre.', 'error' => 'Escribe tu mensaje.']); ?>
        </fieldset>
        <button type="submit" class="btn btn-primary w-full lg:w-auto lg:self-start">Enviar mensaje</button>
      </form>
    </section>

    <?php if ($t('ley_mostrar') === '1') : ?>
      <section aria-labelledby="ley-titulo" class="flex flex-col items-start gap-3 rounded-box bg-surface-subtle p-5 lg:col-start-2 lg:row-start-1 lg:self-start lg:p-6">
        <span class="hidden size-12 items-center justify-center rounded-field bg-base-100 text-primary-text lg:flex"><?php echo inap_icon('scale', 24); ?></span>
        <h2 id="ley-titulo" class="text-xl font-semibold text-base-content"><?php echo esc_html($t('ley_titulo')); ?></h2>
        <?php if ($t('ley_texto') !== '') : ?>
          <p class="text-sm text-neutral-text"><?php echo esc_html($t('ley_texto')); ?></p>
        <?php endif; ?>
        <a href="<?php echo esc_url(home_url('/transparencia/')); ?>" class="btn btn-outline btn-primary w-full lg:w-auto"><?php echo esc_html($t('ley_boton')); ?></a>
      </section>
    <?php endif; ?>

    <?php if ($tarjetas) : ?>
      <nav aria-label="Más ayuda" class="lg:col-start-1 lg:row-start-3 lg:self-start">
        <ul class="flex flex-col gap-4 lg:gap-6">
          <?php foreach ($tarjetas as [$titulo, $texto, $meta, $url]) : ?>
            <li>
              <a href="<?php echo esc_url($url); ?>" class="flex flex-col gap-4 rounded-box border border-base-border bg-base-100 p-5 hover:bg-surface-subtle lg:p-6 <?php echo INAP_FOCUS; ?>">
                <span class="flex flex-col gap-2">
                  <span class="text-[1.75rem] leading-9 font-bold text-base-content lg:text-4xl lg:leading-10"><?php echo esc_html($titulo); ?></span>
                  <?php if ($texto !== '') : ?><span class="text-sm text-neutral-text"><?php echo esc_html($texto); ?></span><?php endif; ?>
                </span>
                <span class="flex items-center gap-2 text-sm text-neutral-text"><span class="flex-1"><?php echo esc_html($meta); ?></span><?php echo inap_icon('arrow-right', 20, 'shrink-0 text-primary-text'); ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</main>
<?php get_footer();
