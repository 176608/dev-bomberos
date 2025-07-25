<?php 

function obtenerCuadrosMA($conexion, $codigo_like = '2.MA.%') {
    $sql = "
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
        WHERE ce.codigo_cuadro LIKE ?
        ORDER BY ce.codigo_cuadro ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $codigo_like);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $cuadros = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $cuadros;
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