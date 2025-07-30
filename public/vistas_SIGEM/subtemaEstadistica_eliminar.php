<?php
// ===========================================================
// ACCIÓN DE ADMINISTRADOR - Eliminar subtema estadístico
// Este script elimina un subtema de estadística según el ID
// recibido por GET. Debe ser ejecutado únicamente por el
// usuario con sesión 'admin'.
// ===========================================================
include '../models/subtemaEstadistica_eliminarModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
    eliminarSubtemaPorId($id);
}

header("Location: subtemaEstadistica.php");
exit;
