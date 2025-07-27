<?php
include '../controllers/sesionController.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadística - SIGEM</title>
    <link rel="icon" type="image/png" href="imagenes/imiplogo_200x200_trans.png">
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
        .header-logos img:first-child {
            height: 85px;
        }
        .header-logos img:last-child {
            height: 65px;
        }
        .main-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin: 0 auto 30px auto; /* ← igual que productos.php */
            width: 90%;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .main-menu {
            background-color: #2a6e48;
            border-bottom: 4px solid #ffd700;
            display: flex;
            justify-content: center;
            margin-bottom: 0px;
        }
        .main-menu a {
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            font-weight: bold;
        }
        .main-menu a:hover {
            background-color: #1e4d36;
        }
        .main-menu a.active {
            background-color: #1e4d36;
        }
        .estadistica-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            margin: 0 0 20px;
        }
        .estadistica-header img {
            height: 160px;
        }
        .estadistica-header p {
            font-size: 18px;
            margin: 0;
        }
        .botones-temas {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        .botones-temas a {
            background-color: #2a6e48;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }
        .botones-temas a:hover {
            background-color: #1e4d36;
        }
        .catalogo {
            margin-top: 40px;
            text-align: center;
            font-weight: bold;
        }
        .catalogo a {
            color: #2a6e48;
            text-decoration: none;
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
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#2a6e48" class="bi bi-person" viewBox="0 0 16 16">
                <path d="M10 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-6a6 6 0 0 0-4.472 10.118C4.723 11.29 6.299 11 8 11s3.277.29 4.472.618A6 6 0 0 0 8 2z"/>
            </svg>
        </div>
        <div><strong><?php echo htmlspecialchars($usuario); ?></strong></div>
        <?php if ($usuario !== 'Público'): ?>
            <div>
                <a href="logout.php" style="background-color: white; color: #2a6e48; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">
                    Cerrar sesión
                </a>
            </div>
        <?php else: ?>
            <div>
                <a href="login.php" style="background-color: white; color: #2a6e48; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px;">
                    Iniciar sesión
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="header-logos">
    <img src="imagenes/sige1.png" alt="IMIP Logo">
    <img src="imagenes/sige2.png" alt="SIGEM Logo">
</div>

<?php include 'menuprincipal.php'; ?>

        <!-- 
Quiero que esto sea un blade, que cargara segun si se selecciona en el menu Estadística
        

<div class="main-card">
    <div class="estadistica-header">
        <img src="imagenes/iconoesta2.png" alt="Icono Estadística">
        <p>
            Consultas de información estadística relevante y precisa en cuadros estadísticos, obtenidos de diferentes fuentes 
            Municipales, Estatales, Federales, entre otros.<br>
            Los cuadros estadísticos están categorizados en los siguientes temas:
        </p>
    </div>
**Esto cargaria partials de estadisticas por tema**
    <div class="botones-temas">
        <a href="geografico.php">Geográfico</a>
        <a href="medioambiente.php">Medio Ambiente</a>
        <a href="sociodemografico.php">Sociodemográfico</a>
        <a href="inventariourbano.php">Inventario Urbano</a>
        <a href="economico.php">Económico</a>
        <a href="sectorpublico.php">Sector Público</a>
    </div>
** - **
    <div class="catalogo mt-4">
        <a href="catalogo.php"><span style="font-size: 22px;">📄</span> Catálogo completo de cuadros estadísticos</a>
    </div>
</div>
-->
<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

</body>
</html>
