<?php
// =============================================================
// Vista de ADMINISTRADOR - Listado de Temas de Estadística
// =============================================================
// Esta vista permite al administrador visualizar, agregar, editar
// y eliminar temas relacionados con la sección de Estadística.
// Se conecta con el controlador correspondiente para obtener
// los datos desde la base de datos.

include '../controllers/temaEstadistica_Controller.php';

include '../controllers/temaEstadistica_Controller.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Temas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        .top-bar {
            background-color: #2a6e48;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-bar {
            background-color: white;
            text-align: center;
            padding: 10px;
        }
        .logo-bar img {
            max-height: 90px;
            margin: 0 10px;
        }
        .main-nav {
            background-color: #2a6e48;
            padding: 8px 0;
            display: flex;
            justify-content: center;
            gap: 20px;
            border-bottom: 4px solid #ffd700;
        }
        .main-nav a {
            color: white;
            text-decoration: none;
            padding: 6px 14px;
            font-weight: bold;
        }
        .main-nav a:hover {
            background-color: rgba(255,255,255,0.2);
            border-radius: 4px;
        }
        .footer-logos {
            text-align: center;
            margin-top: 40px;
        }
        .footer-logos img {
            max-height: 60px;
            margin: 10px 25px;
        }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <div class="top-bar">
        <div>Instituto Municipal de Investigación y Planeación</div>
        <div>Ciudad Juárez, Chihuahua</div>
    </div>

    <div class="logo-bar">
        <img src="imagenes/sige1.png" alt="IMIP">
        <img src="imagenes/sige2.png" alt="SIGEM">
    </div>

   
<!-- Menú incluido -->
<?php include 'menuprincipal.php'; ?>
<div class="main-card"></div>

    <!-- Contenido principal -->
    <div class="container mt-4">
        <h2 class="mb-3">Listado de Temas de Estadística</h2>

        <a href="temaEstadistica_agregar.php" class="btn btn-success mb-3">Nuevo Tema</a>

        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>ID</th>
                    <th>Nombre del Tema</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $temas->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td>
                        <a href="temaEstadistica_editar.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="temaEstadistica_eliminar.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar este tema?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Pie de página -->
    <div class="footer-logos">
        <img src="imagenes/logosfinales2.png" alt="IMIP">
        <img src="imagenes/logoadmin.png" alt="Gobierno Municipal">
        <img src="imagenes/sige2.png" alt="SIGEM">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
