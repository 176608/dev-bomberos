<?php

include 'conexion.php';


// models/modelo_subtema.php
function obtenerTemas($conexion) {
    return $conexion->query("SELECT id, nombre FROM tema");
}
function obtenerSubtemas($conexion) {
    return $conexion->query("SELECT id, nombre_subtema, tema FROM subtemas");
}

function insertar_cuadro_estadistico($conexion, $codigo_cuadro, $nombre_cuadro, $tema_id, $subtema_id) {
    $sql = "
        INSERT INTO cuadro_estadistico (codigo_cuadro, cuadro_estadistico_titulo, tema_id, subtema_id)
        VALUES (?, ?, ?, ?)
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssii", $codigo_cuadro, $nombre_cuadro, $tema_id, $subtema_id);
    $resultado = $stmt->execute();
    $stmt->close();
    return $resultado;
}
