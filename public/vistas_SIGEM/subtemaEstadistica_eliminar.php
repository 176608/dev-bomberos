<?php
include '../models/subtemaEstadistica_eliminarModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
    eliminarSubtemaPorId($id);
}

header("Location: subtemaEstadistica.php");
exit;
