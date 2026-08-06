@extends('layouts.app')

@section('title', 'Dictámenes Deshabilitados - Gestor de Dictámenes')

@push('styles')
<style>
table {
    font-size: 0.85rem;
}

#deleted-table tbody tr {
    user-select: none;
}

#deleted-table tbody tr:hover {
    cursor: pointer;
}
</style>
@endpush

@section('content')
<div class="container-fluid pt-4 bg-fonde" style="min-height: 100vh;">
  <div class="container pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="bi bi-eye-slash"></i> Dictámenes Deshabilitados</h2>
        <a href="{{ route('gestor-dictamenes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Regresar a gestor
        </a>
    </div>

    <p class="text-muted">Doble click sobre un registro para editarlo (al guardar con un estatus activo se rehabilitará).</p>

    <div id="tablaDeletedCard">
        <table id="deleted-table" class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th># Registro</th>
                    <th>Fecha</th>
                    <th># Oficio</th>
                    <th>Dependencia</th>
                    <th>Nombre / Puesto</th>
                    <th>Asunto</th>
                    <th>Núm. Oficio</th>
                    <th>Revisado por</th>
                    <th>Observaciones</th>
                    <th>Archivo</th>
                    <th>Estatus Anterior</th>
                    <th>Eliminado Por (ID)</th>
                    <th>Fecha de Eliminación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictamenes as $log)
                <tr
                    data-id="{{ $log->dictamen_id }}"
                    data-route="{{ route('gestor-dictamenes.update', $log->dictamen_id) }}"
                    data-fecha="{{ $log->fecha ? \Carbon\Carbon::parse($log->fecha)->format('Y-m-d') : '' }}"
                    data-oficio-recibido="{{ $log->oficio_recibido ?? '' }}"
                    data-tipo-dictamen="{{ $log->tipo_dictamen ?? '' }}"
                    data-nombre-puesto="{{ $log->nombre_puesto ?? '' }}"
                    data-dependencia="{{ $log->dependencia_empres ?? '' }}"
                    data-asunto="{{ $log->asunto ?? '' }}"
                    data-numero-oficio="{{ $log->numero_oficio ?? '' }}"
                    data-revisado-por="{{ $log->revisado_por ?? '' }}"
                    data-estatus="{{ $log->estatus ?? '' }}"
                    data-observaciones="{{ $log->observaciones ?? '' }}"
                >
                    <td>{{ $log->dictamen_id }}</td>
                    <td>{{ $log->fecha ? \Carbon\Carbon::parse($log->fecha)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $log->oficio ?? 'S/N' }}</td>
                    <td>{{ $log->dependencia_empres ?? 'N/A' }}</td>
                    <td>{{ $log->nombre_puesto ?? 'N/A' }}</td>
                    <td title="{{ $log->asunto }}">{{ \Illuminate\Support\Str::limit($log->asunto, 50) }}</td>
                    <td>{{ $log->numero_oficio ?? 'N/A' }}</td>
                    <td>{{ $log->revisado_por ?? 'N/A' }}</td>
                    <td>{{ $log->observaciones ? \Illuminate\Support\Str::limit($log->observaciones, 30) : 'N/A' }}</td>
                    <td>
                        @if($log->archivo)
                            <span class="badge bg-info text-dark">{{ $log->archivo }} archivo(s) ligado(s)</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-danger">{{ $log->estatus }}</span>
                    </td>
                    <td>{{ $log->deleted_by }}</td>
                    <td>{{ $log->deleted_at ? \Carbon\Carbon::parse($log->deleted_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>
                        <form action="{{ route('gestor-dictamenes.restore', $log->dictamen_id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('¿Estás seguro que deseas restaurar este dictamen?');">
                                <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
  </div>
</div>

<!-- Modal Editar deshabilitado (al guardar con un estatus activo se rehabilitará) -->
<div class="modal fade" id="editDeletedModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Dictamen Deshabilitado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <form id="editDeletedForm" method="POST" onsubmit="return confirm('¿Seguro que deseas guardar los cambios? Al guardar con un estatus activo el dictamen se rehabilitará.');">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="alert alert-warning py-2 mb-3">
                        <i class="bi bi-exclamation-triangle"></i> Este dictamen está <strong>deshabilitado</strong>. Al guardar con un estatus activo se rehabilitará.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label>Recibido en fecha <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <input type="date" class="form-control" id="fecha_edit" name="fecha" required>
                        </div>
                        <div class="col-md-5">
                            <label>Tipo de dictamen</label>
                            <select class="form-select select2-tags" id="tipo_dictamen_edit" name="tipo_dictamen" title="Seleccione un tipo existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($tiposDictamen as $td)
                                    <option value="{{ $td }}">{{ $td }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label>Oficio Recibido <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <input type="text" class="form-control" id="oficio_recibido_edit" name="oficio_recibido" required>
                        </div>
                        <div class="col-md-6">
                            <label>Dependencia</label>
                            <select class="form-select select2-tags" id="dependencia_empres_edit" name="dependencia_empres" title="Seleccione una dependencia existente o escriba una nueva">
                                <option value=""></option>
                                @foreach($dependencias as $dep)
                                    <option value="{{ $dep }}">{{ $dep }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Nombre / Puesto</label>
                            <select class="form-select select2-tags" id="nombre_puesto_edit" name="nombre_puesto" title="Seleccione un nombre/puesto existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($nombresPuestos as $np)
                                    <option value="{{ $np }}">{{ $np }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Estatus <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <select class="form-select" id="estatus_edit" name="estatus" required>
                                <option value="">Seleccione un estatus...</option>
                                @foreach(\App\Models\GestorDictamenes\Dictamen::STATUSES as $estatus)
                                    <option value="{{ $estatus }}">{{ $estatus }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Número de oficio</label>
                            <input type="text" class="form-control" id="numero_oficio_edit" name="numero_oficio" placeholder="Ej. PYP024, DIR143">
                        </div>
                        <div class="col-md-4">
                            <label>Revisado por</label>
                            <select class="form-select select2-tags" id="revisado_por_edit" name="revisado_por" title="Seleccione un nombre existente o escriba uno nuevo">
                                <option value=""></option>
                                @foreach($revisadosPor as $r)
                                    <option value="{{ $r }}">{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label>Asunto <span class="text-danger" title="Campo obligatorio">*</span></label>
                            <textarea class="form-control" id="asunto_edit" name="asunto" required rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label>Observaciones</label>
                            <textarea class="form-control" id="observaciones_edit" name="observaciones" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cerrar sin guardar">Cancelar</button>
                    <button type="submit" class="btn btn-primary" title="Guardar los cambios del dictamen">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    function initSelect2Tags($scope) {
        $scope.find('.select2-tags').each(function () {
            const $sel = $(this);
            if ($sel.data('select2')) {
                $sel.select2('destroy');
            }
            $sel.select2({
                tags: true,
                width: '100%',
                dropdownAutoWidth: false,
                placeholder: 'Seleccione o escriba...',
                allowClear: true,
                theme: 'bootstrap-5',
                dropdownParent: $sel.closest('.modal')
            });
        });
    }

    function initDeletedTable() {
        if ($.fn.DataTable.isDataTable('#deleted-table')) {
            $('#deleted-table').DataTable().destroy();
        }
        $('#deleted-table').DataTable({
            "paging": true,
            "lengthMenu": [[10, 25, 50, 100, -1], ['10', '25', '50', '100', 'Todos los registros']],
            "pageLength": 25,
            "searching": true,
            "info": false,
            "ordering": true,
            "order": [[12, 'desc']],
            "language": {
                "search": "Buscar:",
                "paginate": { "previous": "‹", "next": "›" },
                "emptyTable": "No hay dictámenes deshabilitados",
                "zeroRecords": "No se encontró nada"
            }
        });
        $('#deleted-table_length').addClass('mb-3');
    }

    initDeletedTable();
    initSelect2Tags($('body'));

    // Doble click en una fila: abre el modal de edición (guardar con estatus activo rehabilita)
    $(document).on('dblclick', '#deleted-table tbody tr', function () {
        const $row = $(this);
        $('#fecha_edit').val($row.data('fecha') || '');
        $('#oficio_recibido_edit').val($row.data('oficio-recibido') || '');
        $('#tipo_dictamen_edit').val($row.data('tipo-dictamen') || '').trigger('change');
        $('#nombre_puesto_edit').val($row.data('nombre-puesto') || '').trigger('change');
        $('#dependencia_empres_edit').val($row.data('dependencia') || '').trigger('change');
        $('#asunto_edit').val($row.data('asunto') || '');
        $('#numero_oficio_edit').val($row.data('numero-oficio') || '');
        $('#revisado_por_edit').val($row.data('revisado-por') || '').trigger('change');
        $('#estatus_edit').val($row.data('estatus') || '');
        $('#observaciones_edit').val($row.data('observaciones') || '');
        $('#editDeletedForm').attr('action', $row.data('route'));
        $('#editDeletedModal').modal('show');
    });

    $('#editDeletedModal').on('shown.bs.modal', function () {
        initSelect2Tags($(this));
    });
});
</script>
@endsection
