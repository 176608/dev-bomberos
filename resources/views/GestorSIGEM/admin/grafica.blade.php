<div class="container-fluid py-3" id="app-grafica">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Gráfica</h5>
            <small class="text-muted">
                <code>{{ $cuadro->codigo_cuadro }}</code>
                <strong>{{ $cuadro->c_titulo }}</strong>
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sgiem.admin.cuadros.dataset', $cuadro->cuadro_id) }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-table me-1"></i> Dataset
            </a>
            <a href="{{ route('sgiem.admin.cuadros.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
        <select id="select-tipo-grafica" class="form-select form-select-sm" style="width:auto"></select>
        <button type="button" class="btn btn-sm btn-outline-success" id="btn-toggle-tipo" title="Permitir o no permitir este tipo de gráfica en el dataset">
            <i class="bi bi-gear me-1"></i> <span id="btn-tipo-label">Permitir</span>
        </button>
        <span class="text-muted small mx-1">|</span>
        <button type="button" class="btn btn-sm btn-outline-info" id="btn-toggle-panel" title="Mostrar/ocultar panel">
            <i class="bi bi-list-check"></i> Categorías
        </button>
    </div>

    <div class="d-flex gap-2">
        <div id="chart-panel" class="border rounded p-2" style="width:240px;flex-shrink:0;overflow-y:auto;max-height:500px">
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
            <canvas id="chart-canvas" style="max-height:500px;width:100%"></canvas>
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
#status-bar .badge { font-size: 0.7rem; }
#status-bar #status-text { font-size: 0.8rem; }
#status-bar.status-flash { background: #d1e7fd !important; transition: background 0.3s; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
const CUADRO_ID = {{ $cuadro->cuadro_id }};
const CSRF = '{{ csrf_token() }}';
const BASE = '{{ url("/sgiem/admin/cuadros") }}/' + CUADRO_ID + '/dataset';

let estado = @json($estadoInicial);

// ============ UI HELPERS ============
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

// ============ API ============
function api(path, opts) {
    opts = opts || {};
    opts.headers = opts.headers || {};
    opts.headers['X-CSRF-TOKEN'] = CSRF;
    if (opts.body && typeof opts.body === 'object') {
        opts.body = JSON.stringify(opts.body);
        opts.headers['Content-Type'] = 'application/json';
    }
    return fetch(BASE + path, opts).then(function(r) { return r.json(); }).then(function(j) {
        if (j.data) estado = j.data;
        return j;
    });
}

// ============ CHART STATE ============
var chartAxis = 'vertical';
var visibleV = {};
var visibleH = {};
var tiposPermitidos = [];
var sectionsCache = {};
var selectedSections = {};
var multiSectionUnsupported = ['pie', 'doughnut', 'polarArea'];
var allTypesMap = {};

// ============ CHART.JS HELPERS ============
function generarColor(index, total) {
    if (total === 0) return 'hsl(0,70%,55%)';
    var hue = (index * 360 / total) % 360;
    return 'hsl(' + hue + ',70%,55%)';
}

function generarColorRGBA(index, total, alpha) {
    if (alpha === undefined) alpha = 0.2;
    if (total === 0) return 'hsla(0,70%,55%,' + alpha + ')';
    var hue = (index * 360 / total) % 360;
    return 'hsla(' + hue + ',70%,55%,' + alpha + ')';
}

function parseCellValue(val) {
    if (val === undefined || val === null || val === '') return NaN;
    if (typeof val === 'number') return val;
    var s = String(val).replace(/,/g, '');
    return parseFloat(s);
}

function buildChartData(estado, tipo, opts) {
    opts = opts || {};
    var axis = opts.axis || 'vertical';
    var visibleVIds = opts.visibleV || null;
    var visibleHIds = opts.visibleH || null;
    var sectionDataGrids = opts.sectionDataGrids || null;

    var labelsArr, seriesArr;
    if (axis === 'vertical') {
        labelsArr = estado.verticales || [];
        seriesArr = estado.horizontales || [];
    } else {
        labelsArr = estado.horizontales || [];
        seriesArr = estado.verticales || [];
    }

    var labelFilter, seriesFilter;
    if (axis === 'vertical') {
        labelFilter = visibleVIds ? function(v) { return visibleVIds[v.categoria_id] !== false; } : null;
        seriesFilter = visibleHIds ? function(h) { return visibleHIds[h.categoria_id] !== false; } : null;
    } else {
        labelFilter = visibleHIds ? function(h) { return visibleHIds[h.categoria_id] !== false; } : null;
        seriesFilter = visibleVIds ? function(v) { return visibleVIds[v.categoria_id] !== false; } : null;
    }
    if (labelFilter) labelsArr = labelsArr.filter(labelFilter);
    if (seriesFilter) seriesArr = seriesArr.filter(seriesFilter);

    var parentNameForLabel = {};
    var labelLayers = axis === 'vertical' ? (estado.labels || []) : (estado.headers || []);
    (labelsArr || []).forEach(function(item) {
        if (item.padre_id) {
            labelLayers.forEach(function(row) {
                (row || []).forEach(function(cell) {
                    if (cell.tipo === 'parent' && cell.categoria_id === item.padre_id)
                        parentNameForLabel[item.categoria_id] = cell.nombre;
                });
            });
        }
    });
    var parentNameForSeries = {};
    var seriesLayers = axis === 'vertical' ? (estado.headers || []) : (estado.labels || []);
    (seriesArr || []).forEach(function(item) {
        if (item.padre_id) {
            seriesLayers.forEach(function(row) {
                (row || []).forEach(function(cell) {
                    if (cell.tipo === 'parent' && cell.categoria_id === item.padre_id)
                        parentNameForSeries[item.categoria_id] = cell.nombre;
                });
            });
        }
    });
    var labels = labelsArr.map(function(v) {
        var pName = parentNameForLabel[v.categoria_id];
        return pName ? pName + ': ' + v.nombre : v.nombre;
    });
    var seriesLen = seriesArr.length;

    var dataSources = [];
    if (sectionDataGrids && sectionDataGrids.length) {
        dataSources = sectionDataGrids;
    } else {
        dataSources = [{ nombre: '', data: estado.data || [] }];
    }

    var totalGlobal = dataSources.length * seriesLen;
    var globalIdx = 0;
    var datasets = [];

    dataSources.forEach(function(ds) {
        var dataGrid = ds.data;
        var prefix = ds.nombre ? ds.nombre + ' - ' : '';

        seriesArr.forEach(function(s, si) {
            var vals;
            if (axis === 'vertical') {
                vals = labelsArr.map(function(l) {
                    var rowIndex = (estado.verticales || []).findIndex(function(v) { return v.categoria_id === l.categoria_id; });
                    var colIndex = (estado.horizontales || []).findIndex(function(h) { return h.categoria_id === s.categoria_id; });
                    if (rowIndex < 0 || colIndex < 0) return 0;
                    var cel = dataGrid[rowIndex] ? dataGrid[rowIndex][colIndex] : null;
                    return (cel && cel.valor !== undefined && cel.valor !== '') ? (parseCellValue(cel.valor) || 0) : 0;
                });
            } else {
                var serieVertIdx = (estado.verticales || []).findIndex(function(v) { return v.categoria_id === s.categoria_id; });
                vals = labelsArr.map(function(l) {
                    var horizIdx = (estado.horizontales || []).findIndex(function(h) { return h.categoria_id === l.categoria_id; });
                    if (serieVertIdx < 0 || horizIdx < 0) return 0;
                    var cel = dataGrid[serieVertIdx] ? dataGrid[serieVertIdx][horizIdx] : null;
                    return (cel && cel.valor !== undefined && cel.valor !== '') ? (parseCellValue(cel.valor) || 0) : 0;
                });
            }

            var colorIdx = globalIdx;
            var color = generarColor(colorIdx, Math.max(totalGlobal, 1));
            var bgColor = tipo === 'pie' || tipo === 'doughnut' || tipo === 'polarArea' || tipo === 'radar'
                ? labels.map(function(_, li) { return generarColor(li, Math.max(labels.length, 1)); })
                : generarColorRGBA(colorIdx, Math.max(totalGlobal, 1));

            var label = prefix + s.nombre;
            var pName = parentNameForSeries[s.categoria_id];
            if (pName) label = prefix + pName + ': ' + s.nombre;

            datasets.push({
                label: label,
                data: vals,
                backgroundColor: bgColor,
                borderColor: color,
                borderWidth: 1,
            });
            globalIdx++;
        });
    });

    return { labels: labels, datasets: datasets };
}

function finalizeChartData(chartData, tipo) {
    if (tipo === 'scatter' || tipo === 'bubble') {
        var allVals = [];
        chartData.datasets.forEach(function(ds) { ds.data.forEach(function(v) { allVals.push(Math.abs(v)); }); });
        var maxVal = Math.max.apply(null, allVals) || 1;
        chartData.datasets.forEach(function(ds) {
            ds.data = ds.data.map(function(v, i) {
                if (tipo === 'bubble') {
                    var r = Math.max(5, (Math.abs(v) / maxVal) * 30);
                    return { x: i, y: v, r: r };
                }
                return { x: i, y: v };
            });
            ds.backgroundColor = ds.borderColor;
            ds.pointRadius = tipo === 'scatter' ? 4 : undefined;
            ds.pointHoverRadius = tipo === 'scatter' ? 7 : undefined;
        });
    }
}

function renderChart(tipo) {
    if (window.chartInstance) { window.chartInstance.destroy(); window.chartInstance = null; }
    if (!estado.verticales?.length || !estado.horizontales?.length) {
        var dbg = document.getElementById('chart-debug');
        if (dbg) { dbg.style.display = 'block'; dbg.textContent = 'No hay datos para graficar (0 filas o 0 columnas)'; }
        return;
    }

    var opts = { axis: chartAxis, visibleV: visibleV, visibleH: visibleH };

    var activeSids = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });
    var isMultiUnsupported = multiSectionUnsupported.indexOf(tipo) >= 0;
    var useMulti = activeSids.length > 0 && !isMultiUnsupported;

    if (activeSids.length > 1 && isMultiUnsupported) {
        status('El tipo ' + tipo + ' no soporta múltiples secciones — mostrando solo sección activa');
        useMulti = false;
    }

    if (useMulti) {
        var grids = [];
        activeSids.forEach(function(sid) {
            if (sectionsCache[sid]) grids.push(sectionsCache[sid]);
        });
        if (grids.length > 0) opts.sectionDataGrids = grids;
    } else if (activeSids.length === 1 && sectionsCache[activeSids[0]]) {
        opts.sectionDataGrids = [sectionsCache[activeSids[0]]];
    }

    var chartData = buildChartData(estado, tipo, opts);
    finalizeChartData(chartData, tipo);
    var ctx = document.getElementById('chart-canvas').getContext('2d');
    var chartOpts = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            title: { display: true, text: estado.pivot_label || 'Dataset' }
        }
    };
    if (tipo === 'scatter' || tipo === 'bubble') {
        chartOpts.scales = {
            x: { type: 'linear', ticks: { stepSize: 1, callback: function(val) { return chartData.labels[val] || val; } } },
            y: { beginAtZero: true }
        };
    }
    window.chartInstance = new Chart(ctx, {
        type: tipo === 'scatter' ? 'scatter' : tipo,
        data: chartData,
        options: chartOpts
    });
}

function updateChartDebug() {
    var el = document.getElementById('chart-debug');
    if (!el) return;
    el.style.display = 'block';
    var parentsV = [], parentsH = [];
    try {
        (estado.labels || []).forEach(function(row) { (row || []).forEach(function(cell) { if (cell.tipo === 'parent') parentsV.push(cell.nombre); }); });
        (estado.headers || []).forEach(function(row) { (row || []).forEach(function(cell) { if (cell.tipo === 'parent') parentsH.push(cell.nombre); }); });
    } catch(e) {}
    var labels, series;
    if (chartAxis === 'vertical') {
        labels = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; }).map(function(v) { return v.nombre; });
        series = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; }).map(function(h) { return h.nombre; });
    } else {
        labels = (estado.horizontales || []).filter(function(h) { return visibleH[h.categoria_id] !== false; }).map(function(h) { return h.nombre; });
        series = (estado.verticales || []).filter(function(v) { return visibleV[v.categoria_id] !== false; }).map(function(v) { return v.nombre; });
    }
    var chartDataStr = 'null';
    try {
        var cd = buildChartData(estado, document.getElementById('select-tipo-grafica').value, { axis: chartAxis, visibleV: visibleV, visibleH: visibleH });
        chartDataStr = JSON.stringify(cd, null, 2);
    } catch(e) { chartDataStr = 'Error: ' + e.message; }
    var selectedNames = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; }).map(function(sid) {
        return ((estado.secciones || []).find(function(s) { return s.seccion_id == sid; }) || {}).nombre || sid;
    });
    el.textContent = '── Debug Chart Data ──\n'
        + 'Eje X: ' + chartAxis + '\n'
        + 'Labels: ' + JSON.stringify(labels) + '\n'
        + 'Cantidad labels: ' + labels.length + '\n'
        + 'Series: ' + JSON.stringify(series) + '\n'
        + 'Cantidad series: ' + series.length + '\n'
        + (parentsV.length ? 'Padres verticales: ' + JSON.stringify(parentsV) + '\n' : '')
        + (parentsH.length ? 'Padres horizontales: ' + JSON.stringify(parentsH) + '\n' : '')
        + 'Secciones seleccionadas: ' + (selectedNames.length ? selectedNames.join(', ') : '(ninguna)') + '\n'
        + 'Secciones cacheadas: ' + Object.keys(sectionsCache).join(', ') + '\n'
        + 'chartData = ' + chartDataStr;
}

function renderCategoryPanel() {
    var container = document.getElementById('panel-items');
    if (!container) return;
    var html = '';

    // Section checkboxes
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

    // Sync section checkboxes state
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
            renderChart(document.getElementById('select-tipo-grafica').value);
            updateChartDebug();
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
                // Update parent checkboxes for children
                var pid = ch.dataset.parent;
                if (pid) {
                    var parentCb = container.querySelector('.cat-check[data-axis="' + axis + '"][data-id="' + pid + '"]');
                    if (parentCb) parentCb.checked = on;
                }
            });
            // Update parent visibility state for parent nodes
            container.querySelectorAll('.cat-check[data-axis="' + axis + '"][data-parent=""]').forEach(function(pcb) {
                var visMap2 = axis === 'vertical' ? visibleV : visibleH;
                visMap2[parseInt(pcb.dataset.id)] = on;
            });
            renderChart(document.getElementById('select-tipo-grafica').value);
            updateChartDebug();
            updateEjeHelper();
            saveStateToURL();
        });
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

function syncTodoCheckboxes() {
    var container = document.getElementById('panel-items');
    if (!container) return;
    container.querySelectorAll('.todo-check').forEach(function(cb) {
        var axis = cb.dataset.axis;
        var visMap = axis === 'vertical' ? visibleV : visibleH;
        var ids = Object.keys(visMap);
        if (!ids.length) { cb.checked = false; cb.indeterminate = false; return; }
        var allVis = ids.every(function(id) { return visMap[id] !== false; });
        var anyVis = ids.some(function(id) { return visMap[id] !== false; });
        cb.checked = allVis;
        cb.indeterminate = anyVis && !allVis;
        var icon = cb.nextElementSibling;
        if (icon) {
            icon.className = 'bi me-1 ' + (allVis ? 'bi-check2-square' : (anyVis ? 'bi-dash-square' : 'bi-square'));
        }
    });
}

function initVisibleState() {
    visibleV = {};
    visibleH = {};
    (estado.verticales || []).forEach(function(v) { visibleV[v.categoria_id] = true; });
    (estado.horizontales || []).forEach(function(h) { visibleH[h.categoria_id] = true; });
}

function populateTipoSelect() {
    var select = document.getElementById('select-tipo-grafica');
    if (!select) return;
    var allTypes = [
        { value: 'bar', text: 'Barras' },
        { value: 'line', text: 'Líneas' },
        { value: 'pie', text: 'Circular' },
        { value: 'doughnut', text: 'Dona' },
        { value: 'radar', text: 'Radar' },
        { value: 'polarArea', text: 'Polar' },
        { value: 'scatter', text: 'Dispersión' },
        { value: 'bubble', text: 'Burbujas' },
    ];
    allTypesMap = {};
    allTypes.forEach(function(t) { allTypesMap[t.value] = t.text; });
    tiposPermitidos = (estado.tipos_grafica_permitida) || [];
    if (!tiposPermitidos.length) tiposPermitidos = ['bar', 'line', 'pie'];
    select.innerHTML = '';
    allTypes.forEach(function(t) {
        var opt = document.createElement('option');
        opt.value = t.value;
        opt.textContent = t.text + (tiposPermitidos.indexOf(t.value) >= 0 ? '' : ' (no permitido)');
        select.appendChild(opt);
    });
    if (select.options.length) select.selectedIndex = 0;
    updateTipoLabel();
}

function updateTipoLabel() {
    var tipo = document.getElementById('select-tipo-grafica').value;
    var label = document.getElementById('btn-tipo-label');
    var btn = document.getElementById('btn-toggle-tipo');
    if (!tipo || !label || !btn) return;
    var isAssigned = tiposPermitidos.indexOf(tipo) >= 0;
    label.textContent = isAssigned ? 'No permitir' : 'Permitir';
    btn.classList.toggle('btn-outline-success', !isAssigned);
    btn.classList.toggle('btn-outline-danger', isAssigned);
    // Update the option's display text
    var opt = document.querySelector('#select-tipo-grafica option[value="' + tipo + '"]');
    if (opt) {
        var baseName = allTypesMap[tipo] || tipo;
        opt.textContent = isAssigned ? baseName : baseName + ' (no permitido)';
    }
}

function saveTiposPermitidos() {
    api('/tipos-grafica', { method: 'PUT', body: { tipos: tiposPermitidos } });
}

function updateEjeHelper() {
    var el = document.getElementById('eje-helper');
    if (!el) return;
    var labels = chartAxis === 'vertical' ? (estado.verticales || []) : (estado.horizontales || []);
    var series = chartAxis === 'vertical' ? (estado.horizontales || []) : (estado.verticales || []);
    var visibleLabels = labels.filter(function(l) {
        var m = chartAxis === 'vertical' ? visibleV : visibleH;
        return m[l.categoria_id] !== false;
    });
    var visibleSeries = series.filter(function(s) {
        var m = chartAxis === 'vertical' ? visibleH : visibleV;
        return m[s.categoria_id] !== false;
    });
    var labelAxisName = chartAxis === 'vertical' ? 'Verticales' : 'Horizontales';
    var seriesAxisName = chartAxis === 'vertical' ? 'Horizontales' : 'Verticales';
    var labelNames = visibleLabels.map(function(l) { return l.nombre; });
    var seriesNames = visibleSeries.map(function(s) { return s.nombre; });
    el.innerHTML = '<span title="' + esc(labelNames.join(', ') || '') + '">' + labelAxisName + ' en eje X (' + labelNames.length + ')</span>'
        + ' — <span title="' + esc(seriesNames.join(', ') || '') + '">' + seriesAxisName + ' como series (' + seriesNames.length + ')</span>';
}

function saveStateToURL() {
    var visibleVIds = Object.keys(visibleV).filter(function(id) { return visibleV[id] !== false; });
    var visibleHIds = Object.keys(visibleH).filter(function(id) { return visibleH[id] !== false; });
    var selectedSids = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });
    var ejeCode = chartAxis === 'vertical' ? 'v' : 'h';
    var params = [];
    if (visibleVIds.length) params.push('v=' + visibleVIds.join(','));
    if (visibleHIds.length) params.push('h=' + visibleHIds.join(','));
    if (selectedSids.length) params.push('s=' + selectedSids.join(','));
    params.push('ej=' + ejeCode);
    var tipoSelect = document.getElementById('select-tipo-grafica');
    if (tipoSelect) params.push('t=' + tipoSelect.value);
    var qs = params.join('&');
    var url = window.location.pathname + (qs ? '?' + qs : '');
    try { window.history.replaceState(null, '', url); } catch(e) {}
}

function sanitizeIdList(str) {
    if (!str) return [];
    return str.split(',').map(function(s) { return parseInt(s, 10); }).filter(function(n) { return !isNaN(n) && n > 0; });
}

function loadStateFromURL() {
    var p = new URLSearchParams(window.location.search);
    var vList = sanitizeIdList(p.get('v'));
    var hList = sanitizeIdList(p.get('h'));
    var sList = sanitizeIdList(p.get('s'));
    var ejeCode = p.get('ej');
    var tipoCode = p.get('t');

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
    if (tipoCode) {
        var selTipo = document.getElementById('select-tipo-grafica');
        if (selTipo && selTipo.querySelector('option[value="' + tipoCode + '"]')) {
            selTipo.value = tipoCode;
        }
    }
}

function initGraficaPage() {
    initVisibleState();
    populateTipoSelect();
    var switchEje = document.getElementById('switch-invertir-ejes');
    chartAxis = switchEje && switchEje.checked ? 'horizontal' : 'vertical';

    // Init multi-section state: cache current section's data grid
    var activeId = estado.seccion_activa_id;
    (estado.secciones || []).forEach(function(s) {
        if (s.seccion_id === activeId) {
            sectionsCache[s.seccion_id] = { nombre: s.nombre, data: estado.data || [] };
            selectedSections[s.seccion_id] = true;
        } else {
            selectedSections[s.seccion_id] = false;
        }
    });

    loadStateFromURL();

    // Pre-load uncached selected sections from URL params
    var pendingLoads = [];
    Object.keys(selectedSections).forEach(function(sid) {
        if (selectedSections[sid] && !sectionsCache[sid]) {
            (function(sidNum) {
                status('Cargando sección ' + sidNum + '...');
                pendingLoads.push(
                    api('/seccion/' + sidNum + '/data', { method: 'GET' })
                        .then(function(j) {
                            if (j.data) {
                                var sName = ((estado.secciones || []).find(function(s) { return s.seccion_id === sidNum; }) || {}).nombre || '';
                                sectionsCache[sidNum] = { nombre: sName, data: j.data.data || [] };
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

    function finishGraficaInit() {
        renderCategoryPanel();
        renderChart(document.getElementById('select-tipo-grafica').value);
        updateEjeHelper();
        updateChartDebug();
        saveStateToURL();
        document.getElementById('chart-panel').style.display = 'block';
    }

    if (pendingLoads.length) {
        Promise.all(pendingLoads).then(finishGraficaInit);
    } else {
        finishGraficaInit();
    }
}

// ============ EVENT LISTENERS ============
var selectTipoGrafica = document.getElementById('select-tipo-grafica');
if (selectTipoGrafica) {
    selectTipoGrafica.addEventListener('change', function() {
        updateTipoLabel();
        renderChart(this.value);
        updateChartDebug();
    });
}

document.getElementById('btn-toggle-tipo')?.addEventListener('click', function() {
    var tipo = selectTipoGrafica.value;
    if (!tipo) return;
    var idx = tiposPermitidos.indexOf(tipo);
    if (idx >= 0) {
        tiposPermitidos.splice(idx, 1);
        if (!tiposPermitidos.length) tiposPermitidos = ['bar', 'line', 'pie'];
        populateTipoSelect();
        status('Tipo "' + tipo + '" — no permitido');
    } else {
        tiposPermitidos.push(tipo);
        status('Tipo "' + tipo + '" — permitido');
    }
    saveTiposPermitidos();
    updateTipoLabel();
    renderChart(selectTipoGrafica.value);
    updateChartDebug();
});

document.getElementById('btn-toggle-panel')?.addEventListener('click', function() {
    var panel = document.getElementById('chart-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
});

document.getElementById('btn-cerrar-panel')?.addEventListener('click', function() {
    document.getElementById('chart-panel').style.display = 'none';
});

document.getElementById('switch-invertir-ejes')?.addEventListener('change', function() {
    chartAxis = this.checked ? 'horizontal' : 'vertical';
    renderChart(selectTipoGrafica.value);
    updateChartDebug();
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
        api('/seccion/' + sid + '/data', { method: 'GET' })
            .then(function(j) {
                if (j.data) {
                    var sName = ((estado.secciones || []).find(function(s) { return s.seccion_id === sid; }) || {}).nombre || '';
                    sectionsCache[sid] = { nombre: sName, data: j.data.data || [] };
                    renderChart(selectTipoGrafica.value);
                    updateChartDebug();
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
        renderChart(selectTipoGrafica.value);
        updateChartDebug();
        updateEjeHelper();
        saveStateToURL();
    }
});

// ============ INIT ============
initGraficaPage();
</script>
