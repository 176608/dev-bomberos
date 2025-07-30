<?php
// 🔐 Vista de acceso (login): permite autenticación pero no muestra ni administra contenido
?>
<?php
include '../controllers/loginController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - SIGEM</title>
    <link rel="icon" type="image/png" href="imagenes/imiplogo_200x200_trans.png">
    <style>
        body {
            background-color: #d6d6d6;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .top-bar {
            background-color: #2a6e48;
            color: white;
            padding: 5px 20px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-container {
            background-color: white;
            padding: 10px;
            display: flex;
            align-items: center;
            position: relative;
        }
        .logo-container img {
            height: 80px;
            margin-right: 20px;
        }
        .sidebar-links {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            text-align: right;
        }
        .sidebar-links a {
            display: block;
            text-decoration: none;
            color: #2a6e48;
            background-color: #d9e3da;
            padding: 4px 10px;
            margin-bottom: 5px;
            font-size: 13px;
            border-radius: 4px;
            font-weight: bold;
        }
        .sidebar-links a:hover {
            background-color: #bfd5c1;
        }
        .login-container {
            background-color: #297b7b;
            padding: 20px;
            text-align: center;
        }
        .login-form {
            background-color: white;
            width: 600px;
            margin: 40px auto;
            padding: 40px;
            text-align: center;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 250px;
            padding: 10px;
            margin: 10px;
            font-size: 16px;
        }
        .login-form button {
            background-color: #2a6e48;
            border: none;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 6px;
        }
        .footer-logos {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 40px;
            align-items: center;
        }
        .footer-logos img {
            height: 80px;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div>Instituto Municipal de Investigación y Planeación</div>
        <div>Ciudad Juárez, Chihuahua</div>
    </div>

    <div class="logo-container">
        <img src="imagenes/sige1.png" alt="IMIP Logo">
        <img src="imagenes/sige2.png" alt="SIGEM Logo">

        <div class="sidebar-links">
            <a href="https://www.imip.org.mx/imip/" target="_blank">www.imip.org.mx</a>
            <a href="https://www.imip.org.mx/imip/contacto" target="_blank">Contacto</a>
        </div>
    </div>

    <div class="login-container">
        <div class="login-form">
            <form action="validar_login.php" method="POST">
                <div>
                    <label>Usuario:</label><br>
                    <input type="text" name="usuario" required>
                </div>
                <div>
                    <label>Contraseña:</label><br>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">Ingresar</button>
            </form>
        </div>
    </div>

    <div class="footer-logos">
        <img src="imagenes/sige2.png" alt="SIGEM Logo Footer">
        <img src="imagenes/logosfinales2.png" alt="IMIP Logo Footer">
        <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Logo Footer">
    </div>

</body>
</html>
