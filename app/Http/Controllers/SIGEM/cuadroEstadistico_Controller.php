<?php
// Este script pertenece a una vista restringida del sistema SIGEM.
// Solo usuarios administradores pueden acceder a esta funcionalidad.

include '../models/cuadroEstadistico_Model.php';

$cuadros = obtenerCuadros();

session_start();

$usuario = $_SESSION['usuario'] ?? 'Público';

if ($usuario !== 'admin') {
    header('Location: index.php');
    exit;
}
