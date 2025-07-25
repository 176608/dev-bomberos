<?php

include '../models/temaEstadistica_eliminarModel.php';


$id = $_GET['id'] ?? null;

if ($id) {
eliminarTemaPorId($id);
}

header("Location: temaEstadistica.php");
exit();
?>