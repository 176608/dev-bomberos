<?php 
session_start();
$usuario = $_SESSION['usuario'] ?? 'Público';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - SIGEM</title>
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
        .product-section {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .product-section img {
            max-width: 200px;
            height: auto;
        }
        .product-text {
            flex: 1;
        }
        .product-text h5 {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-text h5 a {
            color: #2a6e48;
            text-decoration: none;
        }
        .product-text h5 a:hover {
            text-decoration: underline;
        }
        .footer-link {
            text-align: center;
            margin-top: 40px;
            font-weight: bold;
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

    <div class="product-section">
        <img src="imagenes/rad2020.png" alt="Radiografía Socioeconómica">
        <div class="product-text">
            <h5><a href="https://www.imip.org.mx/imip/node/41" target="_blank">Radiografía Socioeconómica del Municipio de Juárez</a></h5>
            <p>Este documento se ha convertido en una herramienta de referencia y consulta en cuanto a las diversas características socioeconómicas del municipio. Ofrece datos sobre los principales temas de interés para la toma de decisiones del sector público como privado de la región, así como de apoyo a los estudiantes y población en general.</p>
        </div>
    </div>

    <div class="product-section">
        <img src="imagenes/PoratadaCARTO.png" alt="Cartografía 2019">
        <div class="product-text">
            <h5><a href="https://www.imip.org.mx/imip/node/40" target="_blank">Cuaderno de Información Cartográfica</a></h5>
            <p>Es una guía de información confiable y actualizada, compuesta por mapas con índice de calles, colonias y capas temáticas como escuelas, hospitales, estaciones, museos, teatros, unidades deportivas, hoteles, cines, entre otros. Disponible en formato impreso y digital.</p>
        </div>
    </div>

    <div class="product-section">
        <img src="imagenes/general.png" alt="Directorio 2014">
        <div class="product-text">
            <h5><a href="https://www.imip.org.mx/directorio/" target="_blank">Directorio Georreferenciado de Parques, Zonas Industriales e Industrias en Ciudad Juárez, 2014</a></h5>
            <p>Incluye información estadística y geográfica de empresas manufactureras en la ciudad, clasificadas por tamaño y actividad. Contiene datos de empresas dentro y fuera de parques industriales.</p>
        </div>
    </div>

    <div class="product-section">
        <img src="imagenes/abigail.jpeg" alt="Biblioteca">
        <div class="product-text">
            <h5><a href="https://www.imip.org.mx/imip/node/35" target="_blank">Biblioteca MPDU: Abigail García Espinosa</a></h5>
            <p>Cuenta con un amplio acervo documental y bancos de datos especializados. Ideal para investigaciones urbanas, tesis, proyectos y trabajos académicos. Forma parte de la Red de Consulta del INEGI.</p>
        </div>
    </div>

    <p class="footer-link">
        Encuentra además otros productos estadísticos y cartográficos en
        <a href="https://www.imip.org.mx/imip/publicaciones-en-linea" target="_blank">la página web del Instituto Municipal de Investigación y Planeación</a>
    </p>
</div>

<div class="footer-logos mt-5">
    <img src="imagenes/sige2.png" alt="SIGEM Footer Logo">
    <img src="imagenes/logosfinales2.png" alt="IMIP Footer Logo">
    <img src="imagenes/logoadmin.png" alt="Ciudad Juárez Footer Logo">
</div>

</body>
</html>
