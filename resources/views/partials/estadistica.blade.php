@push('styles')
    <link rel="stylesheet" href="{{ asset('css/estadistica.css') }}">
@endpush

<div class="card shadow-sm">
    <div class="card-body p-0">
        @if(isset($modo_vista) && $modo_vista === 'desde_catalogo')
            <!-- Mantener la lógica existente para el modo catálogo -->
            <div class="row g-0 min-vh-75">
                <!-- SIDEBAR COLAPSABLE (4 columnas) - Solo visible después de seleccionar tema -->
                <div class="col-md-4 bg-light border-end" id="estadistica-sidebar">
                    <!-- Contenido del sidebar para catálogo -->
                </div>

                <!-- VISTA PRINCIPAL - Ancho dinámico según el estado -->
                <div class="col-md-8" id="estadistica-main">
                    <div class="d-flex flex-column h-100">
                        <!-- MODO DESDE CATÁLOGO -->
                        <div id="vista-desde-catalogo">
                            <!-- Selector Dinámico -->
                            <div class="row g-0 border-bottom" id="selector-dinamico">
                                <!-- Contenido del selector dinámico -->
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
                    </div>
                </div>
            </div>

            <!-- Aca se debe visualizar la informacion del cuadro SI viene via catalogo -->
            <div id="cuadro-info-container"></div>
        @elseif(isset($modo_vista) && $modo_vista === 'navegacion_tema')
            <!-- VISTA DE TEMA SELECCIONADO -->
            <div class="row g-0 min-vh-75">
                <!-- SIDEBAR PARA SUBTEMAS (4 columnas) -->
                <div class="col-md-4 bg-light border-end">
                    <div class="d-flex flex-column h-100">
                        <!-- Header del Sidebar -->
                        <div class="p-3 bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bi bi-list-ul me-2"></i>Subtemas de {{ $tema_seleccionado->tema_titulo }}
                                </h6>
                                <a href="{{ url('/sigem?section=estadistica') }}" class="btn btn-sm btn-outline-light" title="Regresar a menu anterior">
                                    <i class="bi bi-arrow-left"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Navegación de Subtemas -->
                        <div class="flex-fill overflow-auto" id="subtemas-navegacion">
                            @if($tema_seleccionado->subtemas && $tema_seleccionado->subtemas->count() > 0)
                                @foreach($tema_seleccionado->subtemas as $subtema)
                                    <a href="{{ url('/sigem/estadistica-subtema/'.$subtema->subtema_id) }}" 
                                       class="subtema-nav-item text-decoration-none text-dark">
                                        @if($subtema->icono_subtema)
                                            <img src="{{ asset('img/subtemas/'.$subtema->icono_subtema) }}" 
                                                 alt="{{ $subtema->subtema_titulo }}" 
                                                 class="subtema-icon"
                                                 onerror="this.src='{{ asset('img/icons/folder-data.png') }}'">
                                        @else
                                            <i class="bi bi-collection text-primary fs-3 me-2"></i>
                                        @endif
                                        <div class="flex-fill">
                                            <h6 class="mb-1">{{ $subtema->subtema_titulo }}</h6>
                                            <small class="text-muted">
                                                @if(isset($subtema->cuadrosEstadisticos))
                                                    {{ $subtema->cuadrosEstadisticos->count() }} cuadros
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
                                    <p class="mt-2 mb-0">No hay subtemas disponibles para este tema</p>
                                    <a href="{{ url('/sigem/partial/estadistica') }}" class="btn btn-outline-secondary btn-sm mt-3">
                                        <i class="bi bi-arrow-left me-1"></i>Volver a temas estadistica 1
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ÁREA DE VISUALIZACIÓN (8 columnas) -->
                <div class="col-md-8" id="estadistica-main">
                    <div class="d-flex flex-column h-100">
                        <!-- Cabecera con selector de tema -->
                        <div class="row g-0 border-bottom p-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="tema-selector" class="text-muted mb-2">
                                        <i class="bi bi-bookmark me-1"></i>Tema seleccionado:
                                    </label>
                                    <select id="tema-selector" class="form-select" onchange="window.location.href='{{ url('/sigem/estadistica-tema') }}/' + this.value">
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->tema_id }}" {{ $tema_seleccionado->tema_id == $tema->tema_id ? 'selected' : '' }}>
                                                {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i>Seleccione un subtema del menú lateral para ver los cuadros estadísticos disponibles
                                </p>
                            </div>
                        </div>

                        <!-- Área de Visualización de Contenido -->
                        <div class="flex-fill p-4 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <h4 class="text-muted">{{ $tema_seleccionado->tema_titulo }}</h4>
                                <p class="text-muted">Seleccione un subtema del menú lateral para ver los cuadros estadísticos disponibles.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        @elseif(isset($modo_vista) && $modo_vista === 'navegacion_subtema')
            <!-- VISTA DE SUBTEMA SELECCIONADO -->
            <div class="row g-0 min-vh-75">
                <!-- SIDEBAR PARA SUBTEMAS (4 columnas) -->
                <div class="col-md-4 bg-light border-end">
                    <div class="d-flex flex-column h-100">
                        <!-- Header del Sidebar -->
                        <div class="p-3 bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bi bi-list-ul me-2"></i>Subtemas de {{ $subtema_seleccionado->tema->tema_titulo }}
                                </h6>
                                <a href="{{ url('/sigem/estadistica-tema/'.$subtema_seleccionado->tema->tema_id) }}" class="btn btn-sm btn-outline-light" title="Volver a tema">
                                    <i class="bi bi-arrow-left"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Navegación de Subtemas -->
                        <div class="flex-fill overflow-auto" id="subtemas-navegacion">
                            @if($tema_subtemas && $tema_subtemas->count() > 0)
                                @foreach($tema_subtemas as $tema_subtema)
                                    <a href="{{ url('/sigem/estadistica-subtema/'.$tema_subtema->subtema_id) }}" 
                                       class="subtema-nav-item text-decoration-none text-dark {{ $tema_subtema->subtema_id == $subtema_seleccionado->subtema_id ? 'active' : '' }}">
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
                                    <a href="{{ url('/sigem/partial/estadistica') }}" class="btn btn-outline-secondary btn-sm mt-3">
                                        <i class="bi bi-arrow-left me-1"></i>Volver a temas estadistica 2
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ÁREA DE VISUALIZACIÓN (8 columnas) -->
                <div class="col-md-8" id="estadistica-main">
                    <div class="d-flex flex-column h-100">
                        <!-- Cabecera con selección de subtema -->
                        <div class="row g-0 border-bottom">
                            <div class="col-12">
                                <div class="p-3">
                                    <h5 class="mb-0 text-primary">
                                        <i class="bi bi-folder-fill me-2"></i>{{ $subtema_seleccionado->tema->tema_titulo }} 
                                        <i class="bi bi-chevron-right mx-2"></i>
                                        <i class="bi bi-collection me-1"></i>{{ $subtema_seleccionado->subtema_titulo }}
                                    </h5>
                                    <!-- Selector de tema -->
                                    <div class="mt-3">
                                        <select class="form-select" onchange="cambiarTema(this.value)">
                                            @foreach($temas as $tema)
                                                <option value="{{ $tema->tema_id }}" {{ $subtema_seleccionado->tema->tema_id == $tema->tema_id ? 'selected' : '' }}>
                                                    {{ $tema->orden_indice }}. {{ $tema->tema_titulo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
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
                                <div class="flex-fill p-4 cuadros-lista" id="cuadros-visualizacion"
                                     style="background: linear-gradient(135deg, #fff9c4 0%, #fff8dc 100%); border-radius: 8px;">
                                    
                                    @if($cuadros && $cuadros->count() > 0)
                                        <div class="mb-4">
                                            <h5 class="text-success">
                                                <i class="bi bi-collection me-2"></i>{{ $subtema_seleccionado->subtema_titulo }}
                                            </h5>
                                            <p class="text-muted">{{ $cuadros->count() }} cuadros estadísticos disponibles</p>
                                        </div>

                                        @foreach($cuadros as $cuadro)
                                            <div class="cuadro-item p-3 mb-3">
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
                                    @else
                                        <div class="text-center py-5">
                                            <i class="bi bi-file-earmark-x text-muted" style="font-size: 4rem;"></i>
                                            <h4 class="mt-3 text-muted">No hay cuadros disponibles</h4>
                                            <p class="text-muted">Este subtema no tiene cuadros estadísticos disponibles actualmente.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- NUEVO ENFOQUE: Incluir la vista de navegación de temas -->
            @include('partials.estadistica_navegacion')
        @endif
    </div>
</div>

<style>
/* Estilos comunes para todas las vistas */
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
</style>

<script>
// Solo mantener código para el modo catálogo
document.addEventListener('DOMContentLoaded', function() {
    // Variables del blade disponibles en JavaScript
    const cuadroId = @json($cuadro_id ?? null);
    const temaSeleccionado = @json($tema_seleccionado ?? null);
    const cuadroData = @json($cuadro_data ?? null);
    const modoVista = @json($modo_vista ?? 'navegacion');
    
    console.log('Estadística cargada:', {
        cuadroId,
        temaSeleccionado,
        modoVista
    });

    // Si viene desde catálogo, mostrar vista correspondiente
    if (modoVista === 'desde_catalogo') {
        mostrarVistaDesdeCatalogo();
        if (cuadroId && cuadroData) {
            cargarDatosDesdeCatalogo();
        }
    }
});

// FUNCIONES PARA MODO DESDE CATÁLOGO (conservar funcionalidad existente)
function mostrarVistaDesdeCatalogo() {
    console.log('Mostrando vista desde catálogo...');
    // Implementación existente...
}

function cargarDatosDesdeCatalogo() {
    console.log('Cargando datos desde catálogo...');
    // Implementar lógica existente para modo catálogo
}

// Funciones placeholder para botones
function descargarExcel(fileName) {
    console.log('Descargar Excel:', fileName);
    // Implementar descarga
}

// Añadir al script existente
function cambiarTema(tema_id) {
    // Al cambiar de tema, dirigimos directamente a estadistica-tema 
    // que automáticamente redirigirá al subtema con mayor índice
    window.location.href = '{{ url('/sigem/estadistica-tema') }}/' + tema_id;
}
</script>