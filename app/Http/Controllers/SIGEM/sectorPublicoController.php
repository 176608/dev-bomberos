<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Muestra cuadros estadísticos del tema Sector Público sin requerir autenticación.

require_once '../controllers/sesionController.php';
require_once '../models/sectorPublicoModel.php';

include '../public/mostrar_csv.php';
include '../models/conexion.php';

$subtema_id = isset($_GET['subtema_id']) ? intval($_GET['subtema_id']) : 10;

$temas = obtenerTemas($conexion);
$tema = 'Sector Público';
$tema_id = 'sectorpublico';
$subtemas = obtenerSubtemasPorTema($conexion, $tema);

$cuadros = obtenerCuadrosPorSubtema($conexion, $subtema_id);

$nombre_actual = 'Subtema'; // valor por defecto
foreach ($subtemas as $subtema) {
    if ($subtema['id'] == $subtema_id) {
        $nombre_actual = $subtema['nombre_subtema'];
        break;
    }
}
$indice = 0;
