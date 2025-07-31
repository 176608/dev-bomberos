<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-journal-text"></i> Catálogo de Cuadros Estadísticos
        </h2>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Sistema de clasificación:</strong> Para su fácil localización, los diferentes cuadros que conforman el módulo estadístico del SIGEM se identifican mediante una clave conformada por el número de tema, identificador del subtema y el número de cuadro estadístico.
        </div>

        <div class="card bg-light">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Ejemplo de Clasificación
                </h5>
            </div>
            <div class="card-body text-center">
                <img src="../../imagenes/ejem.png" alt="Ejemplo clave estadística" class="img-fluid mb-3 rounded shadow-sm" style="max-width: 100%; height: auto;">
                <div class="alert alert-light">
                    <small>
                        El cuadro de "<strong>Población por Municipio</strong>" se encuentra dentro del Tema 3. Sociodemográfico en el subtema de <strong>Población</strong>.
                    </small>
                </div>
            </div>
        </div>

        <p class="text-center lead">Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos.</p>

        <!-- USAR ESTRUCTURA EXACTA QUE FUNCIONA EN SIGEM_ADMIN -->
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Estructura de Índice</h5>
                    </div>
                    <div class="card-body">
                        <div id="indice-container">
                            <div class="loading-state">
                                <div class="loading-spinner"></div>
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
                            <span id="cuadros-count" class="badge bg-light text-dark ms-2">0 cuadros</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="cuadros-container">
                            <div class="loading-state">
                                <div class="loading-spinner"></div>
                                <p>Cargando cuadros...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* === ESTILOS COPIADOS DE SIGEM_ADMIN QUE FUNCIONAN === */
.loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    color: #6c757d;
}

.loading-spinner {
    width: 24px;
    height: 24px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #2a6e48;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* === EFECTOS DE FOCUS COPIADOS DEL SIGEM_ADMIN === */
.highlight-focus {
    background-color: #fff3cd !important;
    border: 2px solid #ffc107 !important;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
    animation: pulseHighlight 1s ease-in-out;
}

@keyframes pulseHighlight {
    0% { 
        transform: scale(1); 
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
    }
    50% { 
        transform: scale(1.02); 
        box-shadow: 0 0 25px rgba(255, 193, 7, 0.8);
    }
    100% { 
        transform: scale(1); 
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
    }
}

/* === EFECTOS HOVER COPIADOS DEL SIGEM_ADMIN === */
.indice-tema-header:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
}

.indice-subtema-row:hover {
    background-color: #e8f4f8 !important;
    transform: translateX(5px) !important;
}

/* === ALTURAS IGUALES COPIADAS DEL SIGEM_ADMIN === */
#indice-container, #cuadros-container {
    overflow-y: auto;
}

.catalogo-row {
    display: flex;
    align-items: stretch;
}

.catalogo-row .card {
    height: 100%;
}

.catalogo-row .card-body {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* === RESPONSIVE COPIADO DEL SIGEM_ADMIN === */
@media (max-width: 768px) {
    .catalogo-row .card-body > div {
        height: 400px;
    }
}

@media (max-width: 576px) {
    .catalogo-row .card-body > div {
        height: 300px;
    }
}
</style>