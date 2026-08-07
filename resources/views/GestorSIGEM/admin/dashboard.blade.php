<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Dashboard SGIEM</h4>
    <form method="GET" action="{{ route('sgiem.admin.index') }}" class="d-flex gap-2 align-items-center">
        <input type="date" name="desde" value="{{ $desde }}" class="form-control form-control-sm">
        <span>—</span>
        <input type="date" name="hasta" value="{{ $hasta }}" class="form-control form-control-sm">
        <button type="submit" class="btn btn-sm btn-success">Aplicar</button>
        <a href="{{ route('sgiem.admin.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
    </form>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-auditor-tab" data-bs-toggle="tab" data-bs-target="#tab-auditor"
                type="button" role="tab" aria-controls="tab-auditor" aria-selected="true">
            <i class="bi bi-activity me-1"></i>Auditor
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-historial-tab" data-bs-toggle="tab" data-bs-target="#tab-historial"
                type="button" role="tab" aria-controls="tab-historial" aria-selected="false">
            <i class="bi bi-clock-history me-1"></i>Historial cambios contenidos
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-auditor" role="tabpanel" aria-labelledby="tab-auditor-tab">

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100 border-0 shadow-sm text-center p-2">
                    <i class="bi bi-activity display-6 text-success"></i>
                    <h4 class="mt-1 mb-0">{{ number_format($audEventos) }}</h4>
                    <small class="text-muted">Eventos en el periodo</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100 border-0 shadow-sm text-center p-2">
                    <i class="bi bi-person-fill display-6 text-primary"></i>
                    <h4 class="mt-1 mb-0">{{ number_format($audVisitantesTotales) }}</h4>
                    <small class="text-muted">Visitantes únicos</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100 border-0 shadow-sm text-center p-2">
                    <i class="bi bi-person-plus-fill display-6 text-info"></i>
                    <h4 class="mt-1 mb-0">{{ number_format($audVisitantesNuevos) }}</h4>
                    <small class="text-muted">Nuevos en el periodo</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100 border-0 shadow-sm text-center p-2">
                    <i class="bi bi-person-check-fill display-6 text-teal"></i>
                    <h4 class="mt-1 mb-0">{{ number_format($audVisitantesActivos) }}</h4>
                    <small class="text-muted">Activos en el periodo</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100 border-0 shadow-sm text-center p-2">
                    <i class="bi bi-robot display-6 text-secondary"></i>
                    <h4 class="mt-1 mb-0">{{ number_format($audBots) }}</h4>
                    <small class="text-muted">Bots / {{ number_format($audHumanos) }} humanos</small>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100 border-0 shadow-sm text-center p-2">
                    <i class="bi bi-image-fill display-6 text-warning"></i>
                    <h4 class="mt-1 mb-0">{{ number_format($audPngDescargas) }}</h4>
                    <small class="text-muted">Descargas PNG</small>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">Eventos por día</div>
                    <div class="card-body">
                        <div style="height:300px"><canvas id="audChartDias"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">Top acciones</div>
                    <div class="card-body">
                        <div style="height:300px"><canvas id="audChartAcciones"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">Top cuadros consultados</div>
            <div class="card-body">
                <div style="height:280px"><canvas id="audChartCuadros"></canvas></div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">Visitas del periodo</div>
            <div class="card-body table-responsive">
                <table id="audVisitasTable" class="table table-striped table-sm table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Visitante</th>
                            <th>Cuadro</th>
                            <th>Acción</th>
                            <th>Detalle</th>
                            <th>Origen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audUltimasVisitas as $visita)
                        <tr>
                            <td class="text-nowrap" data-order="{{ $visita->created_at }}">{{ \Carbon\Carbon::parse($visita->created_at)->format('d/m/Y H:i:s') }}</td>
                            <td title="{{ $visita->visitante?->vuid }}">{{ $visita->visitante_id }}</td>
                            <td>{{ $visita->cuadro?->codigo_cuadro ?? '—' }}</td>
                            <td>{{ \App\Http\Controllers\SGU\DashboardController::ETIQUETAS_ACCION[$visita->accion] ?? $visita->accion }}</td>
                            <td>{{ $visita->detalle ?? '—' }}</td>
                            <td>{{ $visita->origen ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-historial" role="tabpanel" aria-labelledby="tab-historial-tab">
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary bg-opacity-10 border-primary">
                    <div class="card-body text-center">
                        <h3 class="text-primary mb-0">{{ $resumen['total_temas'] }}</h3>
                        <small class="text-muted">Temas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success bg-opacity-10 border-success">
                    <div class="card-body text-center">
                        <h3 class="text-success mb-0">{{ $resumen['total_subtemas'] }}</h3>
                        <small class="text-muted">Subtemas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info bg-opacity-10 border-info">
                    <div class="card-body text-center">
                        <h3 class="text-info mb-0">{{ $resumen['total_cuadros'] }}</h3>
                        <small class="text-muted">Cuadros</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning bg-opacity-10 border-warning">
                    <div class="card-body text-center">
                        <h3 class="text-warning mb-0">{{ $resumen['total_auditoria'] }}</h3>
                        <small class="text-muted">Eventos auditados</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('sgiem.admin.index', ['rango' => 'hoy']) }}" class="btn btn-outline-primary {{ $rangoActual === 'hoy' ? 'active' : '' }}">Hoy</a>
                        <a href="{{ route('sgiem.admin.index', ['rango' => 'semanal']) }}" class="btn btn-outline-primary {{ $rangoActual === 'semanal' ? 'active' : '' }}">Semanal</a>
                        <a href="{{ route('sgiem.admin.index', ['rango' => 'mensual']) }}" class="btn btn-outline-primary {{ $rangoActual === 'mensual' ? 'active' : '' }}">Mensual</a>
                        <a href="{{ route('sgiem.admin.index', ['rango' => 'todos']) }}" class="btn btn-outline-primary {{ $rangoActual === 'todos' ? 'active' : '' }}">Todos</a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <select id="filtro-modelo" class="form-select form-select-sm" style="width:auto">
                            <option value="">Todos los modelos</option>
                            @foreach($modelos as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ $auditoria->count() }} eventos</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tablaAuditoria" class="table table-sm table-hover" style="font-size:0.82rem">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Modelo</th>
                                <th>ID</th>
                                <th>Acción</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditoria as $log)
                                @php
                                    $ds = $log->datos_nuevos;
                                    $accionTexto = $ds['accion'] ?? ($log->accion === 'crear' ? 'Creación' : ($log->accion === 'eliminar' ? 'Eliminación' : 'Actualización'));
                                @endphp
                                <tr data-sesion-id="{{ $log->sesion_id ?? '' }}">
                                    <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                                    <td><small>{{ $log->usuario->name ?? '—' }}</small></td>
                                    <td><code>{{ $log->modelo }}</code></td>
                                    <td><span class="badge bg-secondary">{{ $log->modelo_id }}</span></td>
                                    <td>
                                        @if($log->accion === 'crear')
                                            <span class="badge bg-success">Crear</span>
                                        @elseif($log->accion === 'actualizar')
                                            <span class="badge bg-warning text-dark">Actualizar</span>
                                        @else
                                            <span class="badge bg-danger">Eliminar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted me-2">{{ $accionTexto }}</small>
                                        @if($log->datos_previos || $log->datos_nuevos)
                                            <button class="btn btn-sm btn-outline-info py-0 px-1"
                                                    onclick="verDiff({{ $log->auditoria_id }})" title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDiff" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambios detectados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="diff-content"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 12px;
    }
    .dataTables_wrapper .dataTables_length {
        margin-bottom: 12px;
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
$(document).ready(function () {
    if (!$.fn.dataTable.isDataTable('#audVisitasTable')) {
        $('#audVisitasTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            language: { url: '{{ asset('js/datatables/i18n/es-ES.json') }}' }
        });
    }

    if (!$.fn.dataTable.isDataTable('#tablaAuditoria')) {
        var dt = $('#tablaAuditoria').DataTable({
            stateSave: true,
            stateDuration: -1,
            language: { url: "{{ asset('js/datatables/i18n/es-ES.json') }}", emptyTable: 'No hay eventos en este período.' },
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true,
            drawCallback: function() {
                var api = this.api();
                var tableBody = $(api.table().body());
                tableBody.find('tr.group-sesion').remove();

                var groups = {};
                api.rows({ page: 'current' }).every(function() {
                    var node = this.node();
                    if (!node) return;
                    var id = $(node).data('sesion-id') || '';
                    if (!id) return;
                    if (!groups[id]) groups[id] = { count: 0, user: '', model: '', actions: [] };
                    groups[id].count++;
                    groups[id].user = $(node).find('td:eq(1) small').text();
                    groups[id].model = $(node).find('td:eq(2) code').text();
                    var a = $(node).find('td:eq(5) small').text().trim();
                    if (a) groups[id].actions.push(a);
                });

                var rows = api.rows({ page: 'current' }).nodes();
                var inserted = {};
                $(rows).each(function() {
                    var id = $(this).data('sesion-id') || '';
                    if (!id || inserted[id]) return;
                    inserted[id] = true;
                    var g = groups[id];
                    $(this).before(
                        '<tr class="group-sesion table-secondary">' +
                            '<td colspan="6" style="font-size:0.82rem">' +
                                '<i class="bi bi-clock-history me-2"></i>' +
                                '<strong>Sesión</strong>' +
                                '<span class="text-muted mx-1">·</span>' +
                                '<strong>' + esc(g.user) + '</strong>' +
                                '<span class="text-muted mx-1">·</span>' +
                                '<code>' + esc(g.model) + '</code>' +
                                '<span class="badge bg-info ms-2">' + g.count + '</span>' +
                                '<span class="text-muted ms-2 small">' + esc(g.actions.join(', ')) + '</span>' +
                            '</td>' +
                        '</tr>'
                    );
                });
            }
        });

        $('#filtro-modelo').on('change', function() {
            dt.column(2).search($(this).val()).draw();
        });
    }

    var paleta = ['#2c5f4a', '#4a7c63', '#e9a03a', '#5b8def', '#8e6fbf', '#d95d5d', '#4fb8b8', '#9a9a9a'];
    var base = {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: { legend: { display: false } }
    };

    var diasLabels = @json($audDiasLabels);
    var diasData = @json($audDiasData);
    if (diasLabels.length && document.getElementById('audChartDias')) {
        new Chart(document.getElementById('audChartDias'), {
            type: 'line',
            data: {
                labels: diasLabels,
                datasets: [{
                    label: 'Eventos',
                    data: diasData,
                    borderColor: '#2c5f4a',
                    backgroundColor: 'rgba(44, 95, 74, 0.15)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: Object.assign({}, base, { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } })
        });
    }

    var acciones = @json($audTopAcciones);
    if (acciones.length && document.getElementById('audChartAcciones')) {
        new Chart(document.getElementById('audChartAcciones'), {
            type: 'bar',
            data: {
                labels: acciones.map(function(a) { return a.label; }),
                datasets: [{
                    label: 'Eventos',
                    data: acciones.map(function(a) { return a.total; }),
                    backgroundColor: paleta
                }]
            },
            options: Object.assign({}, base, { indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } })
        });
    }

    var cuadros = @json($audCuadrosChart);
    if (cuadros.length && document.getElementById('audChartCuadros')) {
        new Chart(document.getElementById('audChartCuadros'), {
            type: 'bar',
            data: {
                labels: cuadros.map(function(c) { return c.label; }),
                datasets: [{
                    label: 'Consultas',
                    data: cuadros.map(function(c) { return c.total; }),
                    backgroundColor: '#5b8def'
                }]
            },
            options: Object.assign({}, base, { indexAxis: 'y', scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } })
        });
    }
});

function esc(s) {
    if (s == null) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function renderActions(acciones) {
    if (!acciones || !acciones.length) return '<p class="text-muted">Sin cambios</p>';
    var html = '<ul class="list-group list-group-flush">';
    acciones.forEach(function(a) {
        var accion = a.accion || '—';
        var cls = 'text-primary';
        if (accion.toLowerCase().includes('eliminar')) cls = 'text-danger';
        else if (accion.toLowerCase().includes('agregar') || accion.toLowerCase().includes('crear')) cls = 'text-success';
        var partes = [accion];
        if (a.nombre) partes.push('<strong>' + esc(a.nombre) + '</strong>');
        if (a.padre) partes.push('en <em>' + esc(a.padre) + '</em>');
        if (a.eje) partes.push('(' + esc(a.eje) + ')');
        if (a.desde_fila !== undefined) partes.push('fila ' + a.desde_fila + ', col ' + a.desde_columna + ' [' + a.filas + '×' + a.columnas + ']');
        if (a.cantidad) partes.push('×' + a.cantidad);
        html += '<li class="list-group-item py-1 px-2 ' + cls + '"><i class="bi bi-arrow-right-short"></i> ' + partes.join(' ') + '</li>';
    });
    html += '</ul>';
    return html;
}

function renderDiff(prev, next) {
    if (!prev && !next) return '<p class="text-muted">Sin datos</p>';

    if (next && next.acciones) return renderActions(next.acciones);
    if (prev && prev.acciones) return renderActions(prev.acciones);

    var allKeys = {};
    if (prev) Object.keys(prev).forEach(function(k) { allKeys[k] = true; });
    if (next) Object.keys(next).forEach(function(k) { allKeys[k] = true; });
    var keys = Object.keys(allKeys);

    var html = '<table class="table table-sm table-bordered mb-0"><thead class="table-dark"><tr><th style="width:25%">Campo</th><th>Antes</th><th>Después</th></tr></thead><tbody>';
    var changes = 0;
    keys.forEach(function(key) {
        if (key === 'acciones') return;
        var oldVal = prev ? JSON.stringify(prev[key], null, 2) : null;
        var newVal = next ? JSON.stringify(next[key], null, 2) : null;
        if (oldVal === newVal) return;
        changes++;
        html += '<tr><td><code>' + esc(key) + '</code></td>';
        html += '<td class="text-danger" style="font-size:0.8rem"><pre class="mb-0" style="white-space:pre-wrap">' + esc(oldVal != null ? oldVal : '—') + '</pre></td>';
        html += '<td class="text-success" style="font-size:0.8rem"><pre class="mb-0" style="white-space:pre-wrap">' + esc(newVal != null ? newVal : '—') + '</pre></td></tr>';
    });
    if (changes === 0) {
        html += '<tr><td colspan="3" class="text-muted text-center">Sin cambios</td></tr>';
    }
    html += '</tbody></table>';
    return html;
}

function verDiff(id) {
    fetch('{{ route("sgiem.admin.auditoria.detalle", ":id") }}'.replace(':id', id))
        .then(r => r.json())
        .then(data => {
            document.getElementById('diff-content').innerHTML = renderDiff(data.datos_previos, data.datos_nuevos);
            new bootstrap.Modal(document.getElementById('modalDiff')).show();
        })
        .catch(function() {
            document.getElementById('diff-content').innerHTML = '<div class="alert alert-danger">Error al cargar detalle</div>';
        });
}
</script>
@endpush
