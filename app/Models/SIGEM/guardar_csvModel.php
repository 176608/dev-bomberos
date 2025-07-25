<?php

function guardarNombreCsv($conexion, $cuadro_id, $nombre_csv) {
    // Primero verificamos si ya existe un registro para este cuadro_id
    $sqlCheck = "SELECT COUNT(*) FROM cuadro_estadistico_csv WHERE cuadro_estadistico_id = ?";
    $stmtCheck = $conexion->prepare($sqlCheck);
    if (!$stmtCheck) {
        return false;
    }
    $stmtCheck->bind_param("i", $cuadro_id);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        // Actualizar el registro existente
        $sqlUpdate = "UPDATE cuadro_estadistico_csv SET nombe_archivo_csv = ? WHERE cuadro_estadistico_id = ?";
        $stmtUpdate = $conexion->prepare($sqlUpdate);
        if (!$stmtUpdate) {
            return false;
        }
        $stmtUpdate->bind_param("si", $nombre_csv, $cuadro_id);
        $result = $stmtUpdate->execute();
        $stmtUpdate->close();
        return $result;
    } 
    else {
        // Insertar nuevo registro
        $sqlInsert = "INSERT INTO cuadro_estadistico_csv (cuadro_estadistico_id, nombe_archivo_csv) VALUES (?, ?)";
        $stmtInsert = $conexion->prepare($sqlInsert);
        if (!$stmtInsert) {
            return false;
        }
        $stmtInsert->bind_param("is", $cuadro_id, $nombre_csv);
        $result = $stmtInsert->execute();
        $stmtInsert->close();
        return $result;
    }
}

function obtenerNombreCsvActual($conexion, $cuadro_id) {
    $sql = "SELECT nombe_archivo_csv FROM cuadro_estadistico_csv WHERE cuadro_estadistico_id = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("i", $cuadro_id);
    $stmt->execute();
    $stmt->bind_result($nombre_csv);
    $stmt->fetch();
    $stmt->close();
    return $nombre_csv;
}
