<div class="card shadow-sm">
    <div class="card-body">

        <div class="alert-success mb-4">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Sistema de clasificación:</strong> Para su fácil localización, los diferentes cuadros que conforman el módulo estadístico del SIGEM se identifican mediante una clave conformada por el número de tema, identificador del subtema y el número de cuadro estadístico.
        </div>

        <div class="row">
            <div class="text-center mb-4">
                <img src="{{ asset('imagenes/ejem.png') }}" alt="Catalogo Ejemplo" class="img-fluid rounded shadow-sm">
            </div>
        </div>
        <div class="row">
            <div class="text-center mb-4">
                <p>El cuadro de “Población por Municipio” se encuentra dentro del Tema 3. Sociodemográfico en el subtema de Población</p>
            </div>
        </div>

        <p class="text-center lead">Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos.</p>

    
        <div class="row mt-4 catalogo-row">
            <div class="col-lg-4">
                <div class="card bg-light h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>Estructura de Índice
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="indice-container" style="max-height: 600px; overflow-y: auto;">
                            <div class="text-center p-4">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando índice...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card bg-light h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-table me-2"></i>Cuadros Estadísticos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="cuadros-container" style="max-height: 600px; overflow-y: auto;">
                            <div class="text-center p-4">
                                <div class="spinner-border text-success" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2">Cargando cuadros...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.indice-tema-container {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.indice-tema-container:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.indice-tema-header {
    text-align: center;
    font-weight: bold;
    padding: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.indice-subtema-row {
    display: flex;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: all 0.3s ease;
}

.indice-subtema-row:hover {
    background-color: #e8f4f8 !important;
    transform: translateX(5px) !important;
}

.indice-subtema-row:last-child {
    border-bottom: none;
}

.highlight-focus {
    background-color: #fff3cd !important;
    border: 2px solid #ffc107 !important;
    box-shadow: 0 0 15px rgba(255, 193, 7, 0.5) !important;
    animation: pulseHighlight 1s ease-in-out;
    position: relative;
    z-index: 10;
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

.catalogo-row {
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

#indice-container::-webkit-scrollbar,
#cuadros-container::-webkit-scrollbar {
    width: 8px;
}

#indice-container::-webkit-scrollbar-track,
#cuadros-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#indice-container::-webkit-scrollbar-thumb,
#cuadros-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

#indice-container::-webkit-scrollbar-thumb:hover,
#cuadros-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

@media (max-width: 768px) {
    .catalogo-row .card-body > div {
        max-height: 400px !important;
    }
}

@media (max-width: 576px) {
    .catalogo-row .card-body > div {
        max-height: 300px !important;
    }
}
</style>


