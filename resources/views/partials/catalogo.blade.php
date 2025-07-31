<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-journal-text"></i> Catálogo de Cuadros Estadísticos
        </h2>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Sistema de clasificación:</strong> Para su fácil localización, los diferentes cuadros que conforman el módulo estadístico del SIGEM se identifican mediante una clave conformada por el número de tema, identificador del subtema y el número de cuadro estadístico.
        </div>

        <div class="card bg-light mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Ejemplo de Clasificación
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="{{ asset('imagenes/ejem.png') }}" alt="Ejemplo clave estadística" class="img-fluid mb-3 rounded shadow-sm" style="max-width: 100%; height: auto;">
                <div class="alert alert-light">
                    <small>
                        El cuadro de "<strong>Población por Municipio</strong>" se encuentra dentro del Tema 3. Sociodemográfico en el subtema de <strong>Población</strong>.
                    </small>
                </div>
            </div>
        </div>

        <p class="text-center lead">Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos.</p>

        <div class="row mt-4 catalogo-row">
            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Estructura de Índice</h5>
                    </div>
                    <div class="card-body">
                        <div id="indice-container">
                            <div class="text-center py-3">
                                <i class="bi bi-hourglass-split"></i>
                                <p>Cargando índice...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card bg-light">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-table me-2"></i>Cuadros Estadísticos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="cuadros-container">
                            <div class="text-center py-3">
                                <i class="bi bi-hourglass-split"></i>
                                <p>Cargando cuadros...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>