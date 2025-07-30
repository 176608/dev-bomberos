<?php
// =============================================================
// Vista de ADMINISTRADOR - Eliminar Tema de Estadística
// =============================================================
// Este script elimina un tema del módulo de Estadística 
// según el ID recibido por GET. Solo accesible por el admin.
// Utiliza la función definida en el modelo correspondiente.

include '../models/temaEstadistica_eliminarModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
eliminarTemaPorId($id);
}

header("Location: temaEstadistica.php");
exit();
?>