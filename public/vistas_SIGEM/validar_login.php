<?php
session_start();

require_once '../models/validar_loginModel.php';

    // Obtener usuario y contraseña del formulario
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';



if (verificarUsuario($usuario, $password)) {
        // Login correcto
        $_SESSION['usuario'] = $usuario;
        header('Location: index.php');
        exit();
    } else {
        // Login fallido
        echo "<script>alert('Usuario o contraseña incorrectos'); window.location='login.php';</script>";
    }
?>


