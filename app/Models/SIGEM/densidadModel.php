<?php
require_once 'conexion.php';

function obtenerCuadrosDensidad($codigo_like = '1.DP.%') {
    global $conexion;
    $sql = "
        SELECT 
            ce.cuadro_estadistico_id,
            codigo_cuadro,
            ce.cuadro_estadistico_titulo,
            ce.pdf_file,
            ce.permite_grafica,
            cec.nombe_archivo_csv,
            cec.secuencia
        FROM cuadro_estadistico ce
        JOIN cuadro_estadistico_csv cec 
            ON ce.cuadro_estadistico_id = cec.cuadro_estadistico_id
        WHERE ce.codigo_cuadro LIKE ?
        ORDER BY ce.codigo_cuadro ASC, cec.secuencia ASC
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('s', $codigo_like);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $cuadros = $resultado->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $cuadros;
}
