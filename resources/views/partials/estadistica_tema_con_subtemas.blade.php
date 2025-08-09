<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- VISTA DE TEMA SELECCIONADO CON SUBTEMAS LATERALES -->
        <div class="row g-0 min-vh-75">
            <!-- SIDEBAR PARA SUBTEMAS - Con ID para manipularlo con JS -->
            <div class="col-md-4 bg-light border-end transition-width" id="sidebar-subtemas">
                <div class="d-flex flex-column h-100">
                    <!-- Header del Sidebar -->
                    <div class="p-3 text-white position-relative" style="background-color: #0b584fff;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 sidebar-title">
                                <i class="bi bi-list-ul me-2"></i>Subtemas de {{ $tema_seleccionado->tema_titulo }}
                            </h6>
                        </div>
                        
                        <!-- Botón toggle con mejor posicionamiento -->
                        <div class="toggle-button-container">
                            <button class="btn-toggle-sidebar-fixed" id="toggle-sidebar" title="Expandir/Colapsar panel">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Navegación de Subtemas -->
                    <div class="flex-fill overflow-auto sidebar-content" id="subtemas-navegacion">
                        @if($tema_subtemas && $tema_subtemas->count() > 0)
                            @foreach($tema_subtemas as $tema_subtema)
                                <a href="javascript:void(0)" 
                                   onclick="cargarSubtema({{ $tema_subtema->subtema_id }}); return false;" 
                                   class="subtema-nav-item text-decoration-none text-dark {{ isset($subtema_seleccionado) && $tema_subtema->subtema_id == $subtema_seleccionado->subtema_id ? 'active' : '' }}"
                                >
                                    <div class="row g-0 w-100 align-items-center">
                                        <!-- Columna de la imagen (6 columnas) -->
                                        <div class="col-6 subtema-image-container">
                                            @if($tema_subtema->imagen)
                                                <img src="{{ asset('imagenes/subtemas_u/'.$tema_subtema->imagen) }}" 
                                                     alt="{{ $tema_subtema->subtema_titulo }}" 
                                                     class="subtema-image img-fluid">
                                            @else
                                                <div class="no-image-placeholder d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-collection text-success fs-3"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Columna del texto (6 columnas) -->
                                        <div class="col-6 subtema-texto">
                                            <div class="d-flex align-items-center justify-content-between w-100">
                                                <h6 class="mb-1">{{ $tema_subtema->subtema_titulo }}</h6>
                                                <i class="bi bi-chevron-right ms-2"></i>
                                            </div>
                                        </div>
                                    </div>
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

            <!-- CONTENIDO DE CUADROS ESTADÍSTICOS -->
            <div class="col-md-8 transition-width" id="contenido-principal">
                <div class="d-flex flex-column h-100">
                    <!-- Encabezado con selector de temas y botón para mostrar sidebar -->
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <!-- Botón para mostrar sidebar (visible solo cuando está colapsado) -->
                            <button class="btn btn-sm btn-outline-success me-3 d-none" id="show-sidebar" title="Mostrar panel de subtemas">
                                <i class="bi bi-list"></i>
                            </button>
                            
                            <!-- Selector de Temas -->
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
                        
                        <!-- Enlace para volver a la vista de temas -->
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
                    <div class="flex-fill overflow-auto p-3" id="cuadros-container-estadistica">
                        @php
                            // Función para extraer el número del índice de un código de cuadro
                            function extraerNumeroIndice($codigoCuadro) {
                                // Verificar si el código está vacío
                                if (empty($codigoCuadro)) {
                                    return PHP_FLOAT_MAX; // Valor alto para que queden al final
                                }
                                
                                // Patrón para extraer el número del índice (último componente después del punto)
                                if (preg_match('/\.(\d+(?:\.\d+)*)$/', $codigoCuadro, $matches)) {
                                    return floatval($matches[1]); // Convertir a número para ordenamiento correcto
                                } else if (preg_match('/(\d+(?:\.\d+)*)$/', $codigoCuadro, $matches)) {
                                    // Si no hay punto pero termina en número
                                    return floatval($matches[1]);
                                }
                                
                                return PHP_FLOAT_MAX; // Por defecto al final
                            }
                            
                            // Ordenar cuadros por su número de índice
                            if (isset($cuadros) && $cuadros->count() > 0) {
                                $cuadrosArray = $cuadros->toArray();
                                usort($cuadrosArray, function($a, $b) {
                                    $numA = extraerNumeroIndice($a['codigo_cuadro'] ?? '');
                                    $numB = extraerNumeroIndice($b['codigo_cuadro'] ?? '');
                                    return $numA <=> $numB;
                                });
                                $cuadros = collect($cuadrosArray);
                            }
                        @endphp

                        @if(isset($cuadros) && $cuadros->count() > 0 && isset($subtema_seleccionado))
                            <div class="cuadros-lista">
                                @foreach($cuadros as $cuadro)
                                    <a href="javascript:void(0)" 
                                       onclick="SIGEMApp.verCuadro('{{ $cuadro['cuadro_estadistico_id'] }}', '{{ $cuadro['codigo_cuadro'] }}')" 
                                       class="cuadro-item p-3 mb-3 border rounded text-decoration-none d-block">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="fw-bold d-block text-success">
                                                    <i class="bi bi-file-earmark-excel me-2"></i>
                                                    {{ $cuadro['codigo_cuadro'] ?? 'N/A' }}
                                                </span>
                                                <span class="mb-1 d-block text-dark">{{ $cuadro['cuadro_estadistico_titulo'] ?? 'Sin título' }}</span>
                                                @if(isset($cuadro['cuadro_estadistico_subtitulo']) && !empty($cuadro['cuadro_estadistico_subtitulo']))
                                                    <small class="text-muted d-block">{{ $cuadro['cuadro_estadistico_subtitulo'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
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
    display: block;
    padding: 0;
    position: relative;
    overflow: hidden;
    min-height: 80px;
}

/* Estilos para la imagen del subtema */
.subtema-image-container {
    height: 80px;
    overflow: hidden;
    position: relative;
}

.subtema-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.no-image-placeholder {
    width: 100%;
    height: 100%;
    background-color: #f8f9fa;
}

/* Estilos para el texto del subtema */
.subtema-texto {
    padding: 0.75rem 0.5rem;
    transition: all 0.3s ease;
}

.subtema-texto h6 {
    font-size: 0.9rem;
    line-height: 1.2;
    margin: 0;
    color: #212529;
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Efecto hover */
.subtema-nav-item:hover {
    background-color: rgba(77, 150, 80, 0.1);
}

.subtema-nav-item:hover .subtema-image {
    transform: scale(0.80);
}

/* Estado activo */
.subtema-nav-item.active {
    background-color: rgba(77, 150, 80, 0.1);
    border-left: 4px solid #0b584fff;
}

/* ======== ESTILOS PARA SIDEBAR COLAPSADO ======== */
/* Cambios para cuando el sidebar está colapsado */
.sidebar-mini .subtema-nav-item {
    min-height: 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 5px 0;
    position: relative;
    /*left: 1px;  Pequeño ajuste para centrar en el sidebar colapsado */
}

/* Ocultar la estructura de columnas cuando está colapsado */
.sidebar-mini .subtema-nav-item .row {
    flex-direction: column;
}

/* Ajuste mejorado para el contenedor de imagen cuando el sidebar está colapsado */
.sidebar-mini .subtema-image-container {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin: 0 auto;
    overflow: hidden;
    position: relative;
    /*left: -1px; Pequeño ajuste para alinear con el diseño */
}

/* Ajuste para la imagen cuando el sidebar está colapsado */
.sidebar-mini .subtema-image {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
    position: relative;
    left: 0;
    top: 0;
    object-position: right center;
}

/* Ajuste para el placeholder cuando no hay imagen */
.sidebar-mini .no-image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    position: relative;
    /*left: -1px;  Pequeño ajuste para alinear con el diseño */
}

/* Tooltip para modo colapsado */
.sidebar-mini .subtema-nav-item {
    position: relative;
}

/* Efecto visual para item activo */
.subtema-nav-item.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background-color: #0c7912;
    opacity: 1;
    z-index: 3;
}

.sidebar-mini .subtema-nav-item.active::before {
    width: 100%;
    height: 4px;
    top: 0;
    left: 0;
    background-color: #0c7912;
    opacity: 0.4;
}

/* Ajustes para cuando el sidebar está colapsado */
.sidebar-mini .subtema-nav-item {
    min-height: 60px;
    padding: 0;
}

.sidebar-mini .subtema-content-overlay {
    justify-content: center;
    background-color: rgba(41, 35, 35, 0.09);
}

/* Efecto visual mejorado */
.subtema-nav-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to right, rgba(0,0,0,0.1), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 1;
}

.subtema-nav-item:hover::before {
    opacity: 1;
}

.subtema-nav-item.active::before {
    opacity: 1;
    background: linear-gradient(to right, rgba(84, 151, 99, 0.72), transparent);
}

/* Asegurar que texto sea legible */
.subtema-texto h6 {
    color: #212529;
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

.cuadros-lista {
    max-height: 70vh;
    overflow-y: auto;
}

.cuadro-item {
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    border: 1px solid #dee2e6;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}

.cuadro-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Estilos para el sidebar colapsable */
.transition-width {
    transition: all 0.3s ease;
}

/* Estado colapsado del sidebar */
#sidebar-subtemas.sidebar-mini {
    flex: 0 0 auto;
    width: 60px;
    overflow: hidden;
}

#contenido-principal.content-expanded {
    flex: 0 0 auto;
    width: calc(100% - 60px);
}

/* Ajustes para elementos del sidebar cuando está colapsado */
.sidebar-mini .sidebar-title {
    display: none;
}

.sidebar-mini .sidebar-content {
    overflow: hidden;
}

.sidebar-mini .subtema-texto {
    display: none;
}

.sidebar-mini .subtema-nav-item {
    padding: 15px 0;
    justify-content: center;
}

.sidebar-mini .subtema-icon {
    margin: 0;
    width: 30px;
    height: 30px;
}

/* Estilo para el botón de toggle cuando está colapsado */
.sidebar-mini #toggle-sidebar {
    position: absolute;
    right: -15px;
    top: 10px;
    z-index: 10;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-color: #0c7912ff;
    color: #157415ff;
}

.sidebar-mini #toggle-sidebar i {
    transform: rotate(180deg);
}

/* Estilos específicos para el botón de colapso */
.btn-collapse {
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0);
    border: 1px solid rgba(255, 255, 255, 0.5);
    color: white;
    transition: all 0.3s ease;
    z-index: 10;
}

.btn-collapse:hover {
    background-color: rgba(25, 138, 76, 0.44);
    transform: scale(1.1);
}

/* Estilo para el botón cuando el sidebar está colapsado */
.sidebar-mini .btn-collapse {
    position: absolute;
    right: -16px;
    top: 10px;
    background-color: #168036ff;
    border: 2px solid white;
    color: white;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}

.sidebar-mini .btn-collapse i {
    transform: rotate(180deg);
}

/* Estilos para el botón unificado de toggle del sidebar */
.btn-toggle-sidebar {
    position: absolute;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #0b9936ff;
    color: white;
    border: 2px solid white;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    z-index: 1050; /* Valor muy alto */
    transition: all 0.3s ease;
    cursor: pointer;
    top: 15px;
    right: -18px; /* Un poco más hacia afuera */
}

/* Estilos mejorados para el botón toggle */
.toggle-button-container {
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    width: 0;
    z-index: 1000; /* Valor muy alto para superar cualquier otro elemento */
}

.btn-toggle-sidebar-fixed {
    position: absolute;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: #0c5716ff;
    color: white;
    border: 2px solid white;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    cursor: pointer;
    transition: all 0.3s ease;
    right: -18px;
    top: 15px;
}

.btn-toggle-sidebar-fixed:hover {
    transform: scale(1.15);
    box-shadow: 0 0 15px rgba(40, 182, 123, 0.5);
}

/* Cuando el sidebar está colapsado, el icono rota */
.sidebar-mini .btn-toggle-sidebar-fixed i {
    transform: rotate(180deg);
}

/* Asegurarse que el sidebar no corta el botón */
#sidebar-subtemas {
    position: relative;
    overflow: visible !important; /* Importante para que el botón no se corte */
}

/* Asegurarse que el botón no es afectado por cambios en el sidebar */
.sidebar-mini .btn-toggle-sidebar-fixed {
    right: -18px; /* Mantener misma posición cuando está colapsado */
}

/* Asegurar que el botón siempre esté visible y por encima de otros elementos */
.btn-toggle-sidebar-fixed {
    z-index: 50;
}

/* Asegurar que los modales estén por encima de todo */
.modal {
    z-index: 9999 !important;
}

.modal-backdrop {
    z-index: 9998 !important;
}

/* Hacer que el fondo del modal sea más oscuro para destacarlo mejor */
.modal-backdrop.show {
    opacity: 0.7;
}

/* Asegurar que el contenido del modal no se desborde */
.modal-content {
    max-height: 100vh;
    overflow-y: auto;
}

/* Estilos para la tabla Excel */
.excel-table {
    border-collapse: collapse;
    width: 100%;
}

.excel-table td {
    padding: 6px 8px;
    border: 1px solid #dee2e6;
    min-width: 50px;
}

.excel-table .empty-cell {
    background-color: #f9f9f9;
}

.excel-viewer-container {
    overflow-x: auto;
    max-height: 70vh;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar manejo del sidebar
    initSidebarToggle();
});

// Función para inicializar el toggle del sidebar
function initSidebarToggle() {
    const sidebar = document.getElementById('sidebar-subtemas');
    const content = document.getElementById('contenido-principal');
    const toggleBtn = document.getElementById('toggle-sidebar');
    
    // Verificar si hay preferencia guardada
    const sidebarCollapsed = localStorage.getItem('subtemas-sidebar-collapsed') === 'true';
    
    // Aplicar estado inicial según preferencia
    if (sidebarCollapsed) {
        collapseSidebar();
    }
    
    // Manejar clic en botón toggle
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (sidebar.classList.contains('sidebar-mini')) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
    });
    
    // Función para colapsar sidebar
    function collapseSidebar() {
        sidebar.classList.add('sidebar-mini');
        content.classList.add('content-expanded');
        localStorage.setItem('subtemas-sidebar-collapsed', 'true');
        toggleBtn.setAttribute('title', 'Expandir panel');
        
        // Agregar tooltip a los elementos cuando está colapsado
        document.querySelectorAll('.subtema-nav-item').forEach(item => {
            const subtemaTitle = item.querySelector('.subtema-texto h6')?.innerText || 'Subtema';
            item.setAttribute('title', subtemaTitle);
            
            // Bootstrap tooltip si está disponible
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                new bootstrap.Tooltip(item, {
                    placement: 'right',
                    trigger: 'hover',
                    container: 'body'
                });
            }
        });
    }
    
    // Función para expandir sidebar
    function expandSidebar() {
        sidebar.classList.remove('sidebar-mini');
        content.classList.remove('content-expanded');
        localStorage.setItem('subtemas-sidebar-collapsed', 'false');
        toggleBtn.setAttribute('title', 'Colapsar panel');
        
        // Eliminar tooltips
        document.querySelectorAll('.subtema-nav-item').forEach(item => {
            item.removeAttribute('title');
            
            // Destruir tooltip de Bootstrap si existe
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltip = bootstrap.Tooltip.getInstance(item);
                if (tooltip) {
                    tooltip.dispose();
                }
            }
        });
    }
}

// Función para cambiar de tema
function cambiarTema(tema_id) {
    //console.log('Cambiando a tema:', tema_id);
    window.location.href = '{{ url("/sigem/estadistica-por-tema") }}/' + tema_id;
}

// Función para cargar un subtema específico
function cargarSubtema(subtema_id) {
    console.log('Cargando subtema ID:', subtema_id);
    
    // Mostrar indicador de carga
    document.getElementById('cuadros-container-estadistica').innerHTML = `
        <div class="text-center p-5">
            <div class="spinner-border text-success" role="status">
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
    
    // 1. PRIMERO actualizar el encabezado (para respuesta inmediata)
    const subtemaSeleccionado = event.currentTarget.querySelector('.subtema-texto h6')?.innerText || 'Subtema';
    const temaSeleccionado = document.querySelector('#tema-selector option:checked')?.text || 'Tema';
    
    // Actualizar de forma preliminar mientras se carga la información completa
    const headerContainer = document.getElementById('subtema-header');
    if (headerContainer) {
        headerContainer.innerHTML = `
            <h5 class="mb-0">${subtemaSeleccionado}</h5>
            <p class="text-muted small mb-0">${temaSeleccionado}</p>
        `;
    }
    
    // 2. LUEGO cargar los cuadros del subtema mediante AJAX
    fetch('{{ url("/sigem/obtener-cuadros-estadistica") }}/' + subtema_id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar encabezado con información completa del subtema
                actualizarEncabezadoSubtema(subtema_id);
                
                // Renderizar cuadros
                renderizarCuadros(data.cuadros);
            } else {
                document.getElementById('cuadros-container-estadistica').innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${data.message || 'Error al cargar cuadros estadísticos'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error al cargar subtema:', error);
            document.getElementById('cuadros-container-estadistica').innerHTML = `
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
    const container = document.getElementById('cuadros-container-estadistica');
    
    if (!cuadros || cuadros.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-table" style="font-size: 3rem;"></i>
                <p class="mt-3">No hay cuadros estadísticos disponibles para este subtema.</p>
            </div>
        `;
        return;
    }
    
    // Función para extraer el número del índice después del último punto
    function extraerNumeroIndice(codigoCuadro) {
        // Verificar si el código está vacío
        if (!codigoCuadro) {
            return Number.MAX_VALUE; // Valor alto para que queden al final
        }
        
        // Patrón para extraer el número del índice (último componente después del punto)
        const match = codigoCuadro.match(/\.(\d+(?:\.\d+)*)$/);
        if (match) {
            return parseFloat(match[1]); // Convertir a número para ordenamiento correcto
        } else {
            const matchEnd = codigoCuadro.match(/(\d+(?:\.\d+)*)$/);
            if (matchEnd) {
                // Si no hay punto pero termina en número
                return parseFloat(matchEnd[1]);
            }
        }
        
        return Number.MAX_VALUE; // Por defecto al final
    }
    
    // Ordenar los cuadros por el número de índice
    const cuadrosOrdenados = [...cuadros].sort((a, b) => {
        const numA = extraerNumeroIndice(a.codigo_cuadro || '');
        const numB = extraerNumeroIndice(b.codigo_cuadro || '');
        return numA - numB;
    });
    
    let html = '<div class="cuadros-lista">';
    
    cuadrosOrdenados.forEach(cuadro => {
        html += `
            <a href="javascript:void(0)" 
               onclick="SIGEMApp.verCuadro('${cuadro.cuadro_estadistico_id}', '${cuadro.codigo_cuadro || ''}')" 
               class="cuadro-item p-3 mb-3 border rounded text-decoration-none d-block">
                <div class="row align-items-center">
                    <div class="col-12">
                        <span class="fw-bold d-block text-success">
                            <i class="bi bi-file-earmark-excel me-2"></i>
                            ${cuadro.codigo_cuadro || 'N/A'}
                        </span>
                        <span class="mb-1 d-block text-dark">${cuadro.cuadro_estadistico_titulo || 'Sin título'}</span>
                        ${cuadro.cuadro_estadistico_subtitulo ? `<small class="text-muted d-block">${cuadro.cuadro_estadistico_subtitulo}</small>` : ''}

                    </div>
                </div>
            </a>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Escuchar el evento personalizado desde sigem.js
document.addEventListener('verCuadroEstadistico', function(event) {
    const { cuadroId, codigo } = event.detail;
    console.log(`Blade: Recibiendo evento para mostrar cuadro: ID=${cuadroId}, Código=${codigo}`);
    mostrarModalCuadroSimple(cuadroId, codigo);
});
/*
Imprime el MODAL
*/
// Función simplificada para crear y mostrar el modal del cuadro
function mostrarModalCuadroSimple(cuadroId, codigo) {
    // Primero, obtén la información del cuadro mediante una petición AJAX
    fetch(`/sigem/obtener-excel-cuadro/${cuadroId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message || 'No se pudo obtener información del cuadro');
            }
            
            // Extraer la información del cuadro
            const cuadro = data.cuadro;
            
            // Crear un ID único para el modal
            const modalId = `modal_cuadro_${Date.now()}`;
            
            // Crear el elemento del modal
            const modalContainer = document.createElement('div');
            modalContainer.className = 'modal fade';
            modalContainer.id = modalId;
            modalContainer.setAttribute('tabindex', '-1');
            modalContainer.setAttribute('data-bs-backdrop', 'static');
            modalContainer.style.zIndex = '9999';
            
            // Determinar qué opciones mostrar según los archivos disponibles
            const tieneExcel = cuadro.excel_file && cuadro.excel_file.length > 0;
            const tienePdf = cuadro.pdf_file && cuadro.pdf_file.length > 0;
            const permiteGrafica = cuadro.permite_grafica;
            
            // Construir los botones para las diferentes opciones
            let botonesOpciones = '';
            
            if (tieneExcel) {
                botonesOpciones += `
                    <a href="/archivos/excel/${cuadro.excel_file}" class="btn btn-success me-2" download>
                        <i class="bi bi-file-earmark-excel me-1"></i>Descargar Excel
                    </a>
                `;
            }
            
            if (tienePdf) {
                botonesOpciones += `
                    <a href="/archivos/pdf/${cuadro.pdf_file}" class="btn btn-danger me-2" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Ver PDF
                    </a>
                `;
            }
            
            if (permiteGrafica) {
                botonesOpciones += `
                    <button class="btn btn-primary" onclick="mostrarGraficaCuadro(${cuadroId})">
                        <i class="bi bi-bar-chart me-1"></i>Ver Gráfica
                    </button>
                `;
            }
            
            // Agregar HTML con contenido estructurado
            modalContainer.innerHTML = `
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-table me-2"></i>${cuadro.codigo_cuadro} - ${cuadro.cuadro_estadistico_titulo}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body" id="excel-container-${modalId}">
                            <!-- El contenido del Excel se cargará aquí -->
                            <div class="text-center py-5">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-3">Cargando contenido del cuadro estadístico...</p>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <div>
                                ${botonesOpciones}
                            </div>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            `;
            
            // Agregar el modal al body
            document.body.appendChild(modalContainer);
            
            // Configurar el evento para remover el modal del DOM cuando se cierre
            modalContainer.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modalContainer);
            });
            
            // Mostrar el modal
            const bootstrapModal = new bootstrap.Modal(modalContainer);
            bootstrapModal.show();
            
            // Si tiene archivo Excel, cargarlo en el modal
            if (tieneExcel) {
                // Primero asegúrate de que la biblioteca SheetJS esté cargada
                if (typeof XLSX === 'undefined') {
                    // Cargar dinamicamente SheetJS si no está disponible
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
                    script.onload = () => {
                        // Una vez cargada la librería, cargar el Excel
                        cargarExcelEnModal(modalId, cuadro.excel_file);
                    };
                    document.head.appendChild(script);
                } else {
                    // Si ya está cargada, usar directamente
                    cargarExcelEnModal(modalId, cuadro.excel_file);
                }
            } else {
                // Si no tiene Excel, mostrar un mensaje
                document.getElementById(`excel-container-${modalId}`).innerHTML = `
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Este cuadro no tiene un archivo Excel asociado.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error al cargar el cuadro:', error);
            alert(`Error: ${error.message}`);
        });
}
</script>