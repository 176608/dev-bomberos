<?php
// Este script pertenece a una vista restringida del sistema SIGEM.
// Solo usuarios autorizados pueden agregar nuevos temas estadísticos.

include '../models/temaEstadistica_agregarModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['tema'] ?? '';
    insertar_tema($nombre);
    header("Location: temaEstadistica.php");
    exit();
}
?>
