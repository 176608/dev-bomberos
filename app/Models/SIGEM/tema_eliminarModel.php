<?php
include 'conexion.php';

function eliminarTemaPorId($id) {
    global $conexion;
    if ($id) {
        $conexion->query("DELETE FROM consulta_express_tema WHERE ce_tema_id = $id");
    }
}
