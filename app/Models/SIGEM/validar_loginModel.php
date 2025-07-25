<?php
include_once 'conexion.php'; 

function verificarUsuario($usuario, $password) {
    global $conexion;

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE usuario = ? AND password = ?");
    if (!$stmt) return false;

    $stmt->bind_param("ss", $usuario, $password);
    $stmt->execute();
    $stmt->store_result();

    return $stmt->num_rows > 0;
}
