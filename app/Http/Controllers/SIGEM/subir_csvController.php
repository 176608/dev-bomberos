<?php
require_once '../controllers/sesionController.php';

if ($usuario !== 'admin') {
    echo "❌ Acceso denegado.<br><a href='javascript:history.back()'>Volver</a>";
    exit;
}
$cuadro_id = $_GET['id'] ?? '';
$tema = $_GET['tema'] ?? '';

if (!is_numeric($cuadro_id)) {
    die("ID de cuadro inválido.");
}

$tituloPagina = "Actualizar CSV";