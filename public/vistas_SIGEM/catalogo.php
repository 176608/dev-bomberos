<?php 
    // Vista pública con detección de sesión para mostrar nombre de usuario o enlace a iniciar/cerrar sesión.
    // Todos pueden acceder, pero si el usuario ha iniciado sesión (por ejemplo, el admin), se muestra su nombre y botón "Cerrar sesión".
    include '../controllers/catalogoController.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - SIGEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="imagenes/imiplogo_200x200_trans.png">
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
        .user-icon {
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
            margin: 0 auto 30px auto;
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
    </style>
</head>
<body>

<div class="top-bar">
    <div>Instituto Municipal de Investigación y Planeación</div>
    <div class="right-section">
        <div>Ciudad Juárez, Chihuahua</div>
        <div class="user-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#2a6e48" viewBox="0 0 16 16">
                <path d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-6a6 6 0 0 0-4.472 10.118C4.723 11.29 6.299 11 8 11s3.277.29 4.472.618A6 6 0 0 0 8 2z"/>
            </svg>
        </div>
        <div><strong><?php echo htmlspecialchars($usuario); ?></strong></div>
        <?php if ($usuario !== 'Público'): ?>
            <div><a href="logout.php" style="background-color: white; color: #2a6e48; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">Cerrar sesión</a></div>
        <?php else: ?>
            <div><a href="login.php" style="background-color: white; color: #2a6e48; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">Iniciar sesión</a></div>
        <?php endif; ?>
    </div>
</div>

<div class="header-logos">
    <img src="imagenes/sige1.png" alt="IMIP Logo">
    <img src="imagenes/sige2.png" alt="SIGEM Logo">
</div>

<?php include 'menuprincipal.php'; ?>

<div class="main-card">
    <h2 class="mb-4 text-center">Catálogo de Cuadros Estadísticos</h2>

    <p>Para su fácil localización, los diferentes cuadros que conforman el módulo estadístico del SIGEM se identifican mediante una clave conformada por el número de tema, identificador del subtema y el número de cuadro estadístico.</p>
    <p>Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos.</p>

    <div class="d-flex mb-5 flex-wrap">
        <div class="me-4" style="min-width:300px;">
            ** Aquí va la tabla de temas por tanto en controller publico debe cargar los temas correspondientes **
        </div>

        <div class="flex-fill">
            <p><strong>Ejemplo:</strong></p>
            <img src="imagenes/ejem.png" alt="Ejemplo clave estadística" class="img-fluid mb-3" style="max-width: 400px;">
            <p style="font-size: 15px;">
                El cuadro de “<strong>Población por Municipio</strong>” se encuentra dentro del Tema 3. Sociodemográfico en el subtema de <strong>Población</strong>.
            </p>
        </div>
    </div>

    <h4 class="mb-3">A continuación se presenta el índice general de cuadros estadísticos:</h4>

    <div style="display: flex; justify-content: center;">
        <div class="table-responsive" style="max-width: 1200px; width: 100%;">
            <table class="table table-bordered mx-auto" style="background-color: #e6f4e7; border-color: #7aa037;">
                <thead style="background-color: #7aa037; color: white; text-align: center;">
                    <tr>
                        <th style="width: 25%;">Tema</th>
                        <th style="width: 15%;">Código</th>
                        <th style="width: 60%;">Título del cuadro estadístico</th>
                    </tr>
                </thead>
                <tbody>
Cambiar el uso de php por eloquent para cargar los datos despues de dar alta a base de datos y habilitarla
                <?php
                $temaActual = '';
                foreach ($cuadros as $fila):
                ?>
                <tr>
                    <td style="font-weight: bold; color: #2a6e48; text-align:center;">
                        <?php 
                        if ($fila[1] !== $temaActual) {
                            echo htmlspecialchars($fila[1]);
                            $temaActual = $fila[1];
                        }
                        ?>
                    </td>
                    <td style="color: #2a6e48; text-align:center;"><?php echo htmlspecialchars($fila[2]); ?></td>
                    <td style="color: #2a6e48; text-align:left;"><?php echo htmlspecialchars($fila[3]); ?></td>
                </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="footer-logos">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

</body>
</html>
