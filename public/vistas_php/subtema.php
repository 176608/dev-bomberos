<?php

include '../controllers/subtemaController.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Subtemas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

<!-- Contenido -->
<div class="container mt-4">
    <h2 class="mb-3">Listado de Subtemas</h2>

    <a href="subtema_agregar.php" class="btn btn-success mb-3">Nuevo Subtema</a>

    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Nombre del Subtema</th>
                <th>Tema Relacionado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $subtemas->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ce_subtema_id'] ?></td>
                    <td><?= htmlspecialchars($row['ce_subtema']) ?></td>
                    <td><?= htmlspecialchars($row['tema']) ?></td>
                    <td>
                        <a href="subtema_editar.php?id=<?= $row['ce_subtema_id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="subtema_eliminar.php?id=<?= $row['ce_subtema_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este subtema?')">Eliminar</a>
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

</body>
</html>
