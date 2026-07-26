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
            <div id="table-container" class="table-responsive"></div>
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
#table-container table { font-size:0.85rem; }
#table-container table th { white-space:nowrap; }
#table-container table td.valor { text-align: right; }
#table-container .section-header { background:#e8edf2; font-weight:700; border-bottom:2px solid #cdd5de; }
#table-container .section-header td { padding:0.3rem 0.5rem; font-size:0.8rem; }
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

// ─── Panel helpers ───

function syncTodoCheckboxes() {
    var container = document.getElementById('panel-items');
    if (!container) return;
    container.querySelectorAll('.todo-check').forEach(function(cb) {
        var axis = cb.dataset.axis;
        var visMap = axis === 'vertical' ? visibleV : visibleH;
        var ids = Object.keys(visMap);
        var allVis = ids.length > 0 && ids.every(function(id) { return visMap[id] !== false; });
        var anyVis = ids.some(function(id) { return visMap[id] !== false; });
        cb.checked = allVis;
        var icon = cb.parentNode.querySelector('i');
        if (icon) {
            icon.className = 'bi ' + (allVis ? 'bi-check2-square' : (anyVis ? 'bi-dash-square' : 'bi-square')) + ' me-1';
        }
    });
}

function updateParentCheckState(axis, parentId) {
    var container = document.getElementById('panel-items');
    if (!container) return;
    var children = container.querySelectorAll('.cat-check[data-axis="' + axis + '"][data-parent="' + parentId + '"]');
    var allChecked = true, anyChecked = false;
    children.forEach(function(ch) {
        if (ch.checked) anyChecked = true;
        else allChecked = false;
    });
    var parentCb = container.querySelector('.cat-check[data-axis="' + axis + '"][data-id="' + parentId + '"]');
    if (parentCb) {
        if (!anyChecked) {
            parentCb.checked = false;
            (axis === 'vertical' ? visibleV : visibleH)[parentId] = false;
        } else if (allChecked) {
            parentCb.checked = true;
            (axis === 'vertical' ? visibleV : visibleH)[parentId] = true;
        }
    }
}

function renderCategoryPanel() {
    var container = document.getElementById('panel-items');
    if (!container) return;
    var html = '';

    html += '<div class="mb-2"><label class="small text-muted fw-semibold">Secciones</label></div>';
    (estado.secciones || []).forEach(function(s) {
        var isChecked = selectedSections[s.seccion_id] !== false;
        var loading = !sectionsCache[s.seccion_id] && isChecked;
        html += '<div class="panel-child mb-1">';
        html += '<label style="cursor:pointer">';
        html += '<input type="checkbox" class="me-1 sec-check" data-seccion-id="' + s.seccion_id + '" ' + (isChecked ? 'checked' : '') + '>';
        if (loading) html += '<span class="spinner-border spinner-border-sm me-1" role="status"></span>';
        html += esc(s.nombre);
        html += '</label></div>';
    });
    html += '<hr class="my-1">';

    function axisTree(leaves, layers, axis) {
        var visMap = axis === 'vertical' ? visibleV : visibleH;
        var allIds = (leaves || []).map(function(l) { return l.categoria_id; });
        var allVis = allIds.length > 0 && allIds.every(function(id) { return visMap[id] !== false; });
        var anyVis = allIds.some(function(id) { return visMap[id] !== false; });
        html += '<div class="mb-1 mt-1">';
        html += '<label style="cursor:pointer;font-weight:600" class="small">';
        html += '<input type="checkbox" class="me-1 todo-check" data-axis="' + axis + '" ' + (allVis ? 'checked' : '') + '>';
        html += '<i class="bi ' + (allVis ? 'bi-check2-square' : (anyVis ? 'bi-dash-square' : 'bi-square')) + ' me-1"></i> ' + (axis === 'vertical' ? 'Filas' : 'Columnas');
        html += '</label></div>';

        var parentIds = {}, p2c = {};
        (leaves || []).forEach(function(l) {
            if (l.padre_id) {
                parentIds[l.padre_id] = true;
                (p2c[l.padre_id] = p2c[l.padre_id] || []).push(l);
            }
        });
        var pnames = {};
        (layers || []).forEach(function(row) {
            (row || []).forEach(function(c) {
                if (c.tipo === 'parent' && parentIds[c.categoria_id]) pnames[c.categoria_id] = c.nombre;
            });
        });
        Object.keys(p2c).forEach(function(pid) {
            pid = parseInt(pid);
            var pn = pnames[pid] || ('ID ' + pid);
            var kids = p2c[pid] || [];
            if (visMap[pid] === undefined) visMap[pid] = kids.some(function(ch) { return visMap[ch.categoria_id] !== false; });
            var chk = visMap[pid] !== false ? 'checked' : '';
            html += '<div class="panel-parent"><label style="cursor:pointer;font-weight:600">';
            html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + pid + '" data-parent="" ' + chk + '>';
            html += '<i class="bi ' + (chk ? 'bi-folder2-open' : 'bi-folder2') + ' me-1"></i>' + esc(pn);
            html += '</label></div>';
            kids.forEach(function(ch) {
                var ck = visMap[ch.categoria_id] !== false ? 'checked' : '';
                html += '<div class="panel-child" style="padding-left:1.5rem"><label style="cursor:pointer">';
                html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + ch.categoria_id + '" data-parent="' + pid + '" ' + ck + '>';
                html += esc(ch.nombre);
                html += '</label></div>';
            });
        });
        var flat = (leaves || []).filter(function(l) { return !l.padre_id; });
        flat.forEach(function(l) {
            var ck = visMap[l.categoria_id] !== false ? 'checked' : '';
            html += '<div class="panel-child" style="padding-left:0.3rem"><label style="cursor:pointer">';
            html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + l.categoria_id + '" data-parent="" ' + ck + '>';
            html += '<i class="bi bi-file-earmark me-1"></i>' + esc(l.nombre);
            html += '</label></div>';
        });
        if (!Object.keys(p2c).length && !flat.length) html += '<small class="text-muted">(sin categorías)</small>';
    }

    axisTree(estado.verticales, estado.labels, 'vertical');
    html += '<hr class="my-1">';
    axisTree(estado.horizontales, estado.headers, 'horizontal');
    container.innerHTML = html;
    syncTodoCheckboxes();

    container.querySelectorAll('.cat-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var axis = this.dataset.axis;
            var id = parseInt(this.dataset.id);
            var parent = this.dataset.parent ? parseInt(this.dataset.parent) : null;
            var visMap = axis === 'vertical' ? visibleV : visibleH;
            visMap[id] = this.checked;
            if (parent) {
                updateParentCheckState(axis, parent);
            } else {
                container.querySelectorAll('.cat-check[data-axis="' + axis + '"][data-parent="' + id + '"]').forEach(function(ch) {
                    ch.checked = this.checked;
                    visMap[parseInt(ch.dataset.id)] = this.checked;
                }, this);
            }
            syncTodoCheckboxes();
            renderTable();
            saveStateToURL();
        });
    });
    container.querySelectorAll('.todo-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var axis = this.dataset.axis;
            var visMap = axis === 'vertical' ? visibleV : visibleH;
            var on = this.checked;
            Object.keys(visMap).forEach(function(k) { visMap[k] = on; });
            container.querySelectorAll('.cat-check[data-axis="' + axis + '"]').forEach(function(ch) {
                ch.checked = on;
                var pid = ch.dataset.parent;
                if (pid) {
                    var pc = container.querySelector('.cat-check[data-axis="' + axis + '"][data-id="' + pid + '"]');
                    if (pc) pc.checked = on;
                }
            });
            container.querySelectorAll('.cat-check[data-axis="' + axis + '"][data-parent=""]').forEach(function(pcb) {
                (axis === 'vertical' ? visibleV : visibleH)[parseInt(pcb.dataset.id)] = on;
            });
            renderTable();
            saveStateToURL();
        });
    });
}

// ─── Render table with headers/labels hierarchy + multi-section ───

function renderTable() {
    var container = document.getElementById('table-container');
    if (!container) return;

    var headers = estado.headers || [];
    var labels = estado.labels || [];
    var pivotLabel = esc(estado.pivot_label || 'PIVOTE');

    // Determine visible leaf indices
    var visVertIndices = [];
    (estado.verticales || []).forEach(function(v, idx) {
        if (visibleV[v.categoria_id] !== false) visVertIndices.push(idx);
    });
    var visHorizIndices = [];
    (estado.horizontales || []).forEach(function(h, idx) {
        if (visibleH[h.categoria_id] !== false) visHorizIndices.push(idx);
    });

    var numLabelCols = 1;
    if (headers.length && headers[0].length && headers[0][0].tipo === 'corner') {
        numLabelCols = headers[0][0].colspan || 1;
    }

    var activeSids = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });

    var totalCols = numLabelCols + visHorizIndices.length;
    if (!totalCols || !visVertIndices.length) {
        container.innerHTML = '<div class="text-muted small p-3">Sin datos disponibles.</div>';
        return;
    }

    var html = '<table class="table table-bordered table-sm mb-0">';

    // ── HEADERS (hierarchy with filtered leaves) ──
    // Rebuild header rows, keeping only visible columns
    var headerDepth = headers.length;

    for (var hi = 0; hi < headerDepth; hi++) {
        html += '<thead class="table-light"><tr>';
        var row = headers[hi];

        for (var ci = 0; ci < row.length; ci++) {
            var cell = row[ci];

            if (cell.tipo === 'corner') {
                html += '<th rowspan="' + (cell.rowspan || headerDepth) + '" colspan="' + numLabelCols + '" class="fw-semibold text-center align-middle" style="min-width:60px">' + pivotLabel + '</th>';
                continue;
            }

            if (cell.tipo === 'parent') {
                // Count visible leaves under this parent
                var start = cell.col_index;
                var end = start + cell.colspan;
                var visibleCount = 0;
                for (var vi = 0; vi < visHorizIndices.length; vi++) {
                    var hidx = visHorizIndices[vi];
                    if (hidx >= start && hidx < end) visibleCount++;
                }
                if (visibleCount === 0) continue; // skip parent with no visible children
                html += '<th colspan="' + visibleCount + '" class="fw-semibold text-center small">' + esc(cell.nombre) + '</th>';
                continue;
            }

            if (cell.tipo === 'leaf') {
                var hidx = cell.col_index;
                if (visHorizIndices.indexOf(hidx) < 0) continue;
                html += '<th class="fw-semibold text-center small" style="white-space:nowrap">' + esc(cell.nombre) + '</th>';
                continue;
            }
        }

        html += '</tr></thead>';
    }

    // ── BODY ──
    html += '<tbody>';

    if (!activeSids.length) {
        html += '<tr><td colspan="' + totalCols + '" class="text-muted small text-center p-3">Seleccioná al menos una sección para ver datos.</td></tr>';
        html += '</tbody></table>';
        container.innerHTML = html;
        return;
    }

    // Filter label rows: keep only rows whose leaf is visible
    var visibleLabelRows = [];
    labels.forEach(function(rowCells) {
        var leafCell = null;
        rowCells.forEach(function(c) { if (c.tipo === 'leaf') leafCell = c; });
        if (!leafCell) return;
        var vIdx = leafCell.row_index;
        if (visVertIndices.indexOf(vIdx) >= 0) {
            visibleLabelRows.push({ cells: rowCells, vertIdx: vIdx });
        }
    });

    // Recalculate parent rowspan after filtering
    var parentSpan = {};
    var openParent = null;
    visibleLabelRows.forEach(function(lr) {
        var parentCell = null;
        lr.cells.forEach(function(c) { if (c.tipo === 'parent') parentCell = c; });
        if (parentCell) {
            openParent = parentCell.categoria_id;
            parentSpan[openParent] = 1;
        } else if (openParent !== null) {
            parentSpan[openParent]++;
        }
    });

    // For each active section, render section parent row + data
    activeSids.forEach(function(sid) {
        var sec = (estado.secciones || []).find(function(s) { return s.seccion_id == sid; });
        var secName = sec ? sec.nombre : ('Sección ' + sid);
        var secData = sectionsCache[sid] ? sectionsCache[sid].data : [];

        html += '<tr class="section-header"><td colspan="' + totalCols + '">' + esc(secName) + '</td></tr>';

        visibleLabelRows.forEach(function(lr) {
            html += '<tr>';
            lr.cells.forEach(function(cell) {
                if (cell.tipo === 'parent') {
                    var span = parentSpan[cell.categoria_id] || 1;
                    html += '<th rowspan="' + span + '" class="fw-semibold text-nowrap align-middle small">' + esc(cell.nombre) + '</th>';
                } else if (cell.tipo === 'leaf') {
                    var cs = cell.colspan ? ' colspan="' + cell.colspan + '"' : '';
                    html += '<th' + cs + ' class="fw-semibold text-nowrap small">' + esc(cell.nombre) + '</th>';
                }
            });

            // Data cells for this row
            visHorizIndices.forEach(function(hidx) {
                var val = '';
                if (secData[lr.vertIdx] && secData[lr.vertIdx][hidx]) {
                    var c = secData[lr.vertIdx][hidx];
                    val = (c.valor !== undefined && c.valor !== '') ? c.valor : '';
                }
                html += '<td class="valor">' + esc(String(val)) + '</td>';
            });
            html += '</tr>';
        });
    });

    html += '</tbody></table>';
    container.innerHTML = html;
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

function sanitizeIdList(str) {
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
    var qs = p.join('&');
    try { window.history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : '')); } catch(e) {}
}

function loadStateFromURL() {
    var p = new URLSearchParams(window.location.search);
    var vl = sanitizeIdList(p.get('v')), hl = sanitizeIdList(p.get('h')), sl = sanitizeIdList(p.get('s'));
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
        var c = (estado.verticales || []).find(function(v) { return v.categoria_id == id; });
        return c ? c.nombre : id;
    });
    var hn = Object.keys(visibleH).filter(function(id) { return visibleH[id]; }).map(function(id) {
        var c = (estado.horizontales || []).find(function(h) { return h.categoria_id == id; });
        return c ? c.nombre : id;
    });
    var sn = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; }).map(function(sid) {
        return ((estado.secciones || []).find(function(s) { return s.seccion_id == sid; }) || {}).nombre || sid;
    });
    el.textContent = '── Debug Dataset ──\n'
        + 'Filas visibles: ' + (vn.length ? vn.join(', ') : '(ninguna)') + '\n'
        + 'Columnas visibles: ' + (hn.length ? hn.join(', ') : '(ninguna)') + '\n'
        + 'Secciones activas: ' + (sn.length ? sn.join(', ') : '(ninguna)') + '\n'
        + 'Headers depth: ' + (estado.headers || []).length + ', Labels rows: ' + (estado.labels || []).length;
}

// ─── Init ───

function initDatasetPage() {
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

    function finish() {
        renderCategoryPanel();
        renderTable();
        saveStateToURL();
        if (IS_DEV) updateDebug();
        document.getElementById('chart-panel').style.display = 'block';
    }
    if (pending.length) Promise.all(pending).then(finish).catch(finish);
    else finish();
}

// ─── Events ───

document.getElementById('btn-toggle-panel')?.addEventListener('click', function() {
    var p = document.getElementById('chart-panel');
    p.style.display = p.style.display === 'none' ? 'block' : 'none';
});
document.getElementById('btn-cerrar-panel')?.addEventListener('click', function() {
    document.getElementById('chart-panel').style.display = 'none';
});
document.getElementById('btn-limpiar-seleccion')?.addEventListener('click', function() {
    (estado.verticales || []).forEach(function(v) { visibleV[v.categoria_id] = v.visible !== false; });
    (estado.horizontales || []).forEach(function(h) { visibleH[h.categoria_id] = h.visible !== false; });
    renderCategoryPanel();
    renderTable();
    saveStateToURL();
    status('Selección restaurada');
});
var dt = document.querySelector('.debug-toggle');
if (dt) dt.addEventListener('click', function() {
    var el = document.getElementById('chart-debug');
    if (el) { el.style.display = el.style.display === 'none' ? 'block' : 'none'; if (el.style.display === 'block') updateDebug(); }
});

document.getElementById('panel-items')?.addEventListener('change', function(e) {
    var cb = e.target;
    if (!cb.classList.contains('sec-check')) return;
    var sid = parseInt(cb.dataset.seccionId);
    if (isNaN(sid)) return;
    selectedSections[sid] = cb.checked;

    if (cb.checked && !sectionsCache[sid]) {
        cb.disabled = true;
        cb.insertAdjacentHTML('afterend', ' <span class="spinner-border spinner-border-sm" role="status"></span>');
        status('Cargando sección...');
        loadSectionData(sid)
            .then(function() {
                renderTable();
                saveStateToURL();
                status('');
                if (IS_DEV) updateDebug();
            })
            .catch(function(err) {
                selectedSections[sid] = false;
                cb.checked = false;
                alerta(err.message || 'Error al cargar sección');
            })
            .finally(function() {
                cb.disabled = false;
                var sp = cb.parentNode ? cb.parentNode.querySelectorAll('.spinner-border') : [];
                sp.forEach(function(s) { s.remove(); });
                renderCategoryPanel();
            });
    } else {
        renderTable();
        saveStateToURL();
        if (IS_DEV) updateDebug();
    }
});

initDatasetPage();
</script>
@endpush
