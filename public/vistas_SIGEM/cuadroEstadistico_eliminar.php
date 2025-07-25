<?php
include '../models/cuadroEstadistico_eliminarModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
    eliminarSubtemaPorId($id);
}

header("Location: cuadroEstadistico.php");
exit;
