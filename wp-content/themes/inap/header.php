<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>
<body <?php body_class('bg-base-100 font-sans text-base-content antialiased'); ?>>
<?php wp_body_open(); ?>
<a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded-field focus:bg-base-100 focus:px-4 focus:py-3 focus:text-sm focus:font-semibold focus:text-primary-text focus:outline-2 focus:outline-primary-text">Saltar al contenido principal</a>
<?php echo inap_cabecera(); ?>
