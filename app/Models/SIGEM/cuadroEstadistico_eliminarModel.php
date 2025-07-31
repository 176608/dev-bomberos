<?php
// REQUIERE SESIÓN ADMIN — Este archivo permite eliminar cuadros estadísticos,
// por lo tanto debe estar restringido exclusivamente a usuarios administradores.

include 'conexion.php';

function eliminarSubtemaPorId($id) {
    global $conexion;

    $stmt = $conexion->prepare("DELETE FROM cuadro_estadistico WHERE cuadro_estadistico_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
