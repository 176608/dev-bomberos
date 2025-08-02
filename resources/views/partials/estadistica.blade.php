<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="row g-0 min-vh-75">
            <!-- SIDEBAR COLAPSABLE (4 columnas) - Solo visible en modo desde_catalogo -->
            <div class="col-md-4 bg-light border-end" id="estadistica-sidebar" 
                 @if(!isset($modo_vista) || $modo_vista === 'navegacion') style="display: none;" @endif>
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

            <!-- VISTA PRINCIPAL - Ancho dinámico según el modo -->
            <div class="{{ isset($modo_vista) && $modo_vista === 'desde_catalogo' ? 'col-md-8' : 'col-12' }}" id="estadistica-main">
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

                    @if(isset($modo_vista) && $modo_vista === 'desde_catalogo')
                        <!-- MODO DESDE CATÁLOGO:  -->
                        <!-- Row 2: Selector Dinámico -->
                        <div class="row g-0 border-bottom" id="selector-dinamico">
                            <div class="col-12">
                                <!-- Selector de Tema -->
                                <div class="p-3 border-bottom">
                                    <label for="tema-selector" class="form-label fw-bold mb-2">
                                        <i class="bi bi-folder-fill me-1"></i>Tema:
                                    </label>
                                    <select id="tema-selector" class="form-select form-select-sm" onchange="cargarSubtemasPorTema(this.value)">
                                        <option value="">-- Selecciona un tema --</option>
                                        @if(isset($temas) && $temas->count() > 0)
                                            @foreach($temas as $tema)
                                                <option value="{{ $tema->tema_id }}" 
                                                        @if(isset($tema_seleccionado) && $tema_seleccionado == $tema->tema_id) selected @endif>
                                                    {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                                <!-- Breadcrumb dinámico -->
                                <div class="p-3 bg-info text-white" id="breadcrumb-container" 
                                     @if(!isset($cuadro_data)) style="display: none;" @endif>
                                    <div id="breadcrumb-info">
                                        @if(isset($cuadro_data))
                                            <span class="fw-bold">Navegación:</span>
                                            <span id="current-path">
                                                <i class="bi bi-folder me-1"></i>{{ $cuadro_data->subtema->tema->tema_titulo ?? 'Tema' }} 
                                                <i class="bi bi-chevron-right mx-2"></i>
                                                <i class="bi bi-collection me-1"></i>{{ $cuadro_data->subtema->subtema_titulo ?? 'Subtema' }}
                                                <i class="bi bi-chevron-right mx-2"></i>
                                                <i class="bi bi-file-earmark-excel me-1"></i>{{ $cuadro_data->codigo_cuadro ?? 'Cuadro' }}
                                            </span>
                                        @else
                                            <span class="fw-bold">Navegación:</span>
                                            <span id="current-path">Esperando selección...</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: Área de Visualización (Placeholder amarillo) -->
                        <div class="flex-fill">
                            <div class="h-100 d-flex flex-column">
                                <div class="p-3 bg-warning bg-opacity-25 border-bottom">
                                    <h5 class="mb-0">
                                        <i class="bi bi-table me-2"></i>Cuadro de Estadístico
                                    </h5>
                                </div>
                                <!-- Aca si llegamos desde catalogo -->
                                <div class="flex-fill p-4" id="cuadro-visualizacion">
                                    

                                </div>
                            </div>
                        </div>
                    @else Comentario: [Este ELSE no debe entrar si el modo vista es 'desde_catalogo', solo si se usa la navegación (boton del menu o desde inicio)]
                        <!-- MODO NAVEGACIÓN-->
                        <div class="flex-fill p-4">
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="mb-4">
                                        <i class="bi bi-list-task me-2"></i>Selecciona un tema para explorar:
                                    </h4>
                                    
                                    <div class="row" id="temas-grid">
                                        @if(isset($temas) && $temas->count() > 0)
                                            @foreach($temas as $index => $tema)
                                                @php 
                                                    $coloresEstilo = [
                                                        'background-color: #8FBC8F; color: black;',  // Verde suave
                                                        'background-color: #87CEEB; color: black;',  // Azul cielo
                                                        'background-color: #DDA0DD; color: black;',  // Ciruela
                                                        'background-color: #F0E68C; color: black;',  // Caqui
                                                        'background-color: #FFA07A; color: black;',  // Salmón claro
                                                        'background-color: #98FB98; color: black;'   // Verde pálido
                                                    ];
                                                    $colorTema = $coloresEstilo[$index % count($coloresEstilo)];
                                                @endphp
                                                <div class="col-lg-4 col-md-6 mb-4">
                                                    <div class="card h-100 tema-card" data-tema-id="{{ $tema->tema_id }}" onclick="seleccionarTemaNavegacion({{ $tema->tema_id }})">
                                                        <div class="card-header text-center" style="{{ $colorTema }}">
                                                            <h5 class="mb-0 fw-bold">
                                                                {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                                            </h5>
                                                        </div>
                                                        <div class="card-body text-center p-4">
                                                            <i class="bi bi-folder-fill text-muted" style="font-size: 3rem;"></i>
                                                            <p class="mt-3 mb-0">
                                                                <small class="text-muted">
                                                                    @php
                                                                        // Contar subtemas del tema actual
                                                                        $subtemasCount = $tema->subtemas ? $tema->subtemas->count() : 0;
                                                                    @endphp
                                                                    {{ $subtemasCount }} subtemas disponibles
                                                                </small>
                                                            </p>
                                                        </div>
                                                        <div class="card-footer text-center">
                                                <!--Dandole click a este boton, se va a cargar lista de subtemas en el sidebar,
                                                            En el sidebar al momento de seleccionar un subtema: la lista de cuadros del subtema 
                                                            seleccionado se muestran en la seccion de visualizacion -->
                                                            <button class="btn btn-outline-primary btn-sm">
                                                                <i class="bi bi-arrow-right me-1"></i>Explorar tema
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12">
                                                <div class="alert alert-warning text-center">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    No hay temas disponibles en este momento.
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Aca se debe visualizar la informacion del cuadro SI viene via catalogo --><div id="cuadro-info-container"></div>

    </div>
</div>

<style>
/* === ESTILOS PARA NUEVO LAYOUT === */
.min-vh-75 {
    min-height: 75vh;
}

/* Cards de temas en modo navegación */
.tema-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.tema-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #0d6efd;
}

.tema-card:hover .card-footer .btn {
    background-color: #0d6efd;
    color: white;
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

/* Lista de cuadros en área de visualización para modo navegación */
.cuadros-lista {
    max-height: 500px;
    overflow-y: auto;
}

.cuadro-item {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.cuadro-item:hover {
    border-color: #0d6efd;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
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
    
    .tema-card {
        margin-bottom: 1rem;
    }
}
</style>

<script>
// === FUNCIONES ESPECÍFICAS DE ESTADÍSTICA (SIN AFECTAR SIGEM.JS) ===
document.addEventListener('DOMContentLoaded', function() {
    // Variables del blade disponibles en JavaScript
    const cuadroId = @json($cuadro_id ?? null);
    const temaSeleccionado = @json($tema_seleccionado ?? null);
    const cuadroData = @json($cuadro_data ?? null);
    const modoVista = @json($modo_vista ?? 'navegacion');
    const vieneDesdeCategolo = cuadroId !== null;
    
    console.log('Estadística cargada:', {
        cuadroId,
        temaSeleccionado,
        vieneDesdeCategolo,
        cuadroData,
        modoVista
    });
    
    // Toggle sidebar (solo en modo desde_catalogo)
    const toggleBtn = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('estadistica-sidebar');
    
    if (toggleBtn && sidebar && modoVista === 'desde_catalogo') {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-chevron-left');
            icon.classList.toggle('bi-chevron-right');
        });
    }

    // Si viene desde catálogo, cargar datos automáticamente
    if (vieneDesdeCategolo && cuadroData && modoVista === 'desde_catalogo') {
        console.log('Detectado: viene desde catálogo');
        
        // Mostrar el breadcrumb
        const breadcrumbContainer = document.getElementById('breadcrumb-container');
        if (breadcrumbContainer) {
            breadcrumbContainer.style.display = 'block';
        }
        
        // Cargar la visualización del cuadro
        setTimeout(() => {
            actualizarVisualizacion({
                cuadro: cuadroData,
                tema_info: cuadroData.subtema?.tema || null,
                subtema_info: cuadroData.subtema || null
            });
        }, 100);
        
        // Cargar subtemas del tema seleccionado
        if (temaSeleccionado) {
            cargarSubtemasPorTema(temaSeleccionado);
        }
    }
});

// FUNCIÓN: Seleccionar tema en modo navegación
// Al hacer click en un tema, mostrar el sidebar y cargar subtemas
function seleccionarTemaNavegacion(temaId) {
    console.log('Tema seleccionado en navegación:', temaId);
    
    // Cambiar la vista para mostrar sidebar y selector
    const sidebar = document.getElementById('estadistica-sidebar');
    const mainArea = document.getElementById('estadistica-main');
    
    if (sidebar && mainArea) {
        // Mostrar sidebar
        sidebar.style.display = 'block';
        
        // Cambiar tamaño del área principal
        mainArea.className = 'col-md-8';
        
        // Cargar subtemas en el sidebar
        cargarSubtemasPorTema(temaId);
        
        // Limpiar área de visualización y preparar para mostrar cuadros
        const placeholder = document.getElementById('placeholder-inicial');
        const container = document.getElementById('cuadro-data-container');
        
        if (placeholder) {
            placeholder.innerHTML = `
                <div class="text-center">
                    <i class="bi bi-collection" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Selecciona un subtema</h4>
                    <p>Elige un subtema del menú lateral para ver sus cuadros estadísticos</p>
                </div>
            `;
        }
        if (container) container.style.display = 'none';
    }
}

// FUNCIÓN: Cargar cuadros del subtema seleccionado en el área de visualización
function cargarCuadrosSubtema(subtemaId) {
    console.log('Cargando cuadros del subtema:', subtemaId);
    
    const placeholder = document.getElementById('placeholder-inicial');
    const container = document.getElementById('cuadro-data-container');
    
    // Mostrar loading en área de visualización
    if (placeholder) {
        placeholder.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <h4 class="mt-3">Cargando cuadros...</h4>
                <p>Obteniendo cuadros estadísticos del subtema</p>
            </div>
        `;
        placeholder.style.display = 'flex';
    }
    if (container) container.style.display = 'none';
    
    // Marcar subtema como activo
    document.querySelectorAll('.subtema-nav-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-subtema-id="${subtemaId}"]`)?.classList.add('active');
    
    // Obtener cuadros via AJAX
    const baseUrl = window.SIGEM_BASE_URL || 
                   (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
    
    fetch(`${baseUrl}/cuadros-estadistica/${subtemaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.cuadros.length > 0) {
                mostrarListaCuadros(data.cuadros, data.subtema_info);
            } else {
                if (placeholder) {
                    placeholder.innerHTML = `
                        <div class="text-center">
                            <i class="bi bi-file-earmark-x" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">Sin cuadros disponibles</h4>
                            <p>Este subtema no tiene cuadros estadísticos disponibles</p>
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Error cargando cuadros:', error);
            if (placeholder) {
                placeholder.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Error al cargar</h4>
                        <p>No se pudieron cargar los cuadros del subtema</p>
                    </div>
                `;
            }
        });
}

// FUNCIÓN: Mostrar lista de cuadros en el área de visualización
function mostrarListaCuadros(cuadros, subtemaInfo) {
    const placeholder = document.getElementById('placeholder-inicial');
    const container = document.getElementById('cuadro-data-container');
    
    if (placeholder) placeholder.style.display = 'none';
    if (container) {
        container.style.display = 'block';
        
        let html = `
            <div class="mb-4">
                <h5 class="text-success">
                    <i class="bi bi-collection me-2"></i>${subtemaInfo?.subtema_titulo || 'Subtema'}
                </h5>
                <p class="text-muted">${cuadros.length} cuadros estadísticos disponibles</p>
            </div>
            <div class="cuadros-lista">
        `;
        
        cuadros.forEach(cuadro => {
            html += `
                <div class="cuadro-item p-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <i class="bi bi-file-earmark-excel me-2 text-success"></i>
                                ${cuadro.codigo_cuadro || 'N/A'}
                            </h6>
                            <p class="mb-1">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</p>
                            <small class="text-muted">${cuadro.cuadro_estadistico_subtitulo || ''}</small>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-outline-success btn-sm me-2" onclick="verCuadroDetalle(${cuadro.cuadro_estadistico_id})">
                                <i class="bi bi-eye me-1"></i>Ver
                            </button>
                            ${cuadro.excel_file ? `
                                <button class="btn btn-success btn-sm" onclick="descargarExcel('${cuadro.excel_file}')">
                                    <i class="bi bi-download"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }
}

// FUNCIÓN: Ver detalle completo de un cuadro
function verCuadroDetalle(cuadroId) {
    console.log('Ver detalle del cuadro:', cuadroId);
    
    // Llamar a sigem.js para cargar el cuadro completo
    if (typeof window.verCuadro === 'function') {
        window.verCuadro(cuadroId, '');
    } else {
        // Fallback: recargar página con el cuadro
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        window.location.href = `${baseUrl}?section=estadistica&cuadro_id=${cuadroId}`;
    }
}

// FUNCIÓN: Cargar subtemas por tema
function cargarSubtemasPorTema(temaId) {
    const navegacionContainer = document.getElementById('subtemas-navegacion');
    
    if (!temaId) {
        navegacionContainer.innerHTML = `
            <div class="p-3 text-center text-muted">
                <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
                <p class="mt-2 mb-0">Selecciona un tema para navegar</p>
            </div>
        `;
        return;
    }
    
    // Mostrar loading
    navegacionContainer.innerHTML = `
        <div class="p-3 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 mb-0">Cargando subtemas...</p>
        </div>
    `;
    
    // Obtener subtemas via AJAX
    const baseUrl = window.SIGEM_BASE_URL || 
                   (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
    
    fetch(`${baseUrl}/subtemas-estadistica/${temaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.subtemas.length > 0) {
                generarNavegacionSubtemas(data.subtemas);
            } else {
                navegacionContainer.innerHTML = `
                    <div class="p-3 text-center text-muted">
                        <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">No hay subtemas disponibles</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error cargando subtemas:', error);
            navegacionContainer.innerHTML = `
                <div class="p-3 text-center text-danger">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Error al cargar subtemas</p>
                </div>
            `;
        });
}

function generarNavegacionSubtemas(subtemas) {
    const navegacionContainer = document.getElementById('subtemas-navegacion');
    
    let html = '<div class="list-group list-group-flush">';
    
    subtemas.forEach(subtema => {
        html += `
            <div class="list-group-item list-group-item-action subtema-nav-item" 
                 data-subtema-id="${subtema.subtema_id}"
                 onclick="cargarCuadrosSubtema(${subtema.subtema_id})">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">${subtema.subtema_titulo}</h6>
                        <small class="text-muted">${subtema.cuadros_count || 0} cuadros</small>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    navegacionContainer.innerHTML = html;
}

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
    
    if (placeholder) {
        placeholder.innerHTML = `
            <div class="text-center">
                <i class="bi bi-file-earmark-excel" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Área de Visualización</h4>
                <p>Selecciona un cuadro estadístico para visualizar su contenido</p>
            </div>
        `;
        placeholder.style.display = 'flex';
    }
    if (container) container.style.display = 'none';
    if (breadcrumb) breadcrumb.textContent = 'Esperando selección...';
    
    // Limpiar selecciones activas
    document.querySelectorAll('.subtema-nav-item').forEach(item => {
        item.classList.remove('active');
    });
}

// Exponer funciones necesarias
window.cargarSubtemasPorTema = cargarSubtemasPorTema;
window.limpiarSeleccionEstadistica = limpiarSeleccionEstadistica;
window.seleccionarTemaNavegacion = seleccionarTemaNavegacion;
window.cargarCuadrosSubtema = cargarCuadrosSubtema;
window.verCuadroDetalle = verCuadroDetalle;
</script>