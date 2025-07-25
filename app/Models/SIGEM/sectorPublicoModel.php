<?php 
function obtenerCuadrosPorSubtema($conexion, $subtema_id) {
    $sql = "
        SELECT ce.*, (
            SELECT cec.nombe_archivo_csv 
            FROM cuadro_estadistico_csv cec 
            WHERE cec.cuadro_estadistico_id = ce.cuadro_estadistico_id 
            ORDER BY cec.cuadro_estadistico_csv_id DESC 
            LIMIT 1
        ) AS nombe_archivo_csv 
        FROM cuadro_estadistico ce 
        WHERE ce.subtema_id = ?
                ORDER BY ce.codigo_cuadro ASC
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $subtema_id);
    $stmt->execute();
    return $stmt->get_result();
}
function obtenerTemas($conexion) {
    $query = "SELECT nombre, nombre_archivo FROM tema ORDER BY id ASC";
    $resultado = mysqli_query($conexion, $query);
    return $resultado;
}

function obtenerSubtemasPorTema($conexion, $temaNombre) {
    $query = "SELECT id, nombre_subtema, imagen FROM subtemas WHERE tema = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $temaNombre);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return $resultado;
}