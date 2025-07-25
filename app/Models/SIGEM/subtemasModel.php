<?php
require_once 'conexion.php';

function obtenerSubtemasPorTema($tema_id) {
    global $conexion;
    $tema_id = intval($tema_id);
    return $conexion->query("SELECT * FROM subtema WHERE tema_id = $tema_id");
}
