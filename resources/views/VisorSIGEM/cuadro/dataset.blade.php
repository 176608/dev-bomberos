@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'Dataset — ' . ($cuadro->codigo_cuadro ?? ''))

@section('visor_content')
<div class="container-fluid py-3 show-cb" id="app-dataset">

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
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-toggle-cb" title="Mostrar/ocultar checkboxes en tabla">
            <i class="bi bi-check2-square"></i> Checkboxes
        </button>
        <button type="button" class="btn btn-sm btn-outline-warning" id="btn-limpiar-seleccion" title="Restaurar selección por defecto">
            <i class="bi bi-arrow-counterclockwise"></i> Limpiar
        </button>
        @if(count($estadoInicial['secciones'] ?? []) > 1)
        <button type="button" class="btn btn-sm btn-outline-success" id="btn-activar-todas" title="Activar todas las secciones">
            <i class="bi bi-check-all"></i> Activar todas
        </button>
        @endif
        @if($esDesarrollador)
        <button type="button" class="btn btn-sm btn-outline-secondary debug-toggle" title="Alternar debug">
            <i class="bi bi-bug"></i> Debug
        </button>
        @endif
    </div>

    <div>
        <div id="tables-container"></div>
        @if($esDesarrollador)
        <div id="chart-debug" class="mt-2 small" style="display:none;background:#1e1e1e;color:#d4d4d4;font-family:Consolas,monospace;padding:0.6rem;border-radius:6px;white-space:pre-wrap;overflow-x:auto;max-height:250px;overflow-y:auto"></div>
        @endif
    </div>

    <div class="card-footer py-1 px-0 d-flex justify-content-between align-items-center mt-2" id="status-bar">
        <small id="status-text"></small>
    </div>

</div>

<style>
#status-bar #status-text { font-size: 0.8rem; }
#status-bar.status-flash { background: #d1e7fd !important; transition: background 0.3s; }
#tables-container > .section-block { margin-bottom:1.5rem; }
#tables-container .section-block .section-title { font-weight:700; font-size:0.85rem; padding:0.3rem 0.5rem; background:#e8edf2; border:1px solid #dee2e6; border-bottom:none; border-radius:4px 4px 0 0; }
#tables-container table { font-size:0.85rem; margin-bottom:0; }
#tables-container table th { white-space:nowrap; }
#tables-container table td.valor { text-align: right; }
.vis-cb { cursor:pointer; margin-right:2px; vertical-align:middle; }
#app-dataset.show-cb .vis-cb { display:inline-block; }
#app-dataset:not(.show-cb) .vis-cb { display:none; }
#app-dataset:not(.show-cb) #tables-container th label,
#app-dataset:not(.show-cb) #tables-container .section-title label { pointer-events:none; cursor:default; }
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

    // ─── Parent-children maps ───
    var childrenOf = {};
    (estado.verticales || []).forEach(function(v) {
        if (v.padre_id) { if (!childrenOf[v.padre_id]) childrenOf[v.padre_id] = []; childrenOf[v.padre_id].push(v); }
    });
    var childrenOfH = {};
    (estado.horizontales || []).forEach(function(h) {
        if (h.padre_id) { if (!childrenOfH[h.padre_id]) childrenOfH[h.padre_id] = []; childrenOfH[h.padre_id].push(h); }
    });

    // ─── Parent group boundaries from original labels ───
    var parentGroups = []; // [{parentId, cell, visibleCount}]
    var parentGroupOfIdx = {}; // label rowIndex -> parentId
    labels.forEach(function(rowCells, ri) {
        var pc = null;
        rowCells.forEach(function(c) { if (c.tipo === 'parent') pc = c; });
        if (pc) parentGroups.push({ parentId: pc.categoria_id, cell: pc, visibleCount: 0 });
        parentGroupOfIdx[ri] = parentGroups.length ? parentGroups[parentGroups.length - 1].parentId : null;
    });

    // ─── Filter label rows, strip parent cells, track visibility ───
    var visLabelRows = [];
    var seenParent = {};
    labels.forEach(function(rowCells) {
        var leaf = null;
        rowCells.forEach(function(c) { if (c.tipo === 'leaf') leaf = c; });
        if (!leaf) return;
        if (visVIdx.indexOf(leaf.row_index) >= 0) {
            var stripped = [];
            rowCells.forEach(function(c) { if (c.tipo !== 'parent') stripped.push(c); });
            visLabelRows.push(stripped);
            var pid = parentGroupOfIdx[leaf.row_index] || null;
            if (pid !== null) {
                var pg = parentGroups.find(function(g) { return g.parentId === pid; });
                if (pg) { pg.visibleCount++; if (seenParent[pid] === undefined) seenParent[pid] = visLabelRows.length - 1; }
            }
        }
    });

    // ─── Recalculate parentSpan, inject parent cell into first visible row ───
    var parentSpan = {};
    parentGroups.forEach(function(pg) {
        if (pg.visibleCount > 0) {
            parentSpan[pg.parentId] = pg.visibleCount;
            var insertAt = seenParent[pg.parentId];
            if (insertAt !== undefined) {
                visLabelRows[insertAt].unshift({
                    tipo: 'parent', categoria_id: pg.parentId, nombre: pg.cell.nombre
                });
            }
        }
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
                    var hkids = childrenOfH[pid] || [];
                    var hvis = hkids.filter(function(k) { return visibleH[k.categoria_id] !== false; });
                    var hallVis = hvis.length === hkids.length;
                    var hsomeVis = hvis.length > 0;
                    var hpck = hallVis ? 'checked' : '';
                    var hindet = hsomeVis && !hallVis ? ' data-indet="1"' : '';
                    h += '<th colspan="' + cnt + '" class="fw-semibold text-center small">';
                    h += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb col-cb" data-cid="' + pid + '" ' + hpck + hindet + '> ' + esc(cell.nombre) + '</label>';
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

    var singleSection = (estado.secciones || []).length <= 1;
    var allHtml = '';
    activeSids.forEach(function(sid) {
        var sec = (estado.secciones || []).find(function(s) { return s.seccion_id == sid; });
        var secName = sec ? sec.nombre : ('Sección ' + sid);

        allHtml += '<div class="section-block">';
        allHtml += '<div class="section-title">';
        var secCk = selectedSections[sid] !== false ? 'checked' : '';
        if (singleSection) {
            allHtml += esc(secName);
        } else {
            allHtml += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb sec-table-cb" data-sid="' + sid + '" ' + secCk + '> ' + esc(secName) + '</label>';
        }
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
                    var kids = childrenOf[cell.categoria_id] || [];
                    var visKids = kids.filter(function(k) { return visibleV[k.categoria_id] !== false; });
                    var allVis = visKids.length === kids.length;
                    var someVis = visKids.length > 0;
                    var ck = allVis ? 'checked' : '';
                    var indet = someVis && !allVis ? ' data-indet="1"' : '';
                    allHtml += '<th rowspan="' + span + '" class="fw-semibold text-nowrap align-middle small">';
                    allHtml += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb row-cb" data-cid="' + cell.categoria_id + '" ' + ck + indet + '> ' + esc(cell.nombre) + '</label>';
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

    // Set indeterminate state on parent checkboxes
    container.querySelectorAll('.row-cb[data-indet="1"]').forEach(function(cb) { cb.indeterminate = true; });
    container.querySelectorAll('.col-cb[data-indet="1"]').forEach(function(cb) { cb.indeterminate = true; });

    // Wire up checkboxes inside tables
    container.querySelectorAll('.col-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var cid = parseInt(this.dataset.cid);
            var on = this.checked;
            visibleH[cid] = on;
            if (childrenOfH[cid]) childrenOfH[cid].forEach(function(h) { visibleH[h.categoria_id] = on; });
            saveStateToURL();
            renderTables();
        });
    });
    container.querySelectorAll('.row-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var cid = parseInt(this.dataset.cid);
            var on = this.checked;
            visibleV[cid] = on;
            if (childrenOf[cid]) childrenOf[cid].forEach(function(v) { visibleV[v.categoria_id] = on; });
            saveStateToURL();
            renderTables();
        });
    });
    container.querySelectorAll('.sec-table-cb').forEach(function(cb) {
        var sid = parseInt(cb.dataset.sid);
        cb.addEventListener('change', function() {
            var on = this.checked;
            selectedSections[sid] = on;
            if (on && !sectionsCache[sid]) {
                this.disabled = true;
                status('Cargando sección...');
                loadSectionData(sid)
                    .then(function() { renderTables(); saveStateToURL(); status(''); if (IS_DEV) updateDebug(); })
                    .catch(function(err) { selectedSections[sid] = false; cb.checked = false; alerta(err.message || 'Error'); })
                    .finally(function() { cb.disabled = false; });
            } else {
                renderTables();
                saveStateToURL();
                if (IS_DEV) updateDebug();
            }
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
    var p = new URLSearchParams(window.location.search);
    ['v','h','s'].forEach(function(k) { p.delete(k); });
    var v = Object.keys(visibleV).filter(function(id) { return visibleV[id] !== false; });
    var h = Object.keys(visibleH).filter(function(id) { return visibleH[id] !== false; });
    var s = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });
    if (v.length) p.set('v', v.join(',')); else p.delete('v');
    if (h.length) p.set('h', h.join(',')); else p.delete('h');
    if (s.length) p.set('s', s.join(',')); else p.delete('s');
    var qs = p.toString();
    try { window.history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : '')); } catch(e) {}
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
        renderTables();
        saveStateToURL();
        if (IS_DEV) updateDebug();
    }
    if (pending.length) Promise.all(pending).then(done).catch(done);
    else done();
}

// ─── Events ───

document.getElementById('btn-toggle-cb')?.addEventListener('click', function() {
    var on = document.getElementById('app-dataset').classList.toggle('show-cb');
    this.innerHTML = on
        ? '<i class="bi bi-check2-square"></i> Ocultar'
        : '<i class="bi bi-check2-square"></i> Checkboxes';
});
(function() {
    var btn = document.getElementById('btn-toggle-cb');
    if (btn) btn.innerHTML = '<i class="bi bi-check2-square"></i> Ocultar';
})();
document.getElementById('btn-limpiar-seleccion')?.addEventListener('click', function() {
    (estado.verticales || []).forEach(function(v) { visibleV[v.categoria_id] = v.visible !== false; });
    (estado.horizontales || []).forEach(function(h) { visibleH[h.categoria_id] = h.visible !== false; });
    Object.keys(visibleH).forEach(function(id) {
        if (!(estado.horizontales || []).some(function(h) { return h.categoria_id == id; })) visibleH[id] = true;
    });
    Object.keys(visibleV).forEach(function(id) {
        if (!(estado.verticales || []).some(function(v) { return v.categoria_id == id; })) visibleV[id] = true;
    });
    renderTables();
    saveStateToURL();
    status('Selección restaurada');
});
var dt = document.querySelector('.debug-toggle');
if (dt) dt.addEventListener('click', function() {
    var el = document.getElementById('chart-debug');
    if (el) { el.style.display = el.style.display === 'none' ? 'block' : 'none'; if (el.style.display === 'block') updateDebug(); }
});
document.getElementById('btn-activar-todas')?.addEventListener('click', function() {
    var changed = false;
    (estado.secciones || []).forEach(function(s) {
        if (!selectedSections[s.seccion_id]) { selectedSections[s.seccion_id] = true; changed = true; }
    });
    if (!changed) return;
    var pend = [];
    Object.keys(selectedSections).forEach(function(sid) {
        if (selectedSections[sid] && !sectionsCache[sid]) pend.push(loadSectionData(parseInt(sid)));
    });
    var finish = function() { renderTables(); saveStateToURL(); status('Todas las secciones activadas'); };
    if (pend.length) Promise.all(pend).then(finish).catch(finish);
    else finish();
});

init();
</script>
@endpush
