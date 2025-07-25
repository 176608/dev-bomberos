<?php
include 'conexion.php';

function eliminarTemaPorId($id) {
    global $conexion;
    if ($id) {
        $conexion->query("DELETE FROM tema WHERE id = $id");
    }
}
