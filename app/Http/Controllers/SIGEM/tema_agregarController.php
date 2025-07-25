<?php
include '../models/tema_agregarModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['tema'] ?? '';
    insertar_tema($nombre);
    header("Location: tema.php");
    exit();
}
?>
