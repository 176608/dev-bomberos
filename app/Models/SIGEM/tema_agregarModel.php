<?php
include 'conexion.php';

function insertar_tema($nombre) {
    global $conexion;
    $nombre = $conexion->real_escape_string($nombre);
    $query = "INSERT INTO consulta_express_tema (tema) VALUES ('$nombre')";
    return $conexion->query($query);
}
?>
