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

    <div id="alerts"></div>

    @if(!$estadoInicial['tiene_dataset'])
        <div class="card shadow-sm border-0" id="empty-state">
            <div class="card-body text-center py-5">
                <i class="bi bi-table" style="font-size:3rem;color:var(--bs-primary)"></i>
                <h5 class="mt-3">Generar grilla</h5>
                <p class="text-muted small mb-3">Creá una grilla vacía para empezar a cargar datos</p>
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
                <hr class="my-3" style="max-width:300px;margin-inline:auto">
                <button class="btn btn-outline-secondary btn-sm" id="btn-importar-vacio"><i class="bi bi-upload me-1"></i>Importar CSV</button>
                <input type="file" id="input-csv-vacio" accept=".csv,.txt" hidden>
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
</style>

<script>
(function() {
    const CUADRO_ID = {{ $cuadro->cuadro_id }};
    const CSRF = '{{ csrf_token() }}';
    const BASE = '{{ url("/sgiem/admin/cuadros") }}/' + CUADRO_ID + '/dataset';

    let estado = @json($estadoInicial);

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
        return fetch(BASE + path, opts).then(r => r.json());
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
            return { type: 'horizontal', ri: -1, ci, vId: null, hId: catId };
        }
        if (th && th.closest('tbody')) {
            const catId = parseInt(th.dataset.categoriaId);
            if (!catId) return null;
            const ri = estado.verticales.findIndex(v => v.categoria_id === catId);
            if (ri < 0) return null;
            return { type: 'vertical', ri, ci: -1, vId: catId, hId: null };
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

        const tbody = document.getElementById('tbody');
        if (!tbody) return;

        if (sel.startRi === -1 && sel.startCi >= 0) {
            for (let ri = 0; ri < estado.data.length; ri++) {
                const tr = tbody.children[ri];
                if (!tr) break;
                const tds = Array.from(tr.querySelectorAll('td[data-horizontal-id]'));
                for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
                    const hId = estado.horizontales[ci]?.categoria_id;
                    if (!hId) continue;
                    const td = tds.find(t => parseInt(t.dataset.horizontalId) === hId);
                    if (td) td.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
                }
            }
            return;
        }

        if (sel.startCi === -1 && sel.startRi >= 0) {
            for (let ri = sel.startRi; ri <= sel.endRi; ri++) {
                const tr = tbody.children[ri];
                if (!tr) break;
                tr.querySelectorAll('th').forEach(th => th.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10'));
                const dataTds = tr.querySelectorAll('td[data-horizontal-id]');
                dataTds.forEach(td => td.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10'));
            }
            return;
        }

        for (let ri = sel.startRi; ri <= sel.endRi && ri < estado.data.length; ri++) {
            const tr = tbody.children[ri];
            if (!tr) break;
            const tds = Array.from(tr.querySelectorAll('td[data-horizontal-id]'));
            for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
                const hId = estado.horizontales[ci]?.categoria_id;
                if (!hId) continue;
                const td = tds.find(t => parseInt(t.querySelector('[data-horizontal-id]')?.dataset?.horizontalId) === hId);
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
        status('Guardando...');
        api('/categoria/' + id, {
            method: 'PUT',
            body: { nombre: nombre },
        }).then(j => {
            if (j.success) status('✓ Guardado');
            else alerta(j.message);
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
                + '<button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="window.limpiarDataset()" title="Limpiar todo"><i class="bi bi-trash3"></i></button>'
                + '</th>'
                + '<th class="text-center" style="width:36px;background:#f0f2f5">'
                + '<button class="btn btn-sm btn-outline-primary py-0 px-1" onclick="window.agregarColumna()" title="Agregar columna"><i class="bi bi-plus-lg"></i></button>'
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
                            + '<button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="window.limpiarDataset()" title="Limpiar todo"><i class="bi bi-trash3"></i></button>'
                            + '<small class="text-muted" style="font-size:0.65rem">PIVOTE</small>'
                            + '</div></th>';
                    } else if (cell.tipo === 'parent') {
                        theadHtml += '<th colspan="' + cell.colspan + '" data-categoria-id="' + cell.categoria_id + '" class="align-middle position-relative text-center" style="background:#e2e6ea;min-width:90px">'
                            + '<div contenteditable="true" data-categoria-id="' + cell.categoria_id + '" onblur="window.renombrarHeader(this, ' + cell.categoria_id + ')" class="fw-semibold px-1 small">' + esc(cell.nombre) + '</div>'
                            + '<button class="btn btn-sm text-primary p-0 position-absolute top-0 end-0" onclick="window.agregarHijo(' + cell.categoria_id + ')" style="z-index:2;font-size:0.55rem;background:rgba(255,255,255,0.8)"><i class="bi bi-plus-circle"></i></button>'
                            + '<button class="btn btn-sm text-danger p-0 position-absolute bottom-0 end-0" onclick="window.eliminarColumna(' + cell.categoria_id + ')" style="z-index:2;font-size:0.55rem;background:rgba(255,255,255,0.8)"><i class="bi bi-x-circle"></i></button>'
                            + '</th>';
                    } else {
                        theadHtml += '<th data-categoria-id="' + cell.categoria_id + '" class="align-middle position-relative text-center" style="background:#f0f2f5;min-width:90px">'
                            + '<div contenteditable="true" data-categoria-id="' + cell.categoria_id + '" onblur="window.renombrarHeader(this, ' + cell.categoria_id + ')" class="fw-normal px-1 small">' + esc(cell.nombre) + '</div>'
                            + '<button class="btn btn-sm text-primary p-0 position-absolute top-0 end-0" onclick="window.agregarHijo(' + cell.categoria_id + ')" style="z-index:2;font-size:0.55rem;background:rgba(255,255,255,0.8)"><i class="bi bi-plus-circle"></i></button>'
                            + '<button class="btn btn-sm text-danger p-0 position-absolute bottom-0 end-0" onclick="window.eliminarColumna(' + cell.categoria_id + ')" style="z-index:2;font-size:0.55rem;background:rgba(255,255,255,0.8)"><i class="bi bi-x-circle"></i></button>'
                            + '</th>';
                    }
                }
                if (ri === 0) {
                    theadHtml += '<th rowspan="' + numHeaderRows + '" class="text-center" style="width:36px"><button class="btn btn-sm btn-outline-primary py-0 px-1" onclick="window.agregarColumna()" title="Agregar columna"><i class="bi bi-plus-lg"></i></button></th>';
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
                    tbodyHtml += '<th rowspan="' + label.rowspan + '" data-categoria-id="' + label.categoria_id + '" class="position-relative" style="background:#f8f9fa;min-width:110px;font-weight:500">'
                        + '<div contenteditable="true" data-categoria-id="' + label.categoria_id + '" onblur="window.renombrarHeader(this, ' + label.categoria_id + ')" class="px-1 small fw-semibold">' + esc(label.nombre) + '</div>'
                        + '<button class="btn btn-sm text-primary p-0 position-absolute top-0 start-100 translate-middle" onclick="window.agregarHijo(' + label.categoria_id + ')" style="z-index:2;font-size:0.55rem;background:rgba(255,255,255,0.8)"><i class="bi bi-plus-circle"></i></button>'
                        + '<button class="btn btn-sm text-danger p-0 position-absolute bottom-0 start-100 translate-middle" onclick="window.eliminarFila(' + label.categoria_id + ')" style="z-index:2;font-size:0.55rem;background:rgba(255,255,255,0.8)"><i class="bi bi-x-circle"></i></button>'
                        + '</th>';
                } else {
                    tbodyHtml += '<th data-categoria-id="' + label.categoria_id + '" class="position-relative" style="background:#f8f9fa;min-width:110px;font-weight:400">'
                        + '<div contenteditable="true" data-categoria-id="' + label.categoria_id + '" onblur="window.renombrarHeader(this, ' + label.categoria_id + ')" class="px-1 small">' + esc(label.nombre) + '</div>'
                        + '<button class="btn btn-sm text-primary p-0 position-absolute top-0 start-100 translate-middle" onclick="window.agregarHijo(' + label.categoria_id + ')" style="z-index:2;font-size:0.55rem;background:rgba(255,255,255,0.8)"><i class="bi bi-plus-circle"></i></button>'
                        + '<button class="btn btn-sm text-danger p-0 position-absolute bottom-0 start-100 translate-middle" onclick="window.eliminarFila(' + label.categoria_id + ')" style="z-index:2;font-size:0.55rem;background:rgba(255,255,255,0.8)"><i class="bi bi-x-circle"></i></button>'
                        + '</th>';
                }
            }
            const dataRow = data[ri] || [];
            for (const cel of dataRow) {
                tbodyHtml += '<td class="position-relative" data-vertical-id="' + cel.cat_vertical_id + '" data-horizontal-id="' + cel.cat_horizontal_id + '" data-dato-id="' + (cel.dato_id || '') + '">'
                    + '<div contenteditable="true" onblur="window.guardarCelda(this, ' + cel.cat_vertical_id + ', ' + cel.cat_horizontal_id + ')">'
                    + esc(cel.valor || '') + '</div></td>';
            }
            const lastLabel = labelRow[labelRow.length - 1];
            tbodyHtml += '<td class="text-center"><button class="btn btn-sm text-danger py-0 px-1" onclick="window.eliminarFila(' + (lastLabel ? lastLabel.categoria_id : 'null') + ')" title="Eliminar fila"><i class="bi bi-x-circle"></i></button></td></tr>';
        }

        const footerCols = numLabelCols + horizontales.length + 1;
        tbodyHtml += '<tr class="table-light">'
            + '<td colspan="' + numLabelCols + '"><button class="btn btn-sm btn-outline-success py-0" onclick="window.agregarFila()"><i class="bi bi-plus-lg me-1"></i> Fila</button></td>'
            + '<td colspan="' + (horizontales.length + 1) + '" class="text-muted">'
            + '<a href="#" onclick="window.importarCSV(); return false" class="text-decoration-none small"><i class="bi bi-upload"></i> Importar CSV</a>'
            + '<input type="file" id="input-csv" accept=".csv,.txt" style="display:none">'
            + ' <span class="vr mx-2"></span> '
            + '<span class="small">Ctrl+V para pegar</span></td></tr>';

        tbody.innerHTML = tbodyHtml;
        renderSelection();
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

            if (e.shiftKey) {
                e.preventDefault();
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

            const anchor = getPasteAnchor();

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
    });

    // === GLOBAL FUNCTIONS ===
    window.guardarCelda = guardarCelda;

    window.renombrarHeader = function(el, id) {
        renombrar(el, id);
    };

    window.agregarHijo = function(padreId) {
        saveAllBeforeAction();
        api('/hijo', { method: 'POST', body: { padre_id: padreId } }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Hijo agregado'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.agregarFila = function() {
        saveAllBeforeAction();
        api('/fila', { method: 'POST' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); status('Fila agregada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.agregarColumna = function() {
        saveAllBeforeAction();
        api('/columna', { method: 'POST' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); status('Columna agregada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.eliminarFila = function(id) {
        saveAllBeforeAction();
        if (!confirm('¿Eliminar esta fila?')) return;
        api('/fila/' + id, { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Fila eliminada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.eliminarColumna = function(id) {
        saveAllBeforeAction();
        if (!confirm('¿Eliminar esta columna?')) return;
        api('/columna/' + id, { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Columna eliminada'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.limpiarDataset = function() {
        saveAllBeforeAction();
        if (!confirm('¿Eliminar todo el dataset?')) return;
        api('/limpiar', { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Dataset limpiado'); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
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

    // === IMPORT (empty state) ===
    document.getElementById('btn-importar-vacio')?.addEventListener('click', function() {
        document.getElementById('input-csv-vacio').click();
    });
    document.getElementById('input-csv-vacio')?.addEventListener('change', function(e) {
        importarFile(e.target);
    });

    // === IMPORT (grid state) ===
    document.addEventListener('change', function(e) {
        if (e.target.id === 'input-csv') importarFile(e.target);
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

    window.importarCSV = function() {
        document.getElementById('input-csv').click();
    };

    // === INIT ===
    if (estado.tiene_dataset) {
        renderGrid(estado);
    }
})();
</script>
