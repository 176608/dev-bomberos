<?php
// VISTA PÚBLICA — Este archivo contiene funciones que obtienen cuadros, temas y subtemas 
// para ser mostrados en secciones visibles al público (como módulos temáticos del SIGEM).
// No realiza inserciones ni eliminaciones en la base de datos.

require_once 'conexion.php';

function obtenerCuadrosPorSubtema($subtema_id) {
    global $conexion;

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
ORDER BY 
    CAST(SUBSTRING_INDEX(ce.codigo_cuadro, '.', 1) AS UNSIGNED), -- Primer número
    SUBSTRING_INDEX(SUBSTRING_INDEX(ce.codigo_cuadro, '.', 2), '.', -1), -- Parte alfabética (ECO)
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(ce.codigo_cuadro, '.', -2), '.', 1) AS UNSIGNED), -- Número principal
    CAST(SUBSTRING_INDEX(ce.codigo_cuadro, '.', -1) AS UNSIGNED); -- Decimal final, si existe
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $subtema_id);
    $stmt->execute();
    return $stmt->get_result();
}

function obtenerSubtemasOrdenadosPorCuadro($conexion, $temaNombre) {
    $sql = "
SELECT 
    s.id, 
    s.nombre_subtema, 
    s.imagen,
    MIN(c.codigo_cuadro) AS primer_codigo
FROM subtemas s
INNER JOIN cuadro_estadistico c ON c.subtema_id = s.id
WHERE s.tema = ?
GROUP BY s.id, s.nombre_subtema, s.imagen
ORDER BY
    CAST(SUBSTRING_INDEX(c.codigo_cuadro, '.', 1) AS UNSIGNED),            -- Parte numérica inicial
    SUBSTRING_INDEX(SUBSTRING_INDEX(c.codigo_cuadro, '.', -2), '.', 1),    -- Parte del medio (texto)
    CAST(SUBSTRING_INDEX(c.codigo_cuadro, '.', -1) AS UNSIGNED)            -- Parte final numérica
    ";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $temaNombre);
    $stmt->execute();
    return $stmt->get_result();
}

function obtenerTemas($conexion) {
    $query = "SELECT nombre, nombre_archivo FROM tema ORDER BY id ASC";
    $resultado = mysqli_query($conexion, $query);
    return $resultado;
}

function obtenerSubtemasPorTema($conexion, $temaNombre) {
    $query = "SELECT id, nombre_subtema, imagen FROM subtemas WHERE tema = ? ORDER BY nombre_subtema ASC";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $temaNombre);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return $resultado;
}
