<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Muestra cuadros estadísticos del tema Sociodemográfico sin requerir autenticación.

include '../controllers/sesionController.php';
include '../models/socioDemograficoModel.php';
include '../models/conexion.php';
include '../public/mostrar_csv.php';

$tema_id = 'sociodemografico';
$tema = 'Sociodemográfico';

$subtema_id = isset($_GET['subtema_id']) ? intval($_GET['subtema_id']) : 3;

$cuadros = obtenerCuadrosPorSubtema($subtema_id);

// ORDENAR POR CÓDIGO DE MANERA NATURAL (para que 3.S.10 venga después de 3.S.9)
usort($cuadros, function($a, $b) {
    return strnatcmp($a['codigo_cuadro'], $b['codigo_cuadro']);
});

$temas = obtenerTemas($conexion);
$subtemas = obtenerSubtemasPorTema($conexion, $tema);

$nombre_actual = 'Subtema';
foreach ($subtemas as $subtema) {
    if ($subtema['id'] == $subtema_id) {
        $nombre_actual = $subtema['nombre_subtema'];
        break;
    }
}
