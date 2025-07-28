<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Muestra cuadros estadísticos del tema Medio Ambiente sin requerir autenticación.

require_once 'sesionController.php';

include '../models/conexion.php';
include '../public/mostrar_csv.php';
require_once '../models/medioAmbienteModel.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$tituloPagina = "Medio Ambiente";

$subtema_id = isset($_GET['subtema_id']) ? intval($_GET['subtema_id']) : 0;

$cuadros = obtenerCuadrosMA($conexion); // Ejecuta la función

// ORDENAR POR CÓDIGO (orden natural, como humano: 1, 2, 10, 11, etc.)
usort($cuadros, function($a, $b) {
    return strnatcmp($a['codigo_cuadro'], $b['codigo_cuadro']);
});

$temas = obtenerTemas($conexion);

$tema = 'Medio Ambiente';
$tema_id = 'medioambiente';

$subtemas = obtenerSubtemasPorTema($conexion, $tema);

// Buscar el nombre del subtema en $subtemas según $subtema_id
$nombre_actual = 'Subtema'; // valor por defecto
foreach ($subtemas as $subtema) {
    if ($subtema['id'] == $subtema_id) {
        $nombre_actual = $subtema['nombre_subtema'];
        break;
    }
}
