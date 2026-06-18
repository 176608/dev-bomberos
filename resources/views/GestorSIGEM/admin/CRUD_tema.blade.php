<div class="row justify-content-center">
<div class="col-10">
<div class="card bg-dark bg-opacity-10 border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-dark bg-opacity-75 text-white border-bottom">
        <h5 class="mb-0"><i class="bi bi-bookmark"></i> Panel CRUD de Temas</h5>
        <div>
            <span class="badge bg-light text-dark me-2">Total: <strong>{{ count($temas ?? []) }}</strong></span>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarTema">
                <i class="bi bi-plus-circle"></i> Nuevo Tema
            </button>
        </div>
    </div>
    <div class="card-body bg-transparent">
        @if(isset($temas) && count($temas) > 0)
            <div class="table-responsive">
                <table id="tablaTemas" class="table table-striped table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Título del Tema</th>    
                            <th>Orden Índice</th>
                            <th>Clave Tema</th>
                            <th>Subtemas</th>
                            <th>Publicado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($temas as $tema)
                        <tr>
                            <td>
                                <strong>{{ $tema->tema_titulo }}</strong>
                                @if($tema->clave_tema)
                                    <br><small class="text-muted">Clave: {{ $tema->clave_tema }}</small>
                                @endif
                            </td>
                            <td data-order="{{ $tema->orden_indice ?? 0 }}">
                                <span class="badge bg-info">{{ $tema->orden_indice ?? 0 }}</span>
                            </td>
                            <td>
                                @if($tema->clave_tema)
                                    <span class="badge bg-primary">{{ $tema->clave_tema }}</span>
                                @else
                                    <span class="text-muted">Sin clave</span>
                                @endif
                            </td>
                            <td data-order="{{ $tema->subtemas()->count() ?? 0 }}">
                                @php
                                    $subtemas_count = $tema->subtemas()->count() ?? 0;
                                @endphp
                                @if($subtemas_count > 0)
                                    <span class="badge bg-warning">{{ $subtemas_count }} subtemas</span>
                                @else
                                    <span class="text-muted">Sin subtemas</span>
                                @endif
                            </td>
                            <td class="text-center" data-order="{{ $tema->publicado ? 1 : 0 }}">
                                @if($tema->publicado)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i></span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i></span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-warning" 
                                            title="Editar" 
                                            onclick="editarTema({{ $tema->tema_id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" 
                                            title="Eliminar" 
                                            onclick="eliminarTema({{ $tema->tema_id }}, '{{ addslashes($tema->tema_titulo) }}')">
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
                <i class="bi bi-bookmark text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">No hay temas registrados</h5>
                <p class="text-muted">Comienza agregando tu primer tema haciendo clic en el botón "Nuevo Tema".</p>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregarTema">
                    <i class="bi bi-plus-circle"></i> Agregar Primer Tema
                </button>
            </div>
        @endif
    </div>
</div>
</div>
</div>

<!-- Modal para agregar nuevo tema -->
<div class="modal fade" id="modalAgregarTema" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Tema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarTema" method="POST" action="{{ route('sgiem.admin.temas.crear') }}">
                @csrf
                <div class="modal-body bg-fonde">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="tema_titulo" class="form-label">Título del Tema <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('tema_titulo') is-invalid @enderror" 
                                       id="tema_titulo" name="tema_titulo" 
                                       placeholder="Ej: Demografía y Población" 
                                       value="{{ old('tema_titulo') }}" required>
                                @error('tema_titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="clave_tema" class="form-label">Clave del Tema</label>
                                <input type="text" class="form-control @error('clave_tema') is-invalid @enderror" 
                                       id="clave_tema" name="clave_tema" 
                                       placeholder="Ej: ECO" maxlength="10"
                                       value="{{ old('clave_tema') }}">
                                @error('clave_tema')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="orden_indice" class="form-label">Orden en Índice</label>
                                <input type="number" class="form-control @error('orden_indice') is-invalid @enderror" 
                                       id="orden_indice" name="orden_indice" 
                                       placeholder="1" min="0" max="999" 
                                       value="{{ old('orden_indice', 1) }}">
                                <small class="form-text text-muted">Orden de aparición en listados</small>
                                @error('orden_indice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 form-check form-switch mt-4">
                                <input type="checkbox" class="form-check-input" id="publicado" name="publicado" value="1" checked>
                                <label class="form-check-label" for="publicado">Publicado</label>
                                <small class="form-text text-muted d-block">Visible en SIGEM</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Información:</strong> Una vez creado el tema, podrás agregar subtemas asociados desde el panel de Subtemas.
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Tema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar tema -->
<div class="modal fade" id="modalEditarTema" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Tema</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarTema" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_tema_id" name="tema_id">
                <div class="modal-body bg-fonde">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_tema_titulo" class="form-label">Título del Tema <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_tema_titulo" name="tema_titulo" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_clave_tema" class="form-label">Clave del Tema</label>
                                <input type="text" class="form-control" id="edit_clave_tema" name="clave_tema" maxlength="10">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_orden_indice" class="form-label">Orden en Índice</label>
                                <input type="number" class="form-control" id="edit_orden_indice" name="orden_indice" 
                                       min="0" max="999">
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3 form-check form-switch mt-2">
                                <input type="checkbox" class="form-check-input" id="edit_publicado" name="publicado" value="1">
                                <label class="form-check-label" for="edit_publicado">Publicado</label>
                                <small class="form-text text-muted d-block">Visible en SIGEM</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Tema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enlaces a librerías DataTables 
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>-->

<!-- JavaScript funcional para acciones -->
<script>
// Inicializar DataTables
$(document).ready(function() {
    @if(isset($temas) && count($temas) > 0)
    $('#tablaTemas').DataTable({
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
                targets: 0, // Columna Título
                width: "30%"
            },
            {
                targets: 1, // Columna Orden
                width: "12%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 2, // Columna Clave
                width: "15%",
                className: "text-center"
            },
            {
                targets: 3, // Columna Subtemas
                width: "15%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 4, // Columna Publicado
                width: "10%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 5, // Columna Acciones
                width: "120px",
                className: "text-center",
                orderable: false,
                searchable: false
            }
        ],
        order: [[1, 'asc']], // Ordenar por orden_indice por defecto
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
const routesTemas = {
    update: '{{ route("sgiem.admin.temas.actualizar", ":id") }}',
    delete: '{{ route("sgiem.admin.temas.eliminar", ":id") }}'
};

function editarTema(id) {
    const fila = event.target.closest('tr');
    const tema_titulo = fila.cells[0].querySelector('strong').textContent;
    const orden_indice = fila.cells[1].getAttribute('data-order') || fila.cells[1].querySelector('.badge').textContent;
    const clave_tema_cell = fila.cells[2];
    const clave_tema = clave_tema_cell.querySelector('.badge')?.textContent || '';
    const publicado = fila.cells[4].querySelector('.badge.bg-success') !== null;
    
    document.getElementById('edit_tema_id').value = id;
    document.getElementById('edit_tema_titulo').value = tema_titulo;
    document.getElementById('edit_orden_indice').value = orden_indice;
    document.getElementById('edit_clave_tema').value = clave_tema;
    document.getElementById('edit_publicado').checked = publicado;
    
    const form = document.getElementById('formEditarTema');
    form.action = routesTemas.update.replace(':id', id);
    
    new bootstrap.Modal(document.getElementById('modalEditarTema')).show();
}

function eliminarTema(id, titulo) {
    const fila = event.target.closest('tr');
    const subtemasCell = fila.cells[3];
    const subtemaBadge = subtemasCell.querySelector('.badge');
    const tieneSubtemas = subtemaBadge && !subtemaBadge.classList.contains('text-muted');
    
    let mensaje = `¿Estás seguro de eliminar el tema "${titulo}"?`;
    
    if (tieneSubtemas) {
        const numSubtemas = subtemaBadge.textContent.split(' ')[0];
        mensaje += `\n\n ADVERTENCIA: Este tema tiene ${numSubtemas} subtema(s) asociado(s).`;
        mensaje += `\nPara eliminarlo, primero debes eliminar o reasignar todos los subtemas.`;
        mensaje += `\n\n¿Deseas continuar de todas formas?`;
    } else {
        mensaje += `\n\nEsta acción no se puede deshacer.`;
    }
    
    if (confirm(mensaje)) {
        // Crear formulario temporal para envío DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routesTemas.delete.replace(':id', id);
        form.style.display = 'none';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
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

// Auto-generar clave basada en el título
document.getElementById('tema_titulo')?.addEventListener('input', function() {
    const titulo = this.value;
    if (titulo.length > 2) {
        const clave = titulo.toUpperCase()
                          .replace(/[ÁÉÍÓÚÑÜ]/g, function(match) {
                              const replacements = {'Á':'A','É':'E','Í':'I','Ó':'O','Ú':'U','Ñ':'N','Ü':'U'};
                              return replacements[match];
                          })
                          .replace(/[^A-Z0-9]/g, '')
                          .substring(0, 6) + '01';
        
        document.getElementById('clave_tema').value = clave;
    }
});

// Limpiar modales al cerrar
document.getElementById('modalAgregarTema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});

document.getElementById('modalEditarTema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});
</script>