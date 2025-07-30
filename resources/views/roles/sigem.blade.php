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

/* Cargando indicator */
.Cargando {
    text-align: center;
    padding: 40px;
    color: #2a6e48;
}

.Cargando i {
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
        <div class="Cargando">
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
        main, principal, inicio, sigem
    `,

    estadistica: `cargar 6 temas aca, modelo tema
    `,

    cartografia: `Cargar mapas aca, modelo mapa
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

    catalogo: `Debe cargar modelo de catalogo aca, lista de los temas y sus respectivos subtemas
    `
};

    // Función para cargar contenido
    function loadContent(section) {
        // Mostrar C
        contentContainer.innerHTML = `
            <div class="Cargando">
                <i class="bi bi-hourglass-split"></i>
                <p>Cargando ${section}...</p>
            </div>
        `;
        
        setTimeout(() => {
            if (section === 'cartografia') {
                loadCartografia();
            } else if (section === 'catalogo') {
                loadCatalogo();
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
                console.log('DATOS MAPAS RECIBIDOS:', data); // AGREGAR: Para debug
                
                if (data.success) {
                    const mapasHtml = generateMapasHtml(data);
                    contentContainer.innerHTML = mapasHtml;
                } else {
                    contentContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            Error al cargar mapas: ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                contentContainer.innerHTML = '<div class="alert alert-danger">Error de conexión</div>';
            });
    }

    // Generar HTML para los mapas - ACTUALIZADO para mostrar datos del modelo
    function generateMapasHtml(data) {
        let html = `
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-success mb-4">Cartografía - Datos del Modelo Mapa</h2>
                    
                    <div class="alert alert-success">
                        <strong>Total de mapas:</strong> ${data.total_mapas}<br>
                        <strong>Mensaje:</strong> ${data.message}
                    </div>
                    
                    <h4>Datos Raw del Modelo:</h4>
                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; max-height: 400px;">
${JSON.stringify(data, null, 2)}
                    </pre>
                    
                    ${data.mapas && data.mapas.length > 0 ? `
                        <h4>Mapas Individuales:</h4>
                        <div class="row">
                            ${data.mapas.map((mapa, index) => `
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <strong>Mapa #${index + 1}</strong>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>ID:</strong> ${mapa.mapa_id || 'N/A'}</p>
                                            <p><strong>Nombre:</strong> ${mapa.nombre_mapa || 'N/A'}</p>
                                            <p><strong>Sección:</strong> ${mapa.nombre_seccion || 'N/A'}</p>
                                            <p><strong>Descripción:</strong> ${mapa.descripcion || 'N/A'}</p>
                                            <p><strong>Enlace:</strong> ${mapa.enlace || 'N/A'}</p>
                                            <p><strong>Icono:</strong> ${mapa.icono || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : '<p>No hay mapas disponibles</p>'}
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
                console.log('DATOS CATÁLOGO RECIBIDOS:', data); // AGREGAR: Para debug
                
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

    // Generar HTML para el catálogo - ACTUALIZADO con cuadros estadísticos
    function generateCatalogoHtml(data) {
        let html = `
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-success mb-4 text-center">
                        <i class="bi bi-journal-text"></i> Catálogo de Cuadros Estadísticos
                    </h2>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Sistema de clasificación:</strong> Para su fácil localización, los diferentes cuadros que conforman el módulo estadístico del SIGEM se identifican mediante una clave conformada por el número de tema, identificador del subtema y el número de cuadro estadístico.
                    </div>

                    <div class="card bg-light">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-lightbulb me-2"></i>Ejemplo de Clasificación
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="../../imagenes/ejem.png" alt="Ejemplo clave estadística" class="img-fluid mb-3 rounded shadow-sm" style="max-width: 100%; height: auto;">
                            <div class="alert alert-light">
                                <small>
                                    El cuadro de "<strong>Población por Municipio</strong>" se encuentra dentro del Tema 3. Sociodemográfico en el subtema de <strong>Población</strong>.
                                </small>
                            </div>
                        </div>
                    </div>

                    <p class="text-center lead">Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos.</p>

                    <div class="row mt-4">
                        <div class="col-lg-4">
                            <div class="card bg-light">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Estructura de Índice</h5>
                                </div>
                                <div class="card-body">
                                    ${data.temas_detalle ? generateEstructuraIndice(data.temas_detalle) : '<p>No hay datos disponibles</p>'}
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card bg-light">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-table me-2"></i>Cuadros Estadísticos
                                        <span class="badge bg-light text-dark ms-2">${data.total_cuadros || 0} cuadros</span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    ${data.cuadros_estadisticos ? ListaCuadros(data.cuadros_estadisticos) : '<p>No hay cuadros disponibles</p>'}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEGUNDA SECCIÓN: Datos Raw -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="bi bi-code-square me-2"></i>Datos del Modelo (Debug)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-success mb-3">
                                        <strong>Total de temas:</strong> ${data.total_temas || 0}<br>
                                        <strong>Total de subtemas:</strong> ${data.total_subtemas || 0}<br>
                                        <strong>Total de cuadros:</strong> ${data.total_cuadros || 0}<br>
                                        <strong>Mensaje:</strong> ${data.message}
                                    </div>
                                    
                                    <h6>Datos Raw del Modelo:</h6>
                                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; max-height: 400px; font-size: 12px;">
${JSON.stringify(data, null, 2)}
                                    </pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        return html;
    }

    // NUEVA FUNCIÓN: Generar lista de cuadros estadísticos organizada por temas
    function ListaCuadros(cuadrosEstadisticos) {
        if (!cuadrosEstadisticos || cuadrosEstadisticos.length === 0) {
            return '<div class="alert alert-warning">No hay cuadros estadísticos disponibles</div>';
        }

        // Organizar cuadros por tema y subtema
        const cuadrosOrganizados = organizarCuadrosPorTema(cuadrosEstadisticos);

        let html = `
            <div style="max-height: 600px; overflow-y: auto;">
                <div class="mb-3">
                    <div class="row text-center">
                        <div class="col">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Total Cuadros</small>
                                <strong class="text-primary">${cuadrosEstadisticos.length}</strong>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Con Excel</small>
                                <strong class="text-success">${cuadrosEstadisticos.filter(c => c.excel_file && c.excel_file.trim() !== '').length}</strong>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Con PDF</small>
                                <strong class="text-danger">${cuadrosEstadisticos.filter(c => c.pdf_file && c.pdf_file.trim() !== '').length}</strong>
                            </div>
                        </div>
                        <div class="col">
                            <div class="border rounded p-2 bg-light">
                                <small class="text-muted d-block">Con Gráficas</small>
                                <strong class="text-warning">${cuadrosEstadisticos.filter(c => c.permite_grafica).length}</strong>
                            </div>
                        </div>
                    </div>
                </div>
        `;

        // Colores para los headers de temas (igual que la estructura de índice)
        const colores = [
            'background-color: #8FBC8F; color: white;', // Verde claro
            'background-color: #87CEEB; color: white;', // Azul cielo
            'background-color: #DDA0DD; color: white;', // Púrpura claro
            'background-color: #F0E68C; color: black;', // Amarillo claro
            'background-color: #FFA07A; color: white;', // Salmón
            'background-color: #98FB98; color: black;'  // Verde pálido
        ];

        Object.keys(cuadrosOrganizados).forEach((temaKey, temaIndex) => {
            const tema = cuadrosOrganizados[temaKey];
            const colorTema = colores[temaIndex % colores.length];

            html += `
                <div class="mb-4" style="border: 1px solid #ddd; border-radius: 5px;">
                    <!-- Header del tema -->
                    <div class="text-center fw-bold py-2" style="${colorTema}">
                        ${temaIndex + 1}. ${tema.nombre.toUpperCase()}
                    </div>
                    
                    <!-- Subtemas y cuadros -->
                    <div style="background-color: white;">
            `;

            Object.keys(tema.subtemas).forEach((subtemaKey, subtemaIndex) => {
                const subtema = tema.subtemas[subtemaKey];
                
                // Header del subtema
                html += `
                    <div class="px-3 py-2 bg-light border-bottom fw-bold" style="font-size: 14px;">
                        ${subtema.clave || 'N/A'} ${subtema.nombre}
                    </div>
                `;

                // Cuadros del subtema
                if (subtema.cuadros && subtema.cuadros.length > 0) {
                    subtema.cuadros.forEach((cuadro, cuadroIndex) => {
                        const bgColor = cuadroIndex % 2 === 0 ? 'background-color: #f8f9fa;' : 'background-color: white;';
                        
                        // Generar badges para archivos
                        let archivos = [];
                        if (cuadro.excel_file && cuadro.excel_file.trim() !== '') {
                            archivos.push('<span class="badge bg-success me-1" style="font-size: 9px;">XLS</span>');
                        }
                        if (cuadro.pdf_file && cuadro.pdf_file.trim() !== '') {
                            archivos.push('<span class="badge bg-danger me-1" style="font-size: 9px;">PDF</span>');
                        }
                        if (cuadro.img_name && cuadro.img_name.trim() !== '') {
                            archivos.push('<span class="badge bg-info me-1" style="font-size: 9px;">IMG</span>');
                        }
                        if (cuadro.permite_grafica) {
                            archivos.push('<span class="badge bg-warning me-1" style="font-size: 9px;">GRAF</span>');
                        }

                        html += `
                            <div class="d-flex align-items-center border-bottom py-2 px-3" style="${bgColor}">
                                <div class="me-3" style="min-width: 80px;">
                                    <code class="text-primary fw-bold">${cuadro.codigo_cuadro || 'N/A'}</code>
                                </div>
                                <div class="flex-grow-1 me-3" style="font-size: 12px;">
                                    <div class="fw-bold">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</div>
                                    ${cuadro.cuadro_estadistico_subtitulo ? `<small class="text-muted">${cuadro.cuadro_estadistico_subtitulo}</small>` : ''}
                                </div>
                                <div class="me-3" style="min-width: 120px;">
                                    ${archivos.length > 0 ? archivos.join('') : '<small class="text-muted">Sin archivos</small>'}
                                </div>
                                <div>
                                    <a href="#" class="btn btn-sm btn-outline-primary" 
                                       onclick="verCuadro(${cuadro.cuadro_estadistico_id}, '${cuadro.codigo_cuadro}')"
                                       title="Ver cuadro ${cuadro.codigo_cuadro}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html += `
                        <div class="px-3 py-2 text-muted">
                            <em>Sin cuadros estadísticos en este subtema</em>
                        </div>
                    `;
                }
            });

            html += `
                    </div>
                </div>
            `;
        });

        html += `</div>`;
        return html;
    }

    // NUEVA FUNCIÓN: Organizar cuadros por tema y subtema
    function organizarCuadrosPorTema(cuadrosEstadisticos) {
        const organizacion = {};

        cuadrosEstadisticos.forEach(cuadro => {
            // Determinar tema y subtema
            const temaInfo = cuadro.tema || cuadro.subtema?.tema || { tema_id: 'sin_tema', tema_titulo: 'Sin Tema' };
            const subtemaInfo = cuadro.subtema || { subtema_id: 'sin_subtema', subtema_titulo: 'Sin Subtema' };

            const temaKey = `tema_${temaInfo.tema_id}`;
            const subtemaKey = `subtema_${subtemaInfo.subtema_id}`;

            // Inicializar tema si no existe
            if (!organizacion[temaKey]) {
                organizacion[temaKey] = {
                    nombre: temaInfo.tema_titulo || 'Sin Tema',
                    clave: temaInfo.clave_tema || '',
                    subtemas: {}
                };
            }

            // Inicializar subtema si no existe
            if (!organizacion[temaKey].subtemas[subtemaKey]) {
                organizacion[temaKey].subtemas[subtemaKey] = {
                    nombre: subtemaInfo.subtema_titulo || 'Sin Subtema',
                    clave: subtemaInfo.clave_subtema || subtemaInfo.clave_efectiva || temaInfo.clave_tema || '',
                    cuadros: []
                };
            }

            // Agregar cuadro al subtema
            organizacion[temaKey].subtemas[subtemaKey].cuadros.push(cuadro);
        });

        return organizacion;
    }

    // NUEVA FUNCIÓN: Ver cuadro (placeholder)
    function verCuadro(cuadroId, codigo) {
        console.log(`Ver cuadro: ID=${cuadroId}, Código=${codigo}`);
        alert(`Función para ver cuadro ${codigo} (ID: ${cuadroId})`);
        // Aquí se implementará la lógica para mostrar el cuadro
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