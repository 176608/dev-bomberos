<?php
require_once 'conexion.php';


function obtenerTemas() {
    global $conexion;
    $resultado = $conexion->query("SELECT id, nombre FROM tema ORDER BY nombre ASC");
    return $resultado;
}
