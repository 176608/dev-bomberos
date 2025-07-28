<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Muestra cuadros estadísticos del tema Económico sin requerir autenticación.

require_once '../controllers/sesionController.php';
require_once '../models/conexion.php';
require_once '../models/EconomicoModel.php';
require_once '../public/mostrar_csv.php';

$subtema_id = isset($_GET['subtema_id']) ? intval($_GET['subtema_id']) : 26;

$tema = 'Económico';
$tema_id = 'economico';

$cuadros = obtenerCuadrosPorSubtema($subtema_id);

$temas = obtenerTemas($conexion);
$subtemas = obtenerSubtemasOrdenadosPorCuadro($conexion, $tema);

$nombre_actual = 'Subtema'; // valor por defecto

foreach ($subtemas as $subtema) {
    if ($subtema['id'] == $subtema_id) {
        $nombre_actual = $subtema['nombre_subtema'];
        break;
    }
}
