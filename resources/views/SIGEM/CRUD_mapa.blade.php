<!-- Header del CRUD -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-map"></i> Panel CRUD de Mapas</h5>
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
                <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalAgregarMapa">
                    <i class="bi bi-plus-circle"></i> Nuevo Mapa
                </button>
                <small class="text-muted d-block mt-2">Total de registros: <strong>{{ count($mapas ?? []) }}</strong></small>
            </div>
        </div>
    </div>
</div>


<!-- Tabla de datos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-table"></i> Listado de Mapas</h6>
            </div>
            <div class="card-body">
                @if(isset($mapas) && count($mapas) > 0)
                    <div class="table-responsive">
                        <table id="tablaMapas" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Sección</th>
                                    <th>Nombre Mapa</th>
                                    <th>Descripción</th>
                                    <th>Enlace</th>
                                    <th>Icono</th>
                                    <th>Código</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mapas as $mapa)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $mapa->mapa_id }}</span></td>
                                    <td>{{ $mapa->nombre_seccion ?? 'Sin sección' }}</td>
                                    <td><strong>{{ $mapa->nombre_mapa }}</strong></td>
                                    <td>
                                        @if(strlen($mapa->descripcion ?? '') > 50)
                                            <span title="{{ $mapa->descripcion }}">
                                                {{ substr($mapa->descripcion, 0, 50) }}...
                                            </span>
                                        @else
                                            {{ $mapa->descripcion ?? 'Sin descripción' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($mapa->enlace)
                                            <a href="{{ $mapa->enlace }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">Sin enlace</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($mapa->icono)
                                            <img src="{{ asset('img/SIGEM_mapas/' . $mapa->icono) }}" alt="Icono" 
                                                 class="img-thumbnail" style="max-width: 30px; max-height: 30px;">
                                        @else
                                            <i class="bi bi-image text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $mapa->codigo_mapa ?? 'Sin código' }}</code>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-warning" 
                                                    title="Editar" 
                                                    onclick="editarMapa({{ $mapa->mapa_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    title="Eliminar" 
                                                    onclick="eliminarMapa({{ $mapa->mapa_id }}, '{{ addslashes($mapa->nombre_mapa) }}')">
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
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No hay mapas registrados</h5>
                        <p class="text-muted">Comienza agregando tu primer mapa haciendo clic en el botón "Nuevo Mapa".</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarMapa">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Mapa
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar nuevo mapa -->
<div class="modal fade" id="modalAgregarMapa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Mapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarMapa" method="POST" action="{{ route('sigem.admin.mapas.crear') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_seccion" class="form-label">Nombre de Sección</label>
                                <input type="text" class="form-control" id="nombre_seccion" name="nombre_seccion" 
                                       placeholder="Ej: Cartografía Básica" value="{{ old('nombre_seccion') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_mapa" class="form-label">Nombre del Mapa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre_mapa') is-invalid @enderror" 
                                       id="nombre_mapa" name="nombre_mapa" 
                                       placeholder="Ej: Mapa de División Política" 
                                       value="{{ old('nombre_mapa') }}" required>
                                @error('nombre_mapa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Describe brevemente el contenido del mapa...">{{ old('descripcion') }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="enlace" class="form-label">Enlace</label>
                                <input type="url" class="form-control @error('enlace') is-invalid @enderror" 
                                       id="enlace" name="enlace" 
                                       placeholder="https://ejemplo.com/mapa.html"
                                       value="{{ old('enlace') }}">
                                @error('enlace')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="codigo_mapa" class="form-label">Código del Mapa</label>
                                <input type="text" class="form-control" id="codigo_mapa" name="codigo_mapa" 
                                       placeholder="MP001" value="{{ old('codigo_mapa') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="icono" class="form-label">Icono (PNG)</label>
                        <input type="file" class="form-control @error('icono') is-invalid @enderror" 
                               id="icono" name="icono" accept=".png">
                        <small class="form-text text-muted">Solo archivos PNG. Tamaño recomendado: 64x64px (Max: 2MB)</small>
                        @error('icono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Mapa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar mapa -->
<div class="modal fade" id="modalEditarMapa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Mapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarMapa" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_mapa_id" name="mapa_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nombre_seccion" class="form-label">Nombre de Sección</label>
                                <input type="text" class="form-control" id="edit_nombre_seccion" name="nombre_seccion" 
                                       placeholder="Ej: Cartografía Básica">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nombre_mapa" class="form-label">Nombre del Mapa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nombre_mapa" name="nombre_mapa" 
                                       placeholder="Ej: Mapa de División Política" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3" 
                                  placeholder="Describe brevemente el contenido del mapa..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_enlace" class="form-label">Enlace</label>
                                <input type="url" class="form-control" id="edit_enlace" name="enlace" 
                                       placeholder="https://ejemplo.com/mapa.html">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_codigo_mapa" class="form-label">Código del Mapa</label>
                                <input type="text" class="form-control" id="edit_codigo_mapa" name="codigo_mapa" 
                                       placeholder="MP001">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_icono" class="form-label">Cambiar Icono (PNG)</label>
                        <input type="file" class="form-control" id="edit_icono" name="icono" accept=".png">
                        <small class="form-text text-muted">Solo archivos PNG. Dejar vacío para mantener el icono actual.</small>
                        <div id="icono_actual" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Mapa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript para acciones CRUD -->
<script>
// Inicializar DataTables
$(document).ready(function() {
    @if(isset($mapas) && count($mapas) > 0)
    $('#tablaMapas').DataTable({
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
                targets: 1, // Columna Nombre Sección
                width: "15%"
            },
            {
                targets: 2, // Columna Nombre Mapa
                width: "20%"
            },
            {
                targets: 3, // Columna Descripción
                width: "25%"
            },
            {
                targets: 4, // Columna Enlace
                width: "8%",
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                targets: 5, // Columna Icono
                width: "6%",
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                targets: 6, // Columna Código
                width: "10%",
                className: "text-center"
            },
            {
                targets: 7, // Columna Acciones
                width: "10%",
                className: "text-center",
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'asc']], // Ordenar por nombre_mapa por defecto
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
const routes = {
    update: '{{ route("sigem.admin.mapas.actualizar", ":id") }}',
    delete: '{{ route("sigem.admin.mapas.eliminar", ":id") }}'
};

function editarMapa(id) {
    // Buscar los datos del mapa en la tabla
    const fila = event.target.closest('tr');
    const celdas = fila.cells;
    
    // Extraer datos de las celdas
    const nombre_seccion = celdas[1].textContent.trim();
    const nombre_mapa = celdas[2].querySelector('strong').textContent.trim();
    const descripcion_completa = celdas[3].querySelector('span') ? 
        celdas[3].querySelector('span').getAttribute('title') || celdas[3].textContent.trim() : 
        celdas[3].textContent.trim();
    const enlace_btn = celdas[4].querySelector('a');
    const enlace = enlace_btn ? enlace_btn.getAttribute('href') : '';
    const codigo = celdas[6].querySelector('code').textContent.trim();
    const icono_img = celdas[5].querySelector('img');
    
    // Llenar el modal de edición
    document.getElementById('edit_mapa_id').value = id;
    document.getElementById('edit_nombre_seccion').value = nombre_seccion === 'Sin sección' ? '' : nombre_seccion;
    document.getElementById('edit_nombre_mapa').value = nombre_mapa;
    document.getElementById('edit_descripcion').value = descripcion_completa === 'Sin descripción' ? '' : descripcion_completa;
    document.getElementById('edit_enlace').value = enlace;
    document.getElementById('edit_codigo_mapa').value = codigo === 'Sin código' ? '' : codigo;
    
    // Mostrar icono actual
    const iconoActualDiv = document.getElementById('icono_actual');
    if (icono_img) {
        iconoActualDiv.innerHTML = `
            <small class="text-muted">Icono actual:</small><br>
            <img src="${icono_img.src}" alt="Icono actual" class="img-thumbnail" style="max-width: 60px;">
        `;
    } else {
        iconoActualDiv.innerHTML = '<small class="text-muted">Sin icono actual</small>';
    }
    
    // Actualizar la acción del formulario usando las rutas named
    const form = document.getElementById('formEditarMapa');
    form.action = routes.update.replace(':id', id);
    
    // Mostrar modal
    new bootstrap.Modal(document.getElementById('modalEditarMapa')).show();
}

function eliminarMapa(id, nombre) {
    if (confirm('¿Estás seguro de eliminar el registro del mapa "' + nombre + '"?\n\nEsta acción no se puede deshacer y eliminará también el archivo de icono asociado.')) {
        // Crear formulario temporal para envío DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routes.delete.replace(':id', id);
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

// Limpiar modal al cerrarlo
document.getElementById('modalAgregarMapa')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});

document.getElementById('modalEditarMapa')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('icono_actual').innerHTML = '';
});
</script>