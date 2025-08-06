<!-- Modal Consulta Express -->
<div class="modal fade" id="consultaExpressModal" tabindex="-1" aria-labelledby="consultaExpressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Header del Modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="consultaExpressModalLabel">
                    <i class="bi bi-lightning-fill me-2"></i>Consulta Express
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Columna de imagen -->
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <img src="{{ asset('imagenes/express.png') }}" alt="Consulta Express" class="img-fluid rounded shadow-sm" style="max-height: 220px;">
                        </div>
                        
                        <!-- Columna de selectores -->
                        <div class="col-md-3">
                           @php
use App\Models\SIGEM\ce_tema;

$ordenPersonalizado = [
    'Población' => 1,
    'Empleo' => 2,
    'Industria Maquiladora' => 3,
    'Vivienda' => 4,
    'Educación' => 5,
];

$temas = ce_tema::all()->sortBy(function ($tema) use ($ordenPersonalizado) {
    return $ordenPersonalizado[$tema->tema] ?? 999;
});
@endphp

                            
                            <!-- Formulario para selección en modal -->
                            <div id="ce_form_modal">
                                <div class="form-group mb-3">
                                    <label for="ce_tema_select_modal" class="form-label">Tema:</label>
                                    <select id="ce_tema_select_modal" name="ce_tema_id" class="form-select">
                                        <option value="">Seleccione un tema...</option>
                                       @foreach($temas as $tema)
    <option value="{{ $tema->ce_tema_id }}">
        {{ $ordenPersonalizado[$tema->tema] ?? '-' }}. {{ $tema->tema }}
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
                                
                                <button type="button" id="ce_consultar_btn_modal" class="btn btn-primary w-100" disabled>
                                    Consultar <i class="bi bi-arrow-right-circle ms-1"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Columna de contenido -->
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
    //console.log('Express: DOM cargado, iniciando configuración de Consulta Express');

    // Verificar que los elementos existan
const temaSelect = document.getElementById('ce_tema_select_modal');
const subtemaSelect = document.getElementById('ce_subtema_select_modal');
const consultarBtn = document.getElementById('ce_consultar_btn_modal');
const contenidoContainer = document.getElementById('ce_contenido_container_modal');
const metadataDiv = document.getElemenzztById('ce_metadata_modal');
const fechaActualizacion = document.getElementById('ce_fecha_actualizacion_modal');

    
    if (!temaSelect || !subtemaSelect || !consultarBtn || !contenidoContainer) {
        console.error('Faltan elementos necesarios en el DOM:',
            {temaSelect: !!temaSelect, subtemaSelect: !!subtemaSelect, 
            consultarBtn: !!consultarBtn, contenidoContainer: !!contenidoContainer});
        return;
    }

    //console.log('Express: Elementos encontrados, configurando listeners...');

    // Verificar si jQuery está disponible
    const useJQuery = (typeof jQuery !== 'undefined');
    
    // Función para mostrar loader
    function showLoader() {
        contenidoContainer.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando...</p>
            </div>
        `;
    }
    
    // Función para mostrar error
    function showError(message) {
        contenidoContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                ${message}
            </div>
        `;
        metadataDiv.style.display = 'none';
    }
    
    // Función para cargar subtemas cuando se selecciona un tema
    function cargarSubtemas(temaId) {
        //console.log('Express:Intentando cargar subtemas para tema ID:', temaId);
        
        // Deshabilitar el selector de subtemas mientras se cargan
        subtemaSelect.disabled = true;
        subtemaSelect.innerHTML = '<option value="">Cargando subtemas...</option>';
        consultarBtn.disabled = true;
        
        // Depuración: Mostrar URL a la que se envía la petición
        const url = '{{ url("sigem/ajax/consulta-express/subtemas") }}/' + temaId;
        //console.log('Express:Enviando petición a:', url);

        // Realizar petición fetch
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            //console.log('Express:Respuesta recibida, status:', response.status);
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            //console.log('Express:Datos recibidos:', data);

            // Limpiar selector de subtemas
            subtemaSelect.innerHTML = '<option value="">Seleccione un subtema...</option>';
            
            if (data.success && data.subtemas && data.subtemas.length > 0) {
                // Añadir opciones de subtemas
                data.subtemas.forEach(subtema => {
                    const option = document.createElement('option');
                    option.value = subtema.ce_subtema_id;
                    option.textContent = subtema.ce_subtema;
                    subtemaSelect.appendChild(option);
                });
                
                // Habilitar selector y botón
                subtemaSelect.disabled = false;
                consultarBtn.disabled = false;
                //console.log('Express:Subtemas cargados:', data.subtemas.length);
            } else {
                subtemaSelect.innerHTML = '<option value="">No hay subtemas disponibles</option>';
                consultarBtn.disabled = true;
                //console.log('Express:No se encontraron subtemas');
            }
        })
        .catch(error => {
            console.error('Error al cargar subtemas:', error);
            subtemaSelect.innerHTML = '<option value="">Error al cargar subtemas</option>';
            consultarBtn.disabled = true;
        });
    }
    
    // Función para cargar contenido
    function cargarContenido(subtemaId) {
        //console.log('Express:Cargando contenido para subtema ID:', subtemaId);
        showLoader();
        
        const url = '{{ url("sigem/ajax/consulta-express/contenido") }}/' + subtemaId;
        //console.log('Express:URL de contenido:', url);

        // Realizar petición fetch
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            //console.log('Respuesta de contenido recibida:', response.status);
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            //console.log('Express: Datos de contenido:', data);
            if (data.success && data.contenido) {
                // Mostrar contenido y metadata
                contenidoContainer.innerHTML = data.contenido.ce_contenido;
                fechaActualizacion.textContent = data.actualizado;
                metadataDiv.style.display = 'block';
                
                // Actualizar URL sin recargar la página (para poder compartir o guardar en favoritos)
                updateUrlParams();
            } else {
                contenidoContainer.innerHTML = '<div class="alert alert-warning">No se encontró contenido para el subtema seleccionado.</div>';
                metadataDiv.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error al cargar contenido:', error);
            showError('No se pudo cargar el contenido. Intente nuevamente: ' + error.message);
        });
    }
    
    // Función para actualizar parámetros de URL sin recargar
    function updateUrlParams() {
        if (window.history && window.history.pushState) {
            const temaId = temaSelect.value;
            const subtemaId = subtemaSelect.value;
            
            // Obtener URL actual y separar base de parámetros
            const currentUrl = window.location.href;
            const baseUrl = currentUrl.split('?')[0];
            
            // Crear objeto URLSearchParams para manejar parámetros fácilmente
            const searchParams = new URLSearchParams(window.location.search);
            
            // Actualizar parámetros de tema y subtema
            if (temaId) {
                searchParams.set('ce_tema_id', temaId);
            } else {
                searchParams.delete('ce_tema_id');
            }
            
            if (subtemaId) {
                searchParams.set('ce_subtema_id', subtemaId);
            } else {
                searchParams.delete('ce_subtema_id');
            }
            
            // Construir nueva URL
            const newUrl = baseUrl + (searchParams.toString() ? '?' + searchParams.toString() : '');
            
            // Actualizar URL sin recargar
            window.history.pushState({path: newUrl}, '', newUrl);
        }
    }
    
    // === EVENT LISTENERS ===
    
    // Cuando cambia el tema - Asegurarse de que este evento se ejecute
    temaSelect.addEventListener('change', function() {
        const temaId = this.value;
        //console.log('Express:Tema seleccionado:', temaId); // Añadir log para depuración

        if (temaId) {
            cargarSubtemas(temaId);
            
            // Limpiar contenido si había algo mostrado
            contenidoContainer.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-2"></i>
                    <p class="mt-2">Seleccione un subtema para ver la información</p>
                </div>
            `;
            metadataDiv.style.display = 'none';
        } else {
            // Resetear subtemas y contenido
            subtemaSelect.innerHTML = '<option value="">Primero seleccione un tema</option>';
            subtemaSelect.disabled = true;
            consultarBtn.disabled = true;
            
            contenidoContainer.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="bi bi-info-circle fs-2"></i>
                    <p class="mt-2">Seleccione un tema y subtema para ver la información</p>
                </div>
            `;
            metadataDiv.style.display = 'none';
        }
    });
    
    // Cuando cambia el subtema - Auto-cargar contenido
    subtemaSelect.addEventListener('change', function() {
        const subtemaId = this.value;
        
        if (subtemaId) {
            cargarContenido(subtemaId);
            consultarBtn.disabled = false;
        } else {
            consultarBtn.disabled = true;
        }
    });
    
    // Cuando se hace clic en el botón consultar
    consultarBtn.addEventListener('click', function() {
        const subtemaId = subtemaSelect.value;
        
        if (subtemaId) {
            cargarContenido(subtemaId);
        }
    });
    
    // Cargar contenido inicial si hay tema y subtema en la URL
    const initialTemaId = '{{ request('ce_tema_id') }}';
    const initialSubtemaId = '{{ request('ce_subtema_id') }}';
    
    if (initialTemaId && initialSubtemaId) {
        // Si tenemos ambos parámetros, cargar el contenido
        cargarContenido(initialSubtemaId);
    } else if (initialTemaId) {
        // Si solo tenemos tema, cargar los subtemas
        cargarSubtemas(initialTemaId);
    }
});
</script>