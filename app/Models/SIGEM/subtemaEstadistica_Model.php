<?php

require_once 'conexion.php';

function obtenerSubtemas() {
    global $conexion;
    $resultado = $conexion->query("SELECT id, nombre_subtema, tema FROM subtemas ORDER BY tema ASC");
    return $resultado;
}