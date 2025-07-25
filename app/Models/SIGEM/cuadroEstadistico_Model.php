<?php

require_once 'conexion.php';

function obtenerCuadros() {
    global $conexion;

    $query = "
        SELECT 
            ce.cuadro_estadistico_id,
            ce.subtema_id,
            ce.tema_id,
            ce.codigo_cuadro,
            ce.cuadro_estadistico_titulo,
            t.id AS tema_id,
            t.nombre AS nombre_tema,
            s.id AS subtema_id,
            s.nombre_subtema
        FROM cuadro_estadistico ce
        LEFT JOIN tema t ON ce.tema_id = t.id
        LEFT JOIN subtemas s ON ce.subtema_id = s.id
        ORDER BY ce.codigo_cuadro ASC
    ";

    return $conexion->query($query);
}

/*

De cuadro_estadistico quiero cuadro_estadistico_id, subtema_id, tema_id, codigo_cuadrado, cuadrado_estadistico_titutlo

De tema quiero id, nombre.

De subtemas quiero id, nombre_subtema*/