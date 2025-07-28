<!-- Archivo SIGEM -Base de vista sigem publica- - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'SIGEM - Sistema de Información Geográfica')

@section('content')
<style>
.header-logos {
    display: flex;
    width: 100%;
    min-height: 100px;
    border-bottom: 4px solid #ffd700;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.logo-section {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: white;
    margin: 10px 5px;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.logo-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.logo-section img {
    max-width: 100%;
    max-height: 80px;
    object-fit: contain;
}

/* NUEVO: Menú SIGEM */
.main-menu {
    background-color: #2a6e48 !important;
    border-bottom: 3px solid #ffd700;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    width: 100%;
    margin: 0;
    padding: 0;
}

.main-menu .nav-container {
    display: flex;
    justify-content: center;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0;
}

.main-menu a {
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    font-weight: bold;
    font-size: 14px;
    border-radius: 0;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;
}

.main-menu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffd700;
    transform: translateY(-1px);
}

.main-menu a.active {
    background-color: #1e4d35;
    color: #ffd700;
    font-weight: bold;
}

.main-menu a.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: #ffd700;
}

/* Loading indicator */
.loading {
    text-align: center;
    padding: 40px;
    color: #2a6e48;
}

.loading i {
    font-size: 24px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .header-logos {
        flex-direction: column;
        min-height: auto;
    }
    
    .logo-section {
        margin: 5px 10px;
    }
    
    .main-menu .nav-container {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .main-menu a {
        padding: 10px 15px;
        font-size: 13px;
    }
}
</style>

<div class="container-fluid" style="background: linear-gradient(135deg, #2a6e48 0%, #66d193 50%, #2a6e48 100%);">

    <div class="header-logos container-fluid">
        <div class="logo-section">
            <img src="../imagenes/logoadmin.png" alt="JRZ Logo">
        </div>
        <div class="logo-section">
            <img src="../imagenes/sige2.png" alt="SIGEM Logo">
        </div>
    </div>

    <!-- MENÚ SIGEM -->
    <div class="main-menu container-fluid p-0">
        <div class="nav-container">
            <a href="#" data-section="inicio" class="sigem-nav-link active">
                <i class="bi bi-house-fill"></i> INICIO
            </a>
            <a href="#" data-section="estadistica" class="sigem-nav-link">
                <i class="bi bi-bar-chart-fill"></i> ESTADÍSTICA
            </a>
            <a href="#" data-section="cartografia" class="sigem-nav-link">
                <i class="bi bi-map-fill"></i> CARTOGRAFÍA
            </a>
            <a href="#" data-section="productos" class="sigem-nav-link">
                <i class="bi bi-box-seam"></i> PRODUCTOS
            </a>
            <a href="#" data-section="catalogo" class="sigem-nav-link">
                <i class="bi bi-journal-text"></i> CATÁLOGO
            </a>
        </div>
    </div>

    <!-- CONTENEDOR DINÁMICO -->
    <div id="sigem-content" class="container mt-4">
        <div class="loading">
            <i class="bi bi-hourglass-split"></i>
            <p>Cargando contenido...</p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Elementos del DOM
    const navLinks = document.querySelectorAll('.sigem-nav-link');
    const contentContainer = document.getElementById('sigem-content');
    
// Contenidos
const DynamicContent = {
    inicio: `
        Aqui va index de SIGEM publico 
    `,

    estadistica: `
        <div class="main-card">
            <div class="estadistica-header">
                <img src="imagenes/iconoesta2.png" alt="Icono Estadística">
                <p>
                    Consultas de información estadística relevante y precisa en cuadros estadísticos, obtenidos de diferentes fuentes 
                    Municipales, Estatales, Federales, entre otros.<br>
                    Los cuadros estadísticos están categorizados en los siguientes temas:
                </p>
            </div>
        *Esto cargaria partials de estadisticas por tema*
            <div class="botones-temas">
                <a href="geografico.php">Geográfico</a>
                <a href="medioambiente.php">Medio Ambiente</a>
                <a href="sociodemografico.php">Sociodemográfico</a>
                <a href="inventariourbano.php">Inventario Urbano</a>
                <a href="economico.php">Económico</a>
                <a href="sectorpublico.php">Sector Público</a>
            </div>
            
            <div class="catalogo mt-4">
                <a href="catalogo.php"><span style="font-size: 22px;">📄</span> Catálogo completo de cuadros estadísticos</a>
            </div>
        </div>
    `,

    cartografia: `
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
    `,

    productos: `
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
    `,

    catalogo: `
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
                        <?/*php
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
                        <?php endforeach; */?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `
};
    
    // Función para cargar contenido
    function loadContent(section) {
        // Mostrar loading
        contentContainer.innerHTML = `
            <div class="loading">
                <i class="bi bi-hourglass-split"></i>
                <p>Cargando ${section}...</p>
            </div>
        `;
        
        // Simular delay de carga
        setTimeout(() => {
            if (DynamicContent[section]) {
                contentContainer.innerHTML = DynamicContent[section];
            } else {
                contentContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Contenido de <strong>${section}</strong> no disponible
                    </div>
                `;
            }
        }, 500);
    }
    
    // Event listeners para navegación
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const section = this.getAttribute('data-section');
            
            // Remover active de todos
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Agregar active al clickeado
            this.classList.add('active');
            
            // Cargar contenido
            loadContent(section);
            
            console.log(`Cargando sección: ${section}`);
        });
    });
    
    // Cargar contenido inicial
    loadContent('inicio');
});
</script>
@endsection