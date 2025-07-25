<?php
include 'conexion.php';



function obtenerCuadroPorId($conexion, $id) {
    $stmt = $conexion->prepare("SELECT * FROM cuadro_estadistico WHERE cuadro_estadistico_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function obtenerTemas($conexion) {
    return $conexion->query("SELECT id, nombre FROM tema");
}
function obtenerSubtemas($conexion) {
    return $conexion->query("SELECT id, nombre_subtema, tema FROM subtemas");
}

function actualizarCuadroEstadistico($conexion, $id, $codigo, $titulo, $tema_id, $subtema_id) {
    $sql = "UPDATE cuadro_estadistico 
            SET codigo_cuadro = ?, cuadro_estadistico_titulo = ?, tema_id = ?, subtema_id = ? 
            WHERE cuadro_estadistico_id = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ssiii", $codigo, $titulo, $tema_id, $subtema_id, $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}
