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
        @if($auditoria->count() > 0)
        {{-- Time filters --}}
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
                        <tr>
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
        @else
        <p class="text-muted text-center mb-0">No hay eventos en este período.</p>
        @endif
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

@push('scripts')
<script>
$(document).ready(function() {
    var dt = $('#tablaAuditoria').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
    });

    $('#filtro-modelo').on('change', function() {
        dt.column(2).search($(this).val()).draw();
    });
});

function esc(s) {
    if (s == null) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function renderDiff(prev, next) {
    var allKeys = {};
    if (prev) Object.keys(prev).forEach(function(k) { allKeys[k] = true; });
    if (next) Object.keys(next).forEach(function(k) { allKeys[k] = true; });
    var keys = Object.keys(allKeys);

    var html = '<table class="table table-sm table-bordered mb-0"><thead class="table-dark"><tr><th style="width:25%">Campo</th><th>Antes</th><th>Después</th></tr></thead><tbody>';
    var changes = 0;
    keys.forEach(function(key) {
        var oldVal = prev ? JSON.stringify(prev[key], null, 2) : null;
        var newVal = next ? JSON.stringify(next[key], null, 2) : null;
        if (oldVal === newVal) return;
        changes++;
        html += '<tr><td><code>' + esc(key) + '</code></td>';
        html += '<td class="text-danger" style="font-size:0.8rem"><pre class="mb-0" style="white-space:pre-wrap">' + esc(oldVal != null ? oldVal : '—') + '</pre></td>';
        html += '<td class="text-success" style="font-size:0.8rem"><pre class="mb-0" style="white-space:pre-wrap">' + esc(newVal != null ? newVal : '—') + '</pre></td></tr>';
    });
    if (changes === 0) {
        html += '<tr><td colspan="3" class="text-muted text-center">Sin cambios detectados</td></tr>';
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
