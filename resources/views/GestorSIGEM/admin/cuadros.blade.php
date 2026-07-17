@php
    function hexToRgba($hex, $alpha) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "rgba($r, $g, $b, $alpha)";
    }
@endphp

<div class="card bg-dark bg-opacity-10 border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-dark bg-opacity-75 text-white border-bottom">
        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Cuadros Estadísticos</h5>
        <div>
            <span class="badge bg-light text-dark me-2">Total: <strong>{{ $total_cuadros ?? $cuadros->count() ?? 0 }}</strong></span>
            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearCuadro">
                <i class="bi bi-plus-lg"></i> Nuevo Cuadro
            </button>
        </div>
    </div>
    <div class="card-body bg-transparent">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(isset($cuadros) && $cuadros->count() > 0)
            <div class="table-responsive">
                <table id="tablaCuadrosV2" class="table table-striped table-hover table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Tema</th>
                            <th>Subtema</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuadros as $cuadro)
                        @php
                            $esMapa = $cuadro->tipo_mapa_pdf;
                            $tieneGrafica = $cuadro->permite_grafica;
                            $estaPublicado = $cuadro->publicado;
                            $colorTema = $cuadro->subtema && $cuadro->subtema->tema ? ($cuadro->subtema->tema->color ?? '#6c757d') : '#6c757d';
                        @endphp
                        <tr data-id="{{ $cuadro->cuadro_id }}">
                            <td class="text-center"><code class="text-primary">{{ $cuadro->codigo_cuadro }}</code></td>
                            <td><strong>{{ $cuadro->c_titulo }}</strong></td>
                            <td>
                                @if($cuadro->subtema && $cuadro->subtema->tema)
                                    <span class="badge" style="background-color: {{ $colorTema }}; color: #212529;">
                                        {{ $cuadro->subtema->tema->tema_titulo }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Sin tema</span>
                                @endif
                            </td>
                            <td>
                                @if($cuadro->subtema)
                                    <span class="badge" style="background-color: {{ hexToRgba($colorTema, 0.2) }}; color: #212529;">
                                        {{ $cuadro->subtema->subtema_titulo }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    {{-- Editar Metadatos --}}
                                    <button type="button" class="btn btn-outline-warning"
                                            onclick="editarCuadro({{ $cuadro->cuadro_id }})"
                                            title="Editar Metadatos">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>

                                    {{-- Editar Dataset — solo si NO es mapa --}}
                                    @if(!$esMapa)
                                        <button type="button" class="btn btn-outline-info disabled"
                                                title="Editar Dataset (próximamente)">
                                            <i class="bi bi-table"></i> Dataset
                                        </button>
                                    @endif

                                    {{-- Configurar Gráfica — solo si NO es mapa y tiene gráfica --}}
                                    @if(!$esMapa && $tieneGrafica)
                                        <button type="button" class="btn btn-outline-success disabled"
                                                title="Configurar Gráfica (próximamente)">
                                            <i class="bi bi-bar-chart"></i> Gráfica
                                        </button>
                                    @endif

                                    {{-- Publicar/Restringir --}}
                                    <button type="button"
                                            class="btn btn-outline-{{ $estaPublicado ? 'secondary' : 'success' }} btn-toggle-pub"
                                            onclick="togglePublicado({{ $cuadro->cuadro_id }}, this)"
                                            title="{{ $estaPublicado ? 'Restringir (ocultar en SIGEM)' : 'Publicar (visible en SIGEM)' }}">
                                        <i class="bi {{ $estaPublicado ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                        {{ $estaPublicado ? 'Restringir' : 'Publicar' }}
                                    </button>

                                    {{-- Eliminar --}}
                                    <button type="button" class="btn btn-outline-danger"
                                            onclick="eliminarCuadro({{ $cuadro->cuadro_id }}, '{{ addslashes($cuadro->c_titulo) }}')"
                                            title="Eliminar">
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
            <div class="text-center py-5">
                <i class="bi bi-table" style="font-size: 3rem;"></i>
                <p class="mt-3">No hay cuadros V2 registrados.</p>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearCuadro">
                    <i class="bi bi-plus-lg"></i> Crear primer cuadro
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Modal Crear Cuadro --}}
<div class="modal fade" id="modalCrearCuadro" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Cuadro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearCuadro" method="POST" action="{{ route('sgiem.admin.cuadros.store') }}">
                @csrf
                <div class="modal-body bg-fonde">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo_cuadro" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="codigo_cuadro" name="codigo_cuadro" required maxlength="50" placeholder="Ej: 1.1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="crear_tema_id" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select" id="crear_tema_id" required>
                                    <option value="">Seleccionar tema...</option>
                                    @foreach($temas as $tema)
                                        <option value="{{ $tema->tema_id }}">{{ $tema->tema_titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select" id="subtema_id" name="subtema_id" required disabled>
                                    <option value="">Primero selecciona un tema...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="c_titulo" class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="c_titulo" name="c_titulo" required maxlength="255" placeholder="Ej: Población total por sexo">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="c_subtitulo" class="form-label">Subtítulo</label>
                                <input type="text" class="form-control" id="c_subtitulo" name="c_subtitulo" maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="cabecera_gen" class="form-label">Cabecera general</label>
                                <textarea class="form-control" id="cabecera_gen" name="cabecera_gen" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="piepagina_gen" class="form-label">Pie de página</label>
                                <textarea class="form-control" id="piepagina_gen" name="piepagina_gen" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="crear_publicado" name="publicado" value="1">
                                <label class="form-check-label" for="crear_publicado">Publicado</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="crear_tipo_mapa_pdf" name="tipo_mapa_pdf" value="1">
                                <label class="form-check-label" for="crear_tipo_mapa_pdf">Es mapa PDF</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="crear_permite_grafica" name="permite_grafica" value="1">
                                <label class="form-check-label" for="crear_permite_grafica">Permite gráfica</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Crear Cuadro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Editar Metadatos --}}
<div class="modal fade" id="modalEditarCuadro" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Metadatos del Cuadro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarCuadro" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_cuadro_id" name="cuadro_id">
                <div class="modal-body bg-fonde">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_codigo_cuadro" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_codigo_cuadro" name="codigo_cuadro" required maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tema_id" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_tema_id" required>
                                    <option value="">Seleccionar tema...</option>
                                    @foreach($temas as $tema)
                                        <option value="{{ $tema->tema_id }}">{{ $tema->tema_titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_subtema_id" name="subtema_id" required disabled>
                                    <option value="">Primero selecciona un tema...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_c_titulo" class="form-label">Título <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_c_titulo" name="c_titulo" required maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_c_subtitulo" class="form-label">Subtítulo</label>
                                <input type="text" class="form-control" id="edit_c_subtitulo" name="c_subtitulo" maxlength="255">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_cabecera_gen" class="form-label">Cabecera general</label>
                                <textarea class="form-control" id="edit_cabecera_gen" name="cabecera_gen" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_piepagina_gen" class="form-label">Pie de página</label>
                                <textarea class="form-control" id="edit_piepagina_gen" name="piepagina_gen" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="edit_publicado" name="publicado" value="1">
                                <label class="form-check-label" for="edit_publicado">Publicado</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="edit_tipo_mapa_pdf" name="tipo_mapa_pdf" value="1">
                                <label class="form-check-label" for="edit_tipo_mapa_pdf">Es mapa PDF</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3 form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="edit_permite_grafica" name="permite_grafica" value="1">
                                <label class="form-check-label" for="edit_permite_grafica">Permite gráfica</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="formEliminarCuadro" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
const routesCuadros = {
    datos: '{{ route("sgiem.admin.cuadros.datos-json", ":id") }}',
    update: '{{ route("sgiem.admin.cuadros.update", ":id") }}',
    toggle: '{{ route("sgiem.admin.cuadros.toggle-publicado", ":id") }}',
    destroy: '{{ route("sgiem.admin.cuadros.destroy", ":id") }}'
};


$('#formEditarCuadro').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarCuadro'));

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            modal.hide();
            mostrarToast('success', 'Metadatos actualizados correctamente.');
            setTimeout(function() { location.reload(); }, 1500);
        },
        error: function(xhr) {
            let msg = 'Error al guardar.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            }
            mostrarToast('danger', msg);
        }
    });
});

$('#formCrearCuadro').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalCrearCuadro'));

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(response) {
            modal.hide();
            mostrarToast('success', response.message || 'Cuadro creado correctamente.');
            setTimeout(function() { location.reload(); }, 1500);
        },
        error: function(xhr) {
            let msg = 'Error al crear.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
            }
            mostrarToast('danger', msg);
        }
    });
});

function togglePublicado(id, btn) {
    $.ajax({
        url: routesCuadros.toggle.replace(':id', id),
        method: 'PUT',
        data: { _token: '{{ csrf_token() }}' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const publicado = response.publicado;
                btn.classList.remove('btn-outline-success', 'btn-outline-secondary');
                btn.classList.add(publicado ? 'btn-outline-secondary' : 'btn-outline-success');
                btn.title = publicado ? 'Restringir (ocultar en SIGEM)' : 'Publicar (visible en SIGEM)';
                btn.innerHTML = publicado
                    ? '<i class="bi bi-eye-slash"></i> Restringir'
                    : '<i class="bi bi-eye"></i> Publicar';
                mostrarToast('success', publicado ? 'Cuadro publicado.' : 'Cuadro restringido.');
            }
        },
        error: function() {
            mostrarToast('danger', 'Error al cambiar estado.');
        }
    });
}

function eliminarCuadro(id, titulo) {
    if (!confirm('¿Eliminar el cuadro "' + titulo + '"?\n\nEsta acción no se puede deshacer.')) return;

    const form = document.getElementById('formEliminarCuadro');
    form.action = routesCuadros.destroy.replace(':id', id);
    form.submit();
}

$(document).ready(function() {
    @if(isset($cuadros) && $cuadros->count() > 0)
    $('#tablaCuadrosV2').DataTable({
        responsive: true,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
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
            { targets: 0, width: "10%", className: "text-center" },
            { targets: 1, width: "25%" },
            { targets: 2, width: "15%", className: "text-center" },
            { targets: 3, width: "20%", className: "text-center" },
            { targets: 4, width: "auto", className: "text-center", orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        pageLength: 25,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
    @endif

    // ========== CASCADING TEMA → SUBTEMA ==========
    const subtemasPorTema = {
        @foreach($temas as $tema)
            {{ $tema->tema_id }}: [
                @foreach($tema->subtemas as $sub)
                    { id: {{ $sub->subtema_id }}, nombre: '{{ addslashes($sub->subtema_titulo) }}' },
                @endforeach
            ],
        @endforeach
    };

    function poblarSubtemas(temaId, selectId, valorSeleccionado) {
        const select = document.getElementById(selectId);
        select.innerHTML = '';
        if (!temaId || !subtemasPorTema[temaId]) {
            select.innerHTML = '<option value="">Primero selecciona un tema...</option>';
            select.disabled = true;
            return;
        }
        select.disabled = false;
        select.innerHTML = '<option value="">Seleccionar subtema...</option>';
        subtemasPorTema[temaId].forEach(function(sub) {
            const opt = document.createElement('option');
            opt.value = sub.id;
            opt.textContent = sub.nombre;
            if (valorSeleccionado && sub.id == valorSeleccionado) opt.selected = true;
            select.appendChild(opt);
        });
    }

    // Create modal cascading
    document.getElementById('crear_tema_id').addEventListener('change', function() {
        poblarSubtemas(this.value, 'subtema_id', null);
    });

    // Edit modal cascading
    document.getElementById('edit_tema_id').addEventListener('change', function() {
        poblarSubtemas(this.value, 'edit_subtema_id', null);
    });

    // Override editarCuadro to set tema before subtema
    window.editarCuadro = function(id) {
        const form = document.getElementById('formEditarCuadro');
        form.action = routesCuadros.update.replace(':id', id);

        $.get(routesCuadros.datos.replace(':id', id), function(data) {
            document.getElementById('edit_cuadro_id').value = data.cuadro_id;
            document.getElementById('edit_codigo_cuadro').value = data.codigo_cuadro;
            document.getElementById('edit_c_titulo').value = data.c_titulo;
            document.getElementById('edit_c_subtitulo').value = data.c_subtitulo || '';
            document.getElementById('edit_cabecera_gen').value = data.cabecera_gen || '';
            document.getElementById('edit_piepagina_gen').value = data.piepagina_gen || '';
            document.getElementById('edit_publicado').checked = data.publicado ? true : false;
            document.getElementById('edit_tipo_mapa_pdf').checked = data.tipo_mapa_pdf ? true : false;
            document.getElementById('edit_permite_grafica').checked = data.permite_grafica ? true : false;

            // Cascading: set tema, then load subtemas, then select the right one
            document.getElementById('edit_tema_id').value = data.tema_id || '';
            poblarSubtemas(data.tema_id, 'edit_subtema_id', data.subtema_id);

            new bootstrap.Modal(document.getElementById('modalEditarCuadro')).show();
        }).fail(function() {
            mostrarToast('danger', 'Error al cargar datos del cuadro.');
        });
    };

    // Mostrar toasts de sesión
    @if(session('success'))
        mostrarToast('success', '{{ session('success') }}');
    @endif
    @if(session('error'))
        mostrarToast('danger', '{{ session('error') }}');
    @endif

    // Reset modal al cerrar
    document.getElementById('modalCrearCuadro')?.addEventListener('hidden.bs.modal', function() {
        this.querySelector('form').reset();
    });
});
</script>