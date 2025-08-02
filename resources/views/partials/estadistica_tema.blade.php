<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="row g-0 min-vh-75">
            <!-- SIDEBAR PARA SUBTEMAS (4 columnas) -->
            <div class="col-md-4 bg-light border-end">
                <div class="d-flex flex-column h-100">
                    <!-- Header del Sidebar -->
                    <div class="p-3 bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>Subtemas de {{ $tema->tema_titulo }}
                            </h6>
                            <a href="{{ url('/sigem/partial/estadistica') }}" class="btn btn-sm btn-outline-light" title="Volver a temas estadistica tema 0">
                                <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Navegación de Subtemas -->
                    <div class="flex-fill overflow-auto" id="subtemas-navegacion">
                        @if($tema->subtemas && $tema->subtemas->count() > 0)
                            @foreach($tema->subtemas as $subtema)
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
                                    <i class="bi bi-arrow-left me-1"></i>Volver a temas estadistica tema 1
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ÁREA DE VISUALIZACIÓN (8 columnas) -->
            <div class="col-md-8" id="estadistica-main">
                <div class="d-flex flex-column h-100">
                    <!-- Cabecera con selección de tema -->
                    <div class="row g-0 border-bottom">
                        <div class="col-12">
                            <div class="p-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="bi bi-folder-fill me-2"></i>{{ $tema->tema_titulo }}
                                </h5>
                                <p class="text-muted mt-2 mb-0">
                                    <small>Selecciona un subtema del menú lateral para visualizar sus cuadros estadísticos.</small>
                                </p>
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
                            <div class="flex-fill p-4" id="cuadros-visualizacion" 
                                 style="background: linear-gradient(135deg, #fff9c4 0%, #fff8dc 100%); border-radius: 8px;">
                                <!-- Placeholder inicial -->
                                <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                                    <div class="text-center">
                                        <i class="bi bi-collection" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3">Selecciona un subtema</h4>
                                        <p>Elige un subtema del menú lateral para ver sus cuadros estadísticos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
</style>