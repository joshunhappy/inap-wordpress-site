<?php
/**
 * DATOS DE EJEMPLO de Transparencia, tomados de los diseños de Figma. Solo se usan una vez, para crear
 * los 29 numerales en Transparencia › Numerales (inap_semilla_numerales). Después se editan allí.
 * Fechas y enlaces no son reales: los enlaces de OneDrive van vacíos (el sitio muestra «#») hasta que la Unidad los entregue.
 * Nombres de los numerales: art. 10 de la LAIP.
 */

defined('ABSPATH') || exit;

$mes = fn (string $titulo, string $fecha) => ['titulo' => $titulo, 'estado' => 'enlace', 'url' => '', 'fecha' => $fecha, 'nota' => ''];
$pendiente = fn (string $titulo, string $nota = '') => ['titulo' => $titulo, 'estado' => 'pendiente', 'url' => '', 'fecha' => '', 'nota' => $nota];
$sin = fn (string $titulo) => ['titulo' => $titulo, 'estado' => 'sin-informacion', 'url' => '', 'fecha' => '', 'nota' => ''];

/** Octubre pendiente y septiembre a enero publicados (mismas fechas que el numeral 8 en Figma). */
$anio = fn (string $septiembre) => [
    $pendiente('Octubre 2026'),
    $mes('Septiembre 2026', $septiembre),
    $mes('Agosto 2026', '2026-09-04'),
    $mes('Julio 2026', '2026-08-06'),
    $mes('Junio 2026', '2026-07-06'),
    $mes('Mayo 2026', '2026-06-05'),
    $mes('Abril 2026', '2026-05-07'),
    $mes('Marzo 2026', '2026-04-06'),
    $mes('Febrero 2026', '2026-03-05'),
    $mes('Enero 2026', '2026-02-05'),
];

$titulos = [
    1 => 'Estructura orgánica, funciones y marco normativo',
    2 => 'Dirección y teléfonos',
    3 => 'Directorio de empleados y servidores públicos',
    4 => 'Personal y remuneraciones',
    5 => 'Misión, objetivos y plan operativo anual',
    6 => 'Manuales de procedimientos',
    7 => 'Presupuesto de ingresos y egresos',
    8 => 'Informes de ejecución presupuestaria',
    9 => 'Depósitos con fondos públicos',
    10 => 'Cotización y licitación de bienes para beneficiarios',
    11 => 'Contrataciones de bienes y servicios',
    12 => 'Viajes nacionales e internacionales',
    13 => 'Inventario de bienes muebles e inmuebles',
    14 => 'Contratos de mantenimiento',
    15 => 'Subsidios, becas y transferencias',
    16 => 'Contratos, licencias y concesiones de bienes del Estado',
    17 => 'Empresas precalificadas',
    18 => 'Obras en ejecución o ejecutadas',
    19 => 'Contratos de arrendamiento',
    20 => 'Contrataciones por cotización y licitación',
    21 => 'Fideicomisos con fondos públicos',
    22 => 'Compras directas',
    23 => 'Informes finales de auditoría',
    24 => 'Entidades internacionales con fondos públicos',
    25 => 'Entidades no gubernamentales con fondos públicos',
    26 => 'Informe anual del archivo',
    27 => 'Índice de información clasificada',
    28 => 'Pertenencia sociolingüística de los usuarios',
    29 => 'Otra información de utilidad',
];

$numerales = array_map(fn ($titulo) => ['titulo' => $titulo], $titulos);

$numerales[8] += [
    'tipo' => 'periodos',
    'que_publica' => 'Informe mensual de ejecución del presupuesto (PDF) y el detalle por fuente de financiamiento (Excel), generados por el Sistema de Contabilidad Integrada (Sicoin). Cada mes abre su carpeta en OneDrive.',
    'filas' => $anio('2026-10-06'),
];

$numerales[4] += [
    'tipo' => 'apartados',
    'que_publica' => 'Pagos de dietas a la Junta Directiva, nómina del personal, contratos por servicios técnicos y profesionales, viáticos, bono 14, aguinaldo e incentivos por productividad.',
    'apartados' => [
        ['grupo' => '', 'titulo' => 'Pago de dietas de Junta Directiva', 'filas' => $anio('2026-09-29')],
        ['grupo' => '', 'titulo' => 'Nómina de personal permanente, por contrato, viáticos y dietas', 'filas' => $anio('2026-09-29')],
        ['grupo' => '', 'titulo' => 'Contratos por servicios técnicos y profesionales', 'filas' => $anio('2026-09-29')],
        ['grupo' => 'Viáticos', 'titulo' => 'Viáticos al exterior', 'filas' => array_replace($anio('2026-09-29'), [1 => $sin('Septiembre 2026')])],
        ['grupo' => 'Viáticos', 'titulo' => 'Viáticos al interior', 'filas' => $anio('2026-09-29')],
        ['grupo' => '', 'titulo' => 'Bonificación anual (bono 14)', 'filas' => [$mes('Julio 2026', '2026-08-04')]],
        ['grupo' => '', 'titulo' => 'Aguinaldo', 'filas' => [$pendiente('Diciembre 2026', 'Se publica después del pago, en diciembre.')]],
        ['grupo' => '', 'titulo' => 'Incentivo pecuniario por productividad', 'filas' => [$mes('Enero a junio 2026', '2026-02-09')]],
    ],
];

$numerales[21] += [
    'tipo' => 'sin-info',
    'que_publica' => 'Cuánto dinero público se destina a fideicomisos y cuánto cuesta administrarlos. Se publica cada mes.',
    'aviso_titulo' => 'INAP no tiene fideicomisos en 2026',
    'aviso_texto' => 'Por eso no hay documentos en este numeral. Si INAP constituye uno, la información se publicará aquí cada mes.',
];

return $numerales;
