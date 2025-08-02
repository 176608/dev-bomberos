<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="row g-0 min-vh-75">
            <!-- SIDEBAR COLAPSABLE (4 columnas) - Solo visible después de seleccionar tema -->
            <div class="col-md-4 bg-light border-end" id="estadistica-sidebar" 
                 @if(!isset($modo_vista) || $modo_vista === 'navegacion') style="display: none;" @endif>
                <div class="d-flex flex-column h-100">
                    <!-- Header del Sidebar -->
                    <div class="p-3 bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>Subtemas
                            </h6>
                            <button class="btn btn-sm btn-outline-light" id="toggle-sidebar" title="Colapsar/Expandir">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Navegación de Subtemas -->
                    <div class="flex-fill overflow-auto" id="subtemas-navegacion">
                        <div class="p-3 text-center text-muted">
                            <i class="bi bi-arrow-up-circle" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Selecciona un tema para navegar</p>
                        </div>
                    </div>

                    <!-- Footer del Sidebar -->
                    <div class="p-2 border-top">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="volverATemasGrid()">
                            <i class="bi bi-arrow-left me-1"></i>Volver a Temas
                        </button>
                    </div>
                </div>
            </div>

            <!-- VISTA PRINCIPAL - Ancho dinámico según el estado -->
            <div class="col-12" id="estadistica-main">
                <div class="d-flex flex-column h-100">
                    
                    <!-- VISTA 1: Grid de Temas (inicial) -->
                    <div id="vista-temas-grid">
                        <!-- Row 1: Título y Imagen (solo en vista inicial) -->
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

                        <!-- Grid de Temas -->
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
                    </div>

                    <!-- VISTA 2: Selector + Área de Visualización (después de seleccionar tema) -->
                    <div id="vista-navegacion-tema" style="display: none;">
                        <!-- Selector de Tema -->
                        <div class="row g-0 border-bottom">
                            <div class="col-12">
                                <div class="p-3">
                                    <label for="tema-selector" class="form-label fw-bold mb-2">
                                        <i class="bi bi-folder-fill me-1"></i>Tema seleccionado:
                                    </label>
                                    <select id="tema-selector" class="form-select" onchange="cambiarTemaSelector(this.value)">
                                        <option value="">-- Selecciona un tema --</option>
                                        @if(isset($temas) && $temas->count() > 0)
                                            @foreach($temas as $tema)
                                                <option value="{{ $tema->tema_id }}">
                                                    {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Área de Visualización de Cuadros -->
                        <div class="flex-fill">
                            <div class="h-100 d-flex flex-column">
                                <div class="p-3 bg-warning bg-opacity-25 border-bottom">
                                    <h5 class="mb-0">
                                        <i class="bi bi-table me-2"></i>Cuadros Estadísticos
                                    </h5>
                                </div>
                                <div class="flex-fill p-4" id="cuadros-visualizacion">
                                    <!-- Placeholder inicial -->
                                    <div class="h-100 d-flex align-items-center justify-content-center text-muted" id="placeholder-cuadros">
                                        <div class="text-center">
                                            <i class="bi bi-collection" style="font-size: 4rem;"></i>
                                            <h4 class="mt-3">Selecciona un subtema</h4>
                                            <p>Elige un subtema del menú lateral para ver sus cuadros estadísticos</p>
                                        </div>
                                    </div>

                                    <!-- Contenedor para lista de cuadros -->
                                    <div id="cuadros-lista-container" style="display: none;">
                                        <!-- Aquí se cargarán los cuadros del subtema -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MODO DESDE CATÁLOGO (conservar funcionalidad existente) -->
                    @if(isset($modo_vista) && $modo_vista === 'desde_catalogo')
                        <div id="vista-desde-catalogo">
                            <!-- Selector Dinámico -->
                            <div class="row g-0 border-bottom" id="selector-dinamico">
                                <div class="col-12">
                                    <div class="p-3 border-bottom">
                                        <label for="tema-selector-catalogo" class="form-label fw-bold mb-2">
                                            <i class="bi bi-folder-fill me-1"></i>Tema:
                                        </label>
                                        <select id="tema-selector-catalogo" class="form-select form-select-sm" onchange="cargarSubtemasPorTema(this.value)">
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

                            <!-- Área de Visualización desde catálogo -->
                            <div class="flex-fill">
                                <div class="h-100 d-flex flex-column">
                                    <div class="p-3 bg-warning bg-opacity-25 border-bottom">
                                        <h5 class="mb-0">
                                            <i class="bi bi-table me-2"></i>Visualización de Cuadro Estadístico
                                        </h5>
                                    </div>
                                    <div class="flex-fill p-4" id="cuadro-visualizacion">
                                        <!-- Contenido desde catálogo -->
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
    transition: all 0.3s ease;
}

#estadistica-sidebar.collapsed {
    margin-left: -100%;
    width: 0;
    overflow: hidden;
}

#estadistica-main {
    transition: all 0.3s ease;
}

/* Cuando sidebar está colapsado, main area toma todo el espacio */
#estadistica-sidebar.collapsed + #estadistica-main {
    width: 100% !important;
    flex: 0 0 100% !important;
    max-width: 100% !important;
}

/* Navegación de subtemas */
.subtema-nav-item {
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
}

.subtema-nav-item:hover {
    background-color: #f8f9fa;
}

.subtema-nav-item.active {
    background-color: #e3f2fd;
    border-left: 4px solid #0d6efd;
}

.subtema-icon {
    width: 40px;
    height: 40px;
    object-fit: contain;
    margin-right: 0.75rem;
}

/* Lista de cuadros en área de visualización */
.cuadros-lista {
    max-height: 70vh;
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
#cuadros-visualizacion {
    background: linear-gradient(135deg, #fff9c4 0%, #fff8dc 100%);
    border-radius: 8px;
}

/* Transiciones de vistas */
.vista-transition {
    transition: all 0.4s ease-in-out;
}

/* Responsive */
@media (max-width: 768px) {
    #estadistica-sidebar {
        position: absolute;
        z-index: 1000;
        height: 100%;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    
    .tema-card {
        margin-bottom: 1rem;
    }
}
</style>

<script>
// === FUNCIONES ESPECÍFICAS DE ESTADÍSTICA ===

// DECLARAR FUNCIONES EN SCOPE GLOBAL PRIMERO
function seleccionarTemaNavegacion(temaId) {
    console.log('Tema seleccionado en navegación:', temaId);
    
    // Asegurar que los elementos existen antes de continuar
    const vistaGrid = document.getElementById('vista-temas-grid');
    const vistaNavegacion = document.getElementById('vista-navegacion-tema');
    const sidebar = document.getElementById('estadistica-sidebar');
    const mainArea = document.getElementById('estadistica-main');
    
    console.log('Elementos encontrados:', {
        vistaGrid: !!vistaGrid,
        vistaNavegacion: !!vistaNavegacion,
        sidebar: !!sidebar,
        mainArea: !!mainArea
    });
    
    if (vistaGrid && vistaNavegacion && sidebar && mainArea) {
        // 1. Ocultar vista de grid de temas
        vistaGrid.style.display = 'none';
        console.log('Vista grid ocultada');
        
        // 2. Mostrar vista de navegación
        vistaNavegacion.style.display = 'block';
        console.log('Vista navegación mostrada');
        
        // 3. Mostrar sidebar
        sidebar.style.display = 'block';
        console.log('Sidebar mostrado');
        
        // 4. Cambiar tamaño del área principal a 8 columnas (para hacer espacio al sidebar)
        mainArea.className = 'col-md-8';
        console.log('Main area redimensionada');
        
        // 5. Establecer el tema seleccionado en el selector
        const temaSelector = document.getElementById('tema-selector');
        if (temaSelector) {
            temaSelector.value = temaId;
            console.log('Tema establecido en selector:', temaId);
        } else {
            console.error('Selector de tema no encontrado');
        }
        
        // 6. Cargar subtemas en el sidebar
        console.log('Iniciando carga de subtemas...');
        cargarSubtemasConIconos(temaId);
        
        // 7. Forzar un reflow para asegurar que los cambios se apliquen
        setTimeout(() => {
            console.log('Transición de vista completada');
        }, 100);
        
    } else {
        console.error('Error: No se encontraron todos los elementos necesarios:', {
            vistaGrid: !!vistaGrid,
            vistaNavegacion: !!vistaNavegacion,
            sidebar: !!sidebar,
            mainArea: !!mainArea
        });
    }
}

function cargarSubtemasConIconos(temaId) {
    console.log('Iniciando carga de subtemas para tema:', temaId);
    const navegacionContainer = document.getElementById('subtemas-navegacion');
    
    if (!navegacionContainer) {
        console.error('Container de navegación no encontrado');
        return;
    }
    
    if (!temaId) {
        console.log('No hay tema ID, mostrando placeholder');
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
    console.log('Loading mostrado, iniciando fetch...');
    
    // Obtener subtemas via AJAX
    const baseUrl = window.SIGEM_BASE_URL || 
                   (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
    
    const fetchUrl = `${baseUrl}/subtemas-estadistica/${temaId}`;
    console.log('Fetching desde URL:', fetchUrl);
    
    fetch(fetchUrl)
        .then(response => {
            console.log('Respuesta recibida:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (data.success && data.subtemas && data.subtemas.length > 0) {
                console.log(`Generando navegación para ${data.subtemas.length} subtemas`);
                generarNavegacionSubtemasConIconos(data.subtemas);
                
                // Auto-cargar cuadros del primer subtema (ordenado por orden_indice)
                const subtemaOrdenados = data.subtemas.sort((a, b) => a.orden_indice - b.orden_indice);
                const primerSubtema = subtemaOrdenados[0];
                
                if (primerSubtema) {
                    console.log('Auto-cargando primer subtema:', primerSubtema.subtema_id);
                    setTimeout(() => {
                        cargarCuadrosSubtema(primerSubtema.subtema_id);
                    }, 500);
                }
            } else {
                console.log('No hay subtemas disponibles');
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
                    <small class="d-block mt-1">${error.message}</small>
                </div>
            `;
        });
}

function generarNavegacionSubtemasConIconos(subtemas) {
    console.log('Generando navegación para subtemas:', subtemas);
    const navegacionContainer = document.getElementById('subtemas-navegacion');
    
    if (!navegacionContainer) {
        console.error('Container de navegación no encontrado para generar navegación');
        return;
    }
    
    let html = '';
    
    // Ordenar subtemas por orden_indice
    const subtemasOrdenados = subtemas.sort((a, b) => a.orden_indice - b.orden_indice);
    
    subtemasOrdenados.forEach((subtema, index) => {
        const isActive = index === 0 ? 'active' : ''; // Marcar el primero como activo
        const iconoPath = subtema.icono_subtema ? `imagenes/${subtema.icono_subtema}` : 'imagenes/default-icon.png';
        
        console.log(`Generando item para subtema: ${subtema.subtema_titulo}, icono: ${iconoPath}`);
        
        html += `
            <div class="subtema-nav-item ${isActive}" 
                 data-subtema-id="${subtema.subtema_id}"
                 onclick="cargarCuadrosSubtema(${subtema.subtema_id})"
                 style="cursor: pointer;">
                <img src="${iconoPath}" 
                     alt="${subtema.subtema_titulo}" 
                     class="subtema-icon"
                     onerror="this.style.display='none'; console.log('Error cargando icono: ${iconoPath}');">
                <div class="flex-fill">
                    <h6 class="mb-1">${subtema.subtema_titulo}</h6>
                    <small class="text-muted">${subtema.cuadros_count || 0} cuadros</small>
                </div>
                <i class="bi bi-chevron-right ms-2"></i>
            </div>
        `;
    });
    
    navegacionContainer.innerHTML = html;
    console.log('Navegación de subtemas generada exitosamente');
}

function cargarCuadrosSubtema(subtemaId) {
    console.log('Cargando cuadros del subtema:', subtemaId);
    
    const placeholder = document.getElementById('placeholder-cuadros');
    const container = document.getElementById('cuadros-lista-container');
    
    console.log('Elementos para visualización:', {
        placeholder: !!placeholder,
        container: !!container
    });
    
    // Mostrar loading
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
    const subtemaElement = document.querySelector(`[data-subtema-id="${subtemaId}"]`);
    if (subtemaElement) {
        subtemaElement.classList.add('active');
        console.log('Subtema marcado como activo:', subtemaId);
    }
    
    // Obtener cuadros via AJAX
    const baseUrl = window.SIGEM_BASE_URL || 
                   (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
    
    const fetchUrl = `${baseUrl}/cuadros-estadistica/${subtemaId}`;
    console.log('Fetching cuadros desde:', fetchUrl);
    
    fetch(fetchUrl)
        .then(response => {
            console.log('Respuesta cuadros:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos de cuadros recibidos:', data);
            if (data.success && data.cuadros && data.cuadros.length > 0) {
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
                        <small class="d-block mt-1">${error.message}</small>
                    </div>
                `;
            }
        });
}

function mostrarListaCuadros(cuadros, subtemaInfo) {
    console.log('Mostrando lista de cuadros:', cuadros.length);
    const placeholder = document.getElementById('placeholder-cuadros');
    const container = document.getElementById('cuadros-lista-container');
    
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
        console.log('Lista de cuadros generada exitosamente');
    }
}

function cambiarTemaSelector(temaId) {
    console.log('Cambiando tema desde selector:', temaId);
    if (temaId) {
        cargarSubtemasConIconos(temaId);
    }
}

function volverATemasGrid() {
    console.log('Volviendo al grid de temas');
    const vistaGrid = document.getElementById('vista-temas-grid');
    const vistaNavegacion = document.getElementById('vista-navegacion-tema');
    const sidebar = document.getElementById('estadistica-sidebar');
    const mainArea = document.getElementById('estadistica-main');
    
    if (vistaGrid && vistaNavegacion && sidebar && mainArea) {
        // Transición de vistas
        vistaNavegacion.style.display = 'none';
        vistaGrid.style.display = 'block';
        
        // Ocultar sidebar
        sidebar.style.display = 'none';
        sidebar.classList.remove('collapsed');
        
        // Restaurar tamaño del área principal
        mainArea.className = 'col-12';
        
        // Limpiar selector
        const temaSelector = document.getElementById('tema-selector');
        if (temaSelector) {
            temaSelector.value = '';
        }
        
        console.log('Regreso al grid completado');
    } else {
        console.error('Error al volver: elementos no encontrados');
    }
}

function verCuadroDetalle(cuadroId) {
    console.log('Ver detalle del cuadro:', cuadroId);
    
    // Llamar a sigem.js para cargar el cuadro completo
    if (typeof window.verCuadro === 'function') {
        window.verCuadro(cuadroId, '');
    } else if (typeof window.SIGEMApp !== 'undefined' && typeof window.SIGEMApp.verCuadro === 'function') {
        window.SIGEMApp.verCuadro(cuadroId, '');
    } else {
        // Fallback: recargar página con el cuadro
        const baseUrl = window.SIGEM_BASE_URL || 
                       (window.location.pathname.includes('/m_aux/') ? '/m_aux/public/sigem' : '/sigem');
        window.location.href = `${baseUrl}?section=estadistica&cuadro_id=${cuadroId}`;
    }
}

function mostrarVistaDesdeCatalogo() {
    console.log('Mostrando vista desde catálogo');
    const vistaGrid = document.getElementById('vista-temas-grid');
    const vistaNavegacion = document.getElementById('vista-navegacion-tema');
    const vistaCatalogo = document.getElementById('vista-desde-catalogo');
    const sidebar = document.getElementById('estadistica-sidebar');
    const mainArea = document.getElementById('estadistica-main');
    
    if (vistaGrid) vistaGrid.style.display = 'none';
    if (vistaNavegacion) vistaNavegacion.style.display = 'none';
    if (vistaCatalogo) vistaCatalogo.style.display = 'block';
    if (sidebar) sidebar.style.display = 'block';
    if (mainArea) mainArea.className = 'col-md-8';
}

function cargarDatosDesdeCatalogo() {
    console.log('Cargando datos desde catálogo...');
}

function descargarExcel(fileName) {
    console.log('Descargar Excel:', fileName);
}

// EVENT LISTENERS - Ejecutar después de que las funciones estén definidas
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM completamente cargado, inicializando estadística...');
    
    // Variables del blade disponibles en JavaScript
    const cuadroId = @json($cuadro_id ?? null);
    const temaSeleccionado = @json($tema_seleccionado ?? null);
    const cuadroData = @json($cuadro_data ?? null);
    const modoVista = @json($modo_vista ?? 'navegacion');
    const vieneDesdeCategolo = cuadroId !== null;
    
    console.log('Variables de inicialización:', {
        cuadroId,
        temaSeleccionado,
        vieneDesdeCategolo,
        cuadroData,
        modoVista
    });

    // Configurar sidebar toggle
    configurarToggleSidebar();

    // Si viene desde catálogo, mostrar vista correspondiente
    if (modoVista === 'desde_catalogo') {
        mostrarVistaDesdeCatalogo();
        if (vieneDesdeCategolo && cuadroData) {
            cargarDatosDesdeCatalogo();
        }
    }
    
    console.log('Inicialización de estadística completada');
});

function configurarToggleSidebar() {
    const toggleBtn = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('estadistica-sidebar');
    const mainArea = document.getElementById('estadistica-main');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-chevron-left');
            icon.classList.toggle('bi-chevron-right');
            console.log('Sidebar toggle activado');
        });
        console.log('Toggle sidebar configurado');
    } else {
        console.log('Toggle sidebar no configurado - elementos no encontrados');
    }
}

// Exponer funciones globalmente para debugging
window.estadisticaDebug = {
    seleccionarTemaNavegacion,
    cargarSubtemasConIconos,
    cargarCuadrosSubtema,
    volverATemasGrid
};

console.log('Funciones de estadística cargadas y disponibles globalmente');
</script>