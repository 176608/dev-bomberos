<div class="card shadow-sm">
    <div class="card-body p-0">
        <!-- VISTA DE TEMA SELECCIONADO CON SUBTEMAS LATERALES -->
        <div class="row g-0 min-vh-75">
            <!-- SIDEBAR PARA SUBTEMAS - Con ID para manipularlo con JS -->
            <div class="col-md-4 bg-light border-end transition-width" id="sidebar-subtemas">
                <div class="d-flex flex-column h-100">
                    <!-- Header del Sidebar -->
                    <div class="p-3 bg-success text-white position-relative">
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
                                <a href="#" 
                                   onclick="cargarSubtema({{ $tema_subtema->subtema_id }}); return false;" 
                                   class="subtema-nav-item text-decoration-none text-dark {{ isset($subtema_seleccionado) && $tema_subtema->subtema_id == $subtema_seleccionado->subtema_id ? 'active' : '' }}"
                                   @if($tema_subtema->imagen)
                                   style="background-image: url('{{ asset('imagenes/'.$tema_subtema->imagen) }}'); background-size: cover; background-position: center;"
                                   @endif
                                >
                                    <div class="subtema-content-overlay">
                                        <!-- Ya no necesitamos la imagen aquí, es el fondo -->
                                        @if(!$tema_subtema->imagen)
                                           <i class="bi bi-collection text-success fs-3 me-2"></i>
                                        @endif
                                        <div class="flex-fill subtema-texto">
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
                    <div class="flex-fill overflow-auto p-3" id="cuadros-container">
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
                                    <div class="cuadro-item p-3 mb-3 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="mb-1">
                                                    <i class="bi bi-file-earmark-excel me-2 text-success"></i>
                                                    {{ $cuadro['codigo_cuadro'] ?? 'N/A' }}
                                                </h6>
                                                <p class="mb-1">{{ $cuadro['cuadro_estadistico_titulo'] ?? 'Sin título' }}</p>
                                                <small class="text-muted">{{ $cuadro['cuadro_estadistico_subtitulo'] ?? '' }}</small>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <a href="javascript:void(0)" onclick="SIGEMApp.verCuadro('{{ $cuadro['cuadro_estadistico_id'] }}', '{{ $cuadro['codigo_cuadro'] }}')" class="btn btn-outline-success btn-sm me-2">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </a>
                                                @if(isset($cuadro['excel_file']) && !empty($cuadro['excel_file']))
                                                    <a href="{{ url('/descargas/'.$cuadro['excel_file']) }}" class="btn btn-success btn-sm" download>
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
    position: relative;
    overflow: hidden;
    min-height: 80px; /* Altura mínima para mostrar bien la imagen */
}

/* Overlay para el contenido del subtema */
.subtema-content-overlay {
    display: flex;
    align-items: center;
    width: 100%;
    height: 100%;
    position: relative;
    z-index: 2;
    padding: 0.5rem;
    background-color: rgba(255, 255, 255, 0.85);
    border-radius: 4px;
    transition: all 0.3s ease;
}

/* Subtema al pasar el mouse */
.subtema-nav-item:hover .subtema-content-overlay {
    background-color: rgba(255, 255, 255, 0.95);
}

/* Subtema activo */
.subtema-nav-item.active {
    background-color: transparent;
    border-left: 4px solid #0ea73cff;
}

.subtema-nav-item.active .subtema-content-overlay {
    background-color: rgba(227, 242, 253, 0.9); /* Azul claro semi-transparente */
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.3);
}

/* Ajustes para cuando el sidebar está colapsado */
.sidebar-mini .subtema-nav-item {
    min-height: 60px;
    padding: 0;
}

.sidebar-mini .subtema-content-overlay {
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.9);
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
    background: linear-gradient(to right, rgba(13, 110, 253, 0.2), transparent);
}

/* Asegurar que texto sea legible */
.subtema-texto h6 {
    color: #212529;
    text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
}

.subtema-nav-item.active .subtema-texto h6 {
    color: #0d6efd;
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
    background-color: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.5);
    color: white;
    transition: all 0.3s ease;
    z-index: 10;
}

.btn-collapse:hover {
    background-color: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

/* Estilo para el botón cuando el sidebar está colapsado */
.sidebar-mini .btn-collapse {
    position: absolute;
    right: -16px;
    top: 10px;
    background-color: #0d6efd;
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
    background-color: #0d6efd;
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
    background-color: #147521ff;
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

/* Optimización para móviles */
@media (max-width: 768px) {
    .subtema-nav-item {
        background-size: contain !important; /* Imágenes más pequeñas en móvil */
    }
    
    .subtema-content-overlay {
        background-color: rgba(255, 255, 255, 0.92); /* Más opaco para mejor legibilidad */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    //console.log('Estadística por tema cargada:', {
    //    tema: @json($tema_seleccionado->tema_id ?? null),
    //    subtema: @json($subtema_seleccionado->subtema_id ?? null)
    //});
    
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
        
        // Agregar tooltip a los iconos de subtemas cuando está colapsado
        document.querySelectorAll('.subtema-nav-item').forEach(item => {
            const subtemaTitle = item.querySelector('h6')?.innerText || 'Subtema';
            item.setAttribute('title', subtemaTitle);
        });
    }
    
    // Función para expandir sidebar
    function expandSidebar() {
        sidebar.classList.remove('sidebar-mini');
        content.classList.remove('content-expanded');
        localStorage.setItem('subtemas-sidebar-collapsed', 'false');
        toggleBtn.setAttribute('title', 'Colapsar panel');
    }
}

// Función para cambiar de tema
function cambiarTema(tema_id) {
    //console.log('Cambiando a tema:', tema_id);
    window.location.href = '{{ url("/sigem/estadistica-por-tema") }}/' + tema_id;
}

// Función para cargar un subtema específico
function cargarSubtema(subtema_id) {
    
    // Mostrar indicador de carga
    document.getElementById('cuadros-container').innerHTML = `
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
                        <a href="javascript:void(0)" onclick="SIGEMApp.verCuadro('{{ $cuadro['cuadro_estadistico_id'] }}', '{{ $cuadro['codigo_cuadro'] }}')" class="btn btn-outline-success btn-sm me-2">
                            <i class="bi bi-eye me-1"></i>Ver
                        </a>
                        ${cuadro.excel_file ? `
                            <a href="javascript:void(0)" class="btn btn-success btn-sm" download>
                                <i class="bi bi-download"></i>
                            </a>
                        ` : ''}
                    </div>
                </div>
            </div>
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

// Función simplificada para crear y mostrar el modal del cuadro
function mostrarModalCuadroSimple(cuadroId, codigo) {
    // Crear un ID único para el modal
    const modalId = `modal_cuadro_${Date.now()}`;
    
    // Crear el elemento del modal
    const modalContainer = document.createElement('div');
    modalContainer.className = 'modal fade';
    modalContainer.id = modalId;
    modalContainer.setAttribute('tabindex', '-1');
    modalContainer.setAttribute('data-bs-backdrop', 'static'); // Impedir cerrar al hacer clic fuera
    
    // Aplicar un z-index extremadamente alto para garantizar que esté por encima de todo
    modalContainer.style.zIndex = '9999';
    
    // Agregar HTML con contenido mínimo
    modalContainer.innerHTML = `
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>Información del Cuadro
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="container py-5">
                        <div class="card mx-auto" style="max-width: 500px;">
                            <div class="card-body">
                                <h4 class="card-title">Datos recibidos</h4>
                                <div class="mb-3">
                                    <p><strong>ID del cuadro:</strong> ${cuadroId}</p>
                                    <p><strong>Código del cuadro:</strong> ${codigo}</p>
                                </div>
                                <button id="testJsBtn_${modalId}" class="btn btn-primary">
                                    <i class="bi bi-code-slash me-1"></i>Comprobar JavaScript
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    `;
    
    // Agregar el modal directamente al final del body para evitar conflictos de posicionamiento
    document.body.appendChild(modalContainer);
    
    // Configurar el evento para remover el modal del DOM cuando se cierre
    modalContainer.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modalContainer);
        console.log('Modal eliminado del DOM');
    });
    
    // Mostrar el modal con opciones adicionales para asegurar visibilidad
    const bootstrapModal = new bootstrap.Modal(modalContainer, {
        backdrop: 'static',  // No cerrar al hacer clic fuera
        keyboard: true,      // Permitir cerrar con ESC
        focus: true          // Enfocar el modal automáticamente
    });
    
    // También asegurarse que cualquier modal previo se cierre
    document.querySelectorAll('.modal.show').forEach(modal => {
        if (modal.id !== modalId) {
            const oldModal = bootstrap.Modal.getInstance(modal);
            if (oldModal) oldModal.hide();
        }
    });
    
    // Agregar un pequeño retraso antes de mostrar el nuevo modal
    setTimeout(() => {
        bootstrapModal.show();
        
        // Asegurarse que el backdrop tenga un z-index alto también
        const backdrops = document.querySelectorAll('.modal-backdrop');
        if (backdrops.length > 0) {
            const lastBackdrop = backdrops[backdrops.length - 1];
            lastBackdrop.style.zIndex = '9998'; // Justo debajo del modal
        }
    }, 50);
    
    // Agregar event listener al botón de prueba JS
    const testButton = document.getElementById(`testJsBtn_${modalId}`);
    if (testButton) {
        testButton.addEventListener('click', function() {
            console.log('JavaScript ejecutado en el modal para el cuadro:', codigo);
            alert(`JavaScript funciona correctamente.\nCuadro ID: ${cuadroId}\nCódigo: ${codigo}`);
        });
    }
}
</script>