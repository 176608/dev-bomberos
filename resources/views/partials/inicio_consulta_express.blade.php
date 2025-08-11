<!-- Modal Consulta Express -->
<div class="modal fade" id="consultaExpressModal" tabindex="-1" aria-labelledby="consultaExpressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Header del Modal -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="consultaExpressModalLabel">
                    <i class="bi bi-search me-2"></i>Consulta Express
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Columna de imagen - Ahora 2 columnas -->
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <img src="{{ asset('imagenes/express.png') }}" alt="Consulta Express" class="img-fluid rounded shadow-sm" style="max-height: 220px;">
                        </div>
                        
                        <!-- Columna de selectores - Ahora 4 columnas -->
                        <div class="col-md-4">
                            @php
                                use App\Models\SIGEM\ce_tema;
                                $temas = ce_tema::orderBy('tema', 'asc')->get();
                            @endphp
                            
                            <!-- Formulario para selección en modal -->
                            <div id="ce_form_modal">
                                <div class="form-group mb-3">
                                    <label for="ce_tema_select_modal" class="form-label fw-semibold">
                                        <i class="bi bi-bookmark-fill me-1"></i>Tema:
                                    </label>
                                    <select id="ce_tema_select_modal" name="ce_tema_id" class="form-select">
                                        <option value="">Seleccione un tema...</option>
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->ce_tema_id }}">
                                                {{ $tema->tema }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="ce_subtema_select_modal" class="form-label fw-semibold">
                                        <i class="bi bi-bookmarks-fill me-1"></i>Subtema:
                                    </label>
                                    <select id="ce_subtema_select_modal" name="ce_subtema_id" class="form-select" disabled>
                                        <option value="">Primero seleccione un tema</option>
                                    </select>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="alert alert-info alert-sm">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Consulta Express</strong><br>
                                        <small>Sistema de consulta rápida de información estadística municipal organizada por temas y subtemas.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna de contenido - Se mantiene en 6 columnas -->
                        <div class="col-md-6">
                            <div id="ce_contenido_container_modal" class="border rounded p-3 bg-light" style="min-height: 300px; max-height: 500px; overflow-y: auto;">
                                <div class="text-center text-muted py-5" id="ce_estado_inicial">
                                    <i class="bi bi-table" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5 class="mt-3 text-muted">Consulta Express</h5>
                                    <p class="mb-0">Seleccione un tema y subtema para ver la información estadística</p>
                                </div>
                            </div>
                            
                            <div id="ce_metadata_modal" class="text-end text-muted small mt-2" style="display: none;">
                                <i class="bi bi-clock me-1"></i>Última actualización: <span id="ce_fecha_actualizacion_modal">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <div class="me-auto">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Sistema Integral de Gestión de Estadísticas Municipales
                    </small>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales para el modal CE
let ceTemasData = [];
let ceSubtemasData = [];

// Cargar datos cuando se abre el modal
document.getElementById('consultaExpressModal')?.addEventListener('shown.bs.modal', function() {
    if (ceTemasData.length === 0) {
        cargarTemasConsultaExpress();
    }
});

// Función para cargar temas de CE
function cargarTemasConsultaExpress() {
    fetch('{{ route("sigem.consulta-express.temas") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.temas) {
                ceTemasData = data.temas;
                console.log('Temas CE cargados:', ceTemasData.length);
            }
        })
        .catch(error => {
            console.error('Error al cargar temas CE:', error);
        });
}

// Event listener para cambio de tema
document.getElementById('ce_tema_select_modal')?.addEventListener('change', function() {
    const temaId = this.value;
    const subtemaSelect = document.getElementById('ce_subtema_select_modal');
    
    // Limpiar subtemas
    subtemaSelect.innerHTML = '<option value="">Seleccione un subtema...</option>';
    subtemaSelect.disabled = !temaId;
    
    // Limpiar contenido
    limpiarContenidoCE();
    
    if (temaId) {
        // Cargar subtemas
        cargarSubtemasConsultaExpress(temaId);
    }
});

// Event listener para cambio de subtema
document.getElementById('ce_subtema_select_modal')?.addEventListener('change', function() {
    const subtemaId = this.value;
    
    if (subtemaId) {
        cargarContenidoConsultaExpress(subtemaId);
    } else {
        limpiarContenidoCE();
    }
});

// Función para cargar subtemas por tema
function cargarSubtemasConsultaExpress(temaId) {
    fetch(`{{ url('/sigem/consulta-express/subtemas') }}/${temaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.subtemas) {
                const subtemaSelect = document.getElementById('ce_subtema_select_modal');
                
                data.subtemas.forEach(subtema => {
                    const option = document.createElement('option');
                    option.value = subtema.ce_subtema_id;
                    option.textContent = subtema.ce_subtema;
                    subtemaSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error al cargar subtemas CE:', error);
            mostrarErrorCE('Error al cargar subtemas');
        });
}

// Función para cargar contenido por subtema
function cargarContenidoConsultaExpress(subtemaId) {
    // Mostrar loading
    document.getElementById('ce_contenido_container_modal').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-success mb-3" role="status" style="width: 2.5rem; height: 2.5rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h6 class="text-success">Cargando información...</h6>
            <p class="text-muted mb-0">Obteniendo datos de Consulta Express</p>
        </div>
    `;
    
    // Cargar contenido via AJAX
    fetch(`{{ url('/sigem/consulta-express/contenido') }}/${subtemaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.contenido) {
                mostrarContenidoCE(data.contenido, data.actualizado);
            } else {
                mostrarSinContenidoCE();
            }
        })
        .catch(error => {
            console.error('Error al cargar contenido CE:', error);
            mostrarErrorCE('Error al cargar el contenido');
        });
}

// Función para mostrar contenido CE con renderizado de tabla
function mostrarContenidoCE(contenido, actualizado) {
    console.log('Datos recibidos:', contenido); // Debug
    
    // Usar la misma lógica del CRUD - renderizar HTML directamente
    const tablaHtml = renderizarTablaCESimple(contenido);
    
    const contenidoHtml = `
        <div class="consulta-express-modal-content">
            <!-- Título centrado -->
            <div class="text-center mb-4">
                <h4 class="text-primary fw-bold mb-2">
                    ${contenido.titulo_tabla || 'Información Estadística'}
                </h4>
                <div class="d-flex justify-content-center gap-2 mb-2">
                    <span class="badge bg-success fs-7">
                        <i class="bi bi-grid-3x3 me-1"></i>
                        ${contenido.tabla_filas || 0}×${contenido.tabla_columnas || 0}
                    </span>
                </div>
            </div>
            
            <!-- Tabla -->
            <div class="table-container">
                ${tablaHtml}
            </div>
            
            <!-- Pie de tabla centrado -->
            ${contenido.pie_tabla ? `
                <div class="text-center mt-3">
                    <small class="text-muted fst-italic">
                        <i class="bi bi-info-circle me-1"></i>
                        ${contenido.pie_tabla}
                    </small>
                </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('ce_contenido_container_modal').innerHTML = contenidoHtml;
    
    // Mostrar metadata de actualización
    const metadataDiv = document.getElementById('ce_metadata_modal');
    document.getElementById('ce_fecha_actualizacion_modal').textContent = actualizado || 'No disponible';
    metadataDiv.style.display = 'block';
}

// NUEVA función simple que copia exactamente la lógica del CRUD
function renderizarTablaCESimple(contenido) {
    
    if (!contenido.tabla_datos || !Array.isArray(contenido.tabla_datos) || contenido.tabla_datos.length === 0) {
        return `<div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Sin datos de tabla disponibles
                </div>`;
    }
    
    let html = '<div class="consulta-express-tabla-modal">';
    html += '<div class="table-responsive">';
    html += '<table class="table table-striped table-bordered table-hover">';
    
    // Procesar cada fila exactamente como el CRUD
    contenido.tabla_datos.forEach((fila, filaIndex) => {
        html += '<tr>';
        
        if (Array.isArray(fila)) {
            fila.forEach((celda, colIndex) => {
                // Primera fila = encabezados
                if (filaIndex === 0) {
                    html += `<th class="table-success text-center fw-bold">${celda || '-'}</th>`;
                } else {
                    // Primera columna = categorías (texto en negrita)
                    if (colIndex === 0) {
                        html += `<td class="fw-semibold">${celda || '-'}</td>`;
                    } else {
                        // Otras columnas = números (alineados a la derecha)
                        const esNumero = !isNaN(celda) && !isNaN(parseFloat(celda)) && celda !== '';
                        const clase = esNumero ? 'text-end' : '';
                        html += `<td class="${clase}">${celda || '-'}</td>`;
                    }
                }
            });
        }
        
        html += '</tr>';
    });
    
    html += '</table>';
    html += '</div>';
    html += '</div>';
    
    return html;
}

// Función auxiliar simple: escapar HTML
function escapeHtmlCE(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text.toString();
    return div.innerHTML;
}

// Función para mostrar mensaje sin contenido
function mostrarSinContenidoCE() {
    document.getElementById('ce_contenido_container_modal').innerHTML = `
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
            <h5 class="mt-3 text-muted">Sin información disponible</h5>
            <p class="mb-0">No se encontró contenido para este subtema.</p>
            <small class="text-muted">Seleccione otro subtema o contacte al administrador.</small>
        </div>
    `;
    
    // Ocultar metadata
    document.getElementById('ce_metadata_modal').style.display = 'none';
}

// Función para mostrar errores
function mostrarErrorCE(mensaje) {
    document.getElementById('ce_contenido_container_modal').innerHTML = `
        <div class="text-center py-5">
            <div class="alert alert-danger d-inline-block">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Error:</strong> ${mensaje}
            </div>
            <div class="mt-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reintentar
                </button>
            </div>
        </div>
    `;
    
    // Ocultar metadata
    document.getElementById('ce_metadata_modal').style.display = 'none';
}

// Función para limpiar contenido
function limpiarContenidoCE() {
    document.getElementById('ce_contenido_container_modal').innerHTML = document.getElementById('ce_estado_inicial').outerHTML;
    document.getElementById('ce_metadata_modal').style.display = 'none';
}

// Limpiar al cerrar el modal
document.getElementById('consultaExpressModal')?.addEventListener('hidden.bs.modal', function() {
    document.getElementById('ce_tema_select_modal').value = '';
    document.getElementById('ce_subtema_select_modal').innerHTML = '<option value="">Primero seleccione un tema</option>';
    document.getElementById('ce_subtema_select_modal').disabled = true;
    limpiarContenidoCE();
});
</script>

<style>
/* Estilos específicos para el modal de Consulta Express */
.consulta-express-modal-content {
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.consulta-express-tabla-modal {
    margin: 0;
}

.consulta-express-tabla-modal .table {
    margin-bottom: 0;
    font-size: 0.9rem;
}

.consulta-express-tabla-modal .table th {
    background-color: #198754 !important; /* Verde success */
    color: white;
    font-weight: 600;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 0.85rem;
}

.consulta-express-tabla-modal .table td {
    border: 1px solid #dee2e6;
    vertical-align: middle;
    font-size: 0.85rem;
}

.consulta-express-tabla-modal .table-responsive {
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    max-height: 350px;
    overflow-y: auto;
}

/* Estilos para el título centrado */
.consulta-express-modal-content h4 {
    font-size: 1.3rem;
    line-height: 1.3;
    margin-bottom: 0.5rem;
}

/* Badges más pequeños */
.fs-7 {
    font-size: 0.8rem !important;
}

/* Loading spinner personalizado */
.spinner-border-success {
    border-color: #198754;
    border-right-color: transparent;
}

/* Responsive para modal */
@media (max-width: 768px) {
    .consulta-express-tabla-modal .table {
        font-size: 0.75rem;
    }
    
    .consulta-express-tabla-modal .table th,
    .consulta-express-tabla-modal .table td {
        padding: 0.4rem 0.2rem;
        font-size: 0.75rem;
    }
    
    .consulta-express-modal-content h4 {
        font-size: 1.1rem;
    }
}

/* Scroll personalizado para contenido largo */
#ce_contenido_container_modal::-webkit-scrollbar {
    width: 6px;
}

#ce_contenido_container_modal::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#ce_contenido_container_modal::-webkit-scrollbar-thumb {
    background: #198754;
    border-radius: 3px;
}

#ce_contenido_container_modal::-webkit-scrollbar-thumb:hover {
    background: #146c43;
}
</style>
