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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary active" data-filtro="todos" onclick="filtrarAuditoria('todos')">Todos</button>
                <button type="button" class="btn btn-outline-secondary" data-filtro="estructural" onclick="filtrarAuditoria('estructural')">Estructurales</button>
            </div>
            <small class="text-muted" id="filtro-info">Mostrando todos</small>
        </div>
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
                    @php
                        $ds = $log->datos_nuevos;
                        $esRuido = $log->modelo === 'Dataset' && $ds && in_array($ds['accion'] ?? '', ['Editar celda', 'Renombrar']);
                        $tipoLog = $esRuido ? 'ruido' : 'estructural';
                        $accionTexto = $ds['accion'] ?? ($log->accion === 'crear' ? 'Creación' : ($log->accion === 'eliminar' ? 'Eliminación' : 'Actualización'));
                    @endphp
                    <tr data-tipo="{{ $tipoLog }}">
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
        <p class="text-muted text-center mb-0">No hay eventos registrados aún.</p>
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
let dtAuditoria = null;

$(document).ready(function() {
    dtAuditoria = $('#tablaAuditoria').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
    });
});

window.filtrarAuditoria = function(tipo) {
    document.querySelectorAll('[data-filtro]').forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.filtro === tipo);
    });
    document.getElementById('filtro-info').textContent =
        tipo === 'todos' ? 'Mostrando todos' : 'Solo cambios estructurales';

    if (!dtAuditoria) return;
    // Remove old custom filter and add fresh one
    $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
        return fn.name !== '_filtroAuditoria';
    });
    if (tipo === 'estructural') {
        var filterFn = function _filtroAuditoria(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tablaAuditoria') return true;
            var row = dtAuditoria.row(dataIndex).node();
            return row && row.getAttribute('data-tipo') === 'estructural';
        };
        $.fn.dataTable.ext.search.push(filterFn);
    }
    dtAuditoria.draw();
};

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
