<?php 
// =============================================================
// VISTA PÚBLICA - PROCESO DE LOGIN
// =============================================================
// Este script recibe las credenciales desde el formulario de login,
// valida al usuario contra la base de datos mediante el modelo
// 'validar_loginModel.php', y si son correctas, inicia sesión
// y redirige al panel principal del sistema (index.php).
// En caso contrario, muestra un mensaje de error y regresa al login.

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


