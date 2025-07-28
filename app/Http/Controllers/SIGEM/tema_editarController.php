<?php
// Este script pertenece a una vista restringida del sistema SIGEM.
// Solo usuarios autorizados pueden editar temas existentes.

include '../models/tema_editarModel.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: tema.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_tema = $_POST['tema'] ?? '';
    actualizar_tema($id, $nuevo_tema);
    header("Location: tema.php");
    exit();
}

$tema = obtener_tema_por_id($id);
?>
