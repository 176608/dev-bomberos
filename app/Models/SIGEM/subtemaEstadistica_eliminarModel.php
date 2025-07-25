<?php
include 'conexion.php';

function eliminarSubtemaPorId($id) {
    global $conexion;

    $stmt = $conexion->prepare("DELETE FROM subtemas WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
