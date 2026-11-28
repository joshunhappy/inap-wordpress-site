<?php
/**
 * Transparencia · inicio con formulario (Figma «Transparencia • Desktop • Light • Inicio · Formulario»
 * y su versión de celular). La solicitud se llena en la misma página (#solicitud).
 */
defined('ABSPATH') || exit;

$estado = sanitize_key($_GET['solicitud'] ?? '');
$campos_error = $estado === 'error' ? explode(',', sanitize_text_field(wp_unslash($_GET['campos'] ?? ''))) : [];
$err = fn (string $campo) => in_array($campo, $campos_error, true);
$previo = $estado === 'error' ? inap_tp_solicitud_previa() : [];

$t = 'inap_tp_texto';
$unidad = inap_tp_unidad();
$actualizado = inap_tp_datos()['actualizado'];
$secciones = [
    ['Información pública de oficio', $t('ipo_descripcion'), $actualizado ? "Actualizado el {$actualizado}" : 'Aún sin documentos publicados', inap_tp_url('ipo')],
    ['Transparencia presupuestaria', $t('presupuestaria_descripcion'), $t('presupuestaria_meta'), $t('presupuestaria_url') ?: '#'],
    ['Rendición de cuentas', $t('rendicion_descripcion'), $t('rendicion_meta'), $t('rendicion_url') ?: '#'],
];

/** Campo con etiqueta (componente Field): inap_campo() con lo escrito antes y el error, si lo hay. */
$campo = fn (string $id, string $label, array $o = []) => inap_campo($id, $label, $o, (string) ($previo[$id] ?? ''), $err($id) ? ($o['error'] ?? '') : '');

get_header();
inap_breadcrumb([['Inicio', home_url('/')], ['Transparencia', null]]);
inap_back_link('Inicio', home_url('/'));
?>
<main id="contenido" tabindex="-1" class="relative overflow-hidden">
  <img src="<?php echo esc_url(get_theme_file_uri('assets/brand/waves-lines.svg')); ?>" alt="" width="1440" height="411"
       class="pointer-events-none fixed inset-x-0 top-0 h-auto w-full max-w-none" />
  <div class="relative mx-auto flex max-w-360 flex-col gap-10 px-4 pt-6 pb-12 lg:gap-14 lg:px-30 lg:pt-10 lg:pb-24">

    <div class="flex flex-col gap-10 lg:flex-row lg:gap-6">
      <div class="flex flex-col gap-4 lg:flex-1 lg:gap-5">
        <h1 class="text-4xl font-bold text-base-content lg:text-5xl lg:leading-[1]"><?php echo esc_html($t('inicio_titulo')); ?></h1>
        <p class="text-base text-neutral-text lg:text-lg lg:leading-7"><?php echo esc_html($t('inicio_intro')); ?></p>
        <ul class="flex flex-col gap-3 text-sm font-semibold text-base-content lg:flex-row lg:gap-6">
          <li class="flex items-center gap-2"><?php echo inap_icon('circle-check', 20, 'shrink-0') . esc_html($t('inicio_dato1')); ?></li>
          <li class="flex items-center gap-2"><?php echo inap_icon('circle-check', 20, 'shrink-0') . esc_html($t('inicio_dato2')); ?></li>
          <li class="flex items-center gap-2"><?php echo inap_icon('clock', 20, 'shrink-0') . esc_html($unidad['plazo_corto']); ?></li>
        </ul>
      </div>
      <section aria-labelledby="ley-titulo" class="flex flex-col items-start gap-3 rounded-box bg-surface-subtle p-5 lg:flex-1 lg:self-start lg:p-6">
        <span class="hidden size-12 items-center justify-center rounded-field bg-base-100 text-primary-text lg:flex"><?php echo inap_icon('scale', 24); ?></span>
        <h2 id="ley-titulo" class="text-xl font-semibold text-base-content"><?php echo esc_html($t('ley_titulo')); ?></h2>
        <p class="text-sm text-neutral-text"><?php echo esc_html($t('ley_texto')); ?></p>
        <a href="<?php echo esc_url($t('ley_url') ?: '#'); ?>" class="btn btn-outline btn-primary w-full lg:w-auto">Leer la ley (PDF)</a>
      </section>
    </div>

    <div class="flex flex-col gap-10 lg:flex-row lg:items-start lg:gap-6">
      <div class="flex flex-col gap-10 lg:flex-1 lg:gap-6">
        <h2 class="sr-only">Secciones</h2>
        <ul class="flex flex-col gap-4 lg:gap-6">
          <?php foreach ($secciones as [$titulo, $desc, $meta, $url]) : ?>
            <li>
              <a href="<?php echo esc_url($url); ?>" class="group flex flex-col gap-4 rounded-box border border-base-border bg-base-100 p-5 hover:bg-surface-subtle lg:p-6 <?php echo INAP_FOCUS; ?>">
                <span class="flex flex-col gap-2">
                  <span class="text-[1.75rem] leading-9 font-bold text-base-content lg:text-4xl lg:leading-10"><?php echo esc_html($titulo); ?></span>
                  <span class="text-sm text-neutral-text"><?php echo esc_html($desc); ?></span>
                </span>
                <span class="flex items-center gap-2 text-sm text-neutral-text"><span class="flex-1"><?php echo esc_html($meta); ?></span><?php echo inap_icon('arrow-right', 20, 'shrink-0 text-primary-text'); ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
        <section aria-labelledby="unidad-titulo" class="flex flex-col gap-4 border-t border-base-border pt-6">
          <h2 id="unidad-titulo" class="text-xl font-semibold text-base-content">Unidad de Información Pública</h2>
          <address class="not-italic">
            <ul class="flex flex-col gap-3">
              <?php
              echo inap_tp_contacto('mail', '<a href="mailto:' . esc_attr($unidad['correo']) . '" class="rounded-sm text-primary-text underline ' . INAP_FOCUS . '">' . esc_html($unidad['correo']) . '</a>');
              echo inap_tp_contacto('phone', '<a href="tel:' . esc_attr($unidad['tel']) . '" class="rounded-sm ' . INAP_FOCUS . '">' . esc_html($unidad['telefono']) . '</a>');
              echo inap_tp_contacto('map-pin', esc_html($unidad['direccion']));
              echo inap_tp_contacto('clock', esc_html($unidad['plazo']));
              ?>
            </ul>
          </address>
        </section>
      </div>

      <section id="solicitud" aria-labelledby="solicitud-titulo" class="flex flex-col gap-7 rounded-box border border-base-border bg-base-100 p-5 lg:flex-1 lg:gap-8 lg:p-8">
        <div class="flex flex-col gap-2">
          <h2 id="solicitud-titulo" class="text-2xl font-semibold text-base-content">Solicitud de información</h2>
          <p class="text-lg leading-7 text-neutral-text"><?php echo esc_html($t('solicitud_intro')); ?></p>
        </div>

        <?php if ($estado === 'prueba') : ?>
          <div role="status" class="alert alert-soft alert-info"><?php echo inap_icon('info'); ?><span>Entorno de prueba: los datos son válidos, pero la solicitud no se envió. Falta definir con la Unidad de Información Pública cómo se reciben.</span></div>
        <?php elseif ($estado === 'error') : ?>
          <div role="alert" class="alert alert-soft alert-error"><?php echo inap_icon('circle-x'); ?><span>Revisa los campos marcados para enviar la solicitud.<?php echo !empty($previo['archivo']) ? ' Vuelve a elegir el archivo de apoyo.' : ''; ?></span></div>
        <?php elseif ($estado === 'expirada') : ?>
          <div role="alert" class="alert alert-soft alert-warning"><?php echo inap_icon('triangle-alert'); ?><span>La página estuvo abierta mucho tiempo. Vuelve a enviar la solicitud.</span></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" class="flex flex-col gap-7 lg:gap-8" novalidate>
          <input type="hidden" name="action" value="inap_solicitud" />
          <?php wp_nonce_field('inap_solicitud', '_inap'); ?>
          <fieldset class="flex flex-col gap-5">
            <legend class="mb-5 text-xl font-semibold text-base-content">Tus datos</legend>
            <div class="flex flex-col gap-5 lg:flex-row lg:gap-6">
              <?php echo $campo('nombre', 'Nombre', ['autocomplete' => 'given-name']); ?>
              <?php echo $campo('apellidos', 'Apellidos', ['autocomplete' => 'family-name']); ?>
            </div>
            <div class="flex flex-col gap-5 lg:flex-row lg:gap-6">
              <?php echo $campo('tipo_id', 'Tipo de identificación', ['type' => 'select', 'options' => ['DPI', 'Pasaporte']]); ?>
              <?php echo $campo('numero_id', 'Número de identificación'); ?>
            </div>
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:gap-6">
              <?php echo $campo('correo', 'Correo electrónico (obligatorio)', ['type' => 'email', 'required' => true, 'autocomplete' => 'email', 'placeholder' => 'nombre@correo.com', 'ayuda' => 'Aquí te enviaremos la respuesta.', 'error' => 'Escribe un correo válido, por ejemplo nombre@correo.com.']); ?>
              <?php echo $campo('telefono', 'Teléfono', ['type' => 'tel', 'autocomplete' => 'tel', 'placeholder' => '0000-0000']); ?>
            </div>
          </fieldset>
          <fieldset class="flex flex-col gap-5">
            <legend class="mb-5 text-xl font-semibold text-base-content">Tu solicitud</legend>
            <?php echo $campo('pedido', '¿Qué información necesitas? (obligatorio)', ['type' => 'textarea', 'required' => true, 'placeholder' => 'Por ejemplo: contratos de capacitación firmados en 2025', 'ayuda' => 'Sé lo más específico posible: tema, período y, si lo sabes, la unidad que la tiene.', 'error' => 'Escribe qué información necesitas.']); ?>
            <div class="flex flex-col gap-2">
              <span id="archivo-etiqueta" class="text-sm font-semibold text-base-content">Archivo de apoyo (opcional)</span>
              <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:gap-3">
                <input id="archivo" name="archivo" type="file" class="peer sr-only" aria-labelledby="archivo-etiqueta" aria-describedby="archivo-nombre archivo-ayuda" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" data-max="10485760" />
                <label for="archivo" class="btn btn-outline btn-primary w-full peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-primary-text lg:w-auto">Elegir archivo</label>
                <span id="archivo-nombre" class="text-sm text-base-content/60" aria-live="polite">Ningún archivo seleccionado</span>
              </div>
              <p id="archivo-ayuda" class="text-sm text-base-content/60">Tamaño máximo: 10 MB.</p>
            </div>
          </fieldset>
          <button type="submit" class="btn btn-primary w-full lg:w-auto lg:self-start">Enviar solicitud</button>
        </form>
      </section>
    </div>
  </div>
</main>
<?php get_footer();
