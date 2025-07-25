<?php
include 'conexion.php';

function eliminarSubtemaPorId($id) {
    global $conexion;

    $stmt = $conexion->prepare("DELETE FROM cuadro_estadistico WHERE cuadro_estadistico_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
