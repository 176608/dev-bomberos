<?php

//Forman parte de la lógica de control del flujo de usuario (autenticado o no).

session_start();
$usuario = $_SESSION['usuario'] ?? 'Público';
?>