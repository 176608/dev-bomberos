<!-- Header del CRUD -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-bookmarks"></i> Panel CRUD de Subtemas</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Errores de validación:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#modalAgregarSubtema">
                    <i class="bi bi-plus-circle"></i> Nuevo Subtema
                </button>
                <small class="text-muted d-block mt-2">Total de registros: <strong>{{ count($subtemas ?? []) }}</strong></small>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de datos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-table"></i> Listado de Subtemas</h6>
            </div>
            <div class="card-body">
                @if(isset($subtemas) && count($subtemas) > 0)
                    <div class="table-responsive">
                        <table id="tablaSubtemas" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Título del Subtema</th>
                                    <th>Tema Padre</th>
                                    <th>Imagen</th>
                                    <th>Orden</th>
                                    <th>Clave Subtema</th>
                                    <th>Clave Efectiva</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subtemas as $subtema)
                                @php
                                    $infoClave = $subtema->obtenerInfoClave();
                                @endphp
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $subtema->subtema_id }}</span></td>
                                    <td>
                                        <strong>{{ $subtema->subtema_titulo }}</strong>
                                    </td>
                                    <td>
                                        @if($subtema->tema)
                                            <span class="badge bg-success">{{ $subtema->tema->tema_titulo }}</span>
                                        @else
                                            <span class="text-danger">Sin tema asignado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subtema->imagen)
                                            <img src="{{ asset('imagenes/subtemas_u/' . $subtema->imagen) }}" alt="Imagen" 
                                                 class="img-thumbnail" style="max-width: 40px; max-height: 40px;">
                                        @else
                                            <i class="bi bi-image text-muted"></i>
                                        @endif
                                    </td>
                                    <td data-order="{{ $subtema->orden_indice ?? 0 }}">
                                        <span class="badge bg-info">{{ $subtema->orden_indice ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($subtema->clave_subtema)
                                            <code>{{ $subtema->clave_subtema }}</code>
                                        @else
                                            <span class="text-muted">Sin clave</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($infoClave['clave_efectiva'])
                                            <span class="badge 
                                                @if($infoClave['origen'] == 'propia') bg-primary
                                                @elseif($infoClave['origen'] == 'heredada del tema') bg-secondary
                                                @elseif($infoClave['origen'] == 'propia (menor orden)') bg-success
                                                @else bg-warning text-dark
                                                @endif
                                            " title="Origen: {{ $infoClave['origen'] }}">
                                                {{ $infoClave['clave_efectiva'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin clave</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-warning" 
                                                    title="Editar" 
                                                    onclick="editarSubtema({{ $subtema->subtema_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    title="Eliminar" 
                                                    onclick="eliminarSubtema({{ $subtema->subtema_id }}, '{{ addslashes($subtema->subtema_titulo) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-bookmarks text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No hay subtemas registrados</h5>
                        <p class="text-muted">Comienza agregando tu primer subtema haciendo clic en el botón "Nuevo Subtema".</p>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalAgregarSubtema">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Subtema
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar nuevo subtema -->
<div class="modal fade" id="modalAgregarSubtema" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Subtema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarSubtema" method="POST" action="{{ route('sigem.admin.subtemas.crear') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="subtema_titulo" class="form-label">Título del Subtema <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subtema_titulo') is-invalid @enderror" 
                                       id="subtema_titulo" name="subtema_titulo" 
                                       placeholder="Ej: Población por Edad y Sexo" 
                                       value="{{ old('subtema_titulo') }}" required>
                                @error('subtema_titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="orden_indice" class="form-label">Orden <small class="text-muted">(en el tema)</small></label>
                                <input type="number" class="form-control @error('orden_indice') is-invalid @enderror" 
                                       id="orden_indice" name="orden_indice" 
                                       placeholder="Auto" min="1" max="999" 
                                       value="{{ old('orden_indice') }}">
                                <small class="form-text text-muted">Dejar vacío para usar el siguiente disponible</small>
                                @error('orden_indice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tema_id" class="form-label">Tema Padre <span class="text-danger">*</span></label>
                                <select class="form-select @error('tema_id') is-invalid @enderror" 
                                        id="tema_id" name="tema_id" required>
                                    <option value="">Seleccionar tema...</option>
                                    @if(isset($temas))
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->tema_id }}" 
                                                    {{ old('tema_id') == $tema->tema_id ? 'selected' : '' }}>
                                                {{ $tema->tema_titulo }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('tema_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="clave_subtema" class="form-label">Clave del Subtema</label>
                                <input type="text" class="form-control @error('clave_subtema') is-invalid @enderror" 
                                       id="clave_subtema" name="clave_subtema" 
                                       placeholder="Ej: DEM001A" maxlength="15"
                                       value="{{ old('clave_subtema') }}">
                                <small class="form-text text-muted">Opcional. Si no se especifica, heredará la clave del tema.</small>
                                @error('clave_subtema')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen del Subtema</label>
                        <input type="file" class="form-control @error('imagen') is-invalid @enderror" 
                               id="imagen" name="imagen" accept="image/*">
                        <small class="form-text text-muted">Formatos soportados: JPG, PNG, GIF. Tamaño máximo: 2MB</small>
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Información sobre Orden:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Orden automático:</strong> Si dejas el campo vacío, se asignará automáticamente el siguiente número disponible dentro del tema seleccionado.</li>
                            <li><strong>Orden manual:</strong> Puedes especificar un número específico, pero debe ser único dentro del tema.</li>
                            <li><strong>Rango:</strong> El orden debe estar entre 1 y 999.</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Guardar Subtema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar subtema -->
<div class="modal fade" id="modalEditarSubtema" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Subtema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarSubtema" method="POST" action="#" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_subtema_id" name="subtema_id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_subtema_titulo" class="form-label">Título del Subtema <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_subtema_titulo" name="subtema_titulo" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_orden_indice" class="form-label">Orden</label>
                                <input type="number" class="form-control" id="edit_orden_indice" name="orden_indice" 
                                       min="0" max="999">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tema_id" class="form-label">Tema Padre <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_tema_id" name="tema_id" required>
                                    <option value="">Seleccionar tema...</option>
                                    @if(isset($temas))
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->tema_id }}">{{ $tema->tema_titulo }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_clave_subtema" class="form-label">Clave del Subtema</label>
                                <input type="text" class="form-control" id="edit_clave_subtema" name="clave_subtema" maxlength="15">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_imagen" class="form-label">Cambiar Imagen</label>
                        <input type="file" class="form-control" id="edit_imagen" name="imagen" accept="image/*">
                        <div id="imagen_actual" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Subtema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript actualizado -->
<script>
// Inicializar DataTables
$(document).ready(function() {
    @if(isset($subtemas) && count($subtemas) > 0)
    $('#tablaSubtemas').DataTable({
        responsive: true,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        columnDefs: [
            {
                targets: 0, // Columna ID
                width: "6%",
                className: "text-center"
            },
            {
                targets: 1, // Columna Título Subtema
                width: "25%"
            },
            {
                targets: 2, // Columna Tema Padre
                width: "15%"
            },
            {
                targets: 3, // Columna Imagen
                width: "8%",
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                targets: 4, // Columna Orden
                width: "8%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 5, // Columna Clave Subtema
                width: "12%",
                className: "text-center"
            },
            {
                targets: 6, // Columna Clave Efectiva
                width: "12%",
                className: "text-center"
            },
            {
                targets: 7, // Columna Acciones
                width: "120px",
                className: "text-center",
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'asc'], [4, 'asc']], // Ordenar por tema padre y luego por orden
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Ya no necesitamos reinicializar tooltips de Bootstrap
        }
    });
    @endif
});

// Definir rutas para usar en JavaScript
const routesSubtemas = {
    update: '{{ route("sigem.admin.subtemas.actualizar", ":id") }}',
    delete: '{{ route("sigem.admin.subtemas.eliminar", ":id") }}',
    siguienteOrden: '{{ url("/sigem/admin/subtemas/siguiente-orden") }}'
};

function editarSubtema(id) {
    // Buscar los datos del subtema en la tabla
    const fila = event.target.closest('tr');
    const subtema_titulo = fila.cells[1].querySelector('strong').textContent;
    const tema_badge = fila.cells[2].querySelector('.badge');
    const orden_indice = fila.cells[4].querySelector('.badge').textContent;
    const clave_code = fila.cells[5].querySelector('code');
    
    // Llenar el modal de edición
    document.getElementById('edit_subtema_id').value = id;
    document.getElementById('edit_subtema_titulo').value = subtema_titulo;
    document.getElementById('edit_orden_indice').value = orden_indice;
    document.getElementById('edit_clave_subtema').value = clave_code ? clave_code.textContent : '';
    
    // Seleccionar el tema en el select
    const temaSelect = document.getElementById('edit_tema_id');
    if (tema_badge) {
        const temaTexto = tema_badge.textContent;
        for (let option of temaSelect.options) {
            if (option.text === temaTexto) {
                option.selected = true;
                break;
            }
        }
    }
    
    // Mostrar imagen actual si existe
    const imagenCell = fila.cells[3];
    const imagenImg = imagenCell.querySelector('img');
    const imagenActualDiv = document.getElementById('imagen_actual');
    
    if (imagenImg) {
        imagenActualDiv.innerHTML = `
            <small class="text-muted">Imagen actual:</small><br>
            <img src="${imagenImg.src}" alt="Imagen actual" class="img-thumbnail" style="max-width: 100px;">
        `;
    } else {
        imagenActualDiv.innerHTML = '<small class="text-muted">Sin imagen actual</small>';
    }
    
    // Actualizar la acción del formulario
    const form = document.getElementById('formEditarSubtema');
    form.action = routesSubtemas.update.replace(':id', id);
    
    // Mostrar modal
    new bootstrap.Modal(document.getElementById('modalEditarSubtema')).show();
}

function eliminarSubtema(id, titulo) {
    // Obtener información del tema
    const fila = event.target.closest('tr');
    const temaBadge = fila.cells[2].querySelector('.badge');
    const temaNombre = temaBadge ? temaBadge.textContent : 'tema desconocido';
    
    const mensaje = `¿Estás seguro de eliminar el subtema "${titulo}" del tema "${temaNombre}"?\n\n⚠️ Esta acción también eliminará:\n- La imagen asociada (si existe)\n- NO se puede deshacer\n\nNOTA: Si tiene cuadros estadísticos asociados, no se podrá eliminar.`;
    
    if (confirm(mensaje)) {
        // Crear formulario temporal para envío DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routesSubtemas.delete.replace(':id', id);
        form.style.display = 'none';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Method spoofing para DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Enviar formulario
        document.body.appendChild(form);
        form.submit();
    }
}

// FUNCIONALIDAD CLAVE: Auto-completar orden basado en el tema seleccionado
document.getElementById('tema_id')?.addEventListener('change', function() {
    const temaId = this.value;
    const ordenInput = document.getElementById('orden_indice');
    
    if (temaId) {
        // Obtener siguiente orden para el tema seleccionado mediante AJAX
        fetch(`/sigem/admin/subtemas/siguiente-orden/${temaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.siguiente_orden) {
                    ordenInput.placeholder = `Siguiente: ${data.siguiente_orden}`;
                    
                    // Si el campo está vacío, sugerir el siguiente orden
                    if (!ordenInput.value) {
                        ordenInput.value = data.siguiente_orden;
                    }
                }
            })
            .catch(error => {
                console.error('Error al obtener siguiente orden:', error);
                ordenInput.placeholder = 'Auto';
            });
    } else {
        ordenInput.placeholder = 'Auto';
        ordenInput.value = '';
    }
});

// Auto-generar clave basada en el título y tema
document.getElementById('subtema_titulo')?.addEventListener('input', function() {
    const titulo = this.value;
    const temaSelect = document.getElementById('tema_id');
    
    if (titulo && temaSelect.value) {
        // Generar clave sugerida basada en título (primeras letras)
        const clave = titulo.toUpperCase()
                          .replace(/[ÁÉÍÓÚÑÜ]/g, function(match) {
                              const replacements = {'Á':'A','É':'E','Í':'I','Ó':'O','Ú':'U','Ñ':'N','Ü':'U'};
                              return replacements[match];
                          })
                          .replace(/[^A-Z0-9]/g, '')
                          .substring(0, 6) + 'A';
        
        document.getElementById('clave_subtema').placeholder = `Sugerido: ${clave}`;
    }
});

// Limpiar modales al cerrar
document.getElementById('modalAgregarSubtema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('orden_indice').placeholder = 'Auto';
    document.getElementById('clave_subtema').placeholder = 'Ej: DEM001A';
});

document.getElementById('modalEditarSubtema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('imagen_actual').innerHTML = '';
});
</script>