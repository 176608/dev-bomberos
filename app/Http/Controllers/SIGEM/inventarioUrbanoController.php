<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Muestra cuadros estadísticos del tema Inventario Urbano sin requerir autenticación.

require_once 'sesionController.php';

require_once  '../public/mostrar_csv.php';

require_once  '../models/inventarioUrbanoModel.php';

$subtema_id = isset($_GET['subtema_id']) ? intval($_GET['subtema_id']) : 9;

$temas = obtenerTemas($conexion);

$tema = 'Inventario Urbano';
$tema_id = 'inventariourbano';
$subtemas = obtenerSubtemasPorTema($tema);

$cuadros = obtenerCuadrosPorSubtema( $subtema_id);
$index = 0;

// Buscar el nombre del subtema en $subtemas según $subtema_id
$nombre_actual = 'Subtema'; // valor por defecto
foreach ($subtemas as $subtema) {
    if ($subtema['id'] == $subtema_id) {
        $nombre_actual = $subtema['nombre_subtema'];
        break;
    }
}
