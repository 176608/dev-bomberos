<?php

include '../models/subtemaEstadistica_Model.php';

$subtemas = obtenerSubtemas();

session_start();


$usuario = $_SESSION['usuario'] ?? 'Público';

if ($usuario !== 'admin') {
    header('Location: index.php');
    exit;
}   

