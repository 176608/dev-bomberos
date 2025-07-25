<?php

include '../models/cuadroEstadistico_Model.php';

$cuadros = obtenerCuadros();

session_start();


$usuario = $_SESSION['usuario'] ?? 'Público';

if ($usuario !== 'admin') {
    header('Location: index.php');
    exit;
}   