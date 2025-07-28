<?php
// Este script pertenece a una vista restringida del sistema SIGEM.
// Solo usuarios autorizados pueden editar subtemas estadísticos.

require_once '../controllers/sesionController.php';
include '../models/subtemaEstadistica_editarModel.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subtema = $_POST["ce_subtema"];
    $tema = $_POST["ce_tema"];

    actualizarSubtema($subtema, $tema, $id);
    header("Location: subtemaEstadistica.php");
    exit;
}

$subtemaActual = obtenerSubtemaPorId($id);
$temas = obtenerTemas();
