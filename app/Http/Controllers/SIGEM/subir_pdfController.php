<?php

include '../controllers/sesionController.php';

if ($usuario !== 'admin') {
    echo "❌ Acceso denegado.<br><a href='javascript:history.back()'>Volver</a>";
    exit;
}

include '../models/conexion.php';

$cuadro_id = $_GET['id'] ?? '';
$tema = $_GET['tema'] ?? 'general'; // Valor por defecto si no se proporciona

if (!is_numeric($cuadro_id)) {
    die("ID inválido.");
}

$tituloPagina = "Actualizar PDF";