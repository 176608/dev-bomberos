<?php
// ===========================================================
// ACCIÓN DE ADMINISTRADOR - Eliminación de subtema por ID
// Este archivo ejecuta la eliminación de un subtema específico
// según el parámetro recibido por GET. Debe estar disponible
// solo para el usuario 'admin'.
// ===========================================================

include '../models/subtema_eliminarModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
    eliminarSubtemaPorId($id);
}

header("Location: subtema.php");
exit;
