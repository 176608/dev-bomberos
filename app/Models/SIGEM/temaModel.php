<?php
require_once 'conexion.php';


function obtenerTemas() {
    global $conexion;
    $resultado = $conexion->query("SELECT ce_tema_id, tema FROM consulta_express_tema ORDER BY tema ASC");
    return $resultado;
}
