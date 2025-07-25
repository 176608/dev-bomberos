<?php

include '../models/tema_eliminarModel.php';


$id = $_GET['id'] ?? null;

if ($id) {
eliminarTemaPorId($id);
}

header("Location: tema.php");
exit();
?>