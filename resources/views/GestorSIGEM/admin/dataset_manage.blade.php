<div class="container-fluid py-3" id="app-dataset">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Dataset</h5>
            <small class="text-muted">
                <code>{{ $cuadro->codigo_cuadro }}</code>
                <strong>{{ $cuadro->c_titulo }}</strong>
            </small>
        </div>
        <a href="{{ route('sgiem.admin.cuadros.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2" id="mode-tabs">
        <div class="btn-group btn-group-sm" role="group" aria-label="Modo de edición">
            <button type="button" class="btn btn-outline-primary active" data-mode="diseno" onclick="switchMode('diseno')">
                <i class="bi bi-pencil-square me-1"></i>Diseño
            </button>
            <button type="button" class="btn btn-outline-primary" data-mode="datos" onclick="switchMode('datos')">
                <i class="bi bi-table me-1"></i>Datos
            </button>
        </div>
        <small class="text-muted" id="mode-hint">Editar estructura de filas, columnas y nombres</small>
        <button type="button" class="btn btn-sm btn-outline-danger datos-only" id="btn-limpiar-datos" onclick="window.limpiarDatos()" title="Limpiar todas las celdas">
            <i class="bi bi-eraser me-1"></i>Limpiar datos
        </button>
    </div>

    <div id="alerts"></div>

    @if(!$estadoInicial['tiene_dataset'])
        <div class="card shadow-sm border-0" id="empty-state">
            <div class="card-body text-center py-5">
                <i class="bi bi-table" style="font-size:3rem;color:var(--bs-primary)"></i>
                <h5 class="mt-3">Generar cuadrícula</h5>
                <p class="text-muted small mb-3">Creá una cuadrícula vacía para empezar a cargar datos</p>
                <div class="row justify-content-center g-2 mb-3">
                    <div class="col-auto">
                        <label class="form-label small">Filas</label>
                        <input type="number" class="form-control text-center" id="input-filas" value="5" min="1" max="50" style="width:80px">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small">Columnas</label>
                        <input type="number" class="form-control text-center" id="input-columnas" value="5" min="1" max="50" style="width:80px">
                    </div>
                </div>
                <button class="btn btn-primary px-4" id="btn-generar"><i class="bi bi-plus-square me-1"></i>Generar</button>
            </div>
        </div>
    @endif

    <div id="grid-container" @if(!$estadoInicial['tiene_dataset']) style="display:none" @endif>
        <div class="card shadow-sm border-0">
            <div class="card-body p-2">
                <div class="table-responsive" style="max-height:75vh">
                    <table class="table table-sm table-bordered table-hover mb-0" style="font-size:0.82rem" id="dataset-table">
                        <thead id="thead" class="table-light"></thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-1 d-flex justify-content-between align-items-center" id="status-bar">
                <small class="text-muted" id="status-text"></small>
                <div>
                    <span class="badge bg-secondary" id="dimension-badge"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal regenerar cuadrícula -->
<div class="modal fade" id="modalRegenerar" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-arrow-repeat" style="font-size:2.5rem;color:var(--bs-warning)"></i>
                <h5 class="mt-2">Regenerar cuadrícula</h5>
                <p class="text-muted small mb-3">Se eliminarán todas las categorías y datos actuales</p>
                <div class="row justify-content-center g-2 mb-3">
                    <div class="col-auto">
                        <label class="form-label small">Filas</label>
                        <input type="number" class="form-control text-center" id="modal-input-filas" value="5" min="1" max="50" style="width:80px">
                    </div>
                    <div class="col-auto">
                        <label class="form-label small">Columnas</label>
                        <input type="number" class="form-control text-center" id="modal-input-columnas" value="5" min="1" max="50" style="width:80px">
                    </div>
                </div>
                <button class="btn btn-warning px-4" id="btn-regenerar"><i class="bi bi-arrow-repeat me-1"></i>Regenerar</button>
            </div>
        </div>
    </div>
</div>

<style>
#dataset-table td, #dataset-table th { vertical-align: middle; padding: 0.15rem 0.3rem; }
#dataset-table td > div, #dataset-table th > div { min-height: 26px; outline: none; padding: 0.1rem 0.2rem; border-radius: 2px; }
#dataset-table td > div:focus { box-shadow: inset 0 0 0 1px var(--bs-primary); background: #fff; }
#dataset-table tbody tr:not(:last-child) > td:not(:first-child) { cursor: cell; }
#dataset-table thead th:not(:first-child):not(:last-child) { cursor: pointer; }
#dataset-table tbody tr:not(:last-child) > th:first-child { cursor: pointer; }
#dataset-table .cell-selected { box-shadow: inset 0 0 0 1.5px var(--bs-primary) !important; }
#dataset-table .cell-anchor { background: var(--bs-primary) !important; color: #fff; }
#dataset-table .cell-anchor > div { color: #fff; }
#status-bar .badge { font-size: 0.7rem; }
#mode-tabs .btn-group .btn.active { background: var(--bs-primary); color: #fff; }
.mode-datos .edit-only { display: none !important; }
.mode-datos .datos-only { display: inline-flex !important; }
.mode-diseno .datos-only { display: none !important; }
.mode-diseno .edit-only { display: inline-flex !important; }
#dataset-table .editable-header { cursor: text; }
#dataset-table td[data-vertical-id] > div { cursor: text; }
.mode-diseno #dataset-table td[data-vertical-id] > div { cursor: default; }
/* Headers horizontales con position-relative: espacio para 2 botones sin que salgan */
#dataset-table thead th.position-relative { padding-right: 52px; }
.mode-datos #dataset-table thead th.position-relative { padding-right: 0.3rem; }
/* Labels verticales con position-relative (con hijos): espacio para 2 botones */
#dataset-table tbody th.position-relative { padding-right: 52px; }
.mode-datos #dataset-table tbody th.position-relative { padding-right: 0.3rem; }
/* Labels sin hijos (d-flex): 75/25, modo datos 100% */
.mode-datos #dataset-table th.d-flex .edit-only,
.mode-datos #dataset-table td.d-flex .edit-only { display: none !important; }
.mode-datos #dataset-table th.d-flex > div:first-child,
.mode-datos #dataset-table td.d-flex > div:first-child { width: 100% !important; }
/* Celdas de datos no editables visualmente en modo diseño */
.mode-diseno #dataset-table td[data-vertical-id] { background: #f8f9fa; }
.mode-diseno #dataset-table td[data-vertical-id] > div { color: #6c757d; }
</style>

<script>
(function() {
    const CUADRO_ID = {{ $cuadro->cuadro_id }};
    const CSRF = '{{ csrf_token() }}';
    const BASE = '{{ url("/sgiem/admin/cuadros") }}/' + CUADRO_ID + '/dataset';

    const IS_DEV = @json(auth()->user()?->hasRole('Desarrollador') ?? false);
    function log(...args) { if (IS_DEV) console.log('[Dataset]', ...args); }

    let estado = @json($estadoInicial);
    let currentMode = 'diseno';


    window.switchMode = function(mode) {
        currentMode = mode;
        clearSelection();
        document.querySelectorAll('[data-mode]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.mode === mode);
        });
        document.getElementById('grid-container').classList.toggle('mode-datos', mode === 'datos');
        document.getElementById('grid-container').classList.toggle('mode-diseno', mode === 'diseno');
        const hint = document.getElementById('mode-hint');
        if (mode === 'diseno') {
            hint.textContent = 'Diseño: estructura de filas, columnas y nombres';
        } else {
            hint.textContent = 'Datos: editar celdas. También puede renombrar categorías y pivote';
        }
        // Headers: editable en ambos modos
        document.querySelectorAll('#dataset-table .editable-header').forEach(el => {
            el.contentEditable = 'true';
        });
        // Celdas de datos: solo editables en modo datos
        document.querySelectorAll('#dataset-table td[data-vertical-id] > div').forEach(el => {
            el.contentEditable = mode === 'datos';
            if (mode === 'diseno') el.blur();
        });
        status(mode === 'diseno' ? 'Modo Diseño' : 'Modo Datos');
    };

    const sel = {
        active: false,
        startRi: -1, startCi: -1,
        endRi: -1, endCi: -1,
        anchorVi: null, anchorHi: null,
    };

    const pointer = {
        down: false,
        startRi: -1, startCi: -1,
        startX: 0, startY: 0,
        dragging: false,
    };
    let lastCell = null; // { type: 'cell'|'horizontal'|'vertical', vId, hId }

    function api(path, opts = {}) {
        opts.headers = opts.headers || {};
        opts.headers['X-CSRF-TOKEN'] = CSRF;
        if (opts.body && typeof opts.body === 'object' && !(opts.body instanceof FormData)) {
            opts.body = JSON.stringify(opts.body);
            opts.headers['Content-Type'] = 'application/json';
        }
        return fetch(BASE + path, opts).then(r => r.json()).then(j => {
            if (j.data) estado = j.data;
            return j;
        });
    }

    function alerta(msg, tipo) {
        const div = document.getElementById('alerts');
        div.innerHTML = '<div class="alert alert-' + (tipo || 'danger') + ' alert-dismissible fade show">' +
            msg +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }

    function status(msg) {
        document.getElementById('status-text').textContent = msg || '';
    }

    function getCellCoords(el) {
        const th = el.closest('th');
        const td = el.closest('td');
        if (th && th.closest('thead')) {
            const catId = parseInt(th.dataset.categoriaId);
            if (!catId) return null;
            const ci = estado.horizontales.findIndex(h => h.categoria_id === catId);
            if (ci >= 0) {
                return { type: 'horizontal', ri: -1, ci, vId: null, hId: catId };
            }
            const colIdx = parseInt(th.dataset.colIndex);
            if (!isNaN(colIdx) && estado.horizontales[colIdx]) {
                return { type: 'horizontal', ri: -1, ci: colIdx, vId: null, hId: estado.horizontales[colIdx].categoria_id };
            }
            return null;
        }
        if (th && th.closest('tbody')) {
            const catId = parseInt(th.dataset.categoriaId);
            if (!catId) return null;
            const ri = estado.verticales.findIndex(v => v.categoria_id === catId);
            if (ri >= 0) {
                return { type: 'vertical', ri, ci: -1, vId: catId, hId: null };
            }
            const rowIdx = parseInt(th.dataset.rowIndex);
            if (!isNaN(rowIdx) && estado.verticales[rowIdx]) {
                return { type: 'vertical', ri: rowIdx, ci: -1, vId: estado.verticales[rowIdx].categoria_id, hId: null };
            }
            return null;
        }
        if (td && td.closest('tbody')) {
            if (td.querySelector('button')) return null;
            const vId = parseInt(td.dataset.verticalId);
            const hId = parseInt(td.dataset.horizontalId);
            if (!vId || !hId) return null;
            const ri = estado.verticales.findIndex(v => v.categoria_id === vId);
            const ci = estado.horizontales.findIndex(h => h.categoria_id === hId);
            if (ri < 0 || ci < 0) return null;
            return { type: 'cell', ri, ci, vId, hId };
        }
        return null;
    }

    function setSelection(minRi, minCi, maxRi, maxCi) {
        sel.active = true;
        sel.startRi = minRi;
        sel.startCi = minCi;
        sel.endRi = maxRi;
        sel.endCi = maxCi;
        sel.anchorVi = minRi >= 0 ? estado.verticales[minRi]?.categoria_id : null;
        sel.anchorHi = minCi >= 0 ? estado.horizontales[minCi]?.categoria_id : null;
        renderSelection();
    }

    function clearSelection() {
        sel.active = false;
        document.querySelectorAll('#dataset-table .cell-selected').forEach(el => {
            el.classList.remove('cell-selected', 'bg-primary', 'bg-opacity-10');
        });
    }

    function renderSelection() {
        document.querySelectorAll('#dataset-table .cell-selected').forEach(el => {
            el.classList.remove('cell-selected', 'bg-primary', 'bg-opacity-10');
        });
        if (!sel.active) return;

        const thead = document.getElementById('thead');
        const tbody = document.getElementById('tbody');
        if (!tbody) return;

        // Helper: highlight parent th of a leaf vertical
        function highlightVParent(ri) {
            const v = estado.verticales[ri];
            if (!v || !v.padre_id) return;
            const p = tbody.querySelector('th[data-categoria-id="' + v.padre_id + '"]');
            if (p) p.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
        }

        // Helper: highlight parent th of a leaf horizontal
        function highlightHParent(ci) {
            const h = estado.horizontales[ci];
            if (!h || !h.padre_id) return;
            const p = thead.querySelector('th[data-categoria-id="' + h.padre_id + '"]');
            if (p) p.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
        }

        // — Column selection —
        if (sel.startRi === -1 && sel.startCi >= 0) {
            if (thead) {
                thead.querySelectorAll('th[data-col-index]').forEach(th => {
                    const ci = parseInt(th.dataset.colIndex);
                    if (ci >= sel.startCi && ci <= sel.endCi) {
                        th.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
                    }
                });
            }
            for (let ri = 0; ri < estado.data.length; ri++) {
                const tr = tbody.children[ri];
                if (!tr) break;
                for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
                    const hId = estado.horizontales[ci]?.categoria_id;
                    if (!hId) continue;
                    const td = tr.querySelector('td[data-horizontal-id="' + hId + '"]');
                    if (td) td.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
                    highlightHParent(ci);
                }
            }
            return;
        }

        // — Row selection —
        if (sel.startCi === -1 && sel.startRi >= 0) {
            for (let ri = sel.startRi; ri <= sel.endRi; ri++) {
                const tr = tbody.children[ri];
                if (!tr) break;
                tr.querySelectorAll('th').forEach(th => th.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10'));
                tr.querySelectorAll('td[data-horizontal-id]').forEach(td => td.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10'));
                highlightVParent(ri);
            }
            return;
        }

        // — Cell selection (single or multi) —
        // Highlight column headers + parent column headers
        if (thead) {
            for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
                const hId = estado.horizontales[ci]?.categoria_id;
                if (!hId) continue;
                const headerTh = thead.querySelector('th[data-categoria-id="' + hId + '"]');
                if (headerTh) headerTh.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
                highlightHParent(ci);
            }
        }

        // Highlight rows, labels, parents, and data cells
        for (let ri = sel.startRi; ri <= sel.endRi && ri < estado.data.length; ri++) {
            const tr = tbody.children[ri];
            if (!tr) break;

            // Row labels (direct leaf th + any parent th with rowspan)
            tr.querySelectorAll('th').forEach(th => th.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10'));

            // Parent label if this leaf has a parent
            highlightVParent(ri);

            // Data cells
            for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
                const hId = estado.horizontales[ci]?.categoria_id;
                if (!hId) continue;
                const td = tr.querySelector('td[data-horizontal-id="' + hId + '"]');
                if (td) td.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
            }
        }
    }

    const vivos = {};

    function guardarCelda(el, vId, hId) {
        const val = el.textContent.trim();
        const key = vId + '-' + hId;
        const dato = vivos[key];
        if (!dato) return;
        log('guardarCelda', { vId, hId, val, dato_id: dato.dato_id });
        status('Guardando...');
        api('/celda/' + dato.dato_id, {
            method: 'PUT',
            body: { valor: val },
        }).then(j => {
            if (j.success) { dato.valor = val; status('✓ Guardado'); }
            else alerta(j.message);
        }).catch(() => alerta('Error de red'));
    }

    function renombrar(el, id) {
        const nombre = el.textContent.trim();
        if (!nombre) return;
        log('renombrar', { id, nombre });
        status('Guardando...');
        api('/categoria/' + id, {
            method: 'PUT',
            body: { nombre: nombre },
        }).then(j => {
            if (j.success) {
                if (j.categoria?._renombrado) {
                    el.textContent = j.categoria.nombre;
                    alerta('Ya existía, se renombró a <strong>' + esc(j.categoria.nombre) + '</strong>', 'warning');
                }
                status('✓ Guardado');
            } else {
                alerta(j.message);
                renderGrid(estado);
            }
        }).catch(() => alerta('Error'));
    }

    function esc(s) {
        if (!s) return '';
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function renderGrid(d) {
        if (!d.tiene_dataset) {
            document.getElementById('grid-container').style.display = 'none';
            document.getElementById('empty-state').style.display = '';
            return;
        }
        log('renderGrid', { labels: d.labels, headers: d.headers, dataLen: d.data?.length });
        document.getElementById('grid-container').style.display = '';
        const empty = document.getElementById('empty-state');
        if (empty) empty.style.display = 'none';

        const { verticales, horizontales, headers, labels, data } = d;
        const thead = document.getElementById('thead');
        const tbody = document.getElementById('tbody');

        for (const k in vivos) delete vivos[k];
        for (const row of data) {
            for (const cel of row) {
                if (cel.dato_id) {
                    vivos[cel.cat_vertical_id + '-' + cel.cat_horizontal_id] = cel;
                }
            }
        }

        document.getElementById('dimension-badge').textContent = verticales.length + ' × ' + horizontales.length;

        const numLabelCols = labels.length > 0
            ? Math.max(...labels.map(r => r.length), 1)
            : 1;

        // === HEADERS ===
        let theadHtml = '';
        if (headers.length === 0) {
            theadHtml = '<tr>'
                + '<th class="text-center align-middle" style="width:44px;background:#f0f2f5">'
                + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.limpiarDataset()" title="Limpiar todo"><i class="bi bi-trash3"></i></button>'
                + '</th>'
                + '<th class="text-center" style="width:36px;background:#f0f2f5">'
                + '<button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.agregarColumna()" title="Agregar columna"><i class="bi bi-plus"></i></button>'
                + '</th></tr>';
        } else {
            const numHeaderRows = headers.length;
            for (let ri = 0; ri < numHeaderRows; ri++) {
                theadHtml += '<tr>';
                const headerRow = headers[ri];
                for (const cell of headerRow) {
                    if (cell.tipo === 'corner') {
                        const rspan = cell.rowspan || numHeaderRows;
                        theadHtml += '<th rowspan="' + rspan + '" colspan="' + numLabelCols + '" class="text-center align-middle" style="width:' + (numLabelCols * 44) + 'px;background:#f0f2f5">'
                            + '<div class="d-flex flex-column align-items-center gap-1">'
                            + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.limpiarDataset()" title="Limpiar todo"><i class="bi bi-trash3"></i></button>'
                            + '<span class="pivot-label editable-header text-muted small" contenteditable="false" style="font-size:0.65rem" onblur="window.guardarPivot(this)">' + esc(estado.pivot_label || 'PIVOTE') + '</span>'
                            + '</div></th>';
                    } else if (cell.tipo === 'parent') {
                        // Horizontal parent (1+ children): sequential horizontal buttons, centered vertically
                        theadHtml += '<th colspan="' + cell.colspan + '" data-categoria-id="' + cell.categoria_id + '" data-col-index="' + cell.col_index + '" data-es-hijo="' + (cell.es_hijo ? 1 : 0) + '" class="align-middle text-center position-relative" style="background:#e2e6ea;min-width:90px">'
                            + '<div contenteditable="true" data-categoria-id="' + cell.categoria_id + '" data-es-hijo="' + (cell.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + cell.categoria_id + ')" class="fw-semibold px-1 small editable-header">' + esc(cell.nombre) + '</div>'
                            + '<div class="position-absolute end-0 top-50 translate-middle-y d-flex flex-row gap-0 p-0 edit-only" style="z-index:2">'
                            + (!cell.es_hijo ? '<button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Añadir hijo" onclick="window.agregarHijo(' + cell.categoria_id + ')"><i class="bi bi-plus"></i></button>' : '')
                            + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Eliminar columna" onclick="window.eliminarColumna(' + cell.categoria_id + ')"><i class="bi bi-x"></i></button>'
                            + '</div>'
                            + '</th>';
                    } else {
                        // Leaf (no children): vertical buttons
                        theadHtml += '<th data-categoria-id="' + cell.categoria_id + '" data-col-index="' + cell.col_index + '" data-es-hijo="' + (cell.es_hijo ? 1 : 0) + '" class="align-middle text-center" style="background:#f0f2f5;min-width:90px">'
                            + '<div class="d-flex align-items-center" style="background:#f0f2f5">'
                            + '<div contenteditable="true" data-categoria-id="' + cell.categoria_id + '" data-es-hijo="' + (cell.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + cell.categoria_id + ')" class="w-75 px-1 small editable-header text-start">' + esc(cell.nombre) + '</div>'
                            + '<div class="w-25 d-flex flex-column gap-0 p-0 edit-only align-items-center">'
                            + (!cell.es_hijo ? '<button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Añadir hijo" onclick="window.agregarHijo(' + cell.categoria_id + ')"><i class="bi bi-plus"></i></button>' : '')
                            + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Eliminar columna" onclick="window.eliminarColumna(' + cell.categoria_id + ')"><i class="bi bi-x"></i></button>'
                            + '</div>'
                            + '</div>'
                            + '</th>';
                    }
                }
                if (ri === 0) {
                    theadHtml += '<th rowspan="' + numHeaderRows + '" class="text-center" style="width:36px"><button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.agregarColumna()" title="Agregar columna"><i class="bi bi-plus"></i></button></th>';
                }
                theadHtml += '</tr>';
            }
        }
        thead.innerHTML = theadHtml;

        // === BODY ===
        let tbodyHtml = '';
        for (let ri = 0; ri < labels.length; ri++) {
            tbodyHtml += '<tr>';
            const labelRow = labels[ri];
            for (const label of labelRow) {
                if (label.tipo === 'parent' && label.rowspan > 1) {
                    // Parent 2+ children: vertical buttons (+ top, x bottom), centered
                    tbodyHtml += '<th rowspan="' + label.rowspan + '" data-categoria-id="' + label.categoria_id + '" data-row-index="' + label.row_index + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" class="position-relative" style="background:#f8f9fa;min-width:110px;font-weight:500">'
                        + '<div contenteditable="true" data-categoria-id="' + label.categoria_id + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + label.categoria_id + ')" class="px-1 small fw-semibold editable-header">' + esc(label.nombre) + '</div>'
                        + '<div class="position-absolute end-0 top-50 translate-middle-y d-flex flex-column gap-0 p-0 edit-only" style="z-index:2">'
                        + (!label.es_hijo ? '<button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Añadir hijo" onclick="window.agregarHijo(' + label.categoria_id + ')"><i class="bi bi-plus"></i></button>' : '')
                        + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Eliminar fila" onclick="window.eliminarFila(' + label.categoria_id + ')"><i class="bi bi-x"></i></button>'
                        + '</div>'
                        + '</th>';
                } else if (label.tipo === 'parent') {
                    // Parent 1 child: sequential horizontal buttons (+ then x)
                    tbodyHtml += '<th data-categoria-id="' + label.categoria_id + '" data-row-index="' + label.row_index + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" class="d-flex align-items-center" style="background:#f8f9fa;min-width:110px;font-weight:500">'
                        + '<div contenteditable="true" data-categoria-id="' + label.categoria_id + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + label.categoria_id + ')" class="w-75 px-1 small fw-semibold editable-header">' + esc(label.nombre) + '</div>'
                        + '<div class="w-25 d-flex flex-row gap-0 p-0 edit-only align-items-center justify-content-end">'
                        + (!label.es_hijo ? '<button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Añadir hijo" onclick="window.agregarHijo(' + label.categoria_id + ')"><i class="bi bi-plus"></i></button>' : '')
                        + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Eliminar fila" onclick="window.eliminarFila(' + label.categoria_id + ')"><i class="bi bi-x"></i></button>'
                        + '</div>'
                        + '</th>';
                } else {
                    // Leaf (no children): sequential horizontal buttons (+ then x)
                    tbodyHtml += '<th data-categoria-id="' + label.categoria_id + '" data-row-index="' + label.row_index + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" class="d-flex align-items-center" style="background:#f8f9fa;min-width:110px;font-weight:400">'
                        + '<div contenteditable="true" data-categoria-id="' + label.categoria_id + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + label.categoria_id + ')" class="w-75 px-1 small editable-header">' + esc(label.nombre) + '</div>'
                        + '<div class="w-25 d-flex flex-row gap-0 p-0 edit-only align-items-center justify-content-end">'
                        + (!label.es_hijo ? '<button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Añadir hijo" onclick="window.agregarHijo(' + label.categoria_id + ')"><i class="bi bi-plus"></i></button>' : '')
                        + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:1rem" title="Eliminar fila" onclick="window.eliminarFila(' + label.categoria_id + ')"><i class="bi bi-x"></i></button>'
                        + '</div>'
                        + '</th>';
                }
            }
            const dataRow = data[ri] || [];
            for (const cel of dataRow) {
                tbodyHtml += '<td class="position-relative" data-vertical-id="' + cel.cat_vertical_id + '" data-horizontal-id="' + cel.cat_horizontal_id + '" data-dato-id="' + (cel.dato_id || '') + '">'
                    + '<div contenteditable="true" onblur="window.guardarCelda(this, ' + cel.cat_vertical_id + ', ' + cel.cat_horizontal_id + ')">'
                    + esc(cel.valor || '') + '</div></td>';
            }
            tbodyHtml += '<td></td></tr>';
        }

        const footerCols = numLabelCols + horizontales.length + 1;
        tbodyHtml += '<tr class="table-light">'
            + '<td colspan="' + numLabelCols + '"><button class="btn btn-sm btn-outline-success rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.agregarFila()" title="Agregar fila"><i class="bi bi-plus"></i></button> <span class="edit-only small text-muted">Fila</span></td>'
            + '<td colspan="' + (horizontales.length + 1) + '"></td></tr>';

        tbody.innerHTML = tbodyHtml;

        // Apply theme color if available
        const color = (window.estado && window.estado.tema_color) || null;
        let styleEl = document.getElementById('tema-color-style');
        if (color && hexToRgba(color, 0.5) !== null) {
            const childBg = hexToRgba(color, 0.5);
            const cellBg = hexToRgba(color, 0.12);
            if (!styleEl) {
                styleEl = document.createElement('style');
                styleEl.id = 'tema-color-style';
                document.head.appendChild(styleEl);
            }
            styleEl.textContent =
                '#dataset-table thead th.position-relative .editable-header,' +
                '#dataset-table thead th.position-relative .fw-bold { background:' + color + ';color:#fff;border-radius:3px;padding:1px 6px; }' +
                '#dataset-table thead th .editable-header[data-es-hijo="1"] { background:' + childBg + ';border-radius:3px;padding:1px 6px; }' +
                '#dataset-table tbody td { background:' + cellBg + '; }' +
                '#dataset-table tbody th.position-relative .editable-header,' +
                '#dataset-table tbody th.position-relative .fw-semibold { background:' + color + ';color:#fff;border-radius:3px;padding:1px 6px; }' +
                '#dataset-table tbody th .editable-header[data-es-hijo="1"] { background:' + childBg + ';border-radius:3px;padding:1px 6px; }';
        } else if (styleEl) {
            styleEl.textContent = '';
        }
        renderSelection();
    }

    function hexToRgba(hex, alpha) {
        if (!/^#[0-9a-f]{6}$/i.test(hex)) return null;
        const r = parseInt(hex.slice(1,3), 16);
        const g = parseInt(hex.slice(3,5), 16);
        const b = parseInt(hex.slice(5,7), 16);
        return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
    }

    function saveAllBeforeAction() {
        // Trigger blur on any actively edited cell
        const focused = document.querySelector('#dataset-table div:focus');
        if (focused) focused.blur();
    }

    function getPasteAnchor() {
        if (sel.active) {
            if (sel.startRi === -1 && sel.startCi >= 0) {
                const minCi = Math.min(sel.startCi, sel.endCi);
                return { type: 'horizontal', vId: null, hId: estado.horizontales[minCi]?.categoria_id };
            }
            if (sel.startCi === -1 && sel.startRi >= 0) {
                const minRi = Math.min(sel.startRi, sel.endRi);
                return { type: 'vertical', vId: estado.verticales[minRi]?.categoria_id, hId: null };
            }
            const minRi = Math.min(sel.startRi, sel.endRi);
            const minCi = Math.min(sel.startCi, sel.endCi);
            return {
                type: 'cell',
                vId: estado.verticales[minRi]?.categoria_id,
                hId: estado.horizontales[minCi]?.categoria_id,
            };
        }
        return lastCell;
    }

    // === POINTER EVENTS FOR CELL SELECTION ===
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('dataset-table');
        const tbody = document.getElementById('tbody');

        table.addEventListener('pointerdown', function(e) {
            if (e.target.closest('button, a, input')) return;
            const cell = e.target.closest('td, th');
            if (!cell) return;
            const coords = getCellCoords(cell);
            if (!coords) return;

            if (currentMode === 'datos' && coords.type !== 'cell') return;

            if (e.shiftKey) {
                e.preventDefault();
                if (currentMode === 'datos' && coords.type !== 'cell') return;
                const type = coords.type;
                const prevType = sel.active ? (
                    sel.startRi === -1 ? 'horizontal' :
                    sel.startCi === -1 ? 'vertical' : 'cell'
                ) : type;
                if (type !== prevType) return;
                if (type === 'horizontal') {
                    const mc = sel.active ? Math.min(sel.startCi, coords.ci) : coords.ci;
                    const xc = sel.active ? Math.max(sel.startCi, coords.ci) : coords.ci;
                    setSelection(-1, mc, -1, xc);
                } else if (type === 'vertical') {
                    const mr = sel.active ? Math.min(sel.startRi, coords.ri) : coords.ri;
                    const xr = sel.active ? Math.max(sel.startRi, coords.ri) : coords.ri;
                    setSelection(mr, -1, xr, -1);
                } else {
                    const mr = sel.active ? Math.min(sel.startRi, coords.ri) : coords.ri;
                    const xr = sel.active ? Math.max(sel.startRi, coords.ri) : coords.ri;
                    const mc = sel.active ? Math.min(sel.startCi, coords.ci) : coords.ci;
                    const xc = sel.active ? Math.max(sel.startCi, coords.ci) : coords.ci;
                    setSelection(mr, mc, xr, xc);
                }
                return;
            }

            lastCell = { type: coords.type, vId: coords.vId, hId: coords.hId };
            if (currentMode === 'datos' && coords.type === 'cell' && coords.vId && coords.hId) {
                const vCat = estado.verticales.find(v => v.categoria_id === coords.vId);
                const hCat = estado.horizontales.find(h => h.categoria_id === coords.hId);
                if (vCat && hCat) status('Fila: "' + vCat.nombre + '" | Columna: "' + hCat.nombre + '"');
            }
            pointer.down = true;
            pointer.startRi = coords.ri;
            pointer.startCi = coords.ci;
            pointer.startX = e.clientX;
            pointer.startY = e.clientY;
            pointer.dragging = false;
        });

        document.addEventListener('pointermove', function(e) {
            if (!pointer.down) return;
            const dx = e.clientX - pointer.startX;
            const dy = e.clientY - pointer.startY;
            if (!pointer.dragging && (Math.abs(dx) > 4 || Math.abs(dy) > 4)) {
                pointer.dragging = true;
                document.body.style.userSelect = 'none';
                setSelection(pointer.startRi, pointer.startCi, pointer.startRi, pointer.startCi);
            }
            if (!pointer.dragging) return;
            e.preventDefault();
            const el = document.elementFromPoint(e.clientX, e.clientY);
            if (!el) return;
            const cell = el.closest('td, th');
            if (!cell) return;
            const coords = getCellCoords(cell);
            if (!coords) return;
            const startType = pointer.startRi === -1 ? 'horizontal' : pointer.startCi === -1 ? 'vertical' : 'cell';
            if (coords.type !== startType) return;
            if (currentMode === 'datos' && startType !== 'cell') return;
            if (startType === 'horizontal') {
                const mc = Math.min(pointer.startCi, coords.ci);
                const xc = Math.max(pointer.startCi, coords.ci);
                setSelection(-1, mc, -1, xc);
            } else if (startType === 'vertical') {
                const mr = Math.min(pointer.startRi, coords.ri);
                const xr = Math.max(pointer.startRi, coords.ri);
                setSelection(mr, -1, xr, -1);
            } else {
                const mr = Math.min(pointer.startRi, coords.ri);
                const xr = Math.max(pointer.startRi, coords.ri);
                const mc = Math.min(pointer.startCi, coords.ci);
                const xc = Math.max(pointer.startCi, coords.ci);
                setSelection(mr, mc, xr, xc);
            }
        });

        document.addEventListener('pointerup', function(e) {
            if (pointer.dragging) {
                document.body.style.userSelect = '';
                pointer.down = false;
                pointer.dragging = false;
                if (sel.active) {
                    if (sel.startRi === -1) {
                        status('Selección: ' + (sel.endCi - sel.startCi + 1) + ' columnas');
                    } else if (sel.startCi === -1) {
                        status('Selección: ' + (sel.endRi - sel.startRi + 1) + ' filas');
                    } else {
                        status('Selección: ' + (sel.endRi - sel.startRi + 1) + '×' + (sel.endCi - sel.startCi + 1) + ' celdas');
                    }
                }
            } else if (pointer.down) {
                pointer.down = false;
                clearSelection();
            }
        });

        // === PASTE ===
        table.addEventListener('paste', function(e) {
            const text = (e.clipboardData || window.clipboardData).getData('text');
            if (!text.trim()) return;
            e.preventDefault();

            const clipGrid = text.split('\n').filter(r => r.trim()).map(r => r.split('\t').map(c => c.trim()));
            if (clipGrid.length === 0) return;

            // En modo Datos, solo pegar como celdas
            if (currentMode === 'datos') {
                const anchor = sel.active ? getPasteAnchor() : lastCell;
                if (!anchor || anchor.type !== 'cell' || !anchor.vId || !anchor.hId) {
                    status('Seleccioná una celda para pegar');
                    return;
                }
                saveAllBeforeAction();
                status('Pegando...');
                api('/paste', {
                    method: 'POST',
                    body: {
                        grid: clipGrid,
                        start_vertical_id: anchor.vId,
                        start_horizontal_id: anchor.hId,
                    }
                }).then(j => {
                    if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ Pegado'); }
                    else alerta(j.message);
                }).catch(() => alerta('Error de red'));
                return;
            }

            const anchor = getPasteAnchor();
            log('paste', { clipGrid, anchor });

            if (anchor && anchor.type === 'horizontal' && anchor.hId) {
                const valores = clipGrid[0] || [];
                if (valores.length === 0) return;
                saveAllBeforeAction();
                status('Pegando columnas...');
                api('/paste-categorias', {
                    method: 'POST',
                    body: { eje: 'horizontal', start_categoria_id: anchor.hId, valores }
                }).then(j => {
                    if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ Columnas renombradas'); }
                    else alerta(j.message);
                }).catch(() => alerta('Error de red'));
            } else if (anchor && anchor.type === 'vertical' && anchor.vId) {
                const valores = clipGrid.map(r => r[0]).filter(v => v != null);
                if (valores.length === 0) return;
                saveAllBeforeAction();
                status('Pegando filas...');
                api('/paste-categorias', {
                    method: 'POST',
                    body: { eje: 'vertical', start_categoria_id: anchor.vId, valores }
                }).then(j => {
                    if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ Filas renombradas'); }
                    else alerta(j.message);
                }).catch(() => alerta('Error de red'));
            } else if (anchor && anchor.type === 'cell' && anchor.vId && anchor.hId) {
                saveAllBeforeAction();
                status('Pegando...');
                api('/paste', {
                    method: 'POST',
                    body: {
                        grid: clipGrid,
                        start_vertical_id: anchor.vId,
                        start_horizontal_id: anchor.hId,
                    }
                }).then(j => {
                    if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ Pegado'); }
                    else alerta(j.message);
                }).catch(() => alerta('Error de red'));
            } else {
                if (clipGrid.length < 2 || !confirm('¿Reemplazar todo el dataset (' + clipGrid.length + '×' + clipGrid[0].length + ')?')) return;
                saveAllBeforeAction();
                status('Pegando...');
                api('/paste', {
                    method: 'POST',
                    body: { grid: clipGrid }
                }).then(j => {
                    if (j.success) { estado = j.data; renderGrid(estado); status('✓ Reemplazado'); }
                    else alerta(j.message);
                }).catch(() => alerta('Error de red'));
            }
        });

        // === KEYBOARD NAVIGATION (Datos mode) ===
        table.addEventListener('keydown', function(e) {
            if (currentMode !== 'datos') return;
            if (e.target.closest('button, a, input')) return;

            const td = e.target.closest('td[data-vertical-id]');
            if (!td) return;

            const vId = parseInt(td.dataset.verticalId);
            const hId = parseInt(td.dataset.horizontalId);
            if (!vId || !hId) return;

            let ri = estado.verticales.findIndex(v => v.categoria_id === vId);
            let ci = estado.horizontales.findIndex(h => h.categoria_id === hId);
            if (ri < 0 || ci < 0) return;

            const key = e.key;
            const isArrow = ['ArrowUp','ArrowDown','ArrowLeft','ArrowRight'].includes(key);
            const isTab = key === 'Tab';
            const isEnter = key === 'Enter';

            if (!isArrow && !isTab && !isEnter) return;
            e.preventDefault();

            if (key === 'ArrowRight') ci++;
            else if (key === 'ArrowLeft') ci--;
            else if (key === 'ArrowDown') ri++;
            else if (key === 'ArrowUp') ri--;
            else if (key === 'Tab') { e.shiftKey ? ci-- : ci++; }
            else if (key === 'Enter') { e.shiftKey ? ri-- : ri++; }

            if (ri < 0) ri = 0;
            if (ri >= estado.verticales.length) ri = estado.verticales.length - 1;
            if (ci < 0) ci = 0;
            if (ci >= estado.horizontales.length) ci = estado.horizontales.length - 1;

            const targetTd = document.querySelector(
                '#tbody td[data-vertical-id="' + estado.verticales[ri].categoria_id + '"][data-horizontal-id="' + estado.horizontales[ci].categoria_id + '"]'
            );
            if (!targetTd) return;

            const div = targetTd.querySelector('div[contenteditable]');
            if (div) {
                // Update lastCell for paste anchor
                lastCell = { type: 'cell', vId: estado.verticales[ri].categoria_id, hId: estado.horizontales[ci].categoria_id };
                clearSelection();
                setSelection(ri, ci, ri, ci);
                div.focus();
                // Place cursor at end
                const range = document.createRange();
                const selR = window.getSelection();
                range.selectNodeContents(div);
                range.collapse(false);
                selR.removeAllRanges();
                selR.addRange(range);
                // Show category info
                const vCat = estado.verticales[ri];
                const hCat = estado.horizontales[ci];
                if (vCat && hCat) status('Fila: "' + vCat.nombre + '" | Columna: "' + hCat.nombre + '"');
            }
        });
    });

    // === GLOBAL FUNCTIONS ===
    window.guardarCelda = guardarCelda;

    window.renombrarHeader = function(el, id) {
        renombrar(el, id);
    };

    window.agregarHijo = function(padreId) {
        log('agregarHijo', { padreId });
        saveAllBeforeAction();
        api('/hijo', { method: 'POST', body: { padre_id: padreId } }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Hijo agregado'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.agregarFila = function() {
        log('agregarFila');
        saveAllBeforeAction();
        api('/fila', { method: 'POST' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); status('Fila agregada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.agregarColumna = function() {
        log('agregarColumna');
        saveAllBeforeAction();
        api('/columna', { method: 'POST' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); status('Columna agregada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.eliminarFila = function(id) {
        log('eliminarFila', { id });
        saveAllBeforeAction();
        if (!confirm('¿Eliminar esta fila?')) return;
        api('/fila/' + id, { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Fila eliminada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.eliminarColumna = function(id) {
        log('eliminarColumna', { id });
        saveAllBeforeAction();
        if (!confirm('¿Eliminar esta columna?')) return;
        api('/columna/' + id, { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Columna eliminada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.limpiarDataset = function() {
        saveAllBeforeAction();
        const m = new bootstrap.Modal(document.getElementById('modalRegenerar'));
        m.show();
    };

    window.limpiarDatos = function() {
        if (!confirm('¿Limpiar todos los valores de las celdas? Se conservarán las categorías.')) return;
        saveAllBeforeAction();
        status('Limpiando datos...');
        api('/datos', { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ Datos limpiados'); }
            else alerta(j.message);
        }).catch(() => alerta('Error de red'));
    };

    window.guardarPivot = function(el) {
        const val = el.textContent.trim() || 'PIVOTE';
        estado.pivot_label = val;
        log('guardarPivot', { val });
        status('Guardando pivote...');
        api('/pivot', { method: 'PUT', body: { label: val } }).then(j => {
            if (j.success) status('Pivote: ' + val);
            else alerta(j.message);
        }).catch(() => alerta('Error de red'));
    };

    // === GENERATE ===
    document.getElementById('btn-generar')?.addEventListener('click', function() {
        const filas = parseInt(document.getElementById('input-filas').value) || 5;
        const cols = parseInt(document.getElementById('input-columnas').value) || 5;
        status('Generando...');
        api('/generar', { method: 'POST', body: { filas, columnas: cols } }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Grilla generada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    });

    document.getElementById('btn-regenerar')?.addEventListener('click', function() {
        const filas = parseInt(document.getElementById('modal-input-filas').value) || 5;
        const cols = parseInt(document.getElementById('modal-input-columnas').value) || 5;
        status('Regenerando...');
        api('/generar', { method: 'POST', body: { filas, columnas: cols } }).then(j => {
            if (j.success) {
                estado = j.data; clearSelection(); renderGrid(estado); status('Cuadrícula regenerada');
                bootstrap.Modal.getInstance(document.getElementById('modalRegenerar'))?.hide();
            } else alerta(j.message);
        }).catch(() => alerta('Error'));
    });

    function importarFile(input) {
        const file = input.files[0];
        if (!file) return;
        const fd = new FormData();
        fd.append('dataset', file);
        status('Importando...');
        fetch(BASE + '/importar', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }, body: fd })
            .then(r => r.json()).then(j => {
                if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ Importado'); }
                else alerta(j.message);
            }).catch(() => alerta('Error'));
        input.value = '';
    }

    // === INIT ===
    if (estado.tiene_dataset) {
        renderGrid(estado);
    }
})();
</script>
