<?php
// Este script controla la sesión del usuario en el sistema SIGEM.
// Es utilizado en vistas públicas y restringidas para identificar al usuario actual.

//Forman parte de la lógica de control del flujo de usuario (autenticado o no).

session_start();
$usuario = $_SESSION['usuario'] ?? 'Público';
?>
