
<?php
// ⚙️ Vista privada de administración.
// Este archivo muestra el panel administrativo con accesos rápidos a módulos de gestión (temas, subtemas, cuadros).
// Solo debe estar disponible para el usuario 'admin'.
// Debe estar protegido por validación de sesión en el controlador o directamente aquí.
include '../controllers/dashboardController.php';
?>
<?php
 include '../controllers/dashboardController.php'
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title> Dashboard - SIGEM</title>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #eeeeee; font-family: Arial, sans-serif; }
        .top-bar {
            background-color: #2a6e48; color: white; padding: 5px 15px; font-size: 14px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .top-bar .right-section {
            display: flex; align-items: center; gap: 15px;
        }
        .top-bar .user-icon {
            width: 28px; height: 28px; background-color: white; color: #2a6e48;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }
        .header-logos {
            display: flex; align-items: center; padding: 10px 30px; gap: 20px;
        }
        .header-logos img:first-child { height: 85px; }
        .header-logos img:last-child { height: 65px; margin-left: 0; margin-right: auto; }
        .footer-logos {
            display: flex; justify-content: center; align-items: center; gap: 50px; padding: 20px 0; border-top: 1px solid #ccc;
        }
        .footer-logos img { height: 70px; }
        .main-card {
            background-color: white; border-radius: 8px; padding: 30px; margin: 20px auto; width: 90%; max-width: 850px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { color: #2a6e48; text-align: center; }
        .mensaje { color: red; text-align: center; font-weight: bold; margin-top: 15px; }
    </style>
</head>
<body>

<div class="top-bar">
    <div>Instituto Municipal de Investigación y Planeación</div>
    <div class="right-section">
        <div>Ciudad Juárez, Chihuahua</div>
        <div class="user-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#2a6e48" class="bi bi-person" viewBox="0 0 16 16">
                <path d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-6a6 6 0 0 0-4.472 10.118C4.723 11.29 6.299 11 8 11s3.277.29 4.472.618A6 6 0 0 0 8 2z"/>
            </svg>
        </div>
        <strong><?= htmlspecialchars($usuario) ?></strong>
        <a href="<?= $usuario !== 'Público' ? 'logout.php' : 'login.php' ?>" class="btn btn-sm btn-light text-success">
            <?= $usuario !== 'Público' ? 'Cerrar sesión' : 'Iniciar sesión' ?>
        </a>
    </div>
</div>

<div class="header-logos">
    <img src="/imagenes/sige1.png" alt="Logo IMIP">
    <img src="/imagenes/sige2.png" alt="Logo SIGEM">
</div>

<?php include 'menuprincipal.php'; ?>

<div class="main-card">
<div class="mb-3 text-start">
    <a href="index.php" class="btn btn-outline-success">← Regresar a Inicio</a>
</div>
    <h2>Bienvenido al Panel de Administración, <?= htmlspecialchars($usuario) ?> 👋</h2>

<div class="table-responsive mt-4">
<div class="table-responsive mt-4">
<div class="table-responsive mt-4">
    <table class="table table-bordered text-center align-middle">
        <thead class="table-success">
            <tr>
                <th colspan="2">Panel de Acceso Rápido</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fila 1 -->
            <tr>
                <td>Temas Generales</td>
                <td>
                    <a href="tema.php" class="btn btn-outline-success btn-sm w-100">
                        Ver Temas
                    </a>
                </td>
            </tr>

            <!-- Fila 2 -->
            <tr>
                <td>Subtemas Generales</td>
                <td>
                    <a href="subtema.php" class="btn btn-outline-success btn-sm w-100">
                        Ver Subtemas
                    </a>
                </td>
            </tr>

            <!-- Fila 3 -->
            <tr>
                <td>Temas Estadísticos</td>
                <td>
                    <a href="temaEstadistica.php" class="btn btn-outline-success btn-sm w-100">
                        Ver Temas Estadísticos
                    </a>
                </td>
            </tr>

            <!-- Fila 4 -->
            <tr>
                <td>Subtemas Estadísticos</td>
                <td>
                    <a href="subtemaEstadistica.php" class="btn btn-outline-success btn-sm w-100">
                        Ver Subtemas Estadísticos
                    </a>
                </td>
            </tr>
                        <!-- Fila 5 -->
            <tr>
                <td>Cuadros Estadísticos</td>
                <td>
                    <a href="cuadroEstadistico.php" class="btn btn-outline-success btn-sm w-100">
                        Ver Cuadros Estadísticos
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>

</div>
</div>

</div>

<div class="footer-logos">
    <img src="imagenes/sige2.png" alt="Logo SIGEM Footer">
    <img src="imagenes/logosfinales2.png" alt="Logo IMIP Footer">
    <img src="imagenes/logoadmin.png" alt="Logo Ciudad Juárez Footer">
</div>


</body>
</html>
