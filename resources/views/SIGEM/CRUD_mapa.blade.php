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
                                    <th>Nombre Mapa</th>
                                    <th>Nombre Sección</th>
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
                                    
                                    <td><strong>{{ $mapa->nombre_mapa }}</strong></td>
                                    <td>{{ $mapa->nombre_seccion ?? 'Sin sección' }}</td>
                                    
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
                                                 class="img-thumbnail" style="max-width: 50px; max-height: 50px;">
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
                                                <i class="bi bi-pencil-square"></i>
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
                        <div class="col-12">
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

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="nombre_seccion" class="form-label">Nombre de Sección <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre_seccion') is-invalid @enderror" id="nombre_seccion" name="nombre_seccion" 
                                       placeholder="Ej: Cartografía Básica" value="{{ old('nombre_seccion') }}" required>
                                @error('nombre_seccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                          placeholder="Describe brevemente el contenido del mapa...">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
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
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="codigo_mapa" class="form-label">Código del Mapa</label>
                                <input type="text" class="form-control" id="codigo_mapa" name="codigo_mapa" 
                                       placeholder="http://www.arcgis.com/" value="{{ old('codigo_mapa') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="icono" class="form-label">Icono (PNG)</label>
                                
                                <!-- Vista previa del icono -->
                                <div id="preview_icono_create" class="border rounded p-3 mb-3" style="min-height: 120px; background-color: #f8f9fa;">
                                    <div id="icono_nuevo_container">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image" style="font-size: 2rem;"></i>
                                            <p class="small mt-2 mb-0">Sin icono</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Input de archivo oculto -->
                                <input type="file" class="d-none @error('icono') is-invalid @enderror" 
                                       id="icono" name="icono" accept=".png">
                                
                                <!-- Botones de acción -->
                                <div id="botones_icono_create" class="d-flex gap-2">
                                    <button type="button" class="btn btn-success btn-sm" id="btn_agregar_icono_create">
                                        <i class="bi bi-plus-circle"></i> Agregar imagen
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm d-none" id="btn_remover_icono_create">
                                        Remover
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm d-none" id="btn_cambiar_icono_create">
                                        <i class="bi bi-arrow-clockwise"></i> Cambiar imagen
                                    </button>
                                </div>
                                
                                <small class="form-text text-muted d-block mt-2">Solo archivos PNG.</small>
                                @error('icono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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
                <h5 class="modal-title"> <i class="bi bi-pencil-square"></i> Editar Mapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarMapa" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_mapa_id" name="mapa_id">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_nombre_mapa" class="form-label">Nombre del Mapa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nombre_mapa" name="nombre_mapa" 
                                       placeholder="Ej: Mapa de División Política" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_nombre_seccion" class="form-label">Nombre de Sección</label>
                                <input type="text" class="form-control" id="edit_nombre_seccion" name="nombre_seccion" 
                                       placeholder="Ej: Cartografía Básica">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3" 
                                          placeholder="Describe brevemente el contenido del mapa..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_enlace" class="form-label">Enlace</label>
                                <input type="url" class="form-control" id="edit_enlace" name="enlace" 
                                       placeholder="https://ejemplo.com/mapa.html">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_codigo_mapa" class="form-label">Código del Mapa</label>
                                <input type="text" class="form-control" id="edit_codigo_mapa" name="codigo_mapa" 
                                       placeholder="http://www.arcgis.com/">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_icono" class="form-label">Cambiar Icono (PNG)</label>
                                
                                <!-- Vista previa del icono -->
                                <div id="preview_icono_edit" class="border rounded p-3 mb-3" style="min-height: 120px; background-color: #f8f9fa;">
                                    <div id="icono_actual_container">
                                    </div>
                                </div>
                                
                                <!-- Input de archivo oculto -->
                                <input type="file" class="d-none" id="edit_icono" name="icono" accept=".png">
                                
                                <!-- Botones de acción -->
                                <div id="botones_icono_edit" class="d-flex gap-2">
                                    <button type="button" class="btn btn-success btn-sm" id="btn_agregar_icono_edit">
                                        <i class="bi bi-plus-circle"></i> Agregar imagen
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm d-none" id="btn_remover_icono_edit">
                                        Remover
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm d-none" id="btn_cambiar_icono_edit">
                                        <i class="bi bi-arrow-clockwise"></i> Cambiar imagen
                                    </button>
                                </div>
                                
                                <small class="form-text text-muted d-block mt-2">Solo archivos PNG. Tamaño recomendado: 64x64px (Max: 2MB)</small>
                            </div>
                        </div>
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
                width: "5%",
                className: "text-center"
            },
            {
                targets: 1, // Columna Nombre Mapa
                width: "20%"
            },
            {
                targets: 2, // Columna Nombre Sección
                width: "15%"
            },
            {
                targets: 3, // Columna Descripción
                width: "25%"
            },
            {
                targets: 4, // Columna Enlace
                width: "5%",
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                targets: 5, // Columna Icono
                width: "10%",
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
        }

    });
    @endif

    // ========================================
    // EVENTOS PARA MODAL DE CREACIÓN
    // ========================================
    
    // Botón agregar/cambiar imagen para CREAR
    $('#btn_agregar_icono_create, #btn_cambiar_icono_create').on('click', function() {
        $('#icono').click();
    });
    
    // Botón remover imagen para CREAR
    $('#btn_remover_icono_create').on('click', function() {
        // Limpiar input y vista previa
        $('#icono').val('');
        $('#icono_nuevo_container').html(`
            <div class="text-center text-muted">
                <i class="bi bi-image" style="font-size: 2rem;"></i>
                <p class="small mt-2 mb-0">Sin icono</p>
            </div>
        `);
        
        // Cambiar botones
        $('#btn_agregar_icono_create').removeClass('d-none');
        $('#btn_remover_icono_create').addClass('d-none');
        $('#btn_cambiar_icono_create').addClass('d-none');
    });
    
    // Manejar cambio de archivo para CREAR
    $('#icono').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#icono_nuevo_container').html(`
                    <div class="text-center">
                        <img src="${e.target.result}" alt="Vista previa" style="max-width: 100%; max-height: 100px;" class="rounded">
                        <p class="text-muted small mt-2 mb-0">Nueva imagen seleccionada</p>
                    </div>
                `);
                
                // Cambiar botones
                $('#btn_agregar_icono_create').addClass('d-none');
                $('#btn_remover_icono_create').removeClass('d-none');
                $('#btn_cambiar_icono_create').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // ========================================
    // EVENTOS PARA MODAL DE EDICIÓN
    // ========================================

    // Botón agregar/cambiar imagen para EDITAR
    $('#btn_agregar_icono_edit, #btn_cambiar_icono_edit').on('click', function() {
        $('#edit_icono').click();
    });
    
    // Botón remover imagen para EDITAR
    $('#btn_remover_icono_edit').on('click', function() {
        // Limpiar input y vista previa
        $('#edit_icono').val('');
        $('#icono_actual_container').html(`
            <div class="text-center text-muted">
                <i class="bi bi-image" style="font-size: 2rem;"></i>
                <p class="small mt-2 mb-0">Sin icono</p>
            </div>
        `);
        
        // Cambiar botones
        $('#btn_agregar_icono_edit').removeClass('d-none');
        $('#btn_remover_icono_edit').addClass('d-none');
        $('#btn_cambiar_icono_edit').addClass('d-none');
    });
    
    // Manejar cambio de archivo para EDITAR
    $('#edit_icono').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#icono_actual_container').html(`
                    <div class="text-center">
                        <img src="${e.target.result}" alt="Vista previa" style="max-width: 100%; max-height: 100px;" class="rounded">
                        <p class="text-muted small mt-2 mb-0">Nueva imagen seleccionada</p>
                    </div>
                `);
                
                // Cambiar botones
                $('#btn_agregar_icono_edit').addClass('d-none');
                $('#btn_remover_icono_edit').removeClass('d-none');
                $('#btn_cambiar_icono_edit').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

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
    const nombre_mapa = celdas[1].querySelector('strong').textContent.trim();
    const nombre_seccion = celdas[2].textContent.trim();
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
    
    // Configurar vista previa del icono
    const iconoContainer = document.getElementById('icono_actual_container');
    const btnAgregar = document.getElementById('btn_agregar_icono_edit');
    const btnRemover = document.getElementById('btn_remover_icono_edit');
    const btnCambiar = document.getElementById('btn_cambiar_icono_edit');
    
    if (icono_img) {
        // Mostrar icono existente
        iconoContainer.innerHTML = `
            <div class="text-center">
                <img src="${icono_img.src}" alt="Icono actual" style="max-width: 100%; max-height: 100px;" class="rounded">
                <p class="text-muted small mt-2 mb-0">Icono actual</p>
            </div>
        `;
        btnAgregar.classList.add('d-none');
        btnRemover.classList.remove('d-none');
        btnCambiar.classList.remove('d-none');
    } else {
        // Mostrar placeholder sin icono
        iconoContainer.innerHTML = `
            <div class="text-center text-muted">
                <i class="bi bi-image" style="font-size: 2rem;"></i>
                <p class="small mt-2 mb-0">Sin icono</p>
            </div>
        `;
        btnAgregar.classList.remove('d-none');
        btnRemover.classList.add('d-none');
        btnCambiar.classList.add('d-none');
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

// Limpiar modales al cerrarse
document.getElementById('modalAgregarMapa')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    // Resetear la vista previa del icono
    document.getElementById('icono_nuevo_container').innerHTML = `
        <div class="text-center text-muted">
            <i class="bi bi-image" style="font-size: 2rem;"></i>
            <p class="small mt-2 mb-0">Sin icono</p>
        </div>
    `;
    // Resetear botones
    document.getElementById('btn_agregar_icono_create').classList.remove('d-none');
    document.getElementById('btn_remover_icono_create').classList.add('d-none');
    document.getElementById('btn_cambiar_icono_create').classList.add('d-none');
});

document.getElementById('modalEditarMapa')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('icono_actual_container').innerHTML = '';
});
</script>