<!-- Modal Consulta Express -->
<div class="modal fade" id="consultaExpressModal" tabindex="-1" aria-labelledby="consultaExpressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Header del Modal -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="consultaExpressModalLabel">
                    Consulta Express
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
                                    <label for="ce_tema_select_modal" class="form-label">Tema:</label>
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
                                    <label for="ce_subtema_select_modal" class="form-label">Subtema:</label>
                                    <select id="ce_subtema_select_modal" name="ce_subtema_id" class="form-select" disabled>
                                        <option value="">Primero seleccione un tema</option>
                                    </select>
                                </div>
                                
                                <!-- Eliminamos el botón de consultar ya que ahora cargará automáticamente -->
                            </div>
                        </div>
                        
                        <!-- Columna de contenido - Se mantiene en 6 columnas -->
                        <div class="col-md-6">
                            <div id="ce_contenido_container_modal" class="border rounded p-3" style="min-height: 250px; max-height: 500px; overflow-y: auto;">
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-info-circle fs-2"></i>
                                    <p class="mt-2">Seleccione un tema y subtema para ver la información</p>
                                </div>
                            </div>
                            
                            <div id="ce_metadata_modal" class="text-end text-muted small mt-2" style="display: none;">
                                Última actualización: <span id="ce_fecha_actualizacion_modal">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Esperamos a que el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Añadir referencias a los elementos en el modal
    const temaSelectModal = document.getElementById('ce_tema_select_modal');
    const subtemaSelectModal = document.getElementById('ce_subtema_select_modal');
    const contenidoContainerModal = document.getElementById('ce_contenido_container_modal');
    const metadataDivModal = document.getElementById('ce_metadata_modal');
    const fechaActualizacionModal = document.getElementById('ce_fecha_actualizacion_modal');
    
    // Verificar elementos del modal
    if (!temaSelectModal || !subtemaSelectModal || !contenidoContainerModal) {
        console.error('Faltan elementos necesarios en el modal:',
            {temaSelectModal: !!temaSelectModal, subtemaSelectModal: !!subtemaSelectModal, 
             contenidoContainerModal: !!contenidoContainerModal});
    }

    // Función para mostrar loader en el modal
    function showLoaderModal() {
        contenidoContainerModal.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando...</p>
            </div>
        `;
    }
    
    // Función para mostrar error en el modal
    function showErrorModal(message) {
        contenidoContainerModal.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                ${message}
            </div>
        `;
        metadataDivModal.style.display = 'none';
    }
    
    // Función para cargar subtemas cuando se selecciona un tema en el modal
    function cargarSubtemasModal(temaId) {
        // Deshabilitar el selector de subtemas mientras se cargan
        subtemaSelectModal.disabled = true;
        subtemaSelectModal.innerHTML = '<option value="">Cargando subtemas...</option>';
        
        const url = '{{ url("sigem/ajax/consulta-express/subtemas") }}/' + temaId;

        // Realizar petición fetch
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Limpiar selector de subtemas
            subtemaSelectModal.innerHTML = '<option value="">Seleccione un subtema...</option>';
            
            if (data.success && data.subtemas && data.subtemas.length > 0) {
                // Añadir opciones de subtemas
                data.subtemas.forEach(subtema => {
                    const option = document.createElement('option');
                    option.value = subtema.ce_subtema_id;
                    option.textContent = subtema.ce_subtema;
                    subtemaSelectModal.appendChild(option);
                });
                
                // Habilitar selector
                subtemaSelectModal.disabled = false;
            } else {
                subtemaSelectModal.innerHTML = '<option value="">No hay subtemas disponibles</option>';
            }
        })
        .catch(error => {
            console.error('Error al cargar subtemas en modal:', error);
            subtemaSelectModal.innerHTML = '<option value="">Error al cargar subtemas</option>';
        });
    }
    
    // Función para cargar contenido en el modal
    function cargarContenidoModal(subtemaId) {
        showLoaderModal();
        
        const url = '{{ url("sigem/ajax/consulta-express/contenido") }}/' + subtemaId;

        // Realizar petición fetch
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.contenido) {
                // Mostrar contenido y metadata
                contenidoContainerModal.innerHTML = data.contenido.ce_contenido;
                fechaActualizacionModal.textContent = data.actualizado;
                metadataDivModal.style.display = 'block';
            } else {
                contenidoContainerModal.innerHTML = '<div class="alert alert-warning">No se encontró contenido para el subtema seleccionado.</div>';
                metadataDivModal.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error al cargar contenido en modal:', error);
            showErrorModal('No se pudo cargar el contenido. Intente nuevamente: ' + error.message);
        });
    }
    
    // EVENT LISTENERS PARA EL MODAL
    
    // Cuando cambia el tema en el modal
    temaSelectModal.addEventListener('change', function() {
        const temaId = this.value;

        if (temaId) {
            cargarSubtemasModal(temaId);
            
            // Limpiar contenido si había algo mostrado
            contenidoContainerModal.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-2"></i>
                    <p class="mt-2">Seleccione un subtema para ver la información</p>
                </div>
            `;
            metadataDivModal.style.display = 'none';
        } else {
            // Resetear subtemas y contenido
            subtemaSelectModal.innerHTML = '<option value="">Primero seleccione un tema</option>';
            subtemaSelectModal.disabled = true;
            
            contenidoContainerModal.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-2"></i>
                    <p class="mt-2">Seleccione un tema y subtema para ver la información</p>
                </div>
            `;
            metadataDivModal.style.display = 'none';
        }
    });
    
    // Cuando cambia el subtema en el modal - Ahora carga automáticamente el contenido
    subtemaSelectModal.addEventListener('change', function() {
        const subtemaId = this.value;
        
        if (subtemaId) {
            // Cargar contenido automáticamente al seleccionar un subtema
            cargarContenidoModal(subtemaId);
        } else {
            // Mostrar mensaje de selección
            contenidoContainerModal.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-2"></i>
                    <p class="mt-2">Seleccione un subtema para ver la información</p>
                </div>
            `;
            metadataDivModal.style.display = 'none';
        }
    });
    
    // Mantener el código existente para la versión no-modal si es necesario
    // ...
    
    // Verificar que los elementos existan (versión no-modal)
    const temaSelect = document.getElementById('ce_tema_select');
    const subtemaSelect = document.getElementById('ce_subtema_select');
    const consultarBtn = document.getElementById('ce_consultar_btn');
    const contenidoContainer = document.getElementById('ce_contenido_container');
    const metadataDiv = document.getElementById('ce_metadata');
    const fechaActualizacion = document.getElementById('ce_fecha_actualizacion');
    
    // Si existen los elementos de la versión no-modal, configurar su funcionalidad
    if (temaSelect && subtemaSelect && contenidoContainer) {
        // Código existente para la versión no-modal...
        // ...
    }
});
</script>