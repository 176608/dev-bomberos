<?php
$conexion = new mysqli("localhost", "root", "", "sigem_replica");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
