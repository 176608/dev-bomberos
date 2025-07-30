<?php
// Este archivo contiene una función para obtener los cuadros estadísticos desde la base de datos
// No tiene restricciones de acceso, por lo tanto puede ser considerado de uso público
// VISTA PÚBLICA

require_once 'conexion.php'; // Conexión a la base de datos

/**
 * Función que obtiene todos los cuadros estadísticos junto con su tema asociado.
 * - Devuelve un arreglo con: ID del tema, nombre del tema, código del cuadro, título del cuadro
 * - Ordena primero por tema y luego por código del cuadro
 *
 * @return array
 */
function obtenerCuadros() {
    global $conexion;

    // Consulta SQL que une las tablas cuadro_estadistico y tema
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

    // Devuelve todos los resultados como arreglo numérico:
    // [0] => tema_id, [1] => nombre tema, [2] => código del cuadro, [3] => título del cuadro
    return $resultado->fetch_all(MYSQLI_NUM);
}
