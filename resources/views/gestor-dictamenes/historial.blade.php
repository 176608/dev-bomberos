@extends('layouts.app')

@section('title', 'Historial de Cambios - Gestor de Dictámenes')

@push('styles')
<style>
table {
    font-size: 0.85rem;
}

#historial-table tbody tr {
    user-select: none;
}

#historial-table tbody tr:hover {
    cursor: pointer;
}

.detalle-item {
    display: flex;
    gap: 8px;
    padding: 6px 0;
    border-bottom: 1px solid #eee;
}

.detalle-item:last-child {
    border-bottom: 0;
}

.detalle-item .detalle-label {
    font-weight: 600;
    min-width: 140px;
    color: #555;
}
</style>
@endpush

@section('content')

@php
    $accionColores = [
        'CREAR' => 'success',
        'MODIFICAR' => 'primary',
        'DESHABILITAR' => 'danger',
        'RESTAURAR' => 'info',
    ];
@endphp

<div class="container-fluid pt-4 bg-fonde" style="min-height: 100vh;">
  <div class="container pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Cambios</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="btnRecargarHistorial" title="Recargar solo la tabla de cambios">
                <i class="bi bi-arrow-clockwise"></i> Recargar
            </button>
            <a href="{{ route('gestor-dictamenes.index') }}" class="btn btn-secondary" title="Regresar al gestor de dictámenes">
                <i class="bi bi-arrow-left"></i> Regresar a gestor
            </a>
        </div>
    </div>

    <div id="tablaHistorialCard">
        <table id="historial-table" class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Acción</th>
                    <th>Dictamen</th>
                    <th>Estatus</th>
                    <th>Núm. Oficio</th>
                    <th>Dependencia</th>
                    <th>Asunto</th>
                    <th>Usuario (ID)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cambios as $c)
                <tr
                    data-fecha="{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : 'N/A' }}"
                    data-accion="{{ $c->accion ?? '—' }}"
                    data-dictamen="#{{ $c->dictamen_id }}"
                    data-estatus="{{ $c->estatus ?? '—' }}"
                    data-oficio="{{ $c->numero_oficio_raw ?? '—' }}"
                    data-dependencia="{{ $c->dependencia_empres ?? '—' }}"
                    data-asunto="{{ $c->asunto ?? '—' }}"
                    data-usuario="{{ $c->deleted_by ?? $c->updated_by ?? $c->created_by ?? '—' }}"
                >
                    <td data-order="{{ $c->created_at }}">{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>
                        <span class="badge bg-{{ $accionColores[$c->accion ?? ''] ?? 'secondary' }}">
                            {{ $c->accion ?? '—' }}
                        </span>
                    </td>
                    <td>#{{ $c->dictamen_id }}</td>
                    <td>{{ $c->estatus ?? '—' }}</td>
                    <td>{{ $c->numero_oficio_raw ?? '—' }}</td>
                    <td>{{ $c->dependencia_empres ?? '—' }}</td>
                    <td title="{{ $c->asunto ?? '' }}">{{ \Illuminate\Support\Str::limit($c->asunto ?? '', 60) }}</td>
                    <td>{{ $c->deleted_by ?? $c->updated_by ?? $c->created_by ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No hay cambios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
  </div>
</div>

<!-- Modal Detalles del cambio -->
<div class="modal fade" id="detalleHistorialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalle del cambio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <div class="modal-body" id="detalleHistorialContenido">
                <!-- Se llena por JS con los datos de la fila -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="Cerrar">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    function initHistorialTable() {
        if ($.fn.DataTable.isDataTable('#historial-table')) {
            $('#historial-table').DataTable().destroy();
        }
        $('#historial-table').DataTable({
            "paging": true,
            "lengthMenu": [[10, 25, 50, 100, -1], ['10', '25', '50', '100', 'Todos los registros']],
            "pageLength": 25,
            "searching": true,
            "info": false,
            "ordering": true,
            "order": [[0, 'desc']],
            "language": {
                "search": "Buscar:",
                "paginate": { "previous": "‹", "next": "›" },
                "emptyTable": "No hay cambios registrados",
                "zeroRecords": "No se encontró nada"
            }
        });
        $('#historial-table_length').addClass('mb-3');
    }

    initHistorialTable();

    // Recargar SOLO la tabla (fetch del HTML y reemplazo del tbody)
    $('#btnRecargarHistorial').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true);
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.text();
            })
            .then(function (html) {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nuevoTbody = doc.getElementById('historial-table');
                if (nuevoTbody) {
                    $('#historial-table').DataTable().destroy();
                    $('#historial-table tbody').html(nuevoTbody.innerHTML);
                    initHistorialTable();
                }
                $btn.prop('disabled', false);
            })
            .catch(function () {
                $btn.prop('disabled', false);
                window.location.reload();
            });
    });

    // Doble click en una fila: ver detalles del cambio
    $(document).on('dblclick', '#historial-table tbody tr', function () {
        const $row = $(this);
        const fecha = $row.data('fecha');
        const accion = $row.data('accion');
        const dictamen = $row.data('dictamen');
        const estatus = $row.data('estatus');
        const oficio = $row.data('oficio');
        const dependencia = $row.data('dependencia');
        const asunto = $row.data('asunto');
        const usuario = $row.data('usuario');

        const $contenido = $('#detalleHistorialContenido');
        $contenido.empty();

        function item(label, valor) {
            return '<div class="detalle-item"><span class="detalle-label">' + label + ':</span><span>' + $('<span>').text(valor).html() + '</span></div>';
        }

        $contenido.append(item('Fecha', fecha));
        $contenido.append(item('Acción', accion));
        $contenido.append(item('Dictamen', dictamen));
        $contenido.append(item('Estatus', estatus));
        $contenido.append(item('Núm. Oficio', oficio));
        $contenido.append(item('Dependencia', dependencia));
        $contenido.append(item('Asunto', asunto));
        $contenido.append(item('Usuario', usuario));

        $('#detalleHistorialModal').modal('show');
    });
});
</script>
@endsection
