<?php
session_start();
include '../models/conexion.php';
include '../models/subtema_agregarModel.php';

$temas = obtenerTemas($conexion);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["ce_subtema"];
    $tema_id = $_POST["ce_tema_id"];

    insertarSubtema($conexion, $nombre, $tema_id);

    header("Location: subtema.php");
    exit;
}
