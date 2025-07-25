<?php
include 'conexion.php';

function obtener_tema_por_id($id) {
    global $conexion;
    $id = intval($id);
    $resultado = $conexion->query("SELECT nombre FROM tema WHERE id = $id");
    return $resultado->fetch_assoc();
}

function actualizar_tema($id, $nuevo_tema) {
    global $conexion;
    $id = intval($id);
    $nuevo = $conexion->real_escape_string($nuevo_tema);
    return $conexion->query("UPDATE tema SET nombre = '$nuevo' WHERE id = $id");
}
?>