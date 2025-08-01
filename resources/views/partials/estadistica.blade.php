<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-bar-chart me-2"></i>Sección Estadística
            <img src="imagenes/iconoesta2.png" alt="Icono Estadística">
        </h2>

        <!-- SELECTOR DE TEMA (inicialmente oculto) -->
        <div id="tema-selector-container" style="display: none;">
            <div class="row mb-4">
                <div class="col-md-8">
                    <label for="tema-selector" class="form-label fw-bold">
                        <i class="bi bi-folder-fill me-1"></i>Selecciona un tema:
                    </label>
                    <select id="tema-selector" class="form-select" onchange="cargarSubtemasPorTema(this.value)">
                        <option value="">-- Selecciona un tema --</option>
                    </select>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-4" onclick="limpiarSeleccionEstadistica()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Limpiar Selección
                    </button>
                </div>
            </div>
        </div>

        <!-- VISTA INICIAL: Menú de temas -->
        <div id="menu-temas-inicial">
            <div class="text-center mb-4">
                <p class="lead">Consultas de información estadística relevante y precisa en cuadros estadísticos, obtenidos de diferentes fuentes Municipales, Estatales, Federales, entre otros.</p>
                <p class="text-muted">Los cuadros estadísticos están categorizados en los siguientes temas:</p>
            </div>
            
            <div id="temas-grid" class="row">
                <!-- Se cargarán los temas aquí -->
            </div>
        </div>

        <!-- VISTA DE SUBTEMAS (Layout dividido) -->
        <div id="subtemas-vista" style="display: none;">
            <div class="row">
                <!-- IZQUIERDA: Lista de subtemas expandibles -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul me-2"></i>Subtemas
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div id="subtemas-lista" style="max-height: 600px; overflow-y: auto;">
                                <!-- Se cargarán los subtemas aquí -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- DERECHA: Información del subtema seleccionado -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Información del Subtema
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="subtema-info-panel">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-cursor-fill" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Selecciona un subtema para ver su información</p>
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
/* === ESTILOS PARA ESTADÍSTICA === */
.tema-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border: 2px solid transparent;
}

.tema-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: var(--bs-primary);
}

.tema-card .card-body {
    text-align: center;
    padding: 1.5rem;
}

.tema-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.subtema-item {
    border-bottom: 1px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
}

.subtema-item:hover {
    background-color: #f8f9fa;
}

.subtema-item:last-child {
    border-bottom: none;
}

.subtema-header {
    padding: 1rem;
    display: flex;
    justify-content: between;
    align-items: center;
}

.subtema-content {
    padding: 0 1rem 1rem 1rem;
    display: none;
    background-color: #f8f9fa;
}

.subtema-content.show {
    display: block;
}

.cuadro-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #dee2e6;
    cursor: pointer;
    transition: all 0.3s ease;
}

.cuadro-item:hover {
    background-color: #e3f2fd;
    padding-left: 0.5rem;
}

.cuadro-item:last-child {
    border-bottom: none;
}

.subtema-image-container {
    text-align: center;
    margin-bottom: 1rem;
}

.subtema-image {
    max-width: 100%;
    height: auto;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.subtema-placeholder {
    width: 100%;
    height: 150px;
    background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    border: 2px dashed #dee2e6;
}

.tema-colors {
    --tema-1: #8FBC8F;
    --tema-2: #87CEEB;
    --tema-3: #DDA0DD;
    --tema-4: #F0E68C;
    --tema-5: #FFA07A;
    --tema-6: #98FB98;
}
</style>