<?php
require_once 'conexion.php';


function obtenerCuadros() {
    global $conexion;
    $sql = "
        SELECT 
            t.id AS tema_id,
            t.nombre AS tema,
            c.codigo_cuadro,
            c.cuadro_estadistico_titulo
        FROM cuadro_estadistico c
        INNER JOIN tema t ON c.tema_id = t.id
        ORDER BY t.id ASC, c.codigo_cuadro ASC
    ";
    $resultado = $conexion->query($sql);
    return $resultado->fetch_all(MYSQLI_NUM);  // [0] => tema_id, [1] => nombre tema, [2] => código, [3] => título
}

