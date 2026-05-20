<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sobre la Biblioteca | Catálogo IMIP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* === RESET CRÍTICO === */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            overflow-x: hidden !important;
            width: 100%;
            max-width: 100vw;
        }

        :root {
            --primary: #1e7390;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('{{ asset('img/biblioteca/fondo.png') }}') center/cover no-repeat fixed;
            color: #333;
            /* Sin padding-top para evitar espacio vacío */
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            border-right: 1px solid var(--gray-300);
            overflow-y: auto;
        }

        .sidebar-logo {
            text-align: center;
            margin-bottom: 20px;
            padding: 0 15px;
        }

        .sidebar-logo img {
            max-height: 70px;
            width: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .sidebar-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin: 10px 0 0;
            line-height: 1.2;
            display: block;
        }

        .sidebar-nav {
            flex: 1;
            padding: 0 10px;
        }

        .sidebar-nav a {
            display: block;
            padding: 12px 15px;
            color: #495057;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: all 0.2s;
            font-size: 0.95rem;
        }

        .sidebar-nav a:hover {
            background: var(--gray-200);
            color: var(--primary);
        }

        .sidebar-nav a.active {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #3a7d7c, #2c5f5e);
            color: white;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            z-index: 999;
            box-shadow: var(--shadow);
            height: 60px;
        }

        .header-title {
            font-weight: 600;
            font-size: 1.2rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 75px 20px 20px 20px;
            min-height: 100vh;
            width: calc(100% - 260px);
        }

        /* Tarjeta principal */
        .about-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 100%;
        }

        .about-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .about-header h2 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .about-body {
            padding: 30px;
        }

        .about-section {
            margin-bottom: 30px;
        }

        .about-section h3 {
            color: var(--primary);
            font-size: 1.3rem;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .about-section p {
            line-height: 1.6;
            color: #555;
            margin-bottom: 15px;
        }

        .about-list {
            padding-left: 20px;
        }

        .about-list li {
            margin-bottom: 12px;
            list-style-type: none;
            position: relative;
            line-height: 1.5;
        }

        .about-list li::before {
            content: "✓";
            color: var(--success);
            font-weight: bold;
            position: absolute;
            left: -20px;
        }

        .contact-info {
            background: var(--light);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        .contact-row {
            display: flex;
            gap: 20px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .contact-icon {
            font-size: 1.4rem;
            color: var(--primary);
            min-width: 32px;
            text-align: center;
        }

        .contact-text strong {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .contact-text p {
            margin: 0;
            line-height: 1.5;
        }

        /* Grid de valores */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .value-card {
            background: white;
            border: 1px solid var(--gray-300);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }

        .value-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
        }

        .value-icon {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .value-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .value-card p {
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0;
        }

        /* Grid de imágenes */
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 24px;
            justify-content: center;
        }

        .image-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            height: auto;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .image-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        .image-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            min-height: 150px;
        }

        .image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
            border-radius: 6px 6px 0 0;
            width: 100%;
        }

        .placeholder {
            font-size: 2.5rem;
            color: #adb5bd;
            text-align: center;
        }

        .image-caption {
            padding: 12px;
            text-align: center;
            font-size: 0.85rem;
            color: #495057;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.85rem;
            background: rgba(255, 255, 255, 0.95);
            margin-top: 40px;
        }

        /* === 📱 RESPONSIVE COMPLETO === */
        @media (max-width: 768px) {

            /* Sidebar Inferior */
            .sidebar {
                width: 100% !important;
                height: 65px !important;
                top: auto !important;
                bottom: 0 !important;
                left: 0 !important;
                padding: 0 !important;
                flex-direction: row !important;
                border-right: none !important;
                border-top: 1px solid var(--gray-300) !important;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1) !important;
                z-index: 1000 !important;
            }

            .sidebar-logo,
            .sidebar-title {
                display: none !important;
            }

            .sidebar-nav {
                display: flex !important;
                flex-direction: row !important;
                width: 100% !important;
                justify-content: space-around !important;
                align-items: center !important;
                padding: 0 !important;
            }

            .sidebar-nav a {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                flex: 1 !important;
                margin: 4px !important;
                padding: 6px 2px !important;
                font-size: 0.65rem !important;
                text-align: center !important;
                height: calc(100% - 8px) !important;
                gap: 4px !important;
                background: transparent !important;
                color: #495057 !important;
                border-radius: 8px !important;
                line-height: 1.1 !important;
                font-weight: normal !important;
            }

            .sidebar-nav a i {
                font-size: 1.4rem !important;
                margin-bottom: 2px !important;
                display: block !important;
            }

            .sidebar-nav a.active {
                background: var(--light) !important;
                color: var(--primary) !important;
                font-weight: 600 !important;
                box-shadow: inset 0 -3px 0 var(--primary) !important;
                border-radius: 8px 8px 0 0 !important;
            }

            /* Header ajustado */
            .header {
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                height: 50px !important;
                padding: 0 10px !important;
            }

            .header-title {
                font-size: 1rem !important;
            }

            /* Contenido principal */
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 60px 10px 80px 10px !important;
            }

            /* Tarjetas y secciones sin desborde */
            .about-card,
            .about-body,
            .about-section,
            .value-card,
            .contact-info {
                width: 100% !important;
                max-width: 100% !important;
                overflow-x: hidden !important;
            }

            .about-body {
                padding: 20px 15px !important;
            }

            .about-section h3 {
                font-size: 1.1rem !important;
            }

            .about-section p {
                font-size: 0.9rem !important;
            }

            /* Grid de imágenes: 2 columnas en móvil */
            .image-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 10px !important;
            }

            .image-container {
                min-height: 120px !important;
            }

            .image-caption {
                font-size: 0.75rem !important;
                padding: 8px !important;
            }

            /* Grid de valores: 1 columna */
            .values-grid {
                grid-template-columns: 1fr !important;
                gap: 15px !important;
            }

            .value-card {
                padding: 15px !important;
            }

            .value-icon {
                font-size: 1.8rem !important;
            }

            .value-title {
                font-size: 1rem !important;
            }

            /* Contacto: apilar verticalmente */
            .contact-row {
                flex-direction: column !important;
                gap: 15px !important;
            }

            .contact-icon {
                font-size: 1.2rem !important;
            }

            /* Listas */
            .about-list {
                padding-left: 25px !important;
            }

            .about-list li {
                font-size: 0.9rem !important;
            }

            /* Footer alineado */
            .footer {
                margin-left: 0 !important;
                width: 100% !important;
                font-size: 0.75rem !important;
                padding: 15px 10px 80px 10px !important;
            }
        }

        /* iPhone SE / Móviles muy pequeños */
        @media (max-width: 480px) {

            /* Mostrar logo pequeño */
            .sidebar-logo {
                display: block !important;
                margin-bottom: 10px !important;
                padding: 0 5px !important;
            }

            .sidebar-logo img {
                max-height: 30px !important;
            }

            .header-title {
                font-size: 0.9rem !important;
                max-width: 70%;
            }

            /* Imágenes: 1 columna en pantallas muy pequeñas */
            .image-grid {
                grid-template-columns: 1fr !important;
            }

            .image-container {
                min-height: 180px !important;
            }

            /* Texto más pequeño */
            .about-section h3 {
                font-size: 1.05rem !important;
            }

            .about-section p,
            .about-list li {
                font-size: 0.85rem !important;
            }

            .value-card p {
                font-size: 0.8rem !important;
            }

            .main-content {
                padding: 60px 5px 5px 5px !important;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar izquierdo -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <a href="https://www.imip.org.mx/imip/" target="_blank" style="text-decoration: none; display: block;">
                <img src="{{ asset('img/logo/IMIP_logo01.png') }}" alt="Logo IMIP" onerror="this.style.display='none';">
            </a>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('home') }}"><i class="fas fa-home"></i> Inicio</a>
            <a href="{{ route('search.advanced') }}"><i class="fas fa-search"></i> Búsqueda Avanzada</a>
            <a href="{{ route('about.library') }}" class="active"><i class="fas fa-info-circle"></i> Sobre la
                biblioteca</a>
        </nav>
    </div>

    <!-- Header superior -->
    <div class="header">
        <div class="header-title">Catálogo General Biblioteca</div>
    </div>

    <!-- Contenido principal -->
    <div class="main-content">
        <div class="about-card">
            <div class="about-header">
                <h2><i class="fas fa-info-circle"></i> MPDU Abigail García Espinosa</h2>
            </div>
            <div class="about-body">
                <!-- Sección: Institución -->
                <div class="about-section">
                    <h3><i class="fas fa-building"></i> Institución</h3>
                    <p>
                        La Biblioteca del <strong>Instituto Municipal de Investigación y Planeación (IMIP)</strong> es
                        un espacio de conocimiento especializado en desarrollo urbano, en donde podrán encontrar uno de
                        los acervos literarios más extensos sobre planeación urbana.
                    </p>

                    <!-- Grid de 4 imágenes -->
                    <div class="image-grid">
                        @foreach([
                                    ['file' => '641222c6a8db7_641222c6a8db8.png', 'title' => 'Espacio físico y acceso'],
                                    ['file' => '641222c6bc008_641222c6bc009.png', 'title' => 'Nuestra biblioteca'],
                                    ['file' => '641222c683f46_641222c683f48.png', 'title' => 'Colección de libros'],
                                    ['file' => '641222c6967b1_641222c6967b2.png', 'title' => 'Historia y memoria']
                                ] as $item)         
                               <div  class="image-card">
                                    <div class="image-container">
                                        <img src="{{ asset('img/biblioteca/' . $item['file']) }}" alt="{{ $item['title'] }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div class="placeholder" style="display:none;">⚠️</div>
                                    </div>
                                <div class="image-caption">{{ $item['title'] }}</div>
                                </div>
                        @endforeach
                    </div>
              </div>

    
                       
                       
                                       <!-- Sección: Misión -->
                <div class="about-section">
                <h3><i class="fas fa-bullseye"></i> Misión</h3>
                    <p>
                        Conservar, organizar y difundir el acervo documental del IMIP, apoyando la investigación, la toma de decisiones informadas y la construcción colectiva del futuro de Ciudad Juárez. Se puede visitar esta biblioteca para consultar temas de interés, principalmente información sobre estadística, cartografía, arquitectura, transporte y planeación urbana.
                    </p>
            </div>

 <!-- Sección: Colección -->

    <div class="about-section">

<h3><i class="fas fa-book-open"></i> Nuestra Colección</h3>
                <ul class="about-list">
                <li>Más de <strong>3,000 títulos registrados</strong> (libros, boletines, revistas, periódicos, videocassettes, CD/DVD, informes, mapas).</li>
                        <li>Foco en temas clave: desarrollo urbano, estadística, cartografía, arquitectura, transporte y planeación urbana.</li>
                        <li>Acceso abierto a recursos digitales y materiales físicos para investigadores, funcionarios y ciudadanía.</li>
                    </ul>
              </div>
 <!-- Sección: Ubicación y Contacto -->
<div class="about-section">
    <h3><i class="fas fa-map-marker-alt"></i> Ubicación y Contacto</h3>
    
    <!-- Tarjetas de contacto -->
    <div class="contact-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        
       <!-- Dirección -->
<div style="background: linear-gradient(135deg, #3a7d7c 0%, #2c5f5e 100%); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
    <div style="font-size: 2.5rem; margin-bottom: 15px; text-align: center; color: white;">
        <i class="fas fa-map-marked-alt"></i>
    </div>
    <h4 style="margin: 0 0 15px 0; font-size: 1.1rem; font-weight: 600; text-align: center; color: white;">Dirección</h4>
    <p style="margin: 0; line-height: 1.6; text-align: center; font-size: 0.95rem; color: white;">
        C. Benjamín Franklin No. 4185<br>
        Colonia Progresista, C.P. 32310<br>
        Ciudad Juárez, Chih.
    </p>
    <a href="https://www.google.com/maps/place/Instituto+Municipal+de+Investigaci%C3%B3n+y+Planeaci%C3%B3n/@31.7425481,-106.4475039,17z/data=!3m1!4b1!4m6!3m5!1s0x86e759594d53cc09:0x6e1b88e088b82e12!8m2!3d31.7425481!4d-106.4475039!16s%2Fg%2F11c5o8q8q8?entry=ttu" 
       target="_blank" 
       style="display: inline-block; margin-top: 15px; padding: 8px 20px; background: rgba(255,255,255,0.2); color: white; text-decoration: none; border-radius: 20px; font-size: 0.85rem; font-weight: 600; transition: background 0.3s;"
       onmouseover="this.style.background='rgba(255,255,255,0.3)'" 
       onmouseout="this.style.background='rgba(255,255,255,0.2)'">
        <i class="fas fa-directions"></i> Cómo llegar
    </a>
</div>

        <!-- Teléfono -->
        <div style="background: linear-gradient(135deg, #1e7390 0%, #1a5d73 100%); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 2.5rem; margin-bottom: 15px; text-align: center; color: white;">
                <i class="fas fa-phone-alt"></i>
            </div>
            <h4 style="margin: 0 0 15px 0; font-size: 1.1rem; font-weight: 600; text-align: center; color: white;">Teléfono</h4>
            <p style="margin: 0 0 10px 0; text-align: center; font-size: 1.3rem; font-weight: 700;">
                <a href="tel:+526566136520" style="color: white; text-decoration: none;">(656) 613 6520</a>
            </p>
            <p style="margin: 0; text-align: center; font-size: 0.85rem; opacity: 0.9; color: white;">
                Llámanos para más información
            </p>
        </div>

        <!-- Horario -->
        <div style="background: linear-gradient(135deg, #4a9b9a 0%, #2d8b8a 100%); color: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 2.5rem; margin-bottom: 15px; text-align: center; color: white;">
                <i class="fas fa-clock"></i>
            </div>
            <h4 style="margin: 0 0 15px 0; font-size: 1.1rem; font-weight: 600; text-align: center; color: white;">Horario de Atención</h4>
            <p style="margin: 0; text-align: center; font-size: 0.95rem; line-height: 1.6; color: white;">
                <strong>Lunes a Viernes</strong><br>
                8:00 a 15:00 horas
            </p>
        </div>

    </div>

    <!-- Mapa del IMIP - CORREGIDO -->
    <div style="margin-top: 30px; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: #f0f0f0; width: 100%;">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d6786.047836879144!2d-106.4475039!3d31.7425481!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x86e759594d53cc09%3A0x6e1b88e088b82e12!2sInstituto%20Municipal%20de%20Investigaci%C3%B3n%20y%20Planeaci%C3%B3n!5e0!3m2!1ses-419!2smx!4v1779210311729!5m2!1ses-419!2smx" 
            width="100%" 
            height="450" 
            style="border:0; display: block;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade"
            title="Ubicación del IMIP">
        </iframe>
    </div>
</div>
                   <!-- Sección : Valores -->
            <div class="about-section">
                <h3><i class="fas fa-heart"></i> Valores Institucionales</h3>
                <div class="values-grid">
                        <div class="value-card">
                            <div class="value-icon"><i class="fas fa-balance-scale"></i></div>
                            <div class="value-title">Compromiso</div>
                            <p>Proveer los planes y proyectos urbanos que Juárez requiere para su desarrollo sustentable y el fortalecimiento de su identidad, con bases científicas y de participación ciudadana.</p>
                        </div>
                        <div class="value-card">
                            <div class="value-icon"><i class="fas fa-users"></i></div>
                            <div class="value-title">Colaboración</div>
                            <p>Elaborar, en coordinación con las instancias del ámbito federal y estatal competentes, estudios de factibilidad que permitan la protección y acrecentamiento del patrimonio arquitectónico de carácter histórico y cultural del Municipio de Juárez.</p>
                        </div>
                        <div class="value-card">
                            <div class="value-icon"><i class="fas fa-search"></i></div>
                            <div class="value-title">Rigor técnico</div>
                            <p>Generar los instrumentos de investigación estadística y de actualización cartográfica y administrar el sistema de información geográfica municipal.</p>
                        </div>
                        <div class="value-card">
                            <div class="value-icon"><i class="fas fa-globe-americas"></i></div>
                        <div class="value-title">Transparencia</div>
                            <p>Hacemos accesible el conocimiento generado para todos con la calidad, la veracidad y el servicio público.</p>
                    
                       </div>
                    </div>

                </div>
    
   
     <!-- Footer -->
<div class="footer">
    Instituto Municipal de Investigación y Planeación<br>
    C. Benjamín Franklin No. 4185 Colonia Progresista C.P. 32310 Ciudad Juárez, Chih.<br>
    Tel. (656) 6136520 &nbsp;|&nbsp; © Derechos reservados 2026
</div>

</body>
</html>