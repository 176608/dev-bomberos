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
                @foreach($cambios as $c)
                @php
                    $ultimos = $c->datos_nuevos ?? $c->datos_previos ?? [];
                @endphp
                <tr
                    data-fecha="{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : 'N/A' }}"
                    data-accion="{{ $c->accion ?? '—' }}"
                    data-dictamen="#{{ $c->dictamen_id }}"
                    data-usuario="{{ $c->usuario?->name ? $c->usuario->name . ' (#' . $c->user_id . ')' : ('Usuario #' . $c->user_id) }}"
                    data-previos='@json($c->datos_previos)'
                    data-nuevos='@json($c->datos_nuevos)'
                >
                    <td data-order="{{ $c->created_at }}">{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>
                        <span class="badge bg-{{ $accionColores[$c->accion ?? ''] ?? 'secondary' }}">
                            {{ $c->accion ?? '—' }}
                        </span>
                    </td>
                    <td>#{{ $c->dictamen_id }}</td>
                    <td>{{ $ultimos['estatus'] ?? '—' }}</td>
                    <td>{{ $ultimos['numero_oficio'] ?? '—' }}</td>
                    <td>{{ $ultimos['dependencia_empres'] ?? '—' }}</td>
                    <td title="{{ $ultimos['asunto'] ?? '' }}">{{ \Illuminate\Support\Str::limit($ultimos['asunto'] ?? '—', 60) }}</td>
                    <td>{{ $c->usuario?->name ? $c->usuario->name . ' (#' . $c->user_id . ')' : ('Usuario #' . $c->user_id) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
  </div>
</div>

<!-- Modal Detalle del cambio (comparación previos vs nuevos, estilo SIGEM v2) -->
<div class="modal fade" id="detalleHistorialModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalle del cambio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" title="Cerrar"></button>
            </div>
            <div class="modal-body" id="detalleHistorialContenido">
                <!-- Se llena por JS: encabezado + tabla de diferencias -->
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
    const LABELS = {
        fecha: 'Fecha',
        oficio_recibido: 'Oficio recibido',
        tipo_dictamen: 'Tipo de dictamen',
        numero_oficio: 'Número de oficio',
        dependencia_empres: 'Dependencia',
        nombre_puesto: 'Nombre / Puesto',
        asunto: 'Asunto',
        estatus: 'Estatus',
        revisado_por: 'Revisado por',
        observaciones: 'Observaciones'
    };

    function esc(s) {
        return $('<div>').text(s).html();
    }

    // Comparación de datos previos vs nuevos (mismo estilo que SIGEM v2):
    // solo muestra los campos que cambiaron, en tabla Campo | Antes | Después
    function renderDiff(prev, next) {
        if (!prev && !next) return '<p class="text-muted">Sin datos</p>';

        const allKeys = {};
        if (prev) Object.keys(prev).forEach(function (k) { allKeys[k] = true; });
        if (next) Object.keys(next).forEach(function (k) { allKeys[k] = true; });
        const keys = Object.keys(allKeys);

        let html = '<table class="table table-sm table-bordered mb-0"><thead class="table-dark"><tr><th style="width:25%">Campo</th><th>Antes</th><th>Después</th></tr></thead><tbody>';
        let changes = 0;
        keys.forEach(function (key) {
            const oldVal = prev ? JSON.stringify(prev[key], null, 2) : null;
            const newVal = next ? JSON.stringify(next[key], null, 2) : null;
            if (oldVal === newVal) return;
            changes++;
            const label = LABELS[key] || key;
            html += '<tr><td><code>' + esc(label) + '</code></td>';
            html += '<td class="text-danger" style="font-size:0.8rem"><pre class="mb-0" style="white-space:pre-wrap">' + esc(oldVal != null ? oldVal : '—') + '</pre></td>';
            html += '<td class="text-success" style="font-size:0.8rem"><pre class="mb-0" style="white-space:pre-wrap">' + esc(newVal != null ? newVal : '—') + '</pre></td></tr>';
        });
        if (changes === 0) {
            html += '<tr><td colspan="3" class="text-muted text-center">Sin cambios</td></tr>';
        }
        html += '</tbody></table>';
        return html;
    }

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

    // Doble click en una fila: comparar datos previos vs nuevos
    $(document).on('dblclick', '#historial-table tbody tr', function () {
        const $row = $(this);
        const previos = JSON.parse($row.data('previos') || 'null');
        const nuevos = JSON.parse($row.data('nuevos') || 'null');

        const $contenido = $('#detalleHistorialContenido');
        $contenido.empty();

        function item(label, valor) {
            return '<div class="detalle-item"><span class="detalle-label">' + label + ':</span><span>' + $('<span>').text(valor).html() + '</span></div>';
        }

        $contenido.append('<div class="mb-3">' +
            item('Fecha', $row.data('fecha')) +
            item('Acción', $row.data('accion')) +
            item('Dictamen', $row.data('dictamen')) +
            item('Usuario', $row.data('usuario')) +
            '</div>');

        $contenido.append(renderDiff(previos, nuevos));

        $('#detalleHistorialModal').modal('show');
    });
});
</script>
@endsection
