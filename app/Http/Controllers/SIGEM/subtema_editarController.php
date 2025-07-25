<?php

require_once '../controllers/sesionController.php';
include '../models/subtema_editarModel.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["ce_subtema"];
    $tema_id = $_POST["ce_tema_id"];

    actualizarSubtema($nombre, $tema_id, $id);
    header("Location: subtema.php");
    exit;
}

$subtema = obtenerSubtemaPorId($id);
$temas = obtenerTemas();