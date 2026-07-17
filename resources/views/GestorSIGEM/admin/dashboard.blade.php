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

<ul class="nav nav-tabs mb-3" id="dashboardTabs">
    <li class="nav-item">
        <button class="nav-link active" id="tab-auditoria" data-bs-toggle="tab" data-bs-target="#panel-auditoria">
            <i class="bi bi-journal-text"></i> Historial de Cambios
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="tab-accesos" data-bs-toggle="tab" data-bs-target="#panel-accesos">
            <i class="bi bi-door-open"></i> Accesos al Sistema
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="panel-auditoria">
        <div class="card">
            <div class="card-body">
                @if($auditoria->count() > 0)
                <div class="table-responsive">
                    <table id="tablaAuditoria" class="table table-striped table-sm">
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
                                    @if($log->datos_nuevos && $log->accion === 'actualizar')
                                        <button class="btn btn-sm btn-outline-info" 
                                                onclick="verDiff({{ $log->auditoria_id }})">
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
                <p class="text-muted text-center mb-0">No hay eventos registrados aún.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="panel-accesos">
        <div class="card">
            <div class="card-body">
                @if(isset($accesos) && $accesos->count() > 0)
                <div class="table-responsive">
                    <table id="tablaAccesos" class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accesos as $log)
                            <tr>
                                <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                                <td><small>{{ $log->usuario->name ?? '—' }}</small></td>
                                <td>
                                    @if($log->accion === 'login')
                                        <span class="badge bg-success">Login</span>
                                    @else
                                        <span class="badge bg-secondary">Logout</span>
                                    @endif
                                </td>
                                <td><code>{{ $log->ip }}</code></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">No hay accesos registrados aún.</p>
                @endif
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#tablaAuditoria').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
    });

    $('#tablaAccesos').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
    });
});

function verDiff(id) {
    fetch('{{ route("sgiem.admin.auditoria.detalle", ":id") }}'.replace(':id', id))
        .then(r => r.json())
        .then(data => {
            let html = '<div class="row">';
            if (data.datos_previos) {
                html += '<div class="col-md-6"><h6>Antes</h6><pre class="bg-light p-2 rounded">' +
                    JSON.stringify(data.datos_previos, null, 2) + '</pre></div>';
            }
            if (data.datos_nuevos) {
                html += '<div class="col-md-6"><h6>Después</h6><pre class="bg-light p-2 rounded">' +
                    JSON.stringify(data.datos_nuevos, null, 2) + '</pre></div>';
            }
            html += '</div>';
            document.getElementById('diff-content').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalDiff')).show();
        })
        .catch(() => {
            document.getElementById('diff-content').innerHTML = '<div class="alert alert-danger">Error al cargar detalle</div>';
        });
}
</script>
@endpush
