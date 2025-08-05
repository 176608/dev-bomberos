<div class="container py-4">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Consulta Express</h5>
        </div>
        <div class="card-body">
            <div class="container" id="consulta-express-container">
                <div class="row">
                    <!-- Columna de imagen -->
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <img src="{{ asset('imagenes/express.png') }}" alt="Consulta Express" class="img-fluid rounded shadow-sm" style="max-height: 220px;">
                    </div>
                    
                    <!-- Columna de selectores -->
                    <div class="col-md-3">
                        @php
                            use App\Models\SIGEM\ce_tema;
                            use App\Models\SIGEM\ce_subtema;
                            use App\Models\SIGEM\ce_contenido;
                            
                            // Obtener datos iniciales
                            $temas = ce_tema::orderBy('ce_tema_id')->get();
                            
                            // Manejo de selección
                            $tema_id = request('ce_tema_id');
                            $subtema_id = request('ce_subtema_id');
                            
                            // Cargar subtemas si hay tema seleccionado
                            $subtemas = ($tema_id) 
                                ? ce_subtema::where('ce_tema_id', $tema_id)->get() 
                                : collect([]);
                        @endphp
                        
                        <!-- Formulario sin action para manejar con AJAX -->
                        <form id="ce_form">
                            @csrf
                            
                            <div class="form-group mb-3">
                                <label for="ce_tema_select" class="form-label">ceTema:</label>
                                <select id="ce_tema_select" name="ce_tema_id" class="form-select">
                                    <option value="">Seleccione un tema...</option>
                                    @foreach($temas as $tema)
                                        <option value="{{ $tema->ce_tema_id }}" {{ $tema_id == $tema->ce_tema_id ? 'selected' : '' }}>
                                            {{ $tema->tema }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="ce_subtema_select" class="form-label">ceSubtema:</label>
                                <select id="ce_subtema_select" name="ce_subtema_id" class="form-select" {{ count($subtemas) > 0 ? '' : 'disabled' }}>
                                    <option value="">{{ count($subtemas) > 0 ? 'Seleccione un subtema...' : 'Primero seleccione un tema' }}</option>
                                    @foreach($subtemas as $subtema)
                                        <option value="{{ $subtema->ce_subtema_id }}" {{ $subtema_id == $subtema->ce_subtema_id ? 'selected' : '' }}>
                                            {{ $subtema->ce_subtema }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="button" id="ce_consultar_btn" class="btn btn-primary w-100" {{ count($subtemas) > 0 ? '' : 'disabled' }}>
                                Consultar <i class="bi bi-arrow-right-circle ms-1"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Columna de contenido -->
                    <div class="col-md-6">
                        <div id="ce_contenido_container" class="border rounded p-3" style="min-height: 250px; max-height: 500px; overflow-y: auto;">
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-info-circle fs-2"></i>
                                <p class="mt-2">Seleccione un tema y subtema para ver la información</p>
                            </div>
                        </div>
                        
                        <div id="ce_metadata" class="text-end text-muted small mt-2" style="display: none;">
                            Última actualización: <span id="ce_fecha_actualizacion">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Esperamos a que el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const temaSelect = document.getElementById('ce_tema_select');
    const subtemaSelect = document.getElementById('ce_subtema_select');
    const consultarBtn = document.getElementById('ce_consultar_btn');
    const contenidoContainer = document.getElementById('ce_contenido_container');
    const metadataDiv = document.getElementById('ce_metadata');
    const fechaActualizacion = document.getElementById('ce_fecha_actualizacion');
    
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
        // Deshabilitar el selector de subtemas mientras se cargan
        subtemaSelect.disabled = true;
        subtemaSelect.innerHTML = '<option value="">Cargando subtemas...</option>';
        consultarBtn.disabled = true;
        
        // Preparar datos para la petición
        const data = new FormData();
        data.append('tema_id', temaId);
        data.append('_token', document.querySelector('input[name="_token"]').value);
        
        // Realizar petición fetch
        fetch('{{ url("sigem/ajax/consulta-express/subtemas") }}/' + temaId, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
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
            } else {
                subtemaSelect.innerHTML = '<option value="">No hay subtemas disponibles</option>';
                consultarBtn.disabled = true;
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
        showLoader();
        
        // Realizar petición fetch
        fetch('{{ url("sigem/ajax/consulta-express/contenido") }}/' + subtemaId, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
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
            showError('No se pudo cargar el contenido. Intente nuevamente.');
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
    
    // Cuando cambia el tema
    temaSelect.addEventListener('change', function() {
        const temaId = this.value;
        
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
        
        // Actualizar URL
        updateUrlParams();
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