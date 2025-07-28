<?php
// Este script pertenece a una vista restringida del sistema SIGEM.
// Solo usuarios administradores pueden acceder a la gestión de subtemas estadísticos.

include '../models/subtemaEstadistica_Model.php';

$subtemas = obtenerSubtemas();

session_start();

$usuario = $_SESSION['usuario'] ?? 'Público';

if ($usuario !== 'admin') {
    header('Location: index.php');
    exit;
}
