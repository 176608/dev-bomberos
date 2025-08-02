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
                                <a href="{{ url('/sigem/partial/estadistica') }}" class="btn btn-sm btn-outline-light" title="Volver a temas">
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
                                                 onerror="this.style.display='none';">
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
                                        <i class="bi bi-arrow-left me-1"></i>Volver a temas
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ÁREA DE VISUALIZACIÓN (8 columnas) -->
                <div class="col-md-8" id="estadistica-main">
                    <!-- Contenido para la visualización de tema -->
                    <!-- ... Contenido del tema ... -->
                </div>
            </div>
            
        @elseif(isset($modo_vista) && $modo_vista === 'navegacion_subtema')
            <!-- VISTA DE SUBTEMA SELECCIONADO -->
            <!-- Similar a la estructura anterior, pero con el contenido del subtema -->
            <!-- ... Implementar la vista del subtema ... -->
            
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
</script>