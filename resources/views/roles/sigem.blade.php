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

/* Estilos para el contenido dinámico */
.main-card {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    padding: 30px;
    margin-bottom: 20px;
}

.estadistica-header {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 30px;
}

.estadistica-header img {
    width: 80px;
    height: auto;
    flex-shrink: 0;
}

.botones-temas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin: 30px 0;
}

.botones-temas a {
    background: linear-gradient(135deg, #2a6e48, #66d193);
    color: white;
    padding: 15px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.botones-temas a:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    color: white;
}

.catalogo {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
}

.catalogo a {
    color: #2a6e48;
    text-decoration: none;
    font-weight: bold;
    font-size: 18px;
}

.catalogo a:hover {
    color: #1e4d35;
}

.product-section {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.product-section img {
    width: 120px;
    height: auto;
    flex-shrink: 0;
    border-radius: 4px;
}

.product-text h5 {
    color: #2a6e48;
    margin-bottom: 10px;
}

.map-section {
    margin: 30px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.map-section iframe {
    width: 100%;
    height: 400px;
    border: 2px solid #dee2e6;
    border-radius: 4px;
}

.title-row {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.title-row img {
    width: 60px;
    height: auto;
}

.intro-text {
    font-size: 16px;
    color: #6c757d;
    margin-bottom: 30px;
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
    
    .estadistica-header {
        flex-direction: column;
        text-align: center;
    }
    
    .product-section {
        flex-direction: column;
        text-align: center;
    }
    
    .product-section img {
        align-self: center;
    }
    
    .botones-temas {
        grid-template-columns: 1fr;
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
        INDEX, check cambios
    `,

    estadistica: `
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="estadistica-header">
                    <img src="../imagenes/iconoesta2.png" alt="Icono Estadística" class="img-fluid">
                    <div>
                        <h3 class="text-success mb-3">
                            <i class="bi bi-bar-chart-fill"></i> Estadísticas Municipales
                        </h3>
                        <p class="lead">
                            Consultas de información estadística relevante y precisa en cuadros estadísticos, obtenidos de diferentes fuentes 
                            Municipales, Estatales, Federales, entre otros.
                        </p>
                        <p class="text-muted">
                            Los cuadros estadísticos están categorizados en los siguientes temas:
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3 text-center">Selecciona un tema de consulta:</h5>
                        <div class="botones-temas">
                            <a href="geografico.php">
                                <i class="bi bi-geo-alt-fill me-2"></i>Geográfico
                            </a>
                            <a href="medioambiente.php">
                                <i class="bi bi-tree-fill me-2"></i>Medio Ambiente
                            </a>
                            <a href="sociodemografico.php">
                                <i class="bi bi-people-fill me-2"></i>Sociodemográfico
                            </a>
                            <a href="inventariourbano.php">
                                <i class="bi bi-building me-2"></i>Inventario Urbano
                            </a>
                            <a href="economico.php">
                                <i class="bi bi-currency-dollar me-2"></i>Económico
                            </a>
                            <a href="sectorpublico.php">
                                <i class="bi bi-bank me-2"></i>Sector Público
                            </a>
                        </div>
                        
                        <div class="catalogo mt-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-journal-text display-6"></i>
                                    </h5>
                                    <a href="catalogo.php" class="btn btn-success btn-lg">
                                        <i class="bi bi-list-ul me-2"></i>
                                        Catálogo completo de cuadros estadísticos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,

    cartografia: `
    `,

    productos: `
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-success mb-4 text-center">
                    <i class="bi bi-box-seam-fill"></i> Productos Cartográficos y Estadísticos
                </h2>

                <div class="row g-4">
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="product-section">
                                    <img src="../imagenes/rad2020.png" alt="Radiografía Socioeconómica" class="img-fluid">
                                    <div class="product-text">
                                        <h5>
                                            <a href="https://www.imip.org.mx/imip/node/41" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-file-earmark-text me-2"></i>
                                                Radiografía Socioeconómica del Municipio de Juárez
                                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        </h5>
                                        <p>Este documento se ha convertido en una herramienta de referencia y consulta en cuanto a las diversas características socioeconómicas del municipio. Ofrece datos sobre los principales temas de interés para la toma de decisiones del sector público como privado de la región, así como de apoyo a los estudiantes y población en general.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="product-section">
                                    <img src="../imagenes/PoratadaCARTO.png" alt="Cartografía 2019" class="img-fluid">
                                    <div class="product-text">
                                        <h5>
                                            <a href="https://www.imip.org.mx/imip/node/40" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-map me-2"></i>
                                                Cuaderno de Información Cartográfica
                                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        </h5>
                                        <p>Es una guía de información confiable y actualizada, compuesta por mapas con índice de calles, colonias y capas temáticas como escuelas, hospitales, estaciones, museos, teatros, unidades deportivas, hoteles, cines, entre otros. Disponible en formato impreso y digital.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="product-section">
                                    <img src="../imagenes/general.png" alt="Directorio 2014" class="img-fluid">
                                    <div class="product-text">
                                        <h5>
                                            <a href="https://www.imip.org.mx/directorio/" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-building me-2"></i>
                                                Directorio Georreferenciado de Parques, Zonas Industriales e Industrias en Ciudad Juárez, 2014
                                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        </h5>
                                        <p>Incluye información estadística y geográfica de empresas manufactureras en la ciudad, clasificadas por tamaño y actividad. Contiene datos de empresas dentro y fuera de parques industriales.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="product-section">
                                    <img src="../imagenes/abigail.jpeg" alt="Biblioteca" class="img-fluid">
                                    <div class="product-text">
                                        <h5>
                                            <a href="https://www.imip.org.mx/imip/node/35" target="_blank" class="text-decoration-none">
                                                <i class="bi bi-book me-2"></i>
                                                Biblioteca MPDU: Abigail García Espinosa
                                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        </h5>
                                        <p>Cuenta con un amplio acervo documental y bancos de datos especializados. Ideal para investigaciones urbanas, tesis, proyectos y trabajos académicos. Forma parte de la Red de Consulta del INEGI.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success text-center mt-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Encuentra además otros productos estadísticos y cartográficos en
                    <a href="https://www.imip.org.mx/imip/publicaciones-en-linea" target="_blank" class="alert-link fw-bold">
                        la página web del Instituto Municipal de Investigación y Planeación <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>
    `,

    catalogo: `
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-success mb-4 text-center">
                    <i class="bi bi-journal-text"></i> Catálogo de Temas y Subtemas
                </h2>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Sistema de clasificación:</strong> El catálogo está organizado en temas principales y subtemas específicos para facilitar la navegación y búsqueda de información.
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-hourglass-split me-2"></i>
                    <strong>Cargando...</strong> Los datos se están obteniendo dinámicamente desde la base de datos.
                </div>

                <div class="alert alert-light text-center mt-4">
                    <i class="bi bi-lightbulb me-2"></i>
                    <strong>Nota:</strong> Este catálogo muestra la estructura organizacional de temas y subtemas. 
                    Los datos se cargan automáticamente al seleccionar esta sección.
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
            if (section === 'cartografia') {
                loadCartografia();
            } else if (section === 'catalogo') {
                loadCatalogo(); // AGREGAR esta línea
            } else if (DynamicContent[section]) {
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
    
    // Función para cargar contenido de cartografía dinámicamente
    function loadCartografia() {
        fetch('{{ route("sigem.laravel.mapas") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const mapasHtml = generateMapasHtml(data.mapas);
                    contentContainer.innerHTML = mapasHtml;
                } else {
                    contentContainer.innerHTML = '<div class="alert alert-danger">Error al cargar mapas</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                contentContainer.innerHTML = '<div class="alert alert-danger">Error de conexión</div>';
            });
    }

    // Generar HTML para los mapas
    function generateMapasHtml(mapas) {
        let html = `
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="title-row">
                        <img src="../imagenes/cartogde.png" alt="Cartografía" class="img-fluid">
                        <h2 class="text-success mb-0">
                            <i class="bi bi-map-fill"></i> Cartografía Digital
                        </h2>
                    </div>
                    
                    <p class="intro-text">En este apartado podrás encontrar mapas temáticos interactivos del Municipio de Juárez.</p>
                    
                    <div class="row">
                        <div class="col-12">
        `;
        
        // Colores para las cards
        const colores = ['success', 'info', 'warning', 'danger', 'primary', 'secondary'];
        
        mapas.forEach((mapa, index) => {
            const color = colores[index % colores.length];
            const textColor = color === 'warning' ? 'text-dark' : 'text-white';
            
            html += `
                <div class="map-section">
                    <div class="card">
                        <div class="card-header bg-${color} ${textColor}">
                            <h5 class="mb-0">
                                <i class="bi bi-map me-2"></i>
                                ${mapa.nombre_mapa}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p>${mapa.descripcion}</p>
                            <div class="text-center">
                                ${mapa.enlace ? 
                                    `<a href="${mapa.enlace}" target="_blank" class="btn btn-${color} mb-3">
                                        <i class="bi bi-box-arrow-up-right me-2"></i>Ver Mapa Interactivo
                                    </a><br>` : ''
                                }
                                <img src="../img/SIGEM_Mapas/${mapa.icono || 'mapa-placeholder.png'}" 
                                     alt="${mapa.nombre_mapa}" 
                                     class="img-fluid rounded shadow-sm"
                                     style="max-height: 300px; cursor: pointer;"
                                     onclick="window.open('${mapa.enlace}', '_blank')">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                            <div class="alert alert-info text-center mt-4">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Para ver otros mapas visita 
                                <a href="https://www.imip.org.mx/imip/node/53" target="_blank" class="alert-link fw-bold">
                                    Mapas Digitales Interactivos <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        return html;
    }
    
    // Función para cargar catálogo dinámicamente
    function loadCatalogo() {
        console.log('Cargando catálogo de cuadros estadísticos...');
        
        fetch('{{route("sigem.laravel.catalogo")}}')
            .then(response => response.json())
            .then(data => {
                console.log('Datos del catálogo:', data);
                
                if (data.success) {
                    const catalogoHtml = generateCatalogoHtml(data);
                    contentContainer.innerHTML = catalogoHtml;
                } else {
                    contentContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            Error al cargar catálogo: ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                contentContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        Error de conexión al cargar catálogo
                    </div>
                `;
            });
    }

    // Generar HTML para el catálogo - VERSIÓN SIMPLE
    function generateCatalogoHtml(data) {
        let html = `
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-success mb-4">Catálogo - Datos del Modelo</h2>
                    
                    <div class="alert alert-success">
                        <strong>Datos cargados exitosamente</strong>
                    </div>
                    
                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;">
${JSON.stringify(data, null, 2)}
                    </pre>
                </div>
            </div>
        `;
        
        return html;
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