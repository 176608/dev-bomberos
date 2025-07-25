<?php

include 'conexion.php';

function obtenerCuadrosPorSubtema($subtema_id)
{
    global $conexion;

    $query = "
        SELECT
            ce.cuadro_estadistico_id,
            ce.codigo_cuadro,
            ce.cuadro_estadistico_titulo,
            ce.pdf_file,
            ce.permite_grafica,
            (
                SELECT cec2.nombe_archivo_csv 
                FROM cuadro_estadistico_csv cec2 
                WHERE cec2.cuadro_estadistico_id = ce.cuadro_estadistico_id 
                ORDER BY cec2.cuadro_estadistico_csv_id DESC 
                LIMIT 1
            ) AS nombe_archivo_csv
        FROM cuadro_estadistico ce
        WHERE ce.subtema_id = ?
        ORDER BY ce.codigo_cuadro ASC
";

    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $subtema_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}




function obtenerTemas($conexion) {
        global $conexion;

    $query = "SELECT nombre, nombre_archivo FROM tema ORDER BY id ASC";
    $resultado = mysqli_query($conexion, $query);
    return $resultado;
}

function obtenerSubtemasPorTema($temaNombre) {
        global $conexion;

    $query = "SELECT id, nombre_subtema, imagen FROM subtemas WHERE tema = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $temaNombre);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return $resultado;
}


?>
