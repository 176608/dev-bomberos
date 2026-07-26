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
            <div id="chart-debug" class="mt-2 small" style="display:none;background:#1e1e1e;color:#d4d4d4;font-family:Consolas,monospace;padding:0.6rem;border-radius:6px;white-space:pre-wrap;overflow-x:auto;max-height:250px;overflow-y:auto"></div>
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
</style>
@endsection

@push('visor_scripts')
<script>
const CUADRO_ID = {{ $cuadro->cuadro_id }};
const BASE = '{{ url("/sigem-v2/cuadro") }}/' + CUADRO_ID;

let estado = @json($estadoInicial);

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

var chartAxis = 'vertical';
var visibleV = {};
var visibleH = {};
var sectionsCache = {};
var selectedSections = {};

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

function renderCategoryPanel() {
    var container = document.getElementById('panel-items');
    if (!container) return;
    var html = '';

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

function enforceSingleSection(tipo) {
}

function renderTable() {
    var tableContainer = document.getElementById('table-container');
    if (!tableContainer) return;

    var rows, cols;
    if (chartAxis === 'vertical') {
        rows = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; });
        cols = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; });
    } else {
        rows = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; });
        cols = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; });
    }

    if (!rows.length || !cols.length) {
        tableContainer.innerHTML = '<div class="text-muted small p-3">Sin datos que mostrar — todas las categorías ocultas.</div>';
        return;
    }

    var html = '<table class="table table-bordered table-sm mb-0">';
    html += '<thead class="table-light"><tr><th></th>';
    cols.forEach(function(col) {
        html += '<th class="fw-semibold text-center">' + esc(col.nombre) + '</th>';
    });
    html += '</tr></thead><tbody>';

    rows.forEach(function(row) {
        html += '<tr><th class="fw-semibold text-nowrap">' + esc(row.nombre) + '</th>';
        cols.forEach(function(col) {
            var rowIdx, colIdx;
            if (chartAxis === 'vertical') {
                rowIdx = (estado.verticales || []).findIndex(function(v) { return v.categoria_id === row.categoria_id; });
                colIdx = (estado.horizontales || []).findIndex(function(h) { return h.categoria_id === col.categoria_id; });
            } else {
                rowIdx = (estado.horizontales || []).findIndex(function(h) { return h.categoria_id === row.categoria_id; });
                colIdx = (estado.verticales || []).findIndex(function(v) { return v.categoria_id === col.categoria_id; });
            }
            var val = '';
            if (rowIdx >= 0 && colIdx >= 0) {
                var cell = estado.data[rowIdx] ? estado.data[rowIdx][colIdx] : null;
                val = (cell && cell.valor !== undefined && cell.valor !== '') ? cell.valor : '';
            }
            html += '<td class="text-center">' + esc(String(val)) + '</td>';
        });
        html += '</tr>';
    });
    html += '</tbody></table>';
    tableContainer.innerHTML = html;
}

function parseCellValue(val) {
    if (val === undefined || val === null || val === '') return NaN;
    if (typeof val === 'number') return val;
    var s = String(val).replace(/,/g, '');
    return parseFloat(s);
}

function updateEjeHelper() {
    var el = document.getElementById('eje-helper');
    if (!el) return;
    var vCount = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; }).length;
    var hCount = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; }).length;
    if (chartAxis === 'vertical') {
        el.textContent = 'Verticales en eje X (' + vCount + ') — Horizontales como series (' + hCount + ')';
    } else {
        el.textContent = 'Horizontales en eje X (' + hCount + ') — Verticales como series (' + vCount + ')';
    }
}

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
    var ejeCode = chartAxis === 'vertical' ? 'v' : 'h';
    params.push('ej=' + ejeCode);
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

function initDatasetPage() {
    (estado.verticales || []).forEach(function(v) { visibleV[v.categoria_id] = v.visible !== false; });
    (estado.horizontales || []).forEach(function(h) { visibleH[h.categoria_id] = h.visible !== false; });
    (estado.secciones || []).forEach(function(s) { selectedSections[s.seccion_id] = s.visible !== false; });
    if (!Object.keys(selectedSections).length && estado.secciones && estado.secciones.length) {
        selectedSections[estado.secciones[0].seccion_id] = true;
    }

    loadStateFromURL();

    var pendingLoads = [];
    Object.keys(selectedSections).forEach(function(sid) {
        if (selectedSections[sid] && !sectionsCache[sid]) {
            (function(sidNum) {
                pendingLoads.push(
                    api('/dataset/seccion/' + sidNum + '/data')
                        .then(function(j) {
                            if (j.data) {
                                var sName = ((estado.secciones || []).find(function(s) { return s.seccion_id === sidNum; }) || {}).nombre || '';
                                sectionsCache[sidNum] = { nombre: sName, data: j.data || [] };
                            } else {
                                selectedSections[sidNum] = false;
                            }
                        })
                        .catch(function() {
                            selectedSections[sidNum] = false;
                        })
                );
            })(parseInt(sid));
        }
    });

    function finishInit() {
        var activeSid = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; })[0];
        if (activeSid && sectionsCache[activeSid]) {
            estado.data = sectionsCache[activeSid].data;
        }
        renderCategoryPanel();
        renderTable();
        updateEjeHelper();
        saveStateToURL();
        document.getElementById('chart-panel').style.display = 'block';
    }

    if (pendingLoads.length) {
        Promise.all(pendingLoads).then(finishInit);
    } else {
        finishInit();
    }
}

document.getElementById('switch-invertir-ejes')?.addEventListener('change', function() {
    chartAxis = this.checked ? 'horizontal' : 'vertical';
    renderTable();
    updateEjeHelper();
    saveStateToURL();
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
        api('/dataset/seccion/' + sid + '/data')
            .then(function(j) {
                if (j.data) {
                    var sName = ((estado.secciones || []).find(function(s) { return s.seccion_id === sid; }) || {}).nombre || '';
                    sectionsCache[sid] = { nombre: sName, data: j.data || [] };
                    estado.data = j.data;
                    renderTable();
                    updateEjeHelper();
                    saveStateToURL();
                    status('Sección: ' + (sName || sid) + ' cargada');
                } else {
                    selectedSections[sid] = false;
                    cb.checked = false;
                    alerta(j.message || 'Error al cargar sección');
                }
            })
            .catch(function() {
                selectedSections[sid] = false;
                cb.checked = false;
                alerta('Error de red al cargar sección');
            })
            .finally(function() {
                cb.disabled = false;
                var spinners = cb.parentNode ? cb.parentNode.querySelectorAll('.spinner-border') : [];
                spinners.forEach(function(sp) { sp.remove(); });
            });
    } else {
        if (cb.checked && sectionsCache[sid]) {
            estado.data = sectionsCache[sid].data;
        }
        renderTable();
        updateEjeHelper();
        saveStateToURL();
    }
});

document.getElementById('btn-cerrar-panel')?.addEventListener('click', function() {
    document.getElementById('chart-panel').style.display = 'none';
});

initDatasetPage();
</script>
@endpush
