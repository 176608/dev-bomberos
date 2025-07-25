<?php
require_once 'conexion.php';

function obtenerItemsGeograficos() {
    global $conexion;
    $items = [];
    $sql = "SELECT * FROM geografico_items";
    $resultado = $conexion->query($sql);
    if ($resultado) {
        while ($row = $resultado->fetch_assoc()) {
            $items[] = $row;
        }
    }
    return $items;
}

function obtenerTemas($conexion) {
    $query = "SELECT nombre, nombre_archivo FROM tema ORDER BY id ASC";
    $resultado = mysqli_query($conexion, $query);
    return $resultado;
}