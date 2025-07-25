<?php
// models/modelo_subtema.php
function obtenerTemas($conexion) {
    return $conexion->query("SELECT id, nombre FROM tema");
}

function insertarSubtema($conexion, $subtema, $tema, $nombreImagen = null) {
    $stmt = $conexion->prepare("INSERT INTO subtemas (nombre_subtema, tema, imagen) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $subtema, $tema, $nombreImagen);
    $stmt->execute();
}

