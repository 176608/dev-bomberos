<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Muestra la densidad de población sin requerir autenticación.

include '../controllers/sesionController.php';

require_once '../models/densidadModel.php';
require_once '../public/mostrar_csv.php';

$cuadros = obtenerCuadrosDensidad();

$tema = 'geografico'; // Aquí indicamos explícitamente que los archivos están en la carpeta "localidades"
$archivo='densidad';

$tituloPagina = "Densidad de Población";
