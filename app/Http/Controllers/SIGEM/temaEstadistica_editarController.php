<?php
include '../models/temaEstadistica_editarModel.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: temaEstadistica.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_tema = $_POST['tema'] ?? '';
    actualizar_tema($id, $nuevo_tema);
    header("Location: temaEstadistica.php");
    exit();
}

$tema = obtener_tema_por_id($id);
?>