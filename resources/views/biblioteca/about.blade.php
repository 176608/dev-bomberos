<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre la Biblioteca | Catálogo IMIP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e7390;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('{{ asset('img/biblioteca/fondo.png') }}') center/cover no-repeat fixed;
            color: #333;
            padding-top: 60px;
        }
        /* Sidebar izquierdo con logo */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: var(--shadow);
            z-index: 100;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            border-right: 1px solid var(--gray-300);
            overflow-y: auto;
        }
        .sidebar-logo {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 20px;
        }
        .sidebar-logo img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto 12px;
        }
        .sidebar-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin: 0;
            line-height: 1.3;
            display: block;
            white-space: normal;
            word-break: break-word;
        }
        .sidebar-nav {
            flex: 1;
            padding: 0 15px;
        }
        .sidebar-nav a {
            display: block;
            padding: 12px 20px;
            color: #495057;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 6px;
            transition: var(--transition);
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
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            z-index: 90;
            box-shadow: var(--shadow);
            background-color: #3a7d7c;
        }
        .header-title {
            font-weight: 600;
            font-size: 1.3rem;
        }
        /* Contenido principal */
        .main-content {
            margin-left: 260px;
            padding: 24px;
            min-height: calc(100vh - 60px);
            background: transparent;
        }
        /* Tarjeta de información */
        .about-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
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
        }
        .about-body {
            padding: 30px;
        }
        .about-section {
            margin-bottom: 30px;
        }
        .about-section h3 {
            color: var(--primary);
            font-size: 1.4rem;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .about-section p {
            line-height: 1.6;
            color: #555;
        }
        .about-list {
            padding-left: 20px;
        }
        .about-list li {
            margin-bottom: 12px;
            list-style-type: none;
            position: relative;
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
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
        }
        .value-icon {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 16px;
        }
        .value-title {
            font-weight: 600;
            color: var(--dark);
        }
        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9rem;
            background: rgba(255,255,255,0.8);
            margin-top: 40px;
            position: relative;
        }
        @media (max-width: 768px) {
            .sidebar { width: 70px; padding: 10px 0; }
            .sidebar-logo, .sidebar-title { display: none; }
            .main-content { margin-left: 70px; }
            .contact-row { flex-direction: column; }
        }
        /* Estilos específicos para el grid de imágenes */
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 24px;
            justify-content: center;
        }
        .image-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            height: auto;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .image-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .image-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }
        .image-container img {
            max-width: 90%;
            max-height: 90%;
            object-fit: cover;
            border-radius: 6px 6px 0 0;
        }
        .placeholder {
            font-size: 2.5rem;
            color: #adb5bd;
            text-align: center;
        }
        .image-caption {
            padding: 12px;
            text-align: center;
            font-size: 0.9rem;
            color: #495057;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<!-- Sidebar izquierdo -->
<div class="sidebar">
    <div class="sidebar-logo">
        <a href="https://www.imip.org.mx/imip/" target="_blank" style="text-decoration: none; display: block;">
            <img src="{{ asset('img/logo/IMIP_logo01.png') }}"
                 alt="Logo IMIP" 
                 style="max-height: 80px; width: auto; object-fit: contain; display: block; margin: 0 auto 12px;"
                 onerror="this.style.display='none'; console.warn('Logo no encontrado');">
        </a>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('home') }}"><i class="fas fa-home"></i> Inicio</a>
        <a href="{{ route('search.advanced') }}"><i class="fas fa-search"></i> Búsqueda Avanzada</a>
        <a href="{{ route('about.library') }}" class="active"><i class="fas fa-info-circle"></i> Sobre la biblioteca</a>
        <!-- Solo vista pública -->
    </nav>
</div>

<!-- Header superior -->
<div class="header">
    <div class="header-title">
        📚 Catálogo General Biblioteca IMIP
    </div>
    <!-- SIN user-menu ni logout - Solo vista pública -->
</div>

<!-- Contenido principal -->
<div class="main-content">
    <div class="about-card">
        <div class="about-header">
            <h2><i class="fas fa-info-circle"></i> MPDU Abigail García Espinosa</h2>
        </div>
        <div class="about-body">
            <!-- Sección: Identidad -->
            <div class="about-section">
                <h3><i class="fas fa-building"></i> Institución</h3>
                <p>
                    La Biblioteca del <strong>Instituto Municipal de Investigación y Planeación (IMIP)</strong> es un espacio de conocimiento especializado en desarrollo urbano, en donde podrán encontrar uno de los acervos literarios más extensos sobre planeación urbana.
                </p>

                <!-- Grid de 4 imágenes -->
                <div class="image-grid">
                    @foreach([
                        ['file' => '641222c6a8db7_641222c6a8db8.png', 'title' => 'Espacio físico y acceso'],
                        ['file' => '641222c6bc008_641222c6bc009.png', 'title' => 'Nuestra biblioteca'],
                        ['file' => '641222c683f46_641222c683f48.png', 'title' => 'Colección de libros'],
                        ['file' => '641222c6967b1_641222c6967b2.png', 'title' => 'Historia y memoria']
                    ] as $item)         
                    <div class="image-card">
                        <div class="image-container">
                            <img src="{{ asset('img/biblioteca/' . $item['file']) }}"
                                 alt="{{ $item['title'] }}"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="placeholder" style="display:none;">⚠️</div>
                        </div>
                        <div class="image-caption">
                            {{ $item['title'] }}
                        </div>
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
                <div class="contact-info">
                    <div class="contact-row">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <strong>Dirección</strong>
                            <p>C. Benjamín Franklin No. 4185<br>
                            Colonia Progresista, C.P. 32310<br>
                            Ciudad Juárez, Chih.</p>
                        </div>
                    </div>
                    <div class="contact-row">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-text">
                            <strong>Teléfono</strong>
                            <p>(656) 613 6520</p>
                        </div>
                    </div>
                    <div class="contact-row">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-text">
                            <strong>Horario</strong>
                            <p>Lunes a Viernes<br>8:00 a 15:00 horas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección: Valores -->
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
                        <p>Generar los instrumentos de investigación estadística y de actualización cartográfica, y administrar el sistema de información geográfica municipal.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon"><i class="fas fa-globe-americas"></i></div>
                        <div class="value-title">Transparencia</div>
                        <p>Hacemos accesible el conocimiento generado para todos con la calidad, la veracidad y el servicio público.</p>
                    </div>
                </div>
            </div>

            <!-- Cierre -->
            <div style="text-align:center; margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--gray-200);">
                <p>© {{ date('Y') }} Instituto Municipal de Investigación y Planeación<br>
                Derechos reservados. Todos los derechos protegidos.</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>