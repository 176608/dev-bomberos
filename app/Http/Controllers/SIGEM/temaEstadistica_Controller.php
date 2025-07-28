<?php
// Este script pertenece a una vista restringida del sistema SIGEM.
// Solo usuarios administradores pueden acceder a la gestión de temas estadísticos.

include '../models/temaEstadistica_Model.php';

$temas = obtenerTemas();

session_start();

$usuario = $_SESSION['usuario'] ?? 'Público';

if ($usuario !== 'admin') {
    header('Location: index.php');
    exit;
}
