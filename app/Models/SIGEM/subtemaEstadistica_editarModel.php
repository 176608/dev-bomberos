<?php
include 'conexion.php';

function obtenerSubtemaPorId($id) {
    global $conexion;
    $stmt = $conexion->prepare("SELECT id, nombre_subtema FROM subtemas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function obtenerTemas() {
    global $conexion;
    return $conexion->query("SELECT id, nombre FROM tema");
}

function actualizarSubtema($subteam, $tema, $id) {
    global $conexion;
    $stmt = $conexion->prepare("UPDATE subtemas SET nombre_subtema = ?, tema = ? WHERE id = ?");
    $stmt->bind_param("ssi", $subteam, $tema, $id);
    return $stmt->execute();
}
