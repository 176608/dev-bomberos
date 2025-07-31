<?php
// Vista Pública
// Muestra temas geográficos y subtemas disponibles.
// No permite acciones de modificación ni funciones exclusivas de administrador.
?>
<?php

    include '../controllers/geograficoController.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($temaSeleccionado) ?> - SIGEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eeeeee;
            font-family: Arial, sans-serif;
        }
        .top-bar {
            background-color: #2a6e48;
            color: white;
            padding: 5px 15px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar .right-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .top-bar .user-icon {
            width: 28px;
            height: 28px;
            background-color: white;
            color: #2a6e48;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-logos {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 10px 0 10px 10px;
        }
        .header-logos img:first-child { height: 85px; }
        .header-logos img:last-child { height: 65px; }
        .main-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin: 0 auto;
            width: 90%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .footer-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            gap: 60px;
            border-top: 1px solid #ccc;
        }
        .footer-logos img:first-child { height: 70px; }
        .footer-logos img:nth-child(2) { height: 90px; }
        .footer-logos img:nth-child(3) { height: 80px; }
        .titulo-cuadro {
            font-weight: bold;
            color: #006633;
            margin-top: 5px;
            font-size: 14px;
        }
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
        <div><strong><?= htmlspecialchars($usuario) ?></strong></div>
        <?php if ($usuario !== 'Público'): ?>
            <a href="logout.php" class="btn btn-sm btn-light text-success">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-sm btn-light text-success">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</div>

<div class="header-logos">
    <img src="imagenes/sige1.png" alt="IMIP Logo">
    <img src="imagenes/sige2.png" alt="SIGEM Logo">
</div>

<?php include 'menuprincipal.php'; ?>

<div class="main-card">
    <h4 class="text-center text-success mb-4">Selecciona un tema:</h4>
    <div class="text-center mb-4">


        <select id="temaSelect" class="form-select w-75 mx-auto">
            <?php
            if ($temas && mysqli_num_rows($temas) > 0) {
                while ($fila = mysqli_fetch_assoc($temas)) {
                    $selected = ($fila['nombre_archivo'] === 'geografico.php') ? 'selected' : '';
                    echo "<option value=\"{$fila['nombre_archivo']}\" $selected>{$fila['nombre']}</option>";
                }
            } else {
                echo '<option value="">No hay temas disponibles</option>';
            }
            ?>
        </select>

    </div>

    <script>
        document.getElementById("temaSelect").addEventListener("change", function () {
            const destino = this.value;
            if (destino) {
                window.location.href = destino;
            }
        });
    </script>

    <div class="row">
        <?php foreach ($items as $item): ?>
            <div class="col-md-6 mb-4">
                <a href="<?= htmlspecialchars($item['link']) ?>" class="btn btn-success d-flex align-items-center justify-content-start w-100" style="height: 60px; font-size: 18px;">
                    <img src="imagenes/<?= htmlspecialchars($item['icono_path']) ?>" alt="icono" style="width: 30px; height: 30px; margin-right: 15px;">
                    <?= htmlspecialchars($item['nombre_item']) ?>
                </a>
                <?php
                    $clave = $item['nombre_item'];
                    if (isset($titulos[$clave])) {
                        echo "<ul class='mt-2'>";
                        foreach ($titulos[$clave] as $titulo) {
                            echo "<li class='titulo-cuadro'>$titulo</li>";
                        }
                        echo "</ul>";
                    }
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

</body>
</html>
