<?php
// models/modelo_subtema.php
function obtenerTemas($conexion) {
    return $conexion->query("SELECT ce_tema_id, tema FROM consulta_express_tema");
}

function insertarSubtema($conexion, $nombre, $tema_id) {
    $stmt = $conexion->prepare("INSERT INTO consulta_express_subtema (ce_subtema, ce_tema_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $tema_id);
    $stmt->execute();
}
