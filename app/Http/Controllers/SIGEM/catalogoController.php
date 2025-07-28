<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Muestra el catálogo general sin requerir autenticación.

//Forman parte de la lógica de control del flujo de usuario (autenticado o no).

include 'sesionController.php';
include '../models/catalogoModel.php';

$cuadros = obtenerCuadros();

?>
