@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'Dataset — ' . ($cuadro->codigo_cuadro ?? ''))

@section('visor_content')
<div class="container-fluid py-3" id="app-dataset">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Dataset</h5>
            <small class="text-muted">
                <code>{{ $cuadro->codigo_cuadro }}</code>
                <strong>{{ $cuadro->c_titulo }}</strong>
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url('/sigem-v2/cuadro/' . $cuadro->cuadro_id . '/grafica') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="btn btn-outline-info btn-sm">
                <i class="bi bi-bar-chart-fill me-1"></i> Gráfica
            </a>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
        <button type="button" class="btn btn-sm btn-outline-info" id="btn-toggle-panel" title="Mostrar/ocultar panel">
            <i class="bi bi-list-check"></i> Categorías
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-toggle-cb" title="Mostrar/ocultar checkboxes en tabla">
            <i class="bi bi-check2-square"></i> Checkboxes
        </button>
        <button type="button" class="btn btn-sm btn-outline-warning" id="btn-limpiar-seleccion" title="Restaurar selección por defecto">
            <i class="bi bi-arrow-counterclockwise"></i> Limpiar
        </button>
        @if($esDesarrollador)
        <button type="button" class="btn btn-sm btn-outline-secondary debug-toggle" title="Alternar debug">
            <i class="bi bi-bug"></i> Debug
        </button>
        @endif
    </div>

    <div class="d-flex gap-2">
        <div id="chart-panel" class="border rounded p-2" style="width:240px;flex-shrink:0;overflow-y:auto;max-height:500px;display:none">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="fw-semibold"><i class="bi bi-list-check me-1"></i>Categorías</small>
                <button type="button" class="btn-close btn-sm" id="btn-cerrar-panel" aria-label="Cerrar panel"></button>
            </div>
            <div id="panel-items" class="small"></div>
        </div>
        <div style="flex:1;min-width:0">
            <div id="tables-container"></div>
            @if($esDesarrollador)
            <div id="chart-debug" class="mt-2 small" style="display:none;background:#1e1e1e;color:#d4d4d4;font-family:Consolas,monospace;padding:0.6rem;border-radius:6px;white-space:pre-wrap;overflow-x:auto;max-height:250px;overflow-y:auto"></div>
            @endif
        </div>
    </div>

    <div class="card-footer py-1 px-0 d-flex justify-content-between align-items-center mt-2" id="status-bar">
        <small id="status-text"></small>
    </div>

</div>

<style>
#chart-panel label.checked { font-weight:600; }
#chart-panel .panel-parent { cursor:pointer; user-select:none; }
#chart-panel .panel-parent:hover { background:#f0f2f5; border-radius:2px; }
#chart-panel .panel-child { padding-left:1.2rem; }
#chart-panel .panel-child label { cursor:pointer; }
#chart-panel .panel-child label:hover { color:var(--bs-primary); }
#status-bar #status-text { font-size: 0.8rem; }
#status-bar.status-flash { background: #d1e7fd !important; transition: background 0.3s; }
#tables-container > .section-block { margin-bottom:1.5rem; }
#tables-container .section-block .section-title { font-weight:700; font-size:0.85rem; padding:0.3rem 0.5rem; background:#e8edf2; border:1px solid #dee2e6; border-bottom:none; border-radius:4px 4px 0 0; }
#tables-container table { font-size:0.85rem; margin-bottom:0; }
#tables-container table th { white-space:nowrap; }
#tables-container table td.valor { text-align: right; }
.vis-cb { cursor:pointer; margin-right:2px; vertical-align:middle; }
#app-dataset:not(.show-cb) .vis-cb { display:none; }
</style>
@endsection

@push('visor_scripts')
<script>
const CUADRO_ID = {{ $cuadro->cuadro_id }};
const BASE = '{{ url("/sigem-v2/cuadro") }}/' + CUADRO_ID;
const IS_DEV = @json($esDesarrollador);

let estado = @json($estadoInicial);

var visibleV = {};
var visibleH = {};
var sectionsCache = {};
var selectedSections = {};

// ─── Utilities ───

function alerta(msg) {
    document.getElementById('status-text').textContent = '⚠ ' + msg;
}
function status(msg) {
    var el = document.getElementById('status-text');
    if (!el) return;
    el.textContent = msg || '';
    var bar = document.getElementById('status-bar');
    if (bar && msg) {
        bar.classList.add('status-flash');
        clearTimeout(bar._flashTimer);
        bar._flashTimer = setTimeout(function() { bar.classList.remove('status-flash'); }, 2500);
    }
}
function esc(s) {
    if (!s) return '';
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function api(path, opts) {
    opts = opts || {};
    opts.headers = opts.headers || {};
    if (opts.body && typeof opts.body === 'object') {
        opts.body = JSON.stringify(opts.body);
        opts.headers['Content-Type'] = 'application/json';
    }
    return fetch(BASE + path, opts).then(function(r) { return r.json(); });
}

// ─── Category panel (sidebar) ───

function syncTodoCheckboxes() {
    var c = document.getElementById('panel-items');
    if (!c) return;
    c.querySelectorAll('.todo-check').forEach(function(cb) {
        var a = cb.dataset.axis;
        var m = a === 'vertical' ? visibleV : visibleH;
        var ids = Object.keys(m);
        var all = ids.length > 0 && ids.every(function(id) { return m[id] !== false; });
        var any = ids.some(function(id) { return m[id] !== false; });
        cb.checked = all;
        var ic = cb.parentNode.querySelector('i');
        if (ic) ic.className = 'bi ' + (all ? 'bi-check2-square' : (any ? 'bi-dash-square' : 'bi-square')) + ' me-1';
    });
}

function updateParentCheck(axis, pid) {
    var c = document.getElementById('panel-items');
    if (!c) return;
    var kids = c.querySelectorAll('.cat-check[data-axis="' + axis + '"][data-parent="' + pid + '"]');
    var all = true, any = false;
    kids.forEach(function(ch) { if (ch.checked) any = true; else all = false; });
    var pc = c.querySelector('.cat-check[data-axis="' + axis + '"][data-id="' + pid + '"]');
    if (pc) {
        if (!any) { pc.checked = false; (axis === 'vertical' ? visibleV : visibleH)[pid] = false; }
        else if (all) { pc.checked = true; (axis === 'vertical' ? visibleV : visibleH)[pid] = true; }
    }
}

function renderPanel() {
    var c = document.getElementById('panel-items');
    if (!c) return;
    var html = '';

    html += '<div class="mb-2"><label class="small text-muted fw-semibold">Secciones</label></div>';
    (estado.secciones || []).forEach(function(s) {
        var ch = selectedSections[s.seccion_id] !== false;
        var ld = !sectionsCache[s.seccion_id] && ch;
        html += '<div class="panel-child mb-1"><label style="cursor:pointer">';
        html += '<input type="checkbox" class="me-1 sec-check" data-sid="' + s.seccion_id + '" ' + (ch ? 'checked' : '') + '>';
        if (ld) html += '<span class="spinner-border spinner-border-sm me-1"></span>';
        html += esc(s.nombre) + '</label></div>';
    });
    html += '<hr class="my-1">';

    function tree(leaves, layers, axis) {
        var m = axis === 'vertical' ? visibleV : visibleH;
        var ids = (leaves || []).map(function(l) { return l.categoria_id; });
        var all = ids.length > 0 && ids.every(function(id) { return m[id] !== false; });
        var any = ids.some(function(id) { return m[id] !== false; });
        html += '<div class="mb-1 mt-1"><label style="cursor:pointer;font-weight:600" class="small">';
        html += '<input type="checkbox" class="me-1 todo-check" data-axis="' + axis + '" ' + (all ? 'checked' : '') + '>';
        html += '<i class="bi ' + (all ? 'bi-check2-square' : (any ? 'bi-dash-square' : 'bi-square')) + ' me-1"></i> ' + (axis === 'vertical' ? 'Filas' : 'Columnas');
        html += '</label></div>';

        var pids = {}, p2c = {};
        (leaves || []).forEach(function(l) {
            if (l.padre_id) { pids[l.padre_id] = true; (p2c[l.padre_id] = p2c[l.padre_id] || []).push(l); }
        });
        var pn = {};
        (layers || []).forEach(function(row) {
            (row || []).forEach(function(cell) {
                if (cell.tipo === 'parent' && pids[cell.categoria_id]) pn[cell.categoria_id] = cell.nombre;
            });
        });
        Object.keys(p2c).forEach(function(pid) {
            pid = parseInt(pid);
            var name = pn[pid] || ('ID ' + pid);
            var kids = p2c[pid] || [];
            if (m[pid] === undefined) m[pid] = kids.some(function(ch) { return m[ch.categoria_id] !== false; });
            var ck = m[pid] !== false ? 'checked' : '';
            html += '<div class="panel-parent"><label style="cursor:pointer;font-weight:600">';
            html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + pid + '" data-parent="" ' + ck + '>';
            html += '<i class="bi ' + (ck ? 'bi-folder2-open' : 'bi-folder2') + ' me-1"></i>' + esc(name) + '</label></div>';
            kids.forEach(function(ch) {
                var ck2 = m[ch.categoria_id] !== false ? 'checked' : '';
                html += '<div class="panel-child" style="padding-left:1.5rem"><label style="cursor:pointer">';
                html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + ch.categoria_id + '" data-parent="' + pid + '" ' + ck2 + '>';
                html += esc(ch.nombre) + '</label></div>';
            });
        });
        var flat = (leaves || []).filter(function(l) { return !l.padre_id; });
        flat.forEach(function(l) {
            var ck = m[l.categoria_id] !== false ? 'checked' : '';
            html += '<div class="panel-child" style="padding-left:0.3rem"><label style="cursor:pointer">';
            html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + l.categoria_id + '" data-parent="" ' + ck + '>';
            html += '<i class="bi bi-file-earmark me-1"></i>' + esc(l.nombre) + '</label></div>';
        });
        if (!Object.keys(p2c).length && !flat.length) html += '<small class="text-muted">(sin categorías)</small>';
    }

    tree(estado.verticales, estado.labels, 'vertical');
    html += '<hr class="my-1">';
    tree(estado.horizontales, estado.headers, 'horizontal');
    c.innerHTML = html;
    syncTodoCheckboxes();

    c.querySelectorAll('.cat-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var a = this.dataset.axis;
            var id = parseInt(this.dataset.id);
            var parent = this.dataset.parent ? parseInt(this.dataset.parent) : null;
            var m = a === 'vertical' ? visibleV : visibleH;
            m[id] = this.checked;
            if (parent) updateParentCheck(a, parent);
            else c.querySelectorAll('.cat-check[data-axis="' + a + '"][data-parent="' + id + '"]').forEach(function(ch) {
                ch.checked = this.checked; m[parseInt(ch.dataset.id)] = this.checked;
            }, this);
            syncTodoCheckboxes();
            renderTables();
            saveStateToURL();
        });
    });
    c.querySelectorAll('.todo-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var a = this.dataset.axis;
            var m = a === 'vertical' ? visibleV : visibleH;
            var on = this.checked;
            Object.keys(m).forEach(function(k) { m[k] = on; });
            c.querySelectorAll('.cat-check[data-axis="' + a + '"]').forEach(function(ch) {
                ch.checked = on;
                var pid = ch.dataset.parent;
                if (pid) { var pc = c.querySelector('.cat-check[data-axis="' + a + '"][data-id="' + pid + '"]'); if (pc) pc.checked = on; }
            });
            c.querySelectorAll('.cat-check[data-axis="' + a + '"][data-parent=""]').forEach(function(pcb) {
                (a === 'vertical' ? visibleV : visibleH)[parseInt(pcb.dataset.id)] = on;
            });
            renderTables();
            saveStateToURL();
        });
    });
}

// ─── Render tables (one table per active section) ───

function renderTables() {
    var container = document.getElementById('tables-container');
    if (!container) return;

    var headers = estado.headers || [];
    var labels = estado.labels || [];
    var pivotLabel = esc(estado.pivot_label || 'PIVOTE');

    var visVIdx = []; // indices of visible vertical leaves
    (estado.verticales || []).forEach(function(v, i) {
        if (visibleV[v.categoria_id] !== false) visVIdx.push(i);
    });
    var visHIdx = [];
    (estado.horizontales || []).forEach(function(h, i) {
        if (visibleH[h.categoria_id] !== false) visHIdx.push(i);
    });

    var numLabelCols = 1;
    if (headers.length && headers[0].length && headers[0][0].tipo === 'corner') {
        numLabelCols = headers[0][0].colspan || 1;
    }

    var activeSids = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });

    if (!visHIdx.length || !visVIdx.length || !activeSids.length) {
        container.innerHTML = '<div class="text-muted small p-3">Sin datos disponibles. Seleccioná al menos una sección, fila y columna.</div>';
        return;
    }

    // Filter label rows
    var visLabelRows = [];
    labels.forEach(function(rowCells) {
        var leaf = null;
        rowCells.forEach(function(c) { if (c.tipo === 'leaf') leaf = c; });
        if (!leaf) return;
        if (visVIdx.indexOf(leaf.row_index) >= 0) visLabelRows.push(rowCells);
    });

    // Recalculate parent rowspan
    var parentSpan = {};
    var openP = null;
    visLabelRows.forEach(function(rowCells) {
        var pc = null;
        rowCells.forEach(function(c) { if (c.tipo === 'parent') pc = c; });
        if (pc) { openP = pc.categoria_id; parentSpan[openP] = 1; }
        else if (openP !== null) parentSpan[openP]++;
    });

    // Build header rows (filtered)
    function buildHeaderRow() {
        var h = '';
        var hDepth = headers.length;
        for (var ri = 0; ri < hDepth; ri++) {
            h += '<tr>';
            var row = headers[ri];
            for (var ci = 0; ci < row.length; ci++) {
                var cell = row[ci];
                if (cell.tipo === 'corner') {
                    var cornerChecks = '<input type="checkbox" class="vis-cb" checked disabled style="opacity:0.4" title="Pivote siempre visible">';
                    h += '<th rowspan="' + (cell.rowspan || hDepth) + '" colspan="' + numLabelCols + '" class="fw-semibold text-center align-middle" style="min-width:60px">' + cornerChecks + pivotLabel + '</th>';
                } else if (cell.tipo === 'parent') {
                    var start = cell.col_index;
                    var end = start + cell.colspan;
                    var cnt = 0;
                    for (var vi = 0; vi < visHIdx.length; vi++) { var hidx = visHIdx[vi]; if (hidx >= start && hidx < end) cnt++; }
                    if (cnt === 0) continue;
                    var pid = cell.categoria_id;
                    var pck = visibleH[pid] !== false ? 'checked' : '';
                    h += '<th colspan="' + cnt + '" class="fw-semibold text-center small">';
                    h += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb col-cb" data-cid="' + pid + '" ' + pck + '> ' + esc(cell.nombre) + '</label>';
                    h += '</th>';
                } else if (cell.tipo === 'leaf') {
                    if (visHIdx.indexOf(cell.col_index) < 0) continue;
                    var cid = cell.categoria_id;
                    var ck = visibleH[cid] !== false ? 'checked' : '';
                    h += '<th class="fw-semibold text-center small" style="white-space:nowrap">';
                    h += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb col-cb" data-cid="' + cid + '" ' + ck + '> ' + esc(cell.nombre) + '</label>';
                    h += '</th>';
                }
            }
            h += '</tr>';
        }
        return h;
    }

    var headerHtml = buildHeaderRow();

    var allHtml = '';
    activeSids.forEach(function(sid) {
        var sec = (estado.secciones || []).find(function(s) { return s.seccion_id == sid; });
        var secName = sec ? sec.nombre : ('Sección ' + sid);

        allHtml += '<div class="section-block">';
        allHtml += '<div class="section-title">';
        var secCk = selectedSections[sid] !== false ? 'checked' : '';
        allHtml += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb sec-table-cb" data-sid="' + sid + '" ' + secCk + '> ' + esc(secName) + '</label>';
        allHtml += '</div>';

        allHtml += '<table class="table table-bordered table-sm mb-0">';
        allHtml += '<thead class="table-light">' + headerHtml + '</thead>';
        allHtml += '<tbody>';

        var secData = sectionsCache[sid] ? sectionsCache[sid].data : [];

        visLabelRows.forEach(function(rowCells) {
            allHtml += '<tr>';
            rowCells.forEach(function(cell) {
                if (cell.tipo === 'parent') {
                    var span = parentSpan[cell.categoria_id] || 1;
                    var ck = visibleV[cell.categoria_id] !== false ? 'checked' : '';
                    allHtml += '<th rowspan="' + span + '" class="fw-semibold text-nowrap align-middle small">';
                    allHtml += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb row-cb" data-cid="' + cell.categoria_id + '" ' + ck + '> ' + esc(cell.nombre) + '</label>';
                    allHtml += '</th>';
                } else if (cell.tipo === 'leaf') {
                    var hasParentInRow = rowCells.some(function(c) { return c.tipo === 'parent'; });
                    var cs2 = hasParentInRow && cell.colspan ? ' colspan="' + cell.colspan + '"' : '';
                    var ck2 = visibleV[cell.categoria_id] !== false ? 'checked' : '';
                    allHtml += '<th' + cs2 + ' class="fw-semibold text-nowrap small">';
                    allHtml += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb row-cb" data-cid="' + cell.categoria_id + '" ' + ck2 + '> ' + esc(cell.nombre) + '</label>';
                    allHtml += '</th>';
                }
            });

            // Data cells
            visHIdx.forEach(function(hidx) {
                var val = '';
                if (secData) {
                    var vertIdx = null;
                    rowCells.forEach(function(c) { if (c.tipo === 'leaf') vertIdx = c.row_index; });
                    if (vertIdx !== null && secData[vertIdx] && secData[vertIdx][hidx]) {
                        var cel = secData[vertIdx][hidx];
                        val = (cel.valor !== undefined && cel.valor !== '') ? cel.valor : '';
                    }
                }
                allHtml += '<td class="valor">' + esc(String(val)) + '</td>';
            });
            allHtml += '</tr>';
        });

        allHtml += '</tbody></table>';
        allHtml += '</div>';
    });

    container.innerHTML = allHtml;

    // Wire up checkboxes inside tables
    container.querySelectorAll('.col-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var cid = parseInt(this.dataset.cid);
            var on = this.checked;
            visibleH[cid] = on;
            (estado.horizontales || []).forEach(function(h) {
                if (h.padre_id === cid) { visibleH[h.categoria_id] = on; }
            });
            saveStateToURL();
            renderTables();
        });
    });
    container.querySelectorAll('.row-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var cid = parseInt(this.dataset.cid);
            var on = this.checked;
            visibleV[cid] = on;
            (estado.verticales || []).forEach(function(v) {
                if (v.padre_id === cid) { visibleV[v.categoria_id] = on; }
            });
            saveStateToURL();
            renderTables();
        });
    });
    container.querySelectorAll('.sec-table-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            selectedSections[parseInt(this.dataset.sid)] = this.checked;
            saveStateToURL();
            renderTables();
        });
    });
}

// ─── Section loading ───

function loadSectionData(sid) {
    if (sectionsCache[sid]) return Promise.resolve(sectionsCache[sid]);
    return api('/dataset/seccion/' + sid + '/data')
        .then(function(j) {
            if (j.data) {
                var sName = ((estado.secciones || []).find(function(s) { return s.seccion_id === sid; }) || {}).nombre || '';
                sectionsCache[sid] = { nombre: sName, data: j.data || [] };
                return sectionsCache[sid];
            }
            throw new Error(j.message || 'Error al cargar sección');
        });
}

// ─── URL state ───

function sanitize(str) {
    if (!str) return [];
    return str.split(',').map(function(s) { return parseInt(s, 10); }).filter(function(n) { return !isNaN(n) && n > 0; });
}
function saveStateToURL() {
    var v = Object.keys(visibleV).filter(function(id) { return visibleV[id] !== false; });
    var h = Object.keys(visibleH).filter(function(id) { return visibleH[id] !== false; });
    var s = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });
    var p = [];
    if (v.length) p.push('v=' + v.join(','));
    if (h.length) p.push('h=' + h.join(','));
    if (s.length) p.push('s=' + s.join(','));
    try { window.history.replaceState(null, '', window.location.pathname + (p.length ? '?' + p.join('&') : '')); } catch(e) {}
}
function loadStateFromURL() {
    var p = new URLSearchParams(window.location.search);
    var vl = sanitize(p.get('v')), hl = sanitize(p.get('h')), sl = sanitize(p.get('s'));
    if (vl.length) Object.keys(visibleV).forEach(function(id) { visibleV[id] = vl.indexOf(parseInt(id)) >= 0; });
    if (hl.length) Object.keys(visibleH).forEach(function(id) { visibleH[id] = hl.indexOf(parseInt(id)) >= 0; });
    if (sl.length) Object.keys(selectedSections).forEach(function(sid) { selectedSections[sid] = sl.indexOf(parseInt(sid)) >= 0; });
}

// ─── Debug ───

function updateDebug() {
    var el = document.getElementById('chart-debug');
    if (!el) return;
    el.style.display = 'block';
    var vn = Object.keys(visibleV).filter(function(id) { return visibleV[id]; }).map(function(id) {
        var c = (estado.verticales || []).find(function(v) { return v.categoria_id == id; }); return c ? c.nombre : id;
    });
    var hn = Object.keys(visibleH).filter(function(id) { return visibleH[id]; }).map(function(id) {
        var c = (estado.horizontales || []).find(function(h) { return h.categoria_id == id; }); return c ? c.nombre : id;
    });
    var sn = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; }).map(function(sid) {
        return ((estado.secciones || []).find(function(s) { return s.seccion_id == sid; }) || {}).nombre || sid;
    });
    el.textContent = '── Debug Dataset ──\n'
        + 'Filas visibles: ' + (vn.length ? vn.join(', ') : '(ninguna)') + '\n'
        + 'Columnas visibles: ' + (hn.length ? hn.join(', ') : '(ninguna)') + '\n'
        + 'Secciones activas: ' + (sn.length ? sn.join(', ') : '(ninguna)');
}

// ─── Init ───

function init() {
    (estado.verticales || []).forEach(function(v) { visibleV[v.categoria_id] = v.visible !== false; });
    (estado.horizontales || []).forEach(function(h) { visibleH[h.categoria_id] = h.visible !== false; });
    (estado.secciones || []).forEach(function(s) { selectedSections[s.seccion_id] = s.visible !== false; });
    if (!Object.keys(selectedSections).length && estado.secciones && estado.secciones.length) {
        selectedSections[estado.secciones[0].seccion_id] = true;
    }
    loadStateFromURL();

    var pending = [];
    Object.keys(selectedSections).forEach(function(sid) {
        if (selectedSections[sid] && !sectionsCache[sid]) pending.push(loadSectionData(parseInt(sid)));
    });

    function done() {
        renderPanel();
        renderTables();
        saveStateToURL();
        if (IS_DEV) updateDebug();
        document.getElementById('chart-panel').style.display = 'block';
    }
    if (pending.length) Promise.all(pending).then(done).catch(done);
    else done();
}

// ─── Events ───

document.getElementById('btn-toggle-panel')?.addEventListener('click', function() {
    var p = document.getElementById('chart-panel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
});
document.getElementById('btn-cerrar-panel')?.addEventListener('click', function() {
    document.getElementById('chart-panel').style.display = 'none';
});
document.getElementById('btn-toggle-cb')?.addEventListener('click', function() {
    document.getElementById('app-dataset').classList.toggle('show-cb');
    this.classList.toggle('active');
    this.innerHTML = this.classList.contains('active')
        ? '<i class="bi bi-check2-square"></i> Ocultar'
        : '<i class="bi bi-check2-square"></i> Checkboxes';
});
document.getElementById('btn-limpiar-seleccion')?.addEventListener('click', function() {
    (estado.verticales || []).forEach(function(v) { visibleV[v.categoria_id] = v.visible !== false; });
    (estado.horizontales || []).forEach(function(h) { visibleH[h.categoria_id] = h.visible !== false; });
    renderPanel();
    renderTables();
    saveStateToURL();
    status('Selección restaurada');
});
var dt = document.querySelector('.debug-toggle');
if (dt) dt.addEventListener('click', function() {
    var el = document.getElementById('chart-debug');
    if (el) { el.style.display = el.style.display === 'none' ? 'block' : 'none'; if (el.style.display === 'block') updateDebug(); }
});

// Section checkbox in side panel
document.getElementById('panel-items')?.addEventListener('change', function(e) {
    var cb = e.target;
    if (!cb.classList.contains('sec-check')) return;
    var sid = parseInt(cb.dataset.sid);
    if (isNaN(sid)) return;
    selectedSections[sid] = cb.checked;

    if (cb.checked && !sectionsCache[sid]) {
        cb.disabled = true;
        cb.insertAdjacentHTML('afterend', ' <span class="spinner-border spinner-border-sm"></span>');
        status('Cargando sección...');
        loadSectionData(sid)
            .then(function() { renderTables(); saveStateToURL(); status(''); if (IS_DEV) updateDebug(); })
            .catch(function(err) { selectedSections[sid] = false; cb.checked = false; alerta(err.message || 'Error'); })
            .finally(function() {
                cb.disabled = false;
                (cb.parentNode.querySelectorAll('.spinner-border') || []).forEach(function(s) { s.remove(); });
                renderPanel();
            });
    } else {
        renderTables();
        saveStateToURL();
        if (IS_DEV) updateDebug();
    }
});

init();
</script>
@endpush
