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

    function getCellCoords(el, targetEl) {
        const tag = el.tagName;
        if (tag === 'TH') {
            const idEl = (targetEl && targetEl !== el) ? targetEl.closest('[data-categoria-id]') || el : el;
            const catId = parseInt(idEl.dataset.categoriaId);
            if (!catId) return null;
            const thead = el.closest('thead');
            if (thead) {
                const ci = estado.horizontales.findIndex(h => h.categoria_id === catId);
                return { type: 'horizontal', ri: -1, ci, vId: null, hId: catId };
            }
            if (el.closest('tbody')) {
                const ri = estado.verticales.findIndex(v => v.categoria_id === catId);
                return { type: 'vertical', ri, ci: -1, vId: catId, hId: null };
            }
            return null;
        }
        const tr = el.closest('tr');
        const tbody = tr ? tr.closest('tbody') : null;
        if (!tbody) return null;
        const ri = Array.from(tbody.children).indexOf(tr);
        if (ri < 0 || ri >= estado.verticales.length) return null;
        const ci = Array.from(tr.children).indexOf(el) - 1;
        if (ci < 0 || ci >= estado.horizontales.length) return null;
        return { type: 'cell', ri, ci, vId: estado.verticales[ri]?.categoria_id, hId: estado.horizontales[ci]?.categoria_id };
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
        if (sel.startRi === -1 && sel.startCi >= 0) {
            const tr = document.querySelector('#dataset-table thead tr');
            if (!tr) return;
            for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
                const th = tr.children[ci + 1];
                if (th && th.tagName === 'TH') th.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
            }
            return;
        }
        if (sel.startCi === -1 && sel.startRi >= 0) {
            const tbody = document.getElementById('tbody');
            for (let ri = sel.startRi; ri <= sel.endRi; ri++) {
                const tr = tbody.children[ri];
                if (!tr) break;
                const th = tr.children[0];
                if (th && th.tagName === 'TH') th.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
            }
            return;
        }
        const tbody = document.getElementById('tbody');
        for (let ri = sel.startRi; ri <= sel.endRi && ri < estado.verticales.length; ri++) {
            const tr = tbody.children[ri];
            if (!tr) break;
            for (let ci = sel.startCi; ci <= sel.endCi && ci < estado.horizontales.length; ci++) {
                const td = tr.children[ci + 1];
                if (td && td.tagName === 'TD') td.classList.add('cell-selected', 'bg-primary', 'bg-opacity-10');
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

    function buildHijosMap(tree) {
        const m = {};
        function walk(nodes) {
            for (const n of nodes) {
                if (n.hijos && n.hijos.length > 0) {
                    m[n.categoria_id] = n.hijos;
                    walk(n.hijos);
                }
            }
        }
        walk(tree || []);
        return m;
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

        const { verticales, horizontales, tabla, vertical_tree, horizontal_tree } = d;
        const thead = document.getElementById('thead');
        const tbody = document.getElementById('tbody');

        for (const k in vivos) delete vivos[k];
        for (const row of tabla.slice(1)) {
            for (const cel of row.slice(1)) {
                if (cel.dato_id) {
                    vivos[cel.cat_vertical_id + '-' + cel.cat_horizontal_id] = cel;
                }
            }
        }

        document.getElementById('dimension-badge').textContent = verticales.length + ' × ' + horizontales.length;

        const vHijos = buildHijosMap(vertical_tree);
        const hHijos = buildHijosMap(horizontal_tree);

        function hijosOf(eje, catId) { return (eje === 'vertical' ? vHijos : hHijos)[catId] || null; }

        // === HEADERS ===
        let theadHtml = '<tr><th class="text-center align-middle" style="width:44px">' +
            '<button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="window.limpiarDataset()" title="Limpiar todo"><i class="bi bi-trash3"></i></button>' +
            '</th>';
        for (const h of horizontales) {
            const hijos = hijosOf('horizontal', h.categoria_id);
            if (hijos) {
                let chHtml = '';
                for (const ch of hijos) {
                    chHtml += '<div contenteditable="true" data-categoria-id="' + ch.categoria_id + '" onblur="window.renombrarHeader(this, ' + ch.categoria_id + ')" class="px-1 text-truncate small">' + esc(ch.nombre) + '</div>';
                }
                theadHtml += '<th data-categoria-id="' + h.categoria_id + '" class="position-relative" style="min-width:120px;padding:0;background:#f0f2f5">' +
                    '<div class="d-flex" style="min-height:52px">' +
                    '<div class="small fw-semibold d-flex align-items-center justify-content-center border-end px-1" style="background:#e2e6ea;width:50%">' + esc(h.nombre) + '</div>' +
                    '<div class="d-flex flex-column justify-content-center small" style="width:50%">' + chHtml + '</div>' +
                    '</div>' +
                    '<button class="btn btn-sm text-primary p-0 position-absolute top-0 end-0" onclick="window.agregarHijo(' + h.categoria_id + ')" style="z-index:2;font-size:0.6rem;background:rgba(255,255,255,0.8)"><i class="bi bi-plus-circle"></i></button>' +
                    '<button class="btn btn-sm text-danger p-0 position-absolute bottom-0 end-0" onclick="window.eliminarColumna(' + h.categoria_id + ')" style="z-index:2;font-size:0.6rem;background:rgba(255,255,255,0.8)"><i class="bi bi-x-circle"></i></button>' +
                    '</th>';
            } else {
                theadHtml += '<th data-categoria-id="' + h.categoria_id + '" class="align-middle position-relative" style="min-width:90px;background:#f0f2f5">' +
                    '<div contenteditable="true" data-categoria-id="' + h.categoria_id + '" onblur="window.renombrarHeader(this, ' + h.categoria_id + ')" class="fw-normal px-1">' + esc(h.nombre) + '</div>' +
                    '<button class="btn btn-sm text-primary p-0 position-absolute top-0 end-0" onclick="window.agregarHijo(' + h.categoria_id + ')" style="z-index:2;font-size:0.6rem;background:rgba(255,255,255,0.8)"><i class="bi bi-plus-circle"></i></button>' +
                    '<button class="btn btn-sm text-danger p-0 position-absolute bottom-0 end-0" onclick="window.eliminarColumna(' + h.categoria_id + ')" style="z-index:2;font-size:0.6rem;background:rgba(255,255,255,0.8)"><i class="bi bi-x-circle"></i></button>' +
                    '</th>';
            }
        }
        theadHtml += '<th class="text-center" style="width:36px"><button class="btn btn-sm btn-outline-primary py-0 px-1" onclick="window.agregarColumna()" title="Agregar columna"><i class="bi bi-plus-lg"></i></button></th></tr>';
        thead.innerHTML = theadHtml;

        // === BODY ===
        let tbodyHtml = '';
        for (let ri = 0; ri < verticales.length; ri++) {
            const v = verticales[ri];
            const hijos = hijosOf('vertical', v.categoria_id);
            const dataRow = tabla[ri + 1] || [];
            tbodyHtml += '<tr>';
            if (hijos) {
                let chHtml = '';
                for (const ch of hijos) {
                    chHtml += '<div contenteditable="true" data-categoria-id="' + ch.categoria_id + '" onblur="window.renombrarHeader(this, ' + ch.categoria_id + ')" class="px-1 text-truncate small">' + esc(ch.nombre) + '</div>';
                }
                tbodyHtml += '<th data-categoria-id="' + v.categoria_id + '" class="position-relative" style="background:#f8f9fa;min-width:110px;padding:0;font-weight:500">' +
                    '<div class="d-flex flex-column" style="min-height:52px">' +
                    '<div class="small fw-semibold d-flex align-items-center border-bottom px-1" style="background:#e2e6ea;flex:1">' + esc(v.nombre) + '</div>' +
                    '<div class="d-flex flex-column small" style="flex:1">' + chHtml + '</div>' +
                    '</div>' +
                    '<button class="btn btn-sm text-primary p-0 position-absolute top-0 start-100 translate-middle" onclick="window.agregarHijo(' + v.categoria_id + ')" style="z-index:2;font-size:0.6rem;background:rgba(255,255,255,0.8)"><i class="bi bi-plus-circle"></i></button>' +
                    '<button class="btn btn-sm text-danger p-0 position-absolute bottom-0 start-100 translate-middle" onclick="window.eliminarFila(' + v.categoria_id + ')" style="z-index:2;font-size:0.6rem;background:rgba(255,255,255,0.8)"><i class="bi bi-x-circle"></i></button>' +
                    '</th>';
            } else {
                tbodyHtml += '<th data-categoria-id="' + v.categoria_id + '" class="position-relative" style="background:#f8f9fa;min-width:110px;font-weight:500">' +
                    '<div contenteditable="true" data-categoria-id="' + v.categoria_id + '" onblur="window.renombrarHeader(this, ' + v.categoria_id + ')" class="text-truncate px-1">' + esc(v.nombre) + '</div>' +
                    '<button class="btn btn-sm text-primary p-0 position-absolute top-0 start-100 translate-middle" onclick="window.agregarHijo(' + v.categoria_id + ')" style="z-index:2;font-size:0.6rem;background:rgba(255,255,255,0.8)"><i class="bi bi-plus-circle"></i></button>' +
                    '<button class="btn btn-sm text-danger p-0 position-absolute bottom-0 start-100 translate-middle" onclick="window.eliminarFila(' + v.categoria_id + ')" style="z-index:2;font-size:0.6rem;background:rgba(255,255,255,0.8)"><i class="bi bi-x-circle"></i></button>' +
                    '</th>';
            }
            for (let ci = 1; ci < dataRow.length; ci++) {
                const cel = dataRow[ci] || {};
                tbodyHtml += '<td class="position-relative"><div contenteditable="true" onblur="window.guardarCelda(this, ' + (cel.cat_vertical_id || 'null') + ', ' + (cel.cat_horizontal_id || 'null') + ')">' + esc(cel.valor || '') + '</div></td>';
            }
            tbodyHtml += '<td class="text-center"><button class="btn btn-sm text-danger py-0 px-1" onclick="window.eliminarFila(' + v.categoria_id + ')" title="Eliminar fila"><i class="bi bi-x-circle"></i></button></td></tr>';
        }

        tbodyHtml += '<tr class="table-light"><td><button class="btn btn-sm btn-outline-success py-0" onclick="window.agregarFila()"><i class="bi bi-plus-lg me-1"></i>Fila</button></td>' +
            '<td colspan="' + (horizontales.length + 1) + '" class="text-muted">' +
            '<a href="#" onclick="window.importarCSV(); return false" class="text-decoration-none small"><i class="bi bi-upload"></i> Importar CSV</a>' +
            '<input type="file" id="input-csv" accept=".csv,.txt" style="display:none">' +
            ' <span class="vr mx-2"></span> ' +
            '<span class="small">Ctrl+V para pegar</span></td></tr>';

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
            const coords = getCellCoords(cell, e.target);
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
