<?php

function guardarNombrePdf($conexion, $cuadro_id, $nombre_pdf) {
    $sqlCheck = "SELECT COUNT(*) FROM cuadro_estadistico WHERE cuadro_estadistico_id = ?";
    $stmtCheck = $conexion->prepare($sqlCheck);
    if (!$stmtCheck) return false;

    $stmtCheck->bind_param("i", $cuadro_id);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        $sqlUpdate = "UPDATE cuadro_estadistico SET pdf_file = ? WHERE cuadro_estadistico_id = ?";
        $stmtUpdate = $conexion->prepare($sqlUpdate);
        if (!$stmtUpdate) return false;

        $stmtUpdate->bind_param("si", $nombre_pdf, $cuadro_id);
        $result = $stmtUpdate->execute();
        $stmtUpdate->close();
        return $result;
    } else {
        $sqlInsert = "INSERT INTO cuadro_estadistico (cuadro_estadistico_id, pdf_file) VALUES (?, ?)";
        $stmtInsert = $conexion->prepare($sqlInsert);
        if (!$stmtInsert) return false;

        $stmtInsert->bind_param("is", $cuadro_id, $nombre_pdf);
        $result = $stmtInsert->execute();
        $stmtInsert->close();
        return $result;
    }
}

function obtenerNombrePdfActual($conexion, $cuadro_id) {
    $sql = "SELECT pdf_file FROM cuadro_estadistico WHERE cuadro_estadistico_id = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) return null;

    $stmt->bind_param("i", $cuadro_id);
    $stmt->execute();
    $stmt->bind_result($nombre_pdf);
    $stmt->fetch();
    $stmt->close();
    return $nombre_pdf;
}
