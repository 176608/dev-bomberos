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
            <div class="mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <label class="small text-muted mb-0">Eje X</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="switch-invertir-ejes" title="Invertir: usa las horizontales como etiquetas del eje X">
                        <label class="form-check-label small" for="switch-invertir-ejes">Invertir ejes</label>
                    </div>
                </div>
                <div id="eje-helper" class="small text-muted mt-1" style="line-height:1.2"></div>
            </div>
            <hr class="my-1">
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
#table-container .section-parent { background:#f0f4f8; font-weight:600; }
#table-container .section-parent td { border-bottom:2px solid #dee2e6; }
</style>
@endsection

@push('visor_scripts')
<script>
const CUADRO_ID = {{ $cuadro->cuadro_id }};
const BASE = '{{ url("/sigem-v2/cuadro") }}/' + CUADRO_ID;
const IS_DEV = @json($esDesarrollador);

let estado = @json($estadoInicial);

var chartAxis = 'vertical';
var visibleV = {};
var visibleH = {};
var sectionsCache = {};
var selectedSections = {};

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

function parseCellValue(val) {
    if (val === undefined || val === null || val === '') return NaN;
    if (typeof val === 'number') return val;
    var s = String(val).replace(/,/g, '');
    return parseFloat(s);
}

// ─── Panel helpers (syncTodoCheckboxes, updateParentCheckState) ───

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
            var visMap = axis === 'vertical' ? visibleV : visibleH;
            visMap[parentId] = false;
        } else if (allChecked) {
            parentCb.checked = true;
            var visMap2 = axis === 'vertical' ? visibleV : visibleH;
            visMap2[parentId] = true;
        }
    }
}

// ─── Render category panel (checkboxes for axes + sections) ───

function renderCategoryPanel() {
    var container = document.getElementById('panel-items');
    if (!container) return;
    var html = '';

    // Sections (multi-select now — each active section stacks vertically in table)
    html += '<div class="mb-2"><label class="small text-muted fw-semibold">Secciones</label></div>';
    (estado.secciones || []).forEach(function(s) {
        var isChecked = selectedSections[s.seccion_id] !== false;
        var loading = !sectionsCache[s.seccion_id] && isChecked;
        var checked = isChecked ? 'checked' : '';
        html += '<div class="panel-child mb-1">';
        html += '<label style="cursor:pointer">';
        html += '<input type="checkbox" class="me-1 sec-check" data-seccion-id="' + s.seccion_id + '" ' + checked + '>';
        if (loading) html += '<span class="spinner-border spinner-border-sm me-1" role="status"></span>';
        html += esc(s.nombre);
        html += '</label></div>';
    });
    html += '<hr class="my-1">';

    // Build axes trees
    function buildAxisTree(leaves, layers, axis) {
        var visMap = axis === 'vertical' ? visibleV : visibleH;
        var allIds = (leaves || []).map(function(l) { return l.categoria_id; });
        var allVisible = allIds.length > 0 && allIds.every(function(id) { return visMap[id] !== false; });
        var anyVisible = allIds.some(function(id) { return visMap[id] !== false; });

        html += '<div class="mb-1 mt-1">';
        html += '<label style="cursor:pointer;font-weight:600" class="small">';
        html += '<input type="checkbox" class="me-1 todo-check" data-axis="' + axis + '" ' + (allVisible ? 'checked' : '') + '>';
        html += '<i class="bi ' + (allVisible ? 'bi-check2-square' : (anyVisible ? 'bi-dash-square' : 'bi-square')) + ' me-1"></i>'
            + (axis === 'vertical' ? 'Verticales' : 'Horizontales');
        html += '</label></div>';
        var parentIds = {}, parentToChildren = {};
        (leaves || []).forEach(function(l) {
            if (l.padre_id) {
                parentIds[l.padre_id] = true;
                if (!parentToChildren[l.padre_id]) parentToChildren[l.padre_id] = [];
                parentToChildren[l.padre_id].push(l);
            }
        });
        var parentNames = {};
        (layers || []).forEach(function(row) {
            (row || []).forEach(function(cell) {
                if (cell.tipo === 'parent' && parentIds[cell.categoria_id])
                    parentNames[cell.categoria_id] = cell.nombre;
            });
        });
        Object.keys(parentToChildren).forEach(function(pid) {
            pid = parseInt(pid);
            var pName = parentNames[pid] || ('ID ' + pid);
            var children = parentToChildren[pid] || [];
            if (visMap[pid] === undefined) {
                visMap[pid] = children.some(function(ch) { return visMap[ch.categoria_id] !== false; });
            }
            var isChecked = visMap[pid] !== false;
            var checked = isChecked ? 'checked' : '';
            html += '<div class="panel-parent">';
            html += '<label style="cursor:pointer;font-weight:600">';
            html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + pid + '" data-parent="" ' + checked + '>';
            html += '<i class="bi ' + (isChecked ? 'bi-folder2-open' : 'bi-folder2') + ' me-1"></i>' + esc(pName);
            html += '</label></div>';
            children.forEach(function(ch) {
                var childChecked = visMap[ch.categoria_id] !== false;
                var chk = childChecked ? 'checked' : '';
                html += '<div class="panel-child" style="padding-left:1.5rem">';
                html += '<label style="cursor:pointer">';
                html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + ch.categoria_id + '" data-parent="' + pid + '" ' + chk + '>';
                html += esc(ch.nombre);
                html += '</label></div>';
            });
        });
        var flatLeaves = (leaves || []).filter(function(l) { return !l.padre_id; });
        flatLeaves.forEach(function(l) {
            var isChecked = visMap[l.categoria_id] !== false;
            var checked = isChecked ? 'checked' : '';
            html += '<div class="panel-child" style="padding-left:0.3rem">';
            html += '<label style="cursor:pointer">';
            html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + l.categoria_id + '" data-parent="" ' + checked + '>';
            html += '<i class="bi bi-file-earmark me-1"></i>' + esc(l.nombre);
            html += '</label></div>';
        });
        if (!Object.keys(parentToChildren).length && !flatLeaves.length)
            html += '<small class="text-muted">(sin categorías)</small>';
    }

    buildAxisTree(estado.verticales, estado.labels, 'vertical');
    html += '<hr class="my-1">';
    buildAxisTree(estado.horizontales, estado.headers, 'horizontal');
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
            updateEjeHelper();
            saveStateToURL();
        });
    });

    container.querySelectorAll('.todo-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var axis = this.dataset.axis;
            var visMap = axis === 'vertical' ? visibleV : visibleH;
            var on = this.checked;
            Object.keys(visMap).forEach(function(id) { visMap[id] = on; });
            container.querySelectorAll('.cat-check[data-axis="' + axis + '"]').forEach(function(ch) {
                ch.checked = on;
                var pid = ch.dataset.parent;
                if (pid) {
                    var parentCb = container.querySelector('.cat-check[data-axis="' + axis + '"][data-id="' + pid + '"]');
                    if (parentCb) parentCb.checked = on;
                }
            });
            container.querySelectorAll('.cat-check[data-axis="' + axis + '"][data-parent=""]').forEach(function(pcb) {
                var visMap2 = axis === 'vertical' ? visibleV : visibleH;
                visMap2[parseInt(pcb.dataset.id)] = on;
            });
            renderTable();
            updateEjeHelper();
            saveStateToURL();
        });
    });
}

// ─── Render table with multi-section support ───

function renderTable() {
    var container = document.getElementById('table-container');
    if (!container) return;

    // Determine visible leaves
    var rows, cols;
    if (chartAxis === 'vertical') {
        rows = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; });
        cols = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; });
    } else {
        rows = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; });
        cols = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; });
    }

    // Build column lookup (map categoria_id → col index in the filtered list)
    var colIdxMap = {};
    cols.forEach(function(c, i) { colIdxMap[c.categoria_id] = i; });

    // Gather active sections
    var activeSids = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });

    if (!rows.length || !cols.length) {
        container.innerHTML = '<div class="text-muted small p-3">Sin datos disponibles.</div>';
        return;
    }

    var pivotLabel = esc(estado.pivot_label || '');
    var html = '<table class="table table-bordered table-sm mb-0">';
    html += '<thead class="table-light"><tr>';
    html += '<th class="fw-semibold text-center align-middle" style="min-width:60px">' + pivotLabel + '</th>';
    cols.forEach(function(col) {
        html += '<th class="fw-semibold text-center small" style="white-space:nowrap">' + esc(col.nombre) + '</th>';
    });
    html += '</tr></thead><tbody>';

    if (!activeSids.length) {
        // No sections selected — show empty
        html += '<tr><td colspan="' + (cols.length + 1) + '" class="text-muted small text-center">Seleccioná una sección para ver datos.</td></tr>';
        html += '</tbody></table>';
        container.innerHTML = html;
        return;
    }

    activeSids.forEach(function(sid) {
        var sec = (estado.secciones || []).find(function(s) { return s.seccion_id == sid; });
        var secName = sec ? sec.nombre : ('Sección ' + sid);
        var secData = sectionsCache[sid] ? sectionsCache[sid].data : [];

        // Section parent row (spans all columns)
        html += '<tr class="section-parent">';
        html += '<td colspan="' + (cols.length + 1) + '" class="fw-bold small px-2 py-1">' + esc(secName) + '</td>';
        html += '</tr>';

        // Data rows for this section
        rows.forEach(function(row) {
            html += '<tr>';
            html += '<th class="fw-semibold text-nowrap small px-2">' + esc(row.nombre) + '</th>';

            cols.forEach(function(col) {
                // Find the original index in the full list for data lookup
                var fullRowIdx = -1, fullColIdx = -1;
                if (chartAxis === 'vertical') {
                    fullRowIdx = (estado.verticales || []).findIndex(function(v) { return v.categoria_id === row.categoria_id; });
                    fullColIdx = (estado.horizontales || []).findIndex(function(h) { return h.categoria_id === col.categoria_id; });
                } else {
                    fullRowIdx = (estado.horizontales || []).findIndex(function(h) { return h.categoria_id === row.categoria_id; });
                    fullColIdx = (estado.verticales || []).findIndex(function(v) { return v.categoria_id === col.categoria_id; });
                }

                var val = '';
                if (fullRowIdx >= 0 && fullColIdx >= 0) {
                    var cell = secData[fullRowIdx] ? secData[fullRowIdx][fullColIdx] : null;
                    val = (cell && cell.valor !== undefined && cell.valor !== '') ? cell.valor : '';
                }
                html += '<td class="valor">' + esc(String(val)) + '</td>';
            });
            html += '</tr>';
        });
    });

    html += '</tbody></table>';
    container.innerHTML = html;
}

// ─── Eje helper ───

function updateEjeHelper() {
    var el = document.getElementById('eje-helper');
    if (!el) return;
    var vCount = 0, hCount = 0;
    if (chartAxis === 'vertical') {
        vCount = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; }).length;
        hCount = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; }).length;
    } else {
        vCount = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; }).length;
        hCount = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; }).length;
    }
    if (chartAxis === 'vertical') {
        el.textContent = 'Verticales en eje X (' + vCount + ') — Horizontales como series (' + hCount + ')';
    } else {
        el.textContent = 'Horizontales en eje X (' + hCount + ') — Verticales como series (' + vCount + ')';
    }
}

// ─── Section loading (multi-section support) ───

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

// ─── Save / Load URL state ───

function sanitizeIdList(str) {
    if (!str) return [];
    return str.split(',').map(function(s) { return parseInt(s, 10); }).filter(function(n) { return !isNaN(n) && n > 0; });
}

function saveStateToURL() {
    var visibleVIds = Object.keys(visibleV).filter(function(id) { return visibleV[id] !== false; });
    var visibleHIds = Object.keys(visibleH).filter(function(id) { return visibleH[id] !== false; });
    var selectedSids = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });
    var params = [];
    if (visibleVIds.length) params.push('v=' + visibleVIds.join(','));
    if (visibleHIds.length) params.push('h=' + visibleHIds.join(','));
    if (selectedSids.length) params.push('s=' + selectedSids.join(','));
    params.push('ej=' + (chartAxis === 'vertical' ? 'v' : 'h'));
    var qs = params.join('&');
    var url = window.location.pathname + (qs ? '?' + qs : '');
    try { window.history.replaceState(null, '', url); } catch(e) {}
}

function loadStateFromURL() {
    var p = new URLSearchParams(window.location.search);
    var vList = sanitizeIdList(p.get('v'));
    var hList = sanitizeIdList(p.get('h'));
    var sList = sanitizeIdList(p.get('s'));
    var ejeCode = p.get('ej');

    if (vList.length) {
        Object.keys(visibleV).forEach(function(id) { visibleV[id] = vList.indexOf(parseInt(id)) >= 0; });
    }
    if (hList.length) {
        Object.keys(visibleH).forEach(function(id) { visibleH[id] = hList.indexOf(parseInt(id)) >= 0; });
    }
    if (sList.length) {
        Object.keys(selectedSections).forEach(function(sid) {
            selectedSections[sid] = sList.indexOf(parseInt(sid)) >= 0;
        });
    }
    if (ejeCode === 'v' || ejeCode === 'h') {
        chartAxis = ejeCode === 'v' ? 'vertical' : 'horizontal';
        var sw = document.getElementById('switch-invertir-ejes');
        if (sw) sw.checked = ejeCode === 'h';
    }
}

// ─── Debug ───

function updateDebug() {
    var el = document.getElementById('chart-debug');
    if (!el) return;
    el.style.display = 'block';
    var vNames = Object.keys(visibleV).filter(function(id) { return visibleV[id]; }).map(function(id) {
        var c = (estado.verticales || []).find(function(v) { return v.categoria_id == id; });
        return c ? c.nombre : id;
    });
    var hNames = Object.keys(visibleH).filter(function(id) { return visibleH[id]; }).map(function(id) {
        var c = (estado.horizontales || []).find(function(h) { return h.categoria_id == id; });
        return c ? c.nombre : id;
    });
    var selectedNames = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; }).map(function(sid) {
        return ((estado.secciones || []).find(function(s) { return s.seccion_id == sid; }) || {}).nombre || sid;
    });
    el.textContent = '── Debug Dataset ──\n'
        + 'Eje X: ' + chartAxis + '\n'
        + 'Verticales visibles: ' + (vNames.length ? vNames.join(', ') : '(todas ocultas)') + '\n'
        + 'Horizontales visibles: ' + (hNames.length ? hNames.join(', ') : '(todas ocultas)') + '\n'
        + 'Secciones activas: ' + (selectedNames.length ? selectedNames.join(', ') : '(ninguna)') + '\n'
        + 'Caché secciones: ' + Object.keys(sectionsCache).join(', ');
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

    // Load initial section data
    var pendingLoads = [];
    Object.keys(selectedSections).forEach(function(sid) {
        if (selectedSections[sid] && !sectionsCache[sid]) {
            pendingLoads.push(loadSectionData(parseInt(sid)));
        }
    });

    function finishInit() {
        renderCategoryPanel();
        renderTable();
        updateEjeHelper();
        saveStateToURL();
        if (IS_DEV) updateDebug();
        document.getElementById('chart-panel').style.display = 'block';
    }

    if (pendingLoads.length) {
        Promise.all(pendingLoads).then(finishInit).catch(finishInit);
    } else {
        finishInit();
    }
}

// ─── Event listeners ───

document.getElementById('btn-toggle-panel')?.addEventListener('click', function() {
    var panel = document.getElementById('chart-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
});

document.getElementById('btn-cerrar-panel')?.addEventListener('click', function() {
    document.getElementById('chart-panel').style.display = 'none';
});

document.getElementById('switch-invertir-ejes')?.addEventListener('change', function() {
    chartAxis = this.checked ? 'horizontal' : 'vertical';
    renderTable();
    updateEjeHelper();
    saveStateToURL();
});

document.getElementById('btn-limpiar-seleccion')?.addEventListener('click', function() {
    (estado.verticales || []).forEach(function(v) { visibleV[v.categoria_id] = v.visible !== false; });
    (estado.horizontales || []).forEach(function(h) { visibleH[h.categoria_id] = h.visible !== false; });
    chartAxis = 'vertical';
    var sw = document.getElementById('switch-invertir-ejes');
    if (sw) sw.checked = false;
    renderCategoryPanel();
    renderTable();
    updateEjeHelper();
    saveStateToURL();
    status('Selección restaurada');
});

var debugToggle = document.querySelector('.debug-toggle');
if (debugToggle) {
    debugToggle.addEventListener('click', function() {
        var el = document.getElementById('chart-debug');
        if (el) {
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
            if (el.style.display === 'block') updateDebug();
        }
    });
}

// Section checkbox handler (multi-select allowed)
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
                var spinners = cb.parentNode ? cb.parentNode.querySelectorAll('.spinner-border') : [];
                spinners.forEach(function(sp) { sp.remove(); });
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
