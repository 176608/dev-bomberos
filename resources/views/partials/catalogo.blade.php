<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="text-success mb-4 text-center">
            <i class="bi bi-journal-text"></i> Catálogo de Cuadros Estadísticos
        </h2>

        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Sistema de clasificación:</strong> Para su fácil localización, los diferentes cuadros que conforman el módulo estadístico del SIGEM se identifican mediante una clave conformada por el número de tema, identificador del subtema y el número de cuadro estadístico.
        </div>

        <p class="text-center lead">Son 6 temas principales y a cada uno le corresponden diferentes subtemas en donde encontramos los cuadros estadísticos.</p>

        <!-- ESTRUCTURA PRINCIPAL -->
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
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-table me-2"></i>Cuadros Estadísticos
                            <span id="cuadros-count" class="badge bg-light text-dark ms-2">0 cuadros</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="cuadros-container" style="max-height: 600px; overflow-y: auto;">
                            <div class="text-center p-4">
                                <div class="spinner-border text-primary" role="status">
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

<!-- DATOS RAW VISIBLES -->
<div class="card mt-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-database me-2"></i>Datos Raw del Sistema
            <button type="button" class="btn btn-sm btn-outline-light float-end" onclick="toggleRawData()">
                <i class="bi bi-eye"></i> Mostrar/Ocultar
            </button>
        </h5>
    </div>
    <div class="card-body" id="raw-data-content" style="display: none;">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="bi bi-journal-text"></i> Modelo Catalogo:</h6>
                <pre id="catalogo-raw" class="bg-light p-3 border" style="max-height: 400px; overflow-y: auto; font-size: 12px;">
                    Cargando datos del modelo Catalogo...
                </pre>
            </div>
            <div class="col-md-6">
                <h6><i class="bi bi-table"></i> Modelo CuadroEstadistico:</h6>
                <pre id="cuadros-raw" class="bg-light p-3 border" style="max-height: 400px; overflow-y: auto; font-size: 12px;">
                    Cargando datos del modelo CuadroEstadistico...
                </pre>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6><i class="bi bi-bar-chart"></i> Estadísticas Generales:</h6>
                <div id="stats-raw" class="bg-success text-white p-3 rounded">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3 id="stat-temas">0</h3>
                            <small>Total Temas</small>
                        </div>
                        <div class="col-md-4">
                            <h3 id="stat-subtemas">0</h3>
                            <small>Total Subtemas</small>
                        </div>
                        <div class="col-md-4">
                            <h3 id="stat-cuadros">0</h3>
                            <small>Total Cuadros</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para mostrar/ocultar datos raw
function toggleRawData() {
    const content = document.getElementById('raw-data-content');
    if (content.style.display === 'none') {
        content.style.display = 'block';
    } else {
        content.style.display = 'none';
    }
}

// Función para mostrar datos raw (llamada desde sigem.js)
function mostrarDebugData(data) {
    console.log('=== MOSTRANDO DATOS RAW EN LA VISTA ===');
    console.log('Data recibida:', data);
    
    // Mostrar modelo Catalogo
    const catalogoRaw = document.getElementById('catalogo-raw');
    if (catalogoRaw && data.catalogo_modelo) {
        catalogoRaw.textContent = JSON.stringify(data.catalogo_modelo, null, 2);
    }
    
    // Mostrar modelo CuadroEstadistico
    const cuadrosRaw = document.getElementById('cuadros-raw');
    if (cuadrosRaw && data.cuadros_modelo) {
        cuadrosRaw.textContent = JSON.stringify(data.cuadros_modelo.slice(0, 10), null, 2) + 
                                 (data.cuadros_modelo.length > 10 ? '\n\n... y ' + (data.cuadros_modelo.length - 10) + ' más' : '');
    }
    
    // Mostrar estadísticas
    const statTemas = document.getElementById('stat-temas');
    const statSubtemas = document.getElementById('stat-subtemas');
    const statCuadros = document.getElementById('stat-cuadros');
    
    if (statTemas) statTemas.textContent = data.total_temas || 0;
    if (statSubtemas) statSubtemas.textContent = data.total_subtemas || 0;
    if (statCuadros) statCuadros.textContent = data.total_cuadros || 0;
    
    console.log('=== DATOS RAW MOSTRADOS EN LA VISTA ===');
}

// Funciones globales para expandir/contraer
window.expandirTodo = function() {
    const allSubtemas = document.querySelectorAll('[id^="subtemas-"]');
    const allChevrons = document.querySelectorAll('.tema-chevron');
    const allHeaders = document.querySelectorAll('.tema-header');
    
    allSubtemas.forEach(container => {
        container.style.display = 'block';
    });
    
    allChevrons.forEach(chevron => {
        chevron.classList.remove('bi-chevron-down');
        chevron.classList.add('bi-chevron-up');
    });
    
    allHeaders.forEach(header => {
        header.classList.add('active');
    });
};

window.contraerTodo = function() {
    const allSubtemas = document.querySelectorAll('[id^="subtemas-"]');
    const allChevrons = document.querySelectorAll('.tema-chevron');
    const allHeaders = document.querySelectorAll('.tema-header');
    
    allSubtemas.forEach(container => {
        container.style.display = 'none';
    });
    
    allChevrons.forEach(chevron => {
        chevron.classList.remove('bi-chevron-up');
        chevron.classList.add('bi-chevron-down');
    });
    
    allHeaders.forEach(header => {
        header.classList.remove('active');
    });
};

// Auto-llamar cuando la página carga
document.addEventListener('DOMContentLoaded', function() {
    // Los datos raw ocultos por defecto
    const content = document.getElementById('raw-data-content');
    if (content) {
        content.style.display = 'none';
    }
});
</script>

<style>
/* === SISTEMA DE LOADING === */
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

/* === ESTILOS PARA ÍNDICE DESPLEGADO (copiados del admin) === */
#indice-container {
    overflow-y: auto;
    scroll-behavior: smooth;
}

.indice-tema-container {
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    overflow: hidden;
}

.indice-tema-header {
    text-align: center;
    font-weight: bold;
    padding: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.indice-tema-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.indice-subtema-row {
    display: flex;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: all 0.3s ease;
}

.indice-subtema-row:hover {
    background-color: #e8f4f8 !important;
    transform: translateX(5px);
}

.indice-subtema-row:last-child {
    border-bottom: none;
}

.indice-subtema-codigo {
    padding: 8px 15px;
    min-width: 60px;
    border-right: 1px solid #ddd;
    font-weight: bold;
    text-align: center;
    background-color: rgba(0,0,0,0.05);
}

.indice-subtema-titulo {
    padding: 8px 15px;
    flex: 1;
}

/* === ALTURAS SINCRONIZADAS === */
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

/* === SCROLLBARS PERSONALIZADOS === */
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

/* === SISTEMA DE FOCUS/HIGHLIGHT === */
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

/* === RESPONSIVE DESIGN === */
@media (max-width: 768px) {
    .catalogo-row .card-body > div {
        max-height: 400px !important;
    }
    
    .indice-tema-header {
        font-size: 11px;
    }
    
    .indice-subtema-titulo {
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .catalogo-row .card-body > div {
        max-height: 300px !important;
    }
}
</style>