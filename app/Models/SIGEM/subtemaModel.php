<?php

require_once 'conexion.php';

function obtenerSubtemas() {
    global $conexion;

    $query = "
        SELECT s.ce_subtema_id, s.ce_subtema, t.tema 
        FROM consulta_express_subtema s
        LEFT JOIN consulta_express_tema t ON s.ce_tema_id = t.ce_tema_id
    ";

    return $conexion->query($query);
}
