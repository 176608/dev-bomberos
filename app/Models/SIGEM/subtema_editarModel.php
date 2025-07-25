<?php
include 'conexion.php';

function obtenerSubtemaPorId($id) {
    global $conexion;
    $stmt = $conexion->prepare("SELECT * FROM consulta_express_subtema WHERE ce_subtema_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function obtenerTemas() {
    global $conexion;
    return $conexion->query("SELECT ce_tema_id, tema FROM consulta_express_tema");
}

function actualizarSubtema($nombre, $tema_id, $id) {
    global $conexion;
    $stmt = $conexion->prepare("UPDATE consulta_express_subtema SET ce_subtema = ?, ce_tema_id = ? WHERE ce_subtema_id = ?");
    $stmt->bind_param("sii", $nombre, $tema_id, $id);
    return $stmt->execute();
}
