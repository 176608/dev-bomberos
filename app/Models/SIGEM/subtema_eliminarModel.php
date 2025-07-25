<?php
include 'conexion.php';

function eliminarSubtemaPorId($id) {
    global $conexion;

    $stmt = $conexion->prepare("DELETE FROM consulta_express_subtema WHERE ce_subtema_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
