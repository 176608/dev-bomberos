<div class="row justify-content-center">
<div class="col-6">
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
                            <th>Color / Icono</th>
                            <th>Publicado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($temas as $tema)
                        <tr>
                            <td>
                                <strong>{{ $tema->tema_titulo }}</strong>
                                @php $subtemas_count = $tema->subtemas()->count() ?? 0; @endphp
                                <br><small class="text-muted">Subtemas: {{ $subtemas_count }} asignados</small>
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
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span style="display: inline-block; width: 24px; height: 24px; border-radius: 4px; background-color: {{ $tema->color ?? '#8FBC8F' }}; border: 1px solid #ddd;"></span>
                                    @if($tema->icono)
                                        <i class="{{ $tema->icono }}" style="font-size: 1.1rem;"></i>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </div>
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
<div class="modal fade" id="modalAgregarTema" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
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
                                       placeholder="Siguiente: {{ $siguienteOrden ?? 1 }}" min="0" max="999" 
                                       value="{{ old('orden_indice', $siguienteOrden ?? 1) }}">
                                <small class="form-text text-muted">Orden de aparición en listados</small>
                                @error('orden_indice')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-2">
                        <i class="bi bi-info-circle"></i>
                        <strong>Información:</strong> Una vez creado el tema, podrás configurar color, ícono, vista previa y publicación desde el panel de edición. El tema se creará como no publicado.
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
<div class="modal fade" id="modalEditarTema" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
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
                        <div class="col-12">
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
                                <input type="number" class="form-control" id="edit_orden_indice" name="orden_indice" min="0" max="999">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Color del Tema</label>
                                <div class="d-flex align-items-center gap-2">
                                    <div id="edit_color_btn" style="width: 48px; height: 48px; border-radius: 8px; background-color: #8FBC8F; border: 2px solid #dee2e6; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.1s;" title="Haz clic para cambiar color">
                                        <i class="bi bi-palette" style="font-size: 1.3rem; color: rgba(0,0,0,0.4);"></i>
                                    </div>
                                    <input type="color" class="d-none" id="edit_color" name="color" value="#8FBC8F">
                                    <input type="text" class="form-control form-control-sm w-auto" id="edit_color_hex" value="#8FBC8F" maxlength="7" style="width: 100px; font-family: monospace; font-size: 0.9rem;">
                                    <span class="text-muted small">Haz clic en el cuadrado o escribe el hex</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_icono" class="form-label">Ícono Bootstrap</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="edit-icono-preview"><i class="bi bi-globe"></i></span>
                                    <input type="text" class="form-control icon-input" id="edit_icono" name="icono" value="bi-globe" placeholder="Ej: bi-globe" data-preview="edit-icono-preview">
                                    <a href="https://icons.getbootstrap.com/" target="_blank" class="btn btn-outline-primary" title="Buscar íconos en Bootstrap Icons">
                                        <i class="bi bi-search"></i> Buscar ícono
                                    </a>
                                </div>
                                <small class="form-text text-muted">Pega la clase del ícono (ej: <code>bi-globe</code>) o el HTML completo</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Vista previa</label>
                                <div id="edit-preview-card" style="background-color: #8FBC8F; color: #3b3b3bff; border-radius: 16px; min-height: 90px; display: flex; align-items: center; justify-content: center; padding: 0.75rem; max-width: 280px; transition: all 0.1s ease;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.3rem;">
                                        <i id="edit-preview-icon" class="bi bi-globe" style="font-size: 1.6rem; color: #3b3b3bff;"></i>
                                        <span id="edit-preview-titulo" style="font-size: 0.85rem; font-weight: 700; text-align: center; color: #3b3b3bff;">1. Vista previa del tema</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check form-switch mt-2">
                                <input type="checkbox" class="form-check-input" id="edit_publicado" name="publicado" value="1">
                                <label class="form-check-label" for="edit_publicado">Publicado</label>
                                <small class="form-text text-muted d-block">Visible en SIGEM</small>
                            </div>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="actualizarPreview('edit')">
                                <i class="bi bi-arrow-repeat"></i> Actualizar vista previa
                            </button>
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
                targets: 3, // Columna Color/Icono
                width: "12%",
                className: "text-center",
                orderable: false,
                searchable: false
            },
            {
                targets: 4, // Columna Publicado
                width: "10%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 5, // Columna Acciones
                width: "100px",
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
    const colorIconoCell = fila.cells[3];
    const colorSwatch = colorIconoCell.querySelector('span');
    const iconElem = colorIconoCell.querySelector('i');
    const color = colorSwatch ? colorSwatch.style.backgroundColor : '#8FBC8F';
    const icono = iconElem ? iconElem.className : 'bi-globe';
    const publicado = fila.cells[4].querySelector('.badge.bg-success') !== null;
    
    document.getElementById('edit_tema_id').value = id;
    document.getElementById('edit_tema_titulo').value = tema_titulo;
    document.getElementById('edit_orden_indice').value = orden_indice;
    document.getElementById('edit_clave_tema').value = clave_tema;
    document.getElementById('edit_color').value = rgbToHex(color);
    document.getElementById('edit_color_hex').value = rgbToHex(color);
    document.getElementById('edit_color_btn').style.backgroundColor = rgbToHex(color);
    document.getElementById('edit_icono').value = icono;
    document.getElementById('edit_publicado').checked = publicado;
    
    actualizarPreview('edit');
    
    const form = document.getElementById('formEditarTema');
    form.action = routesTemas.update.replace(':id', id);
    
    new bootstrap.Modal(document.getElementById('modalEditarTema')).show();
}

function eliminarTema(id, titulo) {
    if (confirm(`¿Estás seguro de eliminar el tema "${titulo}"?\n\nEsta acción no se puede deshacer.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routesTemas.delete.replace(':id', id);
        form.style.display = 'none';
        
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
        
        document.body.appendChild(form);
        form.submit();
    }
}

// ============ PREVIEW EN VIVO ============
function rgbToHex(rgb) {
    if (!rgb || rgb === '') return '#8FBC8F';
    if (rgb.startsWith('#')) return rgb;
    const match = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if (!match) return '#8FBC8F';
    const r = parseInt(match[1]), g = parseInt(match[2]), b = parseInt(match[3]);
    return '#' + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
}

function extraerClaseIcono(valor) {
    if (!valor || valor.trim() === '') return 'bi bi-file-earmark-text';
    // Si es HTML completo: <i class="bi bi-globe"></i>
    const matchHtml = valor.match(/class=["']([^"']+)["']/);
    if (matchHtml) return matchHtml[1];
    // Si ya es clase directa con prefijo bi (con o sin "bi " al inicio)
    let limpio = valor.trim().replace(/^bi\s+/, '');
    if (!limpio.startsWith('bi-')) limpio = 'bi-' + limpio.replace(/^bi-?/, '');
    return limpio;
}

function actualizarPreview(prefix) {
    const colorInput = document.getElementById(prefix === 'edit' ? 'edit_color' : 'color');
    const colorHexInput = document.getElementById(prefix === 'edit' ? 'edit_color_hex' : 'color_hex');
    const iconInput = document.getElementById(prefix === 'edit' ? 'edit_icono' : 'icono');
    const previewCard = document.getElementById(prefix + '-preview-card');
    const previewIcon = document.getElementById(prefix + '-preview-icon');
    const previewTitulo = document.getElementById(prefix + '-preview-titulo');
    const ordenInput = document.getElementById(prefix === 'edit' ? 'edit_orden_indice' : 'orden_indice');
    const tituloInput = document.getElementById(prefix === 'edit' ? 'edit_tema_titulo' : 'tema_titulo');
    
    const color = colorInput ? colorInput.value : '#8FBC8F';
    const iconClass = iconInput ? extraerClaseIcono(iconInput.value) : 'bi-file-earmark-text';
    const orden = ordenInput ? ordenInput.value || '?' : '?';
    const titulo = tituloInput ? tituloInput.value || 'Vista previa' : 'Vista previa';
    
    if (previewCard) {
        previewCard.style.backgroundColor = color;
    }
    if (previewIcon) {
        previewIcon.className = iconClass;
    }
    if (previewTitulo) {
        previewTitulo.textContent = orden + '. ' + titulo;
    }
    if (colorHexInput) {
        colorHexInput.value = color;
    }
    
    // Actualizar preview del icono en el input-group
    const previewSpanId = iconInput ? iconInput.getAttribute('data-preview') : null;
    if (previewSpanId) {
        const previewSpan = document.getElementById(previewSpanId);
        if (previewSpan) {
            previewSpan.innerHTML = '<i class="' + iconClass + '"></i>';
        }
    }
}

// Sincronizar color picker con hex input y preview
document.addEventListener('DOMContentLoaded', function() {
    // Color picker changes
    document.querySelectorAll('input[type="color"]').forEach(function(picker) {
        picker.addEventListener('input', function() {
            const prefix = this.id.startsWith('edit_') ? 'edit' : '';
            const hexInput = document.getElementById(this.id.replace('color', 'color_hex'));
            if (hexInput) hexInput.value = this.value;
            // Sync the color button background
            const btn = document.getElementById(this.id + '_btn');
            if (btn) btn.style.backgroundColor = this.value;
            actualizarPreview(prefix);
        });
    });
    
    // Hex input changes
    document.querySelectorAll('[id$="color_hex"]').forEach(function(hexInput) {
        hexInput.addEventListener('input', function() {
            const prefix = this.id.startsWith('edit_') ? 'edit' : '';
            const colorPicker = document.getElementById(this.id.replace('color_hex', 'color'));
            if (colorPicker && /^#[0-9a-fA-F]{6}$/.test(this.value)) {
                colorPicker.value = this.value;
                const btn = document.getElementById(colorPicker.id + '_btn');
                if (btn) btn.style.backgroundColor = this.value;
                actualizarPreview(prefix);
            }
        });
    });
    
    // Click on color button triggers hidden color input
    document.querySelectorAll('[id$="color_btn"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const colorPicker = document.getElementById(this.id.replace('_btn', ''));
            if (colorPicker) colorPicker.click();
        });
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Icon input changes
    document.querySelectorAll('.icon-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const modal = this.closest('.modal');
            const isEdit = modal && modal.id === 'modalEditarTema';
            const prefix = isEdit ? 'edit' : '';
            actualizarPreview(prefix);
        });
    });
    
    // Título y orden changes (for preview)
    document.querySelectorAll('#tema_titulo, #edit_tema_titulo, #orden_indice, #edit_orden_indice').forEach(function(input) {
        input.addEventListener('input', function() {
            const prefix = this.id.startsWith('edit_') ? 'edit' : '';
            actualizarPreview(prefix);
        });
    });
    
    // Inicializar previews
    actualizarPreview('');
    actualizarPreview('edit');
});

// Limpiar modales al cerrar
document.getElementById('modalAgregarTema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});

document.getElementById('modalEditarTema')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
});
</script>