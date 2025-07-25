<?php
    include '../controllers/sesionController.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cartografía - SIGEM</title>
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
        .title-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .title-row img {
            width: 100px;
            height: auto;
            margin-right: 15px;
        }
        .title-row h2 {
            color: #2a6e48;
            font-weight: bold;
            margin: 0;
        }
        .intro-text {
            margin-bottom: 25px;
            font-size: 16px;
            color: #333;
        }
        .map-section {
            margin-bottom: 40px;
        }
        .map-section h5 {
            font-weight: bold;
            color: #2a6e48;
        }
        .map-section iframe {
            width: 100%;
            height: 320px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        .map-section p {
            margin-top: 8px;
            margin-bottom: 10px;
        }
        p.text-center {
            margin-top: 40px;
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

<div class="main-card">
    <div class="title-row">
        <img src="imagenes/cartogde.png" alt="Cartografía">
        <h2>Cartografía</h2>
    </div>

    <p class="intro-text">En este apartado podrás encontrar mapas temáticos interactivos del Municipio de Juárez.</p>

    <div class="map-section">
        <h5>Carta Urbana, 2018</h5>
        <p>Mapa representativo de la superficie territorial del Municipio de Juárez, Chihuahua, que enumera los principales referentes tales como nombre de calles, vialidades principales, colonias, fraccionamientos, parques industriales, etc.</p>
        <iframe src="https://www.imip.org.mx/imip/files/mapas/curbana/" title="Carta Urbana 2018"></iframe>
    </div>

    <div class="map-section">
        <h5>Niveles de Bienestar Social 2010 - 2020</h5>
        <p>Mapa que representa los niveles de bienestar social de la población. Incluye clasificación de zonas de rezago con base en diversos indicadores sociales.</p>
        <iframe src="https://www.imip.org.mx/imip/files/mapas/nbienestar/index.html" title="Niveles de Bienestar"></iframe>
    </div>

    <div class="map-section">
        <h5>Catálogo de sectores: Gobierno, parques, zonas industriales</h5>
        <p>Mapa que presenta la ubicación e información de sectores industriales, parques, zonas institucionales y de servicios en la ciudad.</p>
        <iframe src="https://www.imip.org.mx/imip/files/mapas/industria/index.html" title="Catálogo de sectores"></iframe>
    </div>

    <div class="map-section">
        <h5>Cruces con mayor incidencia vial</h5>
        <p>Ubicación de los cruceros con más alta incidencia de tránsito en el municipio, basada en información de reportes viales.</p>
        <iframe src="https://www.imip.org.mx/imip/files/mapas/Transito/index.html" title="Cruces viales"></iframe>
    </div>

    <p class="text-center">Para ver otros mapas visita <a href="https://www.imip.org.mx/imip/node/53" target="_blank">Mapas Digitales Interactivos</a></p>
</div>

<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

</body>
</html>
