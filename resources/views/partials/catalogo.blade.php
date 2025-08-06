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

<div class="modal fade" id="modal_cuadro_catalogo" tabindex="-1" aria-labelledby="modalCuadroLabel" aria-hidden="true">
    <!-- El contenido del modal se generará dinámicamente -->
</div>

<style>
/* === ESTILOS PARA ÍNDICE DESPLEGADO (del contexto) === */
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

/* === RESPONSIVE DESIGN === */
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

<script>
// Función para manejar la visualización del cuadro en un modal fullscreen
// Esta función será llamada desde sigem.js
window.catalogoVerCuadro = function(cuadroId, codigo) {
    console.log(`Procesando visualización de cuadro en modal: ID=${cuadroId}, Código=${codigo}`);
    
    // Obtener referencia al modal
    const modal = document.getElementById('modal_cuadro_catalogo');
    
    if (!modal) {
        console.error('Modal no encontrado en el DOM');
        alert('Error: El modal para mostrar el cuadro no está disponible.');
        return;
    }
    
    // Mostrar loader en el modal
    modal.innerHTML = `
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-table me-2"></i>Cargando cuadro ${codigo}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center py-5">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <h4 class="mt-3 text-muted">Cargando datos del cuadro</h4>
                    <p class="text-muted">ID: ${cuadroId} | Código: ${codigo}</p>
                </div>
            </div>
        </div>
    `;
    
    // Abrir el modal con Bootstrap
    let bootstrapModal;
    try {
        bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    } catch (e) {
        console.error('Error al inicializar el modal Bootstrap:', e);
        alert('Error: No se pudo abrir el modal. Verifica que Bootstrap esté correctamente cargado.');
        return;
    }
    
    // URL para obtener los datos del cuadro
    const apiUrl = `${window.SIGEMApp.CONFIG ? window.SIGEMApp.CONFIG.API_URL : '/sigem'}/cuadro-data/${cuadroId}`;
    
    // Cargar datos del cuadro vía AJAX
    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la respuesta: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos del cuadro recibidos:', data);
            
            if (data.success && data.cuadro) {
                // Actualizar contenido del modal con los datos del cuadro
                modal.innerHTML = `
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-table me-2"></i>${data.cuadro.cuadro_estadistico_titulo}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <nav aria-label="breadcrumb">
                                                <ol class="breadcrumb">
                                                    <li class="breadcrumb-item"><strong>Tema:</strong> ${data.tema_info?.tema_titulo || 'No disponible'}</li>
                                                    <li class="breadcrumb-item"><strong>Subtema:</strong> ${data.subtema_info?.subtema_titulo || 'No disponible'}</li>
                                                    <li class="breadcrumb-item active" aria-current="page"><strong>Código:</strong> ${data.cuadro.codigo_cuadro}</li>
                                                </ol>
                                            </nav>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card mb-4">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0 text-center">${data.cuadro.cuadro_estadistico_titulo}</h5>
                                                    ${data.cuadro.cuadro_estadistico_subtitulo ? 
                                                        `<p class="mb-0 text-center text-muted">${data.cuadro.cuadro_estadistico_subtitulo}</p>` : ''}
                                                </div>
                                                <div class="card-body">
                                                    <div class="text-center my-4 cuadro-contenido">
                                                        ${data.cuadro.contenido_html || 'Contenido no disponible'}
                                                    </div>
                                                </div>
                                                <div class="card-footer text-muted">
                                                    <small>${data.cuadro.pie_pagina || ''}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <a href="${window.SIGEMApp.CONFIG ? window.SIGEMApp.CONFIG.BASE_URL : '/sigem'}/descargar-cuadro/${data.cuadro.cuadro_estadistico_id}" class="btn btn-success">
                                                <i class="bi bi-download me-1"></i>Descargar Datos (CSV)
                                            </a>
                                            <button class="btn btn-outline-primary ms-2" onclick="window.print()">
                                                <i class="bi bi-printer me-1"></i>Imprimir
                                            </button>
                                            <button id="testJSButton" class="btn btn-outline-dark ms-2" onclick="testJsModal('${codigo}')">
                                                <i class="bi bi-code-slash me-1"></i>Probar JavaScript
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
                
                // Inicializar cualquier componente adicional en el modal
                inicializarComponentesModal(data.cuadro);
            } else {
                // Mostrar mensaje de error en el modal
                modal.innerHTML = `
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-dark">
                                <h5 class="modal-title">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Cuadro no encontrado
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body text-center py-5">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-circle fs-1 d-block mb-3"></i>
                                    <h4>El cuadro solicitado no está disponible</h4>
                                    <p>No se pudo cargar el cuadro con código "${codigo}" (ID: ${cuadroId})</p>
                                    <small class="d-block mt-3">Error: ${data.message || 'No hay información adicional'}</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error al cargar datos del cuadro:', error);
            
            // Mostrar error en el modal
            modal.innerHTML = `
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle me-2"></i>Error al cargar cuadro
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body text-center py-5">
                            <div class="alert alert-danger">
                                <i class="bi bi-x-circle fs-1 d-block mb-3"></i>
                                <h4>No se pudieron cargar los datos</h4>
                                <p>Ocurrió un error al intentar cargar el cuadro con código "${codigo}" (ID: ${cuadroId})</p>
                                <small class="d-block mt-3">Error: ${error.message}</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            `;
        });
};

// Función para probar JavaScript dentro del modal
function testJsModal(codigo) {
    console.log('Probando JavaScript en el modal para el cuadro:', codigo);
    alert('JavaScript ejecutado correctamente dentro del modal para el cuadro ' + codigo);
}

// Función para inicializar componentes adicionales en el modal
function inicializarComponentesModal(cuadroData) {
    console.log('Inicializando componentes adicionales para el cuadro:', cuadroData.codigo_cuadro);
    
    // Acceder al contenedor del contenido del cuadro
    const contenidoContainer = document.querySelector('.cuadro-contenido');
    if (!contenidoContainer) {
        console.warn('No se encontró el contenedor del contenido');
        return;
    }
    
    // Ejemplo: Añadir interactividad a elementos del contenido
    const tablas = contenidoContainer.querySelectorAll('table');
    if (tablas.length > 0) {
        console.log(`Se encontraron ${tablas.length} tablas en el contenido`);
        
        // Añadir clase y eventos a las tablas
        tablas.forEach((tabla, index) => {
            tabla.classList.add('table', 'table-hover', 'table-bordered');
            tabla.setAttribute('data-tabla-index', index);
            
            // Añadir efecto hover a las filas
            const filas = tabla.querySelectorAll('tr');
            filas.forEach(fila => {
                fila.addEventListener('mouseover', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                fila.addEventListener('mouseout', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    }
    
    // Ejemplo: Detectar imágenes y añadir efecto lightbox
    const imagenes = contenidoContainer.querySelectorAll('img');
    if (imagenes.length > 0) {
        console.log(`Se encontraron ${imagenes.length} imágenes en el contenido`);
        
        imagenes.forEach(img => {
            img.style.cursor = 'pointer';
            img.title = 'Haz clic para ampliar';
            img.addEventListener('click', function() {
                const src = this.getAttribute('src');
                const alt = this.getAttribute('alt') || 'Imagen del cuadro';
                
                // Crear overlay para mostrar la imagen ampliada
                const overlay = document.createElement('div');
                overlay.style.position = 'fixed';
                overlay.style.top = '0';
                overlay.style.left = '0';
                overlay.style.width = '100%';
                overlay.style.height = '100%';
                overlay.style.backgroundColor = 'rgba(0,0,0,0.9)';
                overlay.style.display = 'flex';
                overlay.style.alignItems = 'center';
                overlay.style.justifyContent = 'center';
                overlay.style.zIndex = '9999';
                overlay.style.cursor = 'pointer';
                
                const imgAmpliada = document.createElement('img');
                imgAmpliada.src = src;
                imgAmpliada.alt = alt;
                imgAmpliada.style.maxWidth = '90%';
                imgAmpliada.style.maxHeight = '90%';
                imgAmpliada.style.border = '2px solid white';
                imgAmpliada.style.borderRadius = '5px';
                
                overlay.appendChild(imgAmpliada);
                document.body.appendChild(overlay);
                
                overlay.addEventListener('click', function() {
                    document.body.removeChild(this);
                });
            });
        });
    }
    
    console.log('Componentes inicializados correctamente');
}
</script>

