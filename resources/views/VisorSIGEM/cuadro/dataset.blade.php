@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'Cuadro — ' . ($cuadro->codigo_cuadro ?? ''))

@section('visor_content')
<div class="container-fluid py-3 show-cb" id="app-dataset">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Cuadro</h5>
            <small class="text-muted">
                <code>{{ $cuadro->codigo_cuadro }}</code>
                <strong>{{ $cuadro->c_titulo }}</strong>
            </small>
        </div>
        <div class="d-flex gap-2">
            @if($cuadro->permite_grafica)
            <a href="{{ url('/sigem-v2/cuadro/' . $cuadro->cuadro_id . '/grafica') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="btn btn-outline-success btn-sm" id="link-to-grafica"
               data-base="{{ url('/sigem-v2/cuadro/' . $cuadro->cuadro_id . '/grafica') }}">
                <i class="bi bi-bar-chart-fill me-1"></i> Gráfica
            </a>
            @endif
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ url('/sigem-v2/cuadro/' . $cuadro->cuadro_id . '/exportar/excel?todo=1') }}"><i class="bi bi-grid-3x3 me-2"></i>Todo el cuadro</a></li>
                    <li><a class="dropdown-item" id="link-exportar-excel-seleccion" href="#"><i class="bi bi-check2-square me-2"></i>Solo selección</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-toggle-cb" title="Mostrar u ocultar los checkboxes de la tabla">
            <i class="bi bi-check2-square"></i> Ocultar
        </button>
        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-limpiar-seleccion" title="Restaurar selección por defecto">
            <i class="bi bi-arrow-counterclockwise"></i> Limpiar
        </button>
        <button type="button" class="btn btn-sm btn-outline-success" id="btn-show-desel" title="Mostrar temporalmente las categorías deseleccionadas para poder reactivarlas">
            <i class="bi bi-eye-slash"></i> <span id="show-desel-label">Ver deselecciones</span>
        </button>
        @if(count($estadoInicial['secciones'] ?? []) > 1)
        <button type="button" class="btn btn-sm btn-outline-success" id="btn-activar-todas" title="Activar todas las secciones">
            <i class="bi bi-check-all"></i> Activar todas
        </button>
        @endif
    </div>

    <div>
        <div id="tables-container"></div>
        @if($cuadro->pie_pagina)
        <div class="mt-3 small text-muted pie-pagina">{!! $cuadro->pie_pagina !!}</div>
        @endif
    </div>

    <div class="card-footer py-1 px-0 d-flex justify-content-between align-items-center mt-2" id="status-bar">
        <small id="status-text"></small>
    </div>

</div>

<style>
#status-bar #status-text { font-size: 0.8rem; }
#status-bar.status-flash { background: #d1e7fd !important; transition: background 0.3s; }
#tables-container > .section-block { margin-bottom:1.5rem; overflow-x:auto; }
#tables-container .section-block .section-title { font-weight:700; font-size:0.85rem; padding:0.3rem 0.5rem; background:#e8edf2; border:1px solid #dee2e6; border-bottom:none; border-radius:4px 4px 0 0; }
#tables-container .section-block table { font-size:0.85rem; margin-bottom:0; border-radius:0 0 4px 4px; overflow:hidden; }
#tables-container .section-block table thead tr:first-child th:first-child { border-top-left-radius:0; }
#tables-container table th { white-space:nowrap; text-align:center; width:1%; }
#tables-container table td.valor { text-align:right; white-space:nowrap; }
.vis-cb { cursor:pointer; margin-right:2px; vertical-align:middle; }
#app-dataset.show-cb .vis-cb { display:inline-block; }
#app-dataset:not(.show-cb) .vis-cb { display:none; }
#app-dataset:not(.show-cb) #tables-container th label,
#app-dataset:not(.show-cb) #tables-container .section-title label { pointer-events:none; cursor:default; }
#tables-container .desel-row { opacity:0.5; }
#tables-container .desel-row td,
#tables-container .desel-row th { opacity:0.5; }
#tables-container .total-row td.valor { font-weight:700; }
#tables-container table tbody tr:hover td.valor { background:#f0f0f0; }
.pie-pagina { border-top:1px solid #dee2e6; padding-top:0.5rem; }
</style>
@endsection

@push('visor_scripts')
<script>
const CUADRO_ID = {{ $cuadro->cuadro_id }};
const BASE = '{{ url("/sigem-v2/cuadro") }}/' + CUADRO_ID;
var showDeselected = false;

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
function e(s) {
    return esc(s).replace(/\n/g, '<br>');
}
function hexToIntensity(hex, intensity) {
    hex = (hex || '').replace('#', '');
    if (hex.length !== 6) return null;
    var r = parseInt(hex.substring(0,2), 16);
    var g = parseInt(hex.substring(2,4), 16);
    var b = parseInt(hex.substring(4,6), 16);
    var mix = 1 - intensity;
    r = Math.round(r * intensity + 255 * mix);
    g = Math.round(g * intensity + 255 * mix);
    b = Math.round(b * intensity + 255 * mix);
    return 'rgb(' + r + ',' + g + ',' + b + ')';
}
function applyTheme(hexColor) {
    if (!hexColor) return;
    var old = document.getElementById('theme-style');
    if (old) old.remove();
    var s = document.createElement('style');
    s.id = 'theme-style';
    var bg75 = hexToIntensity(hexColor, 0.75);
    var bg50 = hexToIntensity(hexColor, 0.5);
    var bg25 = hexToIntensity(hexColor, 0.25);
    var bg125 = hexToIntensity(hexColor, 0.125);
    var bg60 = hexToIntensity(hexColor, 0.6);
    s.textContent = '' +
        '#tables-container table thead tr:first-child th:first-child{background:' + hexToIntensity(hexColor, 1) + ';color:#000;border-bottom:2px solid ' + hexColor + '}' +
        '#tables-container table thead tr:first-child th[colspan]:not(:first-child){background:' + bg75 + '}' +
        '#tables-container table thead tr:first-child th:not([colspan]):not(:first-child){background:' + bg50 + '}' +
        '#tables-container table thead tr:not(:first-child) th[colspan]{background:' + bg75 + '}' +
        '#tables-container table thead tr:not(:first-child) th:not([colspan]){background:' + bg50 + '}' +
        '#tables-container table tbody tr th[rowspan]{background:' + bg75 + '}' +
        '#tables-container table tbody tr th:not([rowspan]):not(.sub-cat){background:' + bg50 + '}' +
        '#tables-container table tbody tr th.sub-cat{background:' + bg25 + '}' +
        '#tables-container table tbody tr:nth-child(odd) td.valor{background:' + bg125 + '}' +
        '#tables-container table tbody tr:hover td.valor{background:' + bg50 + '}' +
        '#tables-container .total-row td.valor{background:' + bg60 + '!important;font-weight:700}';
    document.head.appendChild(s);
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

    var visVIdx = [];
    var deselV = {};
    (estado.verticales || []).forEach(function(v, i) {
        if (visibleV[v.categoria_id] !== false || showDeselected) { visVIdx.push(i); if (visibleV[v.categoria_id] === false) deselV[v.categoria_id] = true; }
    });
    var visHIdx = [];
    var deselH = {};
    (estado.horizontales || []).forEach(function(h, i) {
        if (visibleH[h.categoria_id] !== false || showDeselected) { visHIdx.push(i); if (visibleH[h.categoria_id] === false) deselH[h.categoria_id] = true; }
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

    // ─── Child detection maps for sub-cat styling ───
    var isChildV = {};
    (estado.verticales || []).forEach(function(v) { if (v.padre_id) isChildV[v.categoria_id] = true; });
    var isChildH = {};
    (estado.horizontales || []).forEach(function(h) { if (h.padre_id) isChildH[h.categoria_id] = true; });

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
                    h += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb col-cb" data-cid="' + pid + '" ' + hpck + hindet + '> ' + e(cell.nombre) + '</label>';
                    h += '</th>';
                } else if (cell.tipo === 'leaf') {
                    if (visHIdx.indexOf(cell.col_index) < 0) continue;
                    var cid = cell.categoria_id;
                    var ck = visibleH[cid] !== false ? 'checked' : '';
                    var hClasses = 'fw-semibold text-center small' + (isChildH[cid] ? ' sub-cat' : '');
                    h += '<th class="' + hClasses + '" style="white-space:nowrap">';
                    h += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb col-cb" data-cid="' + cid + '" ' + ck + '> ' + e(cell.nombre) + '</label>';
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
        if (!singleSection) {
            allHtml += '<div class="section-title">';
            var secCk = selectedSections[sid] !== false ? 'checked' : '';
            allHtml += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb sec-table-cb" data-sid="' + sid + '" ' + secCk + '> ' + esc(secName) + '</label>';
            allHtml += '</div>';
        }

        allHtml += '<table class="table table-bordered table-sm mb-0">';
        allHtml += '<thead class="table-light">' + headerHtml + '</thead>';
        allHtml += '<tbody>';

        var secData = sectionsCache[sid] ? sectionsCache[sid].data : [];

        visLabelRows.forEach(function(rowCells) {
            var rowLeaf = null; var rowParent = null;
            rowCells.forEach(function(c) { if (c.tipo === 'leaf') rowLeaf = c; if (c.tipo === 'parent') rowParent = c; });
            var isRowDesel = rowLeaf && deselV[rowLeaf.categoria_id];
            var totalName = rowLeaf ? rowLeaf.nombre : (rowParent ? rowParent.nombre : '');
            var isTotal = /^(Total|Totales|Sumatoria)$/i.test(totalName);
            allHtml += '<tr' + (isRowDesel ? ' class="desel-row"' : isTotal ? ' class="total-row"' : '') + '>';
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
                    allHtml += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb row-cb" data-cid="' + cell.categoria_id + '" ' + ck + indet + '> ' + e(cell.nombre) + '</label>';
                    allHtml += '</th>';
                } else if (cell.tipo === 'leaf') {
                    var hasParentInRow = rowCells.some(function(c) { return c.tipo === 'parent'; });
                    var cs2 = hasParentInRow && cell.colspan ? ' colspan="' + cell.colspan + '"' : '';
                    var ck2 = visibleV[cell.categoria_id] !== false ? 'checked' : '';
                    var vClasses = 'fw-semibold text-nowrap small' + (isChildV[cell.categoria_id] ? ' sub-cat' : '');
                    allHtml += '<th' + cs2 + ' class="' + vClasses + '">';
                    allHtml += '<label style="cursor:pointer;font-weight:inherit"><input type="checkbox" class="vis-cb row-cb" data-cid="' + cell.categoria_id + '" ' + ck2 + '> ' + e(cell.nombre) + '</label>';
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
                    .then(function() { renderTables(); saveStateToURL(); status(''); })
                    .catch(function(err) { selectedSections[sid] = false; cb.checked = false; alerta(err.message || 'Error'); })
                    .finally(function() { cb.disabled = false; });
            } else {
                renderTables();
                saveStateToURL();
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

// ─── URL state (inverted: save only exceptions, or all if fewer visible) ───

function parseIdList(str) {
    if (!str) return [];
    return str.split(',').map(function(s) { return parseInt(s, 10); }).filter(function(n) { return !isNaN(n); });
}
function saveStateToURL() {
    var p = new URLSearchParams(window.location.search);
    ['v','h','s'].forEach(function(k) { p.delete(k); });

    function save(key, items, map) {
        var hidden = [], visible = [];
        (items || []).forEach(function(item) {
            var id = item.categoria_id !== undefined ? item.categoria_id : item.seccion_id;
            (map[id] === false ? hidden : visible).push(id);
        });
        if (hidden.length === 0) { p.delete(key); return; }
        p.set(key, hidden.length <= visible.length
            ? hidden.map(function(id) { return '-' + id; }).join(',')
            : visible.join(','));
    }

    save('v', estado.verticales, visibleV);
    save('h', estado.horizontales, visibleH);
    save('s', estado.secciones, selectedSections);

    var qs = p.toString();
    try { window.history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : '')); } catch(e) {}
    var lnk = document.getElementById('link-to-grafica');
    if (lnk) lnk.href = lnk.getAttribute('data-base') + (qs ? '?' + qs : '');
    var lnkExcel = document.getElementById('link-exportar-excel-seleccion');
    if (lnkExcel) lnkExcel.href = window.location.pathname.replace('/dataset', '/exportar/excel') + (qs ? '?' + qs : '');
}
function loadStateFromURL() {
    var p = new URLSearchParams(window.location.search);

    function load(key, items, map) {
        var raw = p.get(key);
        if (!raw) return;
        var ids = parseIdList(raw);
        if (!ids.length) return;
        var isExceptions = raw.charAt(0) === '-';
        var set = {}; ids.forEach(function(id) { set[Math.abs(id)] = true; });
        (items || []).forEach(function(item) {
            var id = item.categoria_id !== undefined ? item.categoria_id : item.seccion_id;
            map[id] = isExceptions ? !set[id] : !!set[id];
        });
    }

    load('v', estado.verticales, visibleV);
    load('h', estado.horizontales, visibleH);
    load('s', estado.secciones, selectedSections);
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
        applyTheme(estado.tema_color);
        saveStateToURL();
    }
    if (pending.length) Promise.all(pending).then(done).catch(done);
    else done();
}

// ─── Events ───

document.getElementById('btn-toggle-cb')?.addEventListener('click', function() {
    var on = document.getElementById('app-dataset').classList.toggle('show-cb');
    this.innerHTML = on
        ? '<i class="bi bi-check2-square"></i> Ocultar'
        : '<i class="bi bi-check2-square"></i> Mostrar';
    this.className = on
        ? 'btn btn-sm btn-outline-secondary'
        : 'btn btn-sm btn-outline-success';
});
document.getElementById('btn-show-desel')?.addEventListener('click', function() {
    showDeselected = !showDeselected;
    var label = document.getElementById('show-desel-label');
    if (label) label.textContent = showDeselected ? 'Ocultar deselecciones' : 'Ver deselecciones';
    this.className = showDeselected
        ? 'btn btn-sm btn-outline-secondary'
        : 'btn btn-sm btn-outline-success';
    renderTables();
    saveStateToURL();
});
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

document.querySelectorAll('.dropdown-item[href*="exportar/excel"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
        console.log('[Excel] href clicked:', this.href);
        console.log('[Excel] target:', this.target);
    });
});

init();
</script>
@endpush
