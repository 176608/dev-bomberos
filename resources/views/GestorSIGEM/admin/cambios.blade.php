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
                        <th>Título</th>
                        <th>Acción</th>
                        <th class="text-center">Detalle</th>
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
                            <td>
                                @php $titulo = $titulos[$log->modelo][$log->modelo_id] ?? null; @endphp
                                @if($titulo)
                                    <span class="small" title="ID {{ $log->modelo_id }}">{{ $titulo }}</span>
                                @else
                                    <span class="badge bg-secondary text-muted">{{ $log->modelo_id }}</span>
                                @endif
                            </td>
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
                                @if($esDataset ? !empty($log->tiene_payload) : ($log->datos_previos || $log->datos_nuevos))
                                    <button class="btn btn-sm btn-outline-info py-0 px-1"
                                            data-modelo="{{ $log->modelo }}"
                                            data-accion-texto="{{ $accionTexto }}"
                                            data-usuario="{{ $log->usuario->name ?? '—' }}"
                                            data-fecha="{{ $log->created_at?->format('d/m/Y H:i') }}"
                                            onclick="verDiff({{ $log->auditoria_id }}, '{{ $esDataset ? 'dataset' : 'sgiem' }}', this)" title="Ver detalle">
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
        autoWidth: false,
        columnDefs: [
            { targets: 5, width: '1%', className: 'text-center text-nowrap' }
        ],
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

var ETIQUETAS_MODELO = {
    TemaV2: {
        tema_id: 'ID', tema_titulo: 'Título', orden_indice: 'Orden', clave_tema: 'Clave',
        color: 'Color', icono: 'Icono', publicado: 'Publicado', created_at: 'Creado', updated_at: 'Actualizado'
    },
    SubtemaV2: {
        subtema_id: 'ID', tema_id: 'Tema (ID)', subtema_titulo: 'Título', orden_indice: 'Orden',
        imagen: 'Imagen', publicado: 'Publicado', created_at: 'Creado', updated_at: 'Actualizado'
    },
    Cuadro: {
        cuadro_id: 'ID', subtema_id: 'Subtema (ID)', codigo_cuadro: 'Código', c_titulo: 'Título',
        c_subtitulo: 'Subtítulo', publicado: 'Publicado', tipo_mapa_pdf: 'Tipo mapa (PDF)',
        permite_grafica: 'Permite gráfica', tipos_grafica_permitida: 'Tipos de gráfica permitidos',
        cabecera_gen: 'Cabecera', piepagina_gen: 'Pie de página (general)', pie_pagina: 'Pie de página',
        pivot_label: 'Pivote', pdf_file: 'Archivo PDF', created_at: 'Creado', updated_at: 'Actualizado'
    },
    ce_tema: {
        ce_tema_id: 'ID', tema: 'Tema', created_at: 'Creado', updated_at: 'Actualizado'
    },
    ce_subtema: {
        ce_subtema_id: 'ID', ce_tema_id: 'Tema (ID)', ce_subtema: 'Título de subtema',
        created_at: 'Creado', updated_at: 'Actualizado'
    },
    ce_contenido: {
        ce_contenido_id: 'ID', ce_subtema_id: 'Subtema (ID)', titulo_tabla: 'Título',
        pie_tabla: 'Pie de tabla', tabla_filas: 'Filas', tabla_columnas: 'Columnas',
        tabla_datos: 'Tabla (datos)', created_at: 'Creado', updated_at: 'Actualizado'
    }
};

function etiquetaCampo(modelo, key) {
    var mapa = ETIQUETAS_MODELO[modelo] || {};
    if (mapa[key]) return mapa[key];
    return key.replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
}

function fmtValor(key, val) {
    if (val === null || val === undefined || val === '') return '—';

    if (key === 'publicado' || key === 'permite_grafica' || key === 'tipo_mapa_pdf') {
        var activo = (val === true || val === 1 || val === '1');
        return activo ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>';
    }
    if (typeof val === 'boolean') {
        return val ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>';
    }
    if (/_id$/.test(key) && /^\d+$/.test(String(val))) return '#' + val;
    if ((key === 'created_at' || key === 'updated_at' || /^fecha/.test(key)) && typeof val === 'string') {
        var d = new Date(String(val).replace(' ', 'T'));
        if (!isNaN(d.getTime())) {
            return d.toLocaleString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    }
    if (key === 'pie_pagina' || key === 'cabecera_gen' || key === 'piepagina_gen' || key === 'pie_tabla') {
        var txt = String(val).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
        return '<span title="' + esc(String(val)) + '">' + esc(txt.substring(0, 140)) + (txt.length > 140 ? '…' : '') + '</span>';
    }
    if (key === 'tabla_datos' && typeof val === 'object') {
        var filas = Array.isArray(val) ? val.length : 0;
        var cols = (filas && Array.isArray(val[0])) ? val[0].length : 0;
        return 'Tabla de ' + filas + '×' + cols;
    }
    if (typeof val === 'object') {
        var s = JSON.stringify(val);
        return '<code style="font-size:0.78rem">' + esc(s.length > 160 ? s.substring(0, 160) + '…' : s) + '</code>';
    }
    var str = String(val);
    if (str.length > 200) return '<span title="' + esc(str) + '">' + esc(str.substring(0, 200)) + '…</span>';
    return esc(str);
}

function renderMeta(data) {
    var m = data._meta || {};
    var partes = [];
    if (data.modelo) partes.push('<span class="badge bg-dark"><i class="bi bi-box me-1"></i>' + esc(data.modelo) + '</span>');
    if (m.accion) partes.push('<span class="badge bg-info text-dark">' + esc(m.accion) + '</span>');
    else if (data.accion) partes.push('<span class="badge bg-info text-dark">' + esc(data.accion) + '</span>');
    if (m.usuario) partes.push('<span class="text-muted small"><i class="bi bi-person me-1"></i>' + esc(m.usuario) + '</span>');
    if (m.fecha) partes.push('<span class="text-muted small"><i class="bi bi-clock me-1"></i>' + esc(m.fecha) + '</span>');
    if (!partes.length) return '';
    return '<div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-2 border-bottom">' + partes.join('') + '</div>';
}

function renderDiff(data) {
    if (!data) return '<p class="text-muted">Sin datos</p>';

    if (data.resumen_cambios) return renderMeta(data) + renderDiffDataset(data);

    var prev = data.datos_previos;
    var next = data.datos_nuevos;
    if (!prev && !next) return renderMeta(data) + '<p class="text-muted mb-0">Sin datos</p>';

    if (next && next.acciones) return renderMeta(data) + renderActions(next.acciones);
    if (prev && prev.acciones) return renderMeta(data) + renderActions(prev.acciones);

    var modelo = data.modelo || '';
    var allKeys = {};
    if (prev) Object.keys(prev).forEach(function(k) { allKeys[k] = true; });
    if (next) Object.keys(next).forEach(function(k) { allKeys[k] = true; });

    var rows = '';
    var changes = 0;
    Object.keys(allKeys).forEach(function(key) {
        if (key === 'acciones') return;
        var hasPrev = prev && Object.prototype.hasOwnProperty.call(prev, key);
        var hasNext = next && Object.prototype.hasOwnProperty.call(next, key);
        var a = hasPrev ? prev[key] : undefined;
        var b = hasNext ? next[key] : undefined;
        if (JSON.stringify(a) === JSON.stringify(b)) return;
        changes++;
        rows += '<tr><td class="fw-semibold text-nowrap">' + esc(etiquetaCampo(modelo, key)) + '</td>'
            + '<td class="text-danger">' + (hasPrev ? fmtValor(key, a) : '—') + '</td>'
            + '<td class="text-success">' + (hasNext ? fmtValor(key, b) : '—') + '</td></tr>';
    });

    var html = renderMeta(data);
    if (!changes) return html + '<p class="text-muted mb-0">Sin cambios en campos.</p>';

    html += '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">'
        + '<thead class="table-dark"><tr><th style="width:30%">Campo</th><th>Antes</th><th>Después</th></tr></thead>'
        + '<tbody>' + rows + '</tbody></table></div>';
    return html;
}

function verDiff(id, tipo, btn) {
    var meta = btn ? {
        accion: btn.dataset.accionTexto || '',
        usuario: btn.dataset.usuario || '',
        fecha: btn.dataset.fecha || ''
    } : null;
    fetch('{{ route("sgiem.admin.auditoria.detalle", ":id") }}'.replace(':id', id) + (tipo === 'dataset' ? '?tipo=dataset' : ''))
        .then(r => r.json())
        .then(data => {
            data._meta = meta;
            document.getElementById('diff-content').innerHTML = renderDiff(data);
            new bootstrap.Modal(document.getElementById('modalDiff')).show();
        })
        .catch(function() {
            document.getElementById('diff-content').innerHTML = '<div class="alert alert-danger">Error al cargar detalle</div>';
        });
}
</script>
@endpush
