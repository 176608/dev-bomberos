<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- VISTA DE TEMA SELECCIONADO CON SUBTEMAS LATERALES -->
        <div class="row g-0 min-vh-75">
            <!-- SIDEBAR PARA SUBTEMAS (4 columnas) - Con ID para manipularlo con JS -->
            <div class="col-md-4 bg-light border-end" id="sidebar-subtemas">
                <div class="d-flex flex-column h-100">
                    <!-- Header del Sidebar -->
                    <div class="p-3 bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>Subtemas de {{ $tema_seleccionado->tema_titulo }}
                            </h6>
                            <!-- Cambiado: Ahora es un botón para colapsar en lugar de enlace de regreso -->
                            <button class="btn btn-sm btn-outline-light" id="toggle-sidebar" title="Colapsar panel lateral">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Selector de Temas (tema con subtemas) -- No Deberia Imprimirme --
                    <div class="p-3 border-bottom">
                        <select class="form-select form-select-sm" id="tema-selector" onchange="cambiarTema(this.value)">
                            @foreach($temas as $tema)
                                <option value="{{ $tema->tema_id }}" {{ $tema_seleccionado->tema_id == $tema->tema_id ? 'selected' : '' }}>
                                    {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>-->

                    <!-- Navegación de Subtemas -->
                    <div class="flex-fill overflow-auto" id="subtemas-navegacion">
                        @if($tema_subtemas && $tema_subtemas->count() > 0)
                            @foreach($tema_subtemas as $tema_subtema)
                                <a href="#" 
                                   onclick="cargarSubtema({{ $tema_subtema->subtema_id }}); return false;" 
                                   class="subtema-nav-item text-decoration-none text-dark {{ isset($subtema_seleccionado) && $tema_subtema->subtema_id == $subtema_seleccionado->subtema_id ? 'active' : '' }}">
                                    @if($tema_subtema->icono_subtema)
                                        <img src="{{ asset('img/subtemas/'.$tema_subtema->icono_subtema) }}" 
                                             alt="{{ $tema_subtema->subtema_titulo }}" 
                                             class="subtema-icon"
                                             onerror="this.src='{{ asset('img/icons/folder-data.png') }}'">
                                    @else
                                        <i class="bi bi-collection text-primary fs-3 me-2"></i>
                                    @endif
                                    <div class="flex-fill">
                                        <h6 class="mb-1">{{ $tema_subtema->subtema_titulo }}</h6>
                                        <small class="text-muted">
                                            @if(isset($tema_subtema->cuadrosEstadisticos))
                                                {{ $tema_subtema->cuadrosEstadisticos->count() }} cuadros
                                            @else
                                                Ver cuadros
                                            @endif
                                        </small>
                                    </div>
                                    <i class="bi bi-chevron-right ms-2"></i>
                                </a>
                            @endforeach
                        @else
                            <div class="p-3 text-center text-muted">
                                <i class="bi bi-folder-x" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">No hay subtemas disponibles</p>
                                <a href="{{ url('/sigem?section=estadistica') }}" class="btn btn-outline-secondary btn-sm mt-3">
                                    <i class="bi bi-arrow-left me-1"></i>Volver a temas estadísticos
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- CONTENIDO DE CUADROS ESTADÍSTICOS (8 columnas) -->
            <div class="col-md-8" id="contenido-principal">
                <div class="d-flex flex-column h-100">
                    <!-- NUEVO: Encabezado con selector de temas y botón para mostrar sidebar -->
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <!-- Botón para mostrar sidebar (visible solo cuando está colapsado) -->
                            <button class="btn btn-sm btn-outline-primary me-3 d-none" id="show-sidebar" title="Mostrar panel de subtemas">
                                <i class="bi bi-list"></i>
                            </button>
                            
                            <!-- Selector de Temas (movido aquí desde el sidebar) -->
                            <div style="min-width: 250px;">
                                <select class="form-select" id="tema-selector" onchange="cambiarTema(this.value)">
                                    @foreach($temas as $tema)
                                        <option value="{{ $tema->tema_id }}" {{ $tema_seleccionado->tema_id == $tema->tema_id ? 'selected' : '' }}>
                                            {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <!-- Enlace para volver a la vista de temas (ubicado a la derecha) -->
                        <a href="{{ url('/sigem?section=estadistica') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Volver a temas
                        </a>
                    </div>

                    <!-- Encabezado de subtema seleccionado -->
                    <div class="p-3 border-bottom" id="subtema-header">
                        @if(isset($subtema_seleccionado))
                            <h5 class="mb-0">{{ $subtema_seleccionado->subtema_titulo }}</h5>
                            <p class="text-muted small mb-0">{{ $tema_seleccionado->tema_titulo }}</p>
                        @else
                            <h5 class="mb-0">{{ $tema_seleccionado->tema_titulo }}</h5>
                            <p class="text-muted small mb-0">Seleccione un subtema para ver sus cuadros</p>
                        @endif
                    </div>

                    <!-- Lista de cuadros -->
                    <div class="flex-fill overflow-auto p-3" id="cuadros-container">
                        @if(isset($cuadros) && $cuadros->count() > 0 && isset($subtema_seleccionado))
                            <div class="cuadros-lista">
                                @foreach($cuadros as $cuadro)
                                    <div class="cuadro-item p-3 mb-3 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">
                                                    <i class="bi bi-file-earmark-excel me-2 text-success"></i>
                                                    {{ $cuadro->codigo_cuadro ?? 'N/A' }}
                                                </h6>
                                                <p class="mb-1">{{ $cuadro->cuadro_estadistico_titulo ?? 'Sin título' }}</p>
                                                <small class="text-muted">{{ $cuadro->cuadro_estadistico_subtitulo ?? '' }}</small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <a href="{{ url('/sigem?section=estadistica&cuadro_id='.$cuadro->cuadro_estadistico_id) }}" class="btn btn-outline-success btn-sm me-2">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </a>
                                                @if(isset($cuadro->excel_file) && !empty($cuadro->excel_file))
                                                    <a href="{{ url('/descargas/'.$cuadro->excel_file) }}" class="btn btn-success btn-sm" download>
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-table" style="font-size: 3rem;"></i>
                                <p class="mt-3">Seleccione un subtema para ver los cuadros estadísticos disponibles.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos necesarios */
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

.cuadros-lista {
    max-height: 70vh;
    overflow-y: auto;
}

.cuadro-item {
    background-color: #fff;
    transition: all 0.3s ease;
}

.cuadro-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Estilos para el sidebar colapsable */
.sidebar-collapsed {
    width: 0;
    padding: 0;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.content-expanded {
    transition: all 0.3s ease;
}

@media (min-width: 768px) {
    .content-expanded {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Estadística por tema cargada:', {
        tema: @json($tema_seleccionado->tema_id ?? null),
        subtema: @json($subtema_seleccionado->subtema_id ?? null)
    });
    
    // Inicializar manejo del sidebar
    initSidebarToggle();
});

// Función para inicializar el toggle del sidebar
function initSidebarToggle() {
    const sidebar = document.getElementById('sidebar-subtemas');
    const content = document.getElementById('contenido-principal');
    const toggleBtn = document.getElementById('toggle-sidebar');
    const showBtn = document.getElementById('show-sidebar');
    
    // Verificar si hay preferencia guardada
    const sidebarCollapsed = localStorage.getItem('subtemas-sidebar-collapsed') === 'true';
    
    // Aplicar estado inicial según preferencia
    if (sidebarCollapsed) {
        collapseSidebar();
    }
    
    // Manejar clic en botón colapsar
    toggleBtn.addEventListener('click', function() {
        if (sidebar.classList.contains('sidebar-collapsed')) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
    });
    
    // Manejar clic en botón mostrar
    showBtn.addEventListener('click', function() {
        expandSidebar();
    });
    
    // Función para colapsar sidebar
    function collapseSidebar() {
        sidebar.classList.add('sidebar-collapsed');
        content.classList.add('content-expanded');
        toggleBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        showBtn.classList.remove('d-none');
        localStorage.setItem('subtemas-sidebar-collapsed', 'true');
    }
    
    // Función para expandir sidebar
    function expandSidebar() {
        sidebar.classList.remove('sidebar-collapsed');
        content.classList.remove('content-expanded');
        toggleBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        showBtn.classList.add('d-none');
        localStorage.setItem('subtemas-sidebar-collapsed', 'false');
    }
}

// Función para cambiar de tema
function cambiarTema(tema_id) {
    console.log('Cambiando a tema:', tema_id);
    window.location.href = '{{ url("/sigem/estadistica-por-tema") }}/' + tema_id;
}

// Función para cargar un subtema específico
function cargarSubtema(subtema_id) {
    console.log('Cargando subtema:', subtema_id);
    
    // Mostrar indicador de carga
    document.getElementById('cuadros-container').innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3">Cargando cuadros estadísticos...</p>
        </div>
    `;
    
    // Actualizar clase activa en la navegación de subtemas
    document.querySelectorAll('#subtemas-navegacion .subtema-nav-item').forEach(item => {
        item.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // Cargar cuadros del subtema mediante AJAX
    fetch('{{ url("/sigem/obtener-cuadros-estadistica") }}/' + subtema_id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar encabezado con información del subtema
                actualizarEncabezadoSubtema(subtema_id);
                
                // Renderizar cuadros
                renderizarCuadros(data.cuadros);
            } else {
                document.getElementById('cuadros-container').innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${data.message || 'Error al cargar cuadros estadísticos'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error al cargar subtema:', error);
            document.getElementById('cuadros-container').innerHTML = `
                <div class="alert alert-danger m-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error de conexión al cargar cuadros estadísticos
                </div>
            `;
        });
}

// Función para actualizar el encabezado del subtema
function actualizarEncabezadoSubtema(subtema_id) {
    // Obtener información del subtema seleccionado
    fetch('{{ url("/sigem/obtener-info-subtema") }}/' + subtema_id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const headerContainer = document.getElementById('subtema-header');
                headerContainer.innerHTML = `
                    <h5 class="mb-0">${data.subtema.subtema_titulo}</h5>
                    <p class="text-muted small mb-0">${data.subtema.tema ? data.subtema.tema.tema_titulo : ''}</p>
                `;
            }
        })
        .catch(error => console.error('Error al obtener info del subtema:', error));
}

// Función para renderizar cuadros
function renderizarCuadros(cuadros) {
    const container = document.getElementById('cuadros-container');
    
    if (!cuadros || cuadros.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-table" style="font-size: 3rem;"></i>
                <p class="mt-3">No hay cuadros estadísticos disponibles para este subtema.</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="cuadros-lista">';
    
    cuadros.forEach(cuadro => {
        html += `
            <div class="cuadro-item p-3 mb-3 border rounded">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-1">
                            <i class="bi bi-file-earmark-excel me-2 text-success"></i>
                            ${cuadro.codigo_cuadro || 'N/A'}
                        </h6>
                        <p class="mb-1">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</p>
                        ${cuadro.cuadro_estadistico_subtitulo ? `<small class="text-muted">${cuadro.cuadro_estadistico_subtitulo}</small>` : ''}
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="/sigem?section=estadistica&cuadro_id=${cuadro.cuadro_estadistico_id}" class="btn btn-outline-success btn-sm me-2">
                            <i class="bi bi-eye me-1"></i>Ver
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}
</script>