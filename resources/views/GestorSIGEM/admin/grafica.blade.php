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
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-config-grafica" title="Configuración de tipos de gráfica">
            <i class="bi bi-gear me-1"></i>Tipos
        </button>
        <select id="select-tipo-grafica" class="form-select form-select-sm" style="width:auto"></select>
        <button type="button" class="btn btn-sm btn-outline-success d-none" id="btn-asignar-tipo" title="Asignar este tipo">
            <i class="bi bi-check-lg"></i> Asignar
        </button>
        <button type="button" class="btn btn-sm btn-outline-danger d-none" id="btn-eliminar-tipo" title="Eliminar este tipo">
            <i class="bi bi-x-lg"></i> Eliminar
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
                <label class="small text-muted mb-1">Eje X (labels)</label>
                <select id="select-eje" class="form-select form-select-sm">
                    <option value="vertical">Verticales (filas)</option>
                    <option value="horizontal">Horizontales (columnas)</option>
                </select>
            </div>
            <div class="mb-2">
                <label class="small text-muted mb-1">Agrupar por</label>
                <select id="select-agrupar" class="form-select form-select-sm">
                    <option value="none">Ninguno</option>
                    <option value="parent_horizontal">Padre horizontal</option>
                    <option value="parent_vertical">Padre vertical</option>
                </select>
            </div>
            <div class="mb-2">
                <label class="small text-muted mb-1">Sección</label>
                <select id="select-seccion" class="form-select form-select-sm"></select>
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
var chartGroup = 'none';
var visibleV = {};
var visibleH = {};
var tiposPermitidos = [];

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

function buildParentMap(items) {
    var map = {};
    (items || []).forEach(function(item) {
        if (item.padre_id) {
            if (!map[item.padre_id]) map[item.padre_id] = [];
            map[item.padre_id].push(item);
        }
    });
    return map;
}

function buildChartData(estado, tipo, opts) {
    opts = opts || {};
    var axis = opts.axis || 'vertical';
    var groupBy = opts.groupBy || 'none';
    var visibleVIds = opts.visibleV || null;
    var visibleHIds = opts.visibleH || null;

    var labelsArr, seriesArr, dataGrid;
    if (axis === 'vertical') {
        labelsArr = estado.verticales || [];
        seriesArr = estado.horizontales || [];
        dataGrid = estado.data || [];
    } else {
        labelsArr = estado.horizontales || [];
        seriesArr = estado.verticales || [];
        dataGrid = estado.data || [];
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

    var labels = labelsArr.map(function(v) { return v.nombre; });
    var totalSeries = seriesArr.length;

    var parentMap;
    if (groupBy === 'parent_horizontal') {
        parentMap = buildParentMap(axis === 'vertical' ? (estado.horizontales || []) : (estado.verticales || []));
    } else if (groupBy === 'parent_vertical') {
        parentMap = buildParentMap(axis === 'vertical' ? (estado.verticales || []) : (estado.horizontales || []));
    }

    var leafToParent = {};
    if (groupBy !== 'none') {
        (estado.verticales || []).concat(estado.horizontales || []).forEach(function(item) {
            if (item.padre_id) leafToParent[item.categoria_id] = item.padre_id;
        });
    }

    var datasets = seriesArr.map(function(s, si) {
        var vals;
        if (axis === 'vertical') {
            vals = labelsArr.map(function(l) {
                var rowIndex = (estado.verticales || []).findIndex(function(v) { return v.categoria_id === l.categoria_id; });
                var colIndex = (estado.horizontales || []).findIndex(function(h) { return h.categoria_id === s.categoria_id; });
                if (rowIndex < 0 || colIndex < 0) return 0;
                var cel = dataGrid[rowIndex] ? dataGrid[rowIndex][colIndex] : null;
                return (cel && cel.valor !== undefined && cel.valor !== '') ? (parseFloat(cel.valor) || 0) : 0;
            });
        } else {
            var serieVertIdx = (estado.verticales || []).findIndex(function(v) { return v.categoria_id === s.categoria_id; });
            vals = labelsArr.map(function(l) {
                var horizIdx = (estado.horizontales || []).findIndex(function(h) { return h.categoria_id === l.categoria_id; });
                if (serieVertIdx < 0 || horizIdx < 0) return 0;
                var cel = dataGrid[serieVertIdx] ? dataGrid[serieVertIdx][horizIdx] : null;
                return (cel && cel.valor !== undefined && cel.valor !== '') ? (parseFloat(cel.valor) || 0) : 0;
            });
        }

        var colorIdx = si;
        var parentId = leafToParent[s.categoria_id];
        if (groupBy !== 'none' && parentId && parentMap && parentMap[parentId]) {
            var siblings = parentMap[parentId] || [];
            var siblingIndex = siblings.findIndex(function(ch) { return ch.categoria_id === s.categoria_id; });
            if (siblingIndex >= 0) {
                var colorBase = Object.keys(leafToParent).indexOf(String(parentId));
                if (colorBase < 0) colorBase = 0;
                colorIdx = colorBase + siblingIndex * 7;
            }
        }

        var color = generarColor(colorIdx, Math.max(totalSeries, 1));
        var bgColor = tipo === 'pie' || tipo === 'doughnut' || tipo === 'polarArea' || tipo === 'radar'
            ? labels.map(function(_, li) { return generarColor(li, Math.max(labels.length, 1)); })
            : generarColorRGBA(colorIdx, Math.max(totalSeries, 1));

        var label = s.nombre;
        if (groupBy !== 'none' && parentId) {
            var parentName = '';
            (estado.headers || []).concat(estado.labels || []).forEach(function(row) {
                (row || []).forEach(function(cell) {
                    if (cell.tipo === 'parent' && cell.categoria_id === parentId) parentName = cell.nombre;
                });
            });
            if (parentName) label = parentName + ' - ' + label;
        }

        return {
            label: label,
            data: vals,
            backgroundColor: bgColor,
            borderColor: color,
            borderWidth: 1,
        };
    });

    return { labels: labels, datasets: datasets };
}

function renderChart(tipo) {
    if (window.chartInstance) { window.chartInstance.destroy(); window.chartInstance = null; }
    if (!estado.verticales?.length || !estado.horizontales?.length) {
        var dbg = document.getElementById('chart-debug');
        if (dbg) { dbg.style.display = 'block'; dbg.textContent = 'No hay datos para graficar (0 filas o 0 columnas)'; }
        return;
    }
    var opts = { axis: chartAxis, groupBy: chartGroup, visibleV: visibleV, visibleH: visibleH };
    var chartData = buildChartData(estado, tipo, opts);
    var ctx = document.getElementById('chart-canvas').getContext('2d');
    window.chartInstance = new Chart(ctx, {
        type: tipo,
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                title: { display: true, text: estado.pivot_label || 'Dataset' }
            }
        }
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
        var cd = buildChartData(estado, document.getElementById('select-tipo-grafica').value, { axis: chartAxis, groupBy: chartGroup, visibleV: visibleV, visibleH: visibleH });
        chartDataStr = JSON.stringify(cd, null, 2);
    } catch(e) { chartDataStr = 'Error: ' + e.message; }
    el.textContent = '── Debug Chart Data ──\n'
        + 'Eje X: ' + chartAxis + '\n'
        + 'Agrupar por: ' + chartGroup + '\n'
        + 'Labels: ' + JSON.stringify(labels) + '\n'
        + 'Cantidad labels: ' + labels.length + '\n'
        + 'Series: ' + JSON.stringify(series) + '\n'
        + 'Cantidad series: ' + series.length + '\n'
        + (parentsV.length ? 'Padres verticales: ' + JSON.stringify(parentsV) + '\n' : '')
        + (parentsH.length ? 'Padres horizontales: ' + JSON.stringify(parentsH) + '\n' : '')
        + 'Sección activa: "' + ((estado.secciones || []).find(function(s) { return s.seccion_id === estado.seccion_activa_id; })?.nombre || '') + '" (id: ' + (estado.seccion_activa_id || '-') + ')\n'
        + 'chartData = ' + chartDataStr;
}

function renderCategoryPanel() {
    var container = document.getElementById('panel-items');
    if (!container) return;
    var html = '';

    function buildAxisTree(leaves, layers, axis) {
        var visMap = axis === 'vertical' ? visibleV : visibleH;
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

    html += '<div class="mb-1 mt-1"><strong class="small">Verticales</strong></div>';
    buildAxisTree(estado.verticales, estado.labels, 'vertical');
    html += '<hr class="my-1"><div class="mb-1 mt-1"><strong class="small">Horizontales</strong></div>';
    buildAxisTree(estado.horizontales, estado.headers, 'horizontal');
    container.innerHTML = html;

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
            renderChart(document.getElementById('select-tipo-grafica').value);
            updateChartDebug();
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
    ];
    tiposPermitidos = (estado.tipos_grafica_permitida) || [];
    if (!tiposPermitidos.length) tiposPermitidos = ['bar', 'line', 'pie'];
    select.innerHTML = '';
    var available = allTypes.filter(function(t) { return tiposPermitidos.indexOf(t.value) >= 0; });
    if (!available.length) available = allTypes.slice(0, 3);
    available.forEach(function(t) {
        var opt = document.createElement('option');
        opt.value = t.value;
        opt.textContent = t.text;
        select.appendChild(opt);
    });
    if (select.options.length) select.selectedIndex = 0;
    updateTipoButtons();
}

function updateTipoButtons() {
    var tipo = document.getElementById('select-tipo-grafica').value;
    var btnAgregar = document.getElementById('btn-asignar-tipo');
    var btnEliminar = document.getElementById('btn-eliminar-tipo');
    if (!tipo) return;
    var isAssigned = tiposPermitidos.indexOf(tipo) >= 0;
    btnAgregar.classList.toggle('d-none', isAssigned);
    btnEliminar.classList.toggle('d-none', !isAssigned);
}

function saveTiposPermitidos() {
    api('/tipos-grafica', { method: 'PUT', body: { tipos: tiposPermitidos } });
}

function initGraficaPage() {
    initVisibleState();
    populateTipoSelect();
    var selectEje = document.getElementById('select-eje');
    var selectGroup = document.getElementById('select-agrupar');
    var selectSeccion = document.getElementById('select-seccion');
    chartAxis = selectEje.value;
    chartGroup = selectGroup.value;
    selectSeccion.innerHTML = '';
    (estado.secciones || []).forEach(function(s) {
        var opt = document.createElement('option');
        opt.value = s.seccion_id;
        opt.textContent = s.nombre;
        if (s.seccion_id === estado.seccion_activa_id) opt.selected = true;
        selectSeccion.appendChild(opt);
    });
    renderCategoryPanel();
    renderChart(document.getElementById('select-tipo-grafica').value);
    updateChartDebug();
    document.getElementById('chart-panel').style.display = 'block';
}

// ============ EVENT LISTENERS ============
var selectTipoGrafica = document.getElementById('select-tipo-grafica');
if (selectTipoGrafica) {
    selectTipoGrafica.addEventListener('change', function() {
        updateTipoButtons();
        renderChart(this.value);
        updateChartDebug();
    });
}

document.getElementById('btn-asignar-tipo')?.addEventListener('click', function() {
    var tipo = selectTipoGrafica.value;
    if (!tipo || tiposPermitidos.indexOf(tipo) >= 0) return;
    tiposPermitidos.push(tipo);
    saveTiposPermitidos();
    updateTipoButtons();
    status('Tipo "' + tipo + '" asignado');
});

document.getElementById('btn-eliminar-tipo')?.addEventListener('click', function() {
    var tipo = selectTipoGrafica.value;
    var idx = tiposPermitidos.indexOf(tipo);
    if (idx < 0) return;
    tiposPermitidos.splice(idx, 1);
    saveTiposPermitidos();
    if (!tiposPermitidos.length) tiposPermitidos = ['bar', 'line', 'pie'];
    populateTipoSelect();
    renderChart(selectTipoGrafica.value);
    updateChartDebug();
    status('Tipo "' + tipo + '" eliminado');
});

document.getElementById('btn-config-grafica')?.addEventListener('click', function() {
    var msg = 'Tipos asignados: ' + (tiposPermitidos.length ? tiposPermitidos.join(', ') : '(ninguno)');
    status(msg);
});

document.getElementById('btn-toggle-panel')?.addEventListener('click', function() {
    var panel = document.getElementById('chart-panel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
});

document.getElementById('btn-cerrar-panel')?.addEventListener('click', function() {
    document.getElementById('chart-panel').style.display = 'none';
});

document.getElementById('select-eje')?.addEventListener('change', function() {
    chartAxis = this.value;
    renderChart(selectTipoGrafica.value);
    updateChartDebug();
});

document.getElementById('select-agrupar')?.addEventListener('change', function() {
    chartGroup = this.value;
    renderChart(selectTipoGrafica.value);
    updateChartDebug();
});

document.getElementById('select-seccion')?.addEventListener('change', function() {
    var sid = parseInt(this.value);
    if (!sid || sid === estado.seccion_activa_id) return;
    status('Cambiando sección...');
    api('/seccion/' + sid + '/data', { method: 'GET' })
        .then(function(j) {
            if (j.data) {
                initVisibleState();
                renderCategoryPanel();
                renderChart(selectTipoGrafica.value);
                updateChartDebug();
                status('Sección: ' + ((estado.secciones || []).find(function(s) { return s.seccion_id === estado.seccion_activa_id; })?.nombre || ''));
            } else {
                alerta(j.message || 'Error al cambiar sección');
            }
        })
        .catch(function() { alerta('Error de red al cambiar sección'); });
});

// ============ INIT ============
initGraficaPage();
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
