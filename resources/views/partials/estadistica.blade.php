<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="row g-0 min-vh-75">
            <!-- SIDEBAR COLAPSABLE (4 columnas) -->
            <div class="col-md-4 bg-light border-end" id="estadistica-sidebar">
                <div class="d-flex flex-column h-100">
                    <!-- Header del Sidebar -->
                    <div class="p-3 bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>Navegación
                            </h6>
                            <button class="btn btn-sm btn-outline-light" id="toggle-sidebar" title="Colapsar/Expandir">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Selector de Tema -->
                    <div class="p-3 border-bottom">
                        <label for="tema-selector" class="form-label fw-bold mb-2">
                            <i class="bi bi-folder-fill me-1"></i>Tema:
                        </label>
                        <select id="tema-selector" class="form-select form-select-sm" onchange="cargarSubtemasPorTema(this.value)">
                            <option value="">-- Selecciona un tema --</option>
                        </select>
                    </div>

                    <!-- Navegación de Subtemas y Cuadros -->
                    <div class="flex-fill overflow-auto" id="subtemas-navegacion">
                        <div class="p-3 text-center text-muted">
                            <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Selecciona un tema para navegar</p>
                        </div>
                    </div>

                    <!-- Footer del Sidebar -->
                    <div class="p-2 border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="limpiarSeleccionEstadistica()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- VISTA PRINCIPAL (8 columnas) -->
            <div class="col-md-8" id="estadistica-main">
                <div class="d-flex flex-column h-100">
                    <!-- Row 1: Título y Imagen -->
                    <div class="row g-0 border-bottom">
                        <div class="col-8">
                            <div class="p-4">
                                <h2 class="text-success mb-2">
                                    <i class="bi bi-bar-chart me-2"></i>Sección Estadística
                                </h2>
                                <p class="text-muted mb-0">Consultas de información estadística relevante y precisa</p>
                            </div>
                        </div>
                        <div class="col-4 d-flex align-items-center justify-content-center bg-light">
                            <img src="imagenes/iconoesta2.png" alt="Icono Estadística" class="img-fluid" style="max-height: 80px;">
                        </div>
                    </div>

                    <!-- Row 2: Selector Dinámico -->
                    <div class="row g-0 border-bottom" id="selector-dinamico">
                        <div class="col-12">
                            <div class="p-3 bg-info text-white">
                                <div id="breadcrumb-info">
                                    <span class="fw-bold">Navegación:</span>
                                    <span id="current-path">Esperando selección...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Área de Visualización (Placeholder amarillo) -->
                    <div class="flex-fill">
                        <div class="h-100 d-flex flex-column">
                            <div class="p-3 bg-warning bg-opacity-25 border-bottom">
                                <h5 class="mb-0">
                                    <i class="bi bi-table me-2"></i>Visualización de Cuadro Estadístico
                                </h5>
                            </div>
                            <div class="flex-fill p-4" id="cuadro-visualizacion">
                                <!-- Placeholder inicial -->
                                <div class="h-100 d-flex align-items-center justify-content-center text-muted" id="placeholder-inicial">
                                    <div class="text-center">
                                        <i class="bi bi-file-earmark-excel" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3">Área de Visualización</h4>
                                        <p>Selecciona un cuadro estadístico para visualizar su contenido</p>
                                    </div>
                                </div>

                                <!-- Contenedor para datos del cuadro -->
                                <div id="cuadro-data-container" style="display: none;">
                                    <!-- Aquí se cargarán los datos del cuadro -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- VISTA DE CUADRO ESPECÍFICO -->
        <div id="cuadro-info-container">
            <!-- Se carga cuando se selecciona un cuadro o viene desde catálogo -->
        </div>

        <!-- Contenedor para mensajes -->
        <div id="info_cuadro_by_click" class="mt-4"></div>


    </div>
</div>

<style>
/* === ESTILOS PARA NUEVO LAYOUT === */
.min-vh-75 {
    min-height: 75vh;
}

/* Sidebar */
#estadistica-sidebar {
    transition: margin-left 0.3s ease;
}

#estadistica-sidebar.collapsed {
    margin-left: -75%;
    min-width: 50px;
}

#estadistica-main {
    transition: all 0.3s ease;
}

#estadistica-sidebar.collapsed + #estadistica-main {
    flex: 0 0 calc(100% + 25%);
}

/* Navegación de subtemas */
.subtema-nav-item {
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    transition: all 0.3s ease;
}

.subtema-nav-item:hover {
    background-color: #f8f9fa;
}

.subtema-nav-item.active {
    background-color: #e3f2fd;
    border-left: 4px solid #0d6efd;
}

.cuadro-nav-item {
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.cuadro-nav-item:hover {
    background-color: #fff3cd;
    padding-left: 1rem;
}

/* Área de visualización */
#cuadro-visualizacion {
    background: linear-gradient(135deg, #fff9c4 0%, #fff8dc 100%);
    border: 2px dashed #ffc107;
    border-radius: 8px;
    margin: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    #estadistica-sidebar {
        position: absolute;
        z-index: 1000;
        height: 100%;
    }
    
    #estadistica-sidebar.collapsed {
        margin-left: -100%;
    }
}
</style>

<script>
// === FUNCIONES ESPECÍFICAS DE ESTADÍSTICA (SIN AFECTAR SIGEM.JS) ===
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const toggleBtn = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('estadistica-sidebar');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-chevron-left');
            icon.classList.toggle('bi-chevron-right');
        });
    }

    // Escuchar datos de cuadros cargados desde sigem.js
    document.addEventListener('cuadroDataLoaded', function(event) {
        const data = event.detail;
        actualizarVisualizacion(data);
    });

    // Si hay parámetros en URL (viene desde catálogo)
    const urlParams = new URLSearchParams(window.location.search);
    const cuadroId = urlParams.get('cuadro_id');
    
    if (cuadroId) {
        // Mostrar datos del cuadro específico
        mostrarCuadroEspecifico(cuadroId);
    }
});

function actualizarVisualizacion(data) {
    const breadcrumb = document.getElementById('current-path');
    const container = document.getElementById('cuadro-data-container');
    const placeholder = document.getElementById('placeholder-inicial');
    
    if (data && data.cuadro) {
        // Actualizar breadcrumb
        if (breadcrumb) {
            breadcrumb.innerHTML = `
                <i class="bi bi-folder me-1"></i>${data.tema_info?.tema_titulo || 'Tema'} 
                <i class="bi bi-chevron-right mx-2"></i>
                <i class="bi bi-collection me-1"></i>${data.subtema_info?.subtema_titulo || 'Subtema'}
                <i class="bi bi-chevron-right mx-2"></i>
                <i class="bi bi-file-earmark-excel me-1"></i>${data.cuadro.codigo_cuadro || 'Cuadro'}
            `;
        }

        // Mostrar datos del cuadro
        if (container && placeholder) {
            placeholder.style.display = 'none';
            container.style.display = 'block';
            container.innerHTML = generarHtmlCuadro(data);
        }
    }
}

function generarHtmlCuadro(data) {
    console.log('Generando HTML en estadistica blade.php con datos...');
    const cuadro = data.cuadro;
    
    return `
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-excel me-2"></i>
                    ${cuadro.codigo_cuadro || 'N/A'}
                </h5>
            </div>
            <div class="card-body">
                <h6 class="card-title">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</h6>
                ${cuadro.cuadro_estadistico_subtitulo ? `<p class="text-muted">${cuadro.cuadro_estadistico_subtitulo}</p>` : ''}
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <strong>Información del archivo:</strong>
                        <ul class="list-unstyled mt-2">
                            <li><i class="bi bi-file-earmark me-2"></i>Excel: ${cuadro.excel_file || 'No disponible'}</li>
                            <li><i class="bi bi-image me-2"></i>Imagen: ${cuadro.img_name || 'No disponible'}</li>
                            <li><i class="bi bi-filetype-pdf me-2"></i>PDF: ${cuadro.pdf_file || 'No disponible'}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>Detalles:</strong>
                        <ul class="list-unstyled mt-2">
                            <li><i class="bi bi-calendar me-2"></i>Actualizado: ${cuadro.timestamp || 'No especificado'}</li>
                            <li><i class="bi bi-gear me-2"></i>Permitir gráfica: ${cuadro.permite_grafica ? 'Sí' : 'No'}</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-success me-2" onclick="descargarExcel('${cuadro.excel_file}')">
                        <i class="bi bi-download me-1"></i>Descargar Excel
                    </button>
                    ${cuadro.pdf_file ? `
                        <button class="btn btn-danger me-2" onclick="verPDF('${cuadro.pdf_file}')">
                            <i class="bi bi-file-pdf me-1"></i>Ver PDF
                        </button>
                    ` : ''}
                    ${cuadro.permite_grafica ? `
                        <button class="btn btn-info" onclick="verGrafico(${cuadro.cuadro_estadistico_id})">
                            <i class="bi bi-bar-chart me-1"></i>Ver Gráfico
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

function mostrarCuadroEspecifico(cuadroId) {
    // Esta función la llamaría sigem.js al cargar los datos
    console.log('Cargando cuadro específico:', cuadroId);
    
    // Aquí podrías hacer una llamada directa o esperar a que sigem.js dispare el evento
}

// Funciones placeholder para botones
function descargarExcel(fileName) {
    console.log('Descargar Excel:', fileName);
    // Implementar descarga
}

function verPDF(fileName) {
    console.log('Ver PDF:', fileName);
    // Implementar visualización PDF
}

function verGrafico(cuadroId) {
    console.log('Ver gráfico:', cuadroId);
    // Implementar visualización de gráfico
}

function limpiarSeleccionEstadistica() {
    const placeholder = document.getElementById('placeholder-inicial');
    const container = document.getElementById('cuadro-data-container');
    const breadcrumb = document.getElementById('current-path');
    
    if (placeholder) placeholder.style.display = 'flex';
    if (container) container.style.display = 'none';
    if (breadcrumb) breadcrumb.textContent = 'Esperando selección...';
}

// Exponer funciones necesarias
window.limpiarSeleccionEstadistica = limpiarSeleccionEstadistica;
</script>