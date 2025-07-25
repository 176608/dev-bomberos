<?php
include '../models/subtema_eliminarModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
    eliminarSubtemaPorId($id);
}

header("Location: subtema.php");
exit;
