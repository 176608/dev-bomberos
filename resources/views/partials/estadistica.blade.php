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
        @else
            <!-- NUEVO ENFOQUE: Incluir la vista de navegación de temas -->
            @include('partials.estadistica_navegacion')
        @endif
    </div>
</div>

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