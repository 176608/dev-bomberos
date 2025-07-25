<?php

include 'conexion.php';

function obtenerNombreArchivoCsv($cuadro_id) {
    global $conexion;
    $sql = "SELECT nombe_archivo_csv FROM cuadro_estadistico_csv WHERE cuadro_estadistico_id = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return false; // Error al preparar la consulta
    }
    $stmt->bind_param("i", $cuadro_id);
    $stmt->execute();
    $stmt->bind_result($nombreArchivoCsv);
    if ($stmt->fetch()) {
        $stmt->close();
        return $nombreArchivoCsv;
    } else {
        $stmt->close();
        return null; // No existe un registro con ese cuadro_id
    }
}

function obtenerDatosCuadro($cuadro_id) {

    global $conexion;
    $sql = "SELECT codigo_cuadro, cuadro_estadistico_titulo FROM cuadro_estadistico WHERE cuadro_estadistico_id = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return false; // Error en la preparación
    }
    $stmt->bind_param("i", $cuadro_id);
    $stmt->execute();
    $stmt->bind_result($codigoCuadro, $tituloCuadro);
    if ($stmt->fetch()) {
        $stmt->close();
        return [
            'codigo_cuadro' => $codigoCuadro,
            'titulo' => $tituloCuadro
        ];
    } else {
        $stmt->close();
        return null; // No encontrado
    }
}