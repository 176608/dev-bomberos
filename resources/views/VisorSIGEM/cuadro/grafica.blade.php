@extends('VisorSIGEM.layouts.visor')

@section('visor_title', 'Gráfica — ' . ($cuadro->codigo_cuadro ?? ''))

@section('visor_content')
<div class="container-fluid py-3" id="app-grafica">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-2"></i>Gráfica</h5>
            <small class="text-muted">
                <code>{{ $cuadro->codigo_cuadro }}</code>
                <strong>{{ $cuadro->c_titulo }}</strong>
            </small>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ url('/sigem-v2/cuadro/' . $cuadro->cuadro_id . '/dataset') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="btn btn-outline-success btn-sm" id="link-to-dataset"
               data-base="{{ url('/sigem-v2/cuadro/' . $cuadro->cuadro_id . '/dataset') }}">
                <i class="bi bi-table me-1"></i> Volver al Cuadro
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-toggle-config" title="Ver configuración de la gráfica, incluye alternar eje, seleccionar o deseleccionar categorías horizontales y verticales">
                <i class="bi bi-gear me-1"></i>Configuración <i class="bi bi-eye ms-1" id="config-eye-icon"></i>
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-download-png" title="Descargar gráfica como PNG">
                <i class="bi bi-download me-1"></i>PNG
            </button>
        </div>
    </div>

    <div class="mb-2 d-flex align-items-center gap-3 flex-wrap" id="row-tipo-grafica">
        <div>
            <label class="small fw-semibold me-2">Tipo de gráfica:</label>
            <select id="select-tipo-grafica" class="form-select form-select-sm d-inline-block" style="width:auto"></select>
        </div>
        <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="switch-invertir-ejes" title="Invertir: usa las horizontales como etiquetas del eje X">
            <label class="form-check-label small" for="switch-invertir-ejes">Invertir ejes</label>
        </div>
        <div id="eje-helper" class="small text-muted" style="line-height:1.2"></div>
    </div>

    <div id="config-panel" class="border rounded p-2 mb-2" style="display:none">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <small class="fw-semibold"><i class="bi bi-gear me-1"></i>Configuración de gráfica</small>
            <button type="button" class="btn-close btn-sm" id="btn-cerrar-config" aria-label="Cerrar"></button>
        </div>
        <div class="mb-2 d-flex align-items-center gap-3 flex-wrap" id="panel-sections"></div>
        <hr class="my-1">
        <div class="row">
            <div class="col-6" id="panel-horizontal">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="fw-semibold">Categorías Horizontales</small>
                </div>
                <div id="panel-horizontal-items" class="small" style="overflow-y:auto;max-height:300px"></div>
            </div>
            <div class="col-6" id="panel-vertical">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="fw-semibold">Categorías Verticales</small>
                </div>
                <div id="panel-vertical-items" class="small" style="overflow-y:auto;max-height:300px"></div>
            </div>
        </div>
    </div>

    <div id="main-chart-area">
        <canvas id="chart-canvas" style="max-height:500px;width:100%"></canvas>
    </div>

    @if($cuadro->pie_pagina)
    <div class="mt-3 small text-muted pie-pagina">{!! $cuadro->pie_pagina !!}</div>
    @endif

    <div class="card-footer py-1 px-0 d-flex justify-content-between align-items-center mt-2" id="status-bar">
        <small id="status-text"></small>
    </div>

</div>

<style>
.panel-parent { cursor:pointer; user-select:none; }
.panel-parent:hover { background:#f0f2f5; border-radius:2px; }
.panel-child { padding-left:1.2rem; }
.panel-child label { cursor:pointer; }
.panel-child label:hover { color:var(--bs-primary); }
#status-bar .badge { font-size: 0.7rem; }
#status-bar #status-text { font-size: 0.8rem; }
#status-bar.status-flash { background: #d1e7fd !important; transition: background 0.3s; }
.pie-pagina { border-top:1px solid #dee2e6; padding-top:0.5rem; text-align:center; }
</style>
@endsection

@push('visor_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
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
var multiSectionUnsupported = ['pie', 'doughnut', 'polarArea'];
var tiposPermitidos = [];

function populateTipoSelect() {
    var sel = document.getElementById('select-tipo-grafica');
    if (!sel) return;
    var allTypes = [
        { value: 'bar', text: 'Barras' },
        { value: 'line', text: 'Líneas' },
        { value: 'pie', text: 'Pastel' },
        { value: 'doughnut', text: 'Dona' },
        { value: 'radar', text: 'Radar' },
        { value: 'polarArea', text: 'Polar' },
        { value: 'scatter', text: 'Dispersión' },
        { value: 'bubble', text: 'Burbujas' },
    ];
    tiposPermitidos = (estado.tipos_grafica_permitida) || [];
    if (!tiposPermitidos.length) tiposPermitidos = ['bar', 'line', 'pie'];
    var html = '';
    allTypes.forEach(function(t) {
        var perm = tiposPermitidos.indexOf(t.value) >= 0;
        if (!perm) return;
        html += '<option value="' + t.value + '">' + t.text + '</option>';
    });
    sel.innerHTML = html;
}

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
    if (!tipo) return;
    if (!estado.verticales?.length || !estado.horizontales?.length) {
        status('No hay datos para graficar (0 filas o 0 columnas)');
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
    try {
        window.chartInstance = new Chart(ctx, {
            type: tipo === 'scatter' ? 'scatter' : tipo,
            data: chartData,
            options: chartOpts
        });
    } catch(e) {
        window.chartInstance = null;
        console.warn('Chart render error:', e);
    }
}

function renderCategoryPanelAxis(container, leaves, layers, axis) {
    if (!container) return;
    var visMap = axis === 'vertical' ? visibleV : visibleH;
    var allIds = (leaves || []).map(function(l) { return l.categoria_id; });
    var allVisible = allIds.length > 0 && allIds.every(function(id) { return visMap[id] !== false; });
    var anyVisible = allIds.some(function(id) { return visMap[id] !== false; });

    var html = '';
    html += '<div class="mb-1 mt-1">';
    html += '<label style="cursor:pointer;font-weight:600" class="small">';
    html += '<input type="checkbox" class="me-1 todo-check" data-axis="' + axis + '" ' + (allVisible ? 'checked' : '') + '>';
    html += '<i class="bi ' + (allVisible ? 'bi-check2-square' : (anyVisible ? 'bi-dash-square' : 'bi-square')) + ' me-1"></i>'
        + (axis === 'vertical' ? 'Todas las verticales' : 'Todas las horizontales');
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
        var visKids = children.filter(function(ch) { return visMap[ch.categoria_id] !== false; });
        var allVis = visKids.length === children.length;
        var someVis = visKids.length > 0;
        var isChecked = visMap[pid] !== false;
        var checked = isChecked ? 'checked' : '';
        html += '<div class="panel-parent">';
        html += '<label style="cursor:pointer;font-weight:600">';
        html += '<input type="checkbox" class="me-1 cat-check" data-axis="' + axis + '" data-id="' + pid + '" data-parent="" ' + checked + (someVis && !allVis ? ' data-indet="1"' : '') + '>';
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

    container.innerHTML = html;

    container.querySelectorAll('.cat-check[data-indet="1"]').forEach(function(cb) { cb.indeterminate = true; });
    syncTodoCheckboxes(container);
    setupCategoryCheckboxListeners(container);
}

function setupCategoryCheckboxListeners(container) {
    container.querySelectorAll('.cat-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var axis = this.dataset.axis;
            var id = parseInt(this.dataset.id);
            var parent = this.dataset.parent ? parseInt(this.dataset.parent) : null;
            var visMap = axis === 'vertical' ? visibleV : visibleH;
            visMap[id] = this.checked;

            if (parent) {
                updateParentCheckState(container, axis, parent);
            } else {
                container.querySelectorAll('.cat-check[data-axis="' + axis + '"][data-parent="' + id + '"]').forEach(function(ch) {
                    ch.checked = this.checked;
                    visMap[parseInt(ch.dataset.id)] = this.checked;
                }, this);
            }
            syncTodoCheckboxes(container);
            renderChart(document.getElementById('select-tipo-grafica').value);
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
            renderChart(document.getElementById('select-tipo-grafica').value);
            updateEjeHelper();
            saveStateToURL();
        });
    });
}

function updateParentCheckState(container, axis, parentId) {
    if (!container) return;
    var children = container.querySelectorAll('.cat-check[data-axis="' + axis + '"][data-parent="' + parentId + '"]');
    var allChecked = true, anyChecked = false;
    children.forEach(function(ch) {
        if (ch.checked) anyChecked = true;
        else allChecked = false;
    });
    var parentCb = container.querySelector('.cat-check[data-axis="' + axis + '"][data-id="' + parentId + '"]');
    if (parentCb) {
        var visMap = axis === 'vertical' ? visibleV : visibleH;
        if (!anyChecked) {
            parentCb.checked = false;
            parentCb.indeterminate = false;
            visMap[parentId] = false;
        } else if (allChecked) {
            parentCb.checked = true;
            parentCb.indeterminate = false;
            visMap[parentId] = true;
        } else {
            parentCb.checked = false;
            parentCb.indeterminate = true;
            visMap[parentId] = false;
        }
    }
}

function syncTodoCheckboxes(container) {
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

function renderSections() {
    var container = document.getElementById('panel-sections');
    if (!container) return;
    var html = '';
    html += '<label class="small fw-semibold me-2">Secciones:</label>';
    (estado.secciones || []).forEach(function(s) {
        var isChecked = selectedSections[s.seccion_id] !== false;
        var loading = !sectionsCache[s.seccion_id] && isChecked;
        var checked = isChecked ? 'checked' : '';
        html += '<label class="me-2" style="cursor:pointer;white-space:nowrap">';
        html += '<input type="checkbox" class="me-1 sec-check" data-seccion-id="' + s.seccion_id + '" ' + checked + '>';
        if (loading) html += '<span class="spinner-border spinner-border-sm me-1" role="status"></span>';
        html += esc(s.nombre);
        html += '</label>';
    });
    container.innerHTML = html;
}

function renderVerticalPanel() {
    renderCategoryPanelAxis(
        document.getElementById('panel-vertical-items'),
        estado.verticales, estado.labels, 'vertical'
    );
}

function renderHorizontalPanel() {
    renderCategoryPanelAxis(
        document.getElementById('panel-horizontal-items'),
        estado.horizontales, estado.headers, 'horizontal'
    );
}

function enforceSingleSection(tipo) {
    var isSingle = multiSectionUnsupported.indexOf(tipo) >= 0;
    var container = document.getElementById('panel-sections');
    if (!container) return;
    if (isSingle) {
        var activeSids = Object.keys(selectedSections).filter(function(sid) { return selectedSections[sid]; });
        if (activeSids.length > 1) {
            var keep = activeSids[0];
            activeSids.slice(1).forEach(function(sid) { selectedSections[sid] = false; });
            container.querySelectorAll('.sec-check').forEach(function(cb) {
                cb.checked = parseInt(cb.dataset.seccionId) === keep;
            });
        } else if (activeSids.length === 0) {
            var first = container.querySelector('.sec-check');
            if (first) {
                var sid = parseInt(first.dataset.seccionId);
                selectedSections[sid] = true;
                first.checked = true;
            }
        }
    }
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

function parseIdList(str) {
    if (!str) return [];
    return str.split(',').map(function(s) { return parseInt(s, 10); }).filter(function(n) { return !isNaN(n); });
}

function saveStateToURL() {
    var p = new URLSearchParams(window.location.search);
    ['v','h','s','ej','t'].forEach(function(k) { p.delete(k); });

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

    p.set('ej', chartAxis === 'vertical' ? 'v' : 'h');
    var tipoSelect = document.getElementById('select-tipo-grafica');
    if (tipoSelect) p.set('t', tipoSelect.value);
    var qs = p.toString();
    var url = window.location.pathname + (qs ? '?' + qs : '');
    try { window.history.replaceState(null, '', url); } catch(e) {}
    var lnk = document.getElementById('link-to-dataset');
    if (lnk) lnk.href = lnk.getAttribute('data-base') + (qs ? '?' + qs : '');
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

    var ejeCode = p.get('ej');
    var tipoCode = p.get('t');
    if (ejeCode === 'v' || ejeCode === 'h') {
        chartAxis = ejeCode === 'v' ? 'vertical' : 'horizontal';
        var sw = document.getElementById('switch-invertir-ejes');
        if (sw) sw.checked = ejeCode === 'h';
    }
    window._urlTipo = tipoCode;
}

function initGraficaPage() {
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

    function finishGraficaInit() {
        populateTipoSelect();
        if (window._urlTipo) {
            var selTipo = document.getElementById('select-tipo-grafica');
            if (selTipo && selTipo.querySelector('option[value="' + window._urlTipo + '"]')) {
                selTipo.value = window._urlTipo;
            }
        }
        renderSections();
        renderVerticalPanel();
        renderHorizontalPanel();
        enforceSingleSection(document.getElementById('select-tipo-grafica').value);
        renderChart(document.getElementById('select-tipo-grafica').value);
        updateEjeHelper();
        saveStateToURL();
    }

    if (pendingLoads.length) {
        Promise.all(pendingLoads).then(finishGraficaInit);
    } else {
        finishGraficaInit();
    }
}

document.getElementById('select-tipo-grafica')?.addEventListener('change', function() {
    enforceSingleSection(this.value);
    renderChart(this.value);
    saveStateToURL();
});

document.getElementById('switch-invertir-ejes')?.addEventListener('change', function() {
    chartAxis = this.checked ? 'horizontal' : 'vertical';
    renderChart(selectTipoGrafica.value);
    updateEjeHelper();
    saveStateToURL();
});

document.getElementById('btn-toggle-config')?.addEventListener('click', function() {
    var panel = document.getElementById('config-panel');
    var eye = document.getElementById('config-eye-icon');
    if (!panel) return;
    var isVisible = panel.style.display !== 'none';
    panel.style.display = isVisible ? 'none' : 'block';
    if (eye) eye.className = 'bi ' + (isVisible ? 'bi-eye' : 'bi-eye-slash') + ' ms-1';
});

document.getElementById('btn-download-png')?.addEventListener('click', function() {
    var canvas = document.getElementById('chart-canvas');
    if (!canvas) return;
    var link = document.createElement('a');
    link.download = 'grafica_' + CUADRO_ID + '.png';
    link.href = canvas.toDataURL('image/png');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});

document.getElementById('btn-cerrar-config')?.addEventListener('click', function() {
    var panel = document.getElementById('config-panel');
    var eye = document.getElementById('config-eye-icon');
    if (panel) panel.style.display = 'none';
    if (eye) eye.className = 'bi bi-eye ms-1';
});

document.getElementById('panel-sections')?.addEventListener('change', function(e) {
    var cb = e.target;
    if (!cb.classList.contains('sec-check')) return;
    var sid = parseInt(cb.dataset.seccionId);
    if (isNaN(sid)) return;

    var tipo = document.getElementById('select-tipo-grafica').value;
    var isSingle = multiSectionUnsupported.indexOf(tipo) >= 0;

    if (isSingle) {
        if (!cb.checked) {
            cb.checked = true;
            return;
        }
        Object.keys(selectedSections).forEach(function(otherSid) {
            selectedSections[otherSid] = parseInt(otherSid) === sid;
        });
        this.querySelectorAll('.sec-check').forEach(function(otherCb) {
            otherCb.checked = parseInt(otherCb.dataset.seccionId) === sid;
        });
    } else {
        selectedSections[sid] = cb.checked;
    }

    if (cb.checked && !sectionsCache[sid]) {
        cb.disabled = true;
        cb.insertAdjacentHTML('afterend', ' <span class="spinner-border spinner-border-sm" role="status"></span>');
        status('Cargando sección...');
        api('/dataset/seccion/' + sid + '/data')
            .then(function(j) {
                if (j.data) {
                    var sName = ((estado.secciones || []).find(function(s) { return s.seccion_id === sid; }) || {}).nombre || '';
                    sectionsCache[sid] = { nombre: sName, data: j.data || [] };
                    renderChart(selectTipoGrafica.value);
        
                    updateEjeHelper();
                    saveStateToURL();
                    status('Sección: ' + (sName || sid) + ' cargada');
                } else {
                    selectedSections[sid] = false;
                    cb.checked = false;
                    enforceSingleSection(selectTipoGrafica.value);
                    alerta(j.message || 'Error al cargar sección');
                }
            })
            .catch(function() {
                selectedSections[sid] = false;
                cb.checked = false;
                enforceSingleSection(selectTipoGrafica.value);
                alerta('Error de red al cargar sección');
            })
            .finally(function() {
                cb.disabled = false;
                var spinners = cb.parentNode ? cb.parentNode.querySelectorAll('.spinner-border') : [];
                spinners.forEach(function(sp) { sp.remove(); });
            });
    } else {
        renderChart(selectTipoGrafica.value);
        updateEjeHelper();
        saveStateToURL();
    }
});

var selectTipoGrafica = document.getElementById('select-tipo-grafica');
initGraficaPage();
</script>
@endpush
