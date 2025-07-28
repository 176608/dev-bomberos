<?php
// Este script gestiona una vista pública del sistema SIGEM.
// Redirige a usuarios autenticados que intenten acceder al login nuevamente.

session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit();
}
