<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-clock-history"></i> Cambios en Contenidos</h4>
    <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('sgiem.admin.cambios', ['rango' => 'hoy']) }}" class="btn btn-outline-primary {{ $rangoActual === 'hoy' ? 'active' : '' }}">Hoy</a>
        <a href="{{ route('sgiem.admin.cambios', ['rango' => 'semanal']) }}" class="btn btn-outline-primary {{ $rangoActual === 'semanal' ? 'active' : '' }}">Semanal</a>
        <a href="{{ route('sgiem.admin.cambios', ['rango' => 'mensual']) }}" class="btn btn-outline-primary {{ $rangoActual === 'mensual' ? 'active' : '' }}">Mensual</a>
        <a href="{{ route('sgiem.admin.cambios', ['rango' => 'todos']) }}" class="btn btn-outline-primary {{ $rangoActual === 'todos' ? 'active' : '' }}">Todos</a>
    </div>
</div>

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
                            $esDataset = $log instanceof \App\Models\SIGEM\AuditoriaDataset;
                            if ($esDataset) {
                                $rc = $log->resumen_cambios ?? [];
                                if (!empty($rc['dataset_creado'])) {
                                    $accionTexto = 'Dataset creado';
                                } elseif (($rc['celdas_modificadas'] ?? 0) > 0) {
                                    $accionTexto = ($rc['celdas_modificadas'] ?? 0) . ' celdas modificadas · V ' . ($rc['categorias_verticales']['antes'] ?? 0) . '→' . ($rc['categorias_verticales']['despues'] ?? 0) . ' · H ' . ($rc['categorias_horizontales']['antes'] ?? 0) . '→' . ($rc['categorias_horizontales']['despues'] ?? 0);
                                } else {
                                    $accionTexto = 'Actualización de dataset';
                                }
                            } else {
                                $accionTexto = $ds['accion'] ?? ($log->accion === 'crear' ? 'Creación' : ($log->accion === 'eliminar' ? 'Eliminación' : 'Actualización'));
                            }
                        @endphp
                        <tr data-sesion-id="{{ $log->sesion_id ?? '' }}" data-tipo="{{ $esDataset ? 'dataset' : 'sgiem' }}">
                            <td><small>{{ $log->created_at->format('d/m/Y H:i') }}</small></td>
                            <td><small>{{ $log->usuario->name ?? '—' }}</small></td>
                            <td><code>{{ $log->modelo }}</code></td>
                            <td><span class="badge bg-secondary">{{ $log->modelo_id }}</span></td>
                            <td>
                                @if(in_array($log->accion, ['crear', 'crear_dataset']))
                                    <span class="badge bg-success">Crear</span>
                                @elseif(in_array($log->accion, ['actualizar', 'actualizar_dataset']))
                                    <span class="badge bg-warning text-dark">Actualizar</span>
                                @else
                                    <span class="badge bg-danger">Eliminar</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted me-2">{{ $accionTexto }}</small>
                                @if($log->datos_previos || $log->datos_nuevos)
                                    <button class="btn btn-sm btn-outline-info py-0 px-1"
                                            onclick="verDiff({{ $log->auditoria_id }}, '{{ $esDataset ? 'dataset' : 'sgiem' }}')" title="Ver detalle">
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
<script>
$(document).ready(function () {
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
        var v = $(this).val();
        if (!v) { dt.column(2).search('').draw(); return; }
        dt.column(2).search('^' + v.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '$', true, false).draw();
    });
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

function filaResumen(label, antes, despues, extraCls) {
    return '<tr' + (extraCls ? ' class="' + extraCls + '"' : '') + '><td><code>' + esc(label) + '</code></td>' +
        '<td class="text-danger">' + esc(antes) + '</td>' +
        '<td class="text-success">' + esc(despues) + '</td></tr>';
}

function nombresCategorias(estado) {
    var m = {};
    (estado.verticales || []).forEach(function(c) { if (c.categoria_id != null) m[c.categoria_id] = c.nombre; });
    (estado.horizontales || []).forEach(function(c) { if (c.categoria_id != null) m[c.categoria_id] = c.nombre; });
    return m;
}

function celdasDeEstado(estado) {
    var m = {};
    (estado.data || []).forEach(function(fila) {
        fila.forEach(function(c) {
            if (c && c.cat_vertical_id != null) m[c.cat_vertical_id + '|' + c.cat_horizontal_id] = c.valor;
        });
    });
    return m;
}

function textoGruposCategorias(grupos) {
    if (!grupos || !grupos.length) return '';
    return grupos.map(function(g) {
        if (g.padre) {
            if (g.hijos.length > 6) return g.padre + ' (' + g.hijos.length + ' hijos)';
            return g.padre + ': ' + g.hijos.join(', ');
        }
        return g.nombre;
    }).join('; ');
}

function nombresJerarquia(estado) {
    var simples = {};
    var listas = (estado.all_verticales || []).concat(estado.all_horizontales || []);
    listas.forEach(function(c) {
        if (c.categoria_id != null) simples[c.categoria_id] = c.nombre || c.categoria_id;
    });
    var etiquetas = {};
    listas.forEach(function(c) {
        if (c.categoria_id == null) return;
        if (c.padre_id != null && simples[c.padre_id] != null) etiquetas[c.categoria_id] = simples[c.padre_id] + ':' + simples[c.categoria_id];
        else etiquetas[c.categoria_id] = simples[c.categoria_id];
    });
    return etiquetas;
}

function renderDatosSeccion(datos, nombres) {
    var claves = Object.keys(datos || {});
    if (!claves.length) return '<p class="text-muted mb-0">Sin datos</p>';
    var html = '<div class="table-responsive" style="max-height:240px;overflow:auto"><table class="table table-sm table-bordered mb-0"><thead class="table-dark"><tr><th>Vertical</th><th>Horizontal</th><th>Valor</th></tr></thead><tbody>';
    claves.forEach(function(k) {
        var p = k.split('|');
        html += '<tr><td>' + esc(nombres[p[0]] || p[0]) + '</td><td>' + esc(nombres[p[1]] || p[1]) + '</td><td>' + esc(datos[k]) + '</td></tr>';
    });
    html += '</tbody></table></div>';
    return html;
}

function filaSeccion(label, secciones, cls, nombres) {
    var html = '<div class="mb-2"><span class="' + cls + '"><i class="bi bi-diagram-3 me-1"></i>' + esc(label) + '</span>';
    secciones.forEach(function(sec) {
        var n = Object.keys(sec.datos || {}).length;
        html += '<details class="mb-1 ms-3"><summary class="small">' + esc(sec.nombre) + ' <span class="badge bg-secondary">' + n + ' celdas</span></summary><div class="mt-1">' + renderDatosSeccion(sec.datos, nombres) + '</div></details>';
    });
    html += '</div>';
    return html;
}

function renderDiffDataset(data) {
    var prev = data.datos_previos || {};
    var next = data.datos_nuevos || {};
    var rc = data.resumen_cambios || {};

    var html = '<h6 class="text-muted">Resumen</h6>';
    html += '<table class="table table-sm table-bordered mb-0"><thead class="table-dark"><tr><th style="width:30%">Bloque</th><th>Antes</th><th>Después</th></tr></thead><tbody>';
    var v = rc.categorias_verticales || {}, h = rc.categorias_horizontales || {}, c = rc.celdas || {}, s = rc.secciones || {};
    html += filaResumen('Dataset creado', rc.dataset_creado ? 'No' : '—', rc.dataset_creado ? 'Sí' : '—');
    html += filaResumen('Categorías verticales', v.antes, v.despues);
    html += filaResumen('Categorías horizontales', h.antes, h.despues);
    html += filaResumen('Secciones', s.antes, s.despues);
    html += filaResumen('Celdas', c.antes, c.despues);
    if ((rc.celdas_modificadas || 0) > 0) html += filaResumen('Celdas modificadas', '—', rc.celdas_modificadas, 'table-warning');
    html += '</tbody></table>';

    var seccionCategorias = '';
    if (v.agregadas && v.agregadas.length) seccionCategorias += '<tr><td class="text-success"><i class="bi bi-plus-circle me-1"></i>Verticales agregadas</td><td>' + esc(textoGruposCategorias(v.agregadas)) + '</td></tr>';
    if (v.eliminadas && v.eliminadas.length) seccionCategorias += '<tr><td class="text-danger"><i class="bi bi-dash-circle me-1"></i>Verticales eliminadas</td><td>' + esc(textoGruposCategorias(v.eliminadas)) + '</td></tr>';
    if (h.agregadas && h.agregadas.length) seccionCategorias += '<tr><td class="text-success"><i class="bi bi-plus-circle me-1"></i>Horizontales agregadas</td><td>' + esc(textoGruposCategorias(h.agregadas)) + '</td></tr>';
    if (h.eliminadas && h.eliminadas.length) seccionCategorias += '<tr><td class="text-danger"><i class="bi bi-dash-circle me-1"></i>Horizontales eliminadas</td><td>' + esc(textoGruposCategorias(h.eliminadas)) + '</td></tr>';
    if (seccionCategorias) {
        html += '<h6 class="mt-3 text-muted">Categorías</h6><table class="table table-sm table-bordered mb-0"><tbody>' + seccionCategorias + '</tbody></table>';
    }

    var seccionSecciones = '';
    var nombresSec = {};
    var n1 = nombresJerarquia(next), n2 = nombresJerarquia(prev);
    Object.keys(n1).forEach(function(k) { nombresSec[k] = n1[k]; });
    Object.keys(n2).forEach(function(k) { if (!nombresSec[k]) nombresSec[k] = n2[k]; });
    if (s.agregadas && s.agregadas.length) seccionSecciones += filaSeccion('Secciones agregadas', s.agregadas, 'text-success', nombresSec);
    if (s.eliminadas && s.eliminadas.length) seccionSecciones += filaSeccion('Secciones eliminadas', s.eliminadas, 'text-danger', nombresSec);
    if (seccionSecciones) {
        html += '<h6 class="mt-3 text-muted">Secciones</h6>' + seccionSecciones;
    }

    var celdasPrev = celdasDeEstado(prev);
    var celdasNext = celdasDeEstado(next);
    var nombres = nombresCategorias(next);
    var nombresPrev = nombresCategorias(prev);
    var keys = {};
    Object.keys(celdasPrev).forEach(function(k) { keys[k] = 1; });
    Object.keys(celdasNext).forEach(function(k) { keys[k] = 1; });
    var cambios = [];
    Object.keys(keys).forEach(function(k) {
        var a = celdasPrev[k], b = celdasNext[k];
        if (a === b) return;
        cambios.push({ k: k, a: a === undefined ? '' : a, b: b === undefined ? '' : b });
    });

    if (cambios.length) {
        html += '<h6 class="mt-3 text-muted">Celdas modificadas (' + cambios.length + ')</h6>';
        html += '<div class="table-responsive" style="max-height:320px;overflow:auto">';
        html += '<table class="table table-sm table-bordered mb-0"><thead class="table-dark"><tr><th>Vertical</th><th>Horizontal</th><th>Antes</th><th>Después</th></tr></thead><tbody>';
        cambios.forEach(function(cel) {
            var p = cel.k.split('|');
            var vNom = nombres[p[0]] || nombresPrev[p[0]] || p[0];
            var hNom = nombres[p[1]] || nombresPrev[p[1]] || p[1];
            html += '<tr><td>' + esc(vNom) + '</td><td>' + esc(hNom) + '</td>' +
                '<td class="text-danger">' + esc(cel.a) + '</td>' +
                '<td class="text-success">' + (cel.b === '' ? '<em class="text-danger">(borrada)</em>' : esc(cel.b)) + '</td></tr>';
        });
        html += '</tbody></table></div>';
    } else {
        html += '<p class="text-muted mt-3 mb-0">Sin cambios en celdas</p>';
    }

    return html;
}

function renderDiff(data) {
    if (!data) return '<p class="text-muted">Sin datos</p>';

    if (data.resumen_cambios) return renderDiffDataset(data);

    var prev = data.datos_previos;
    var next = data.datos_nuevos;
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

function verDiff(id, tipo) {
    fetch('{{ route("sgiem.admin.auditoria.detalle", ":id") }}'.replace(':id', id) + (tipo === 'dataset' ? '?tipo=dataset' : ''))
        .then(r => r.json())
        .then(data => {
            document.getElementById('diff-content').innerHTML = renderDiff(data);
            new bootstrap.Modal(document.getElementById('modalDiff')).show();
        })
        .catch(function() {
            document.getElementById('diff-content').innerHTML = '<div class="alert alert-danger">Error al cargar detalle</div>';
        });
}
</script>
@endpush
