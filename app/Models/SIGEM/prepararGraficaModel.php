<?php

function obtenerCuadro($conexion, $id) {
    $stmt = $conexion->prepare("SELECT * FROM cuadro_estadistico WHERE cuadro_estadistico_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function obtenerCSVs($conexion, $id) {
    $stmt = $conexion->prepare("SELECT * FROM cuadro_estadistico_csv WHERE cuadro_estadistico_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}
