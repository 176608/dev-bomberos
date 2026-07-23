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

    <div class="d-flex align-items-center gap-2 mb-2" id="seccion-tabs">
        <small class="text-muted me-1">Secciones:</small>
        <div class="d-flex gap-2 flex-wrap align-items-center" id="seccion-list"></div>
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
                <small id="status-text"></small>
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
#dataset-table td, #dataset-table th { vertical-align: middle; padding: 0.1rem 0.2rem; }
#dataset-table td > div, #dataset-table th > div { min-height: 26px; outline: none; padding: 0.1rem 0.2rem; border-radius: 2px; }
#dataset-table td > div:focus { box-shadow: inset 0 0 0 1px var(--bs-primary); background: #fff; }
#dataset-table .cell-selected { outline: 2.5px solid var(--bs-primary) !important; outline-offset: -0.5px; background-color: rgba(13,110,253,0.12) !important; }
.mode-diseno #dataset-table .cell-selected { outline: 3px solid var(--bs-primary) !important; outline-offset: 0px; background-color: rgba(13,110,253,0.18) !important; }
#dataset-table .cell-paste-target { outline: 3px dashed #198754 !important; outline-offset: -1px; background-color: rgba(25,135,84,0.10) !important; }
#dataset-table .cell-anchor { background: var(--bs-primary) !important; color: #fff; }
#dataset-table .cell-anchor > div { color: #fff; }

/* Category cell layout */
.cat-cell .cat-inner { min-height: 30px; gap: 2px; }
.cat-cell .cat-name { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: text; padding: 0.1rem 0.15rem; }
.cat-cell .cat-name:focus { box-shadow: inset 0 0 0 1px var(--bs-primary); background: #fff; border-radius: 2px; }
.mode-datos .cat-cell .cat-name { width: 100%; text-align: center; }
.mode-datos .cat-actions { display: none !important; }
.mode-diseno .cat-actions { display: flex !important; }

/* Extra small button group */
.btn-group-xs .btn { padding: 0.1rem 0.25rem; font-size: 0.6rem; line-height: 1.3; border-radius: 0; }
.btn-group-xs .btn:first-child { border-radius: 0.15rem 0 0 0.15rem; }
.btn-group-xs .btn:last-child { border-radius: 0 0.15rem 0.15rem 0; }
.btn-group-xs .btn i { font-size: 0.6rem; }

/* Status bar */
#status-bar .badge { font-size: 0.7rem; }
#status-bar #status-text { font-size: 0.8rem; }
#status-bar.status-flash { background: #d1e7fd !important; transition: background 0.3s; }
#mode-tabs .btn-group .btn.active { background: var(--bs-primary); color: #fff; }

/* Mode visibility */
.mode-datos .edit-only { display: none !important; }
.mode-datos .datos-only { display: inline-flex !important; }
.mode-diseno .datos-only { display: none !important; }
.mode-diseno .edit-only { display: inline-flex !important; }

/* Data cells right-aligned in datos mode */
.mode-datos #dataset-table td[data-vertical-id] > div { text-align: right; }
.mode-diseno #dataset-table td[data-vertical-id] > div { cursor: default; }
</style>

<script>
(function() {
    // ============ ERROR CODES ============
    const ERR = {
        CELDA: 'ERR-CEL',
        RENOM: 'ERR-REN',
        PST_DATOS: 'ERR-PDA',
        PST_HCAT: 'ERR-PHC',
        PST_VCAT: 'ERR-PVC',
        PST_CEL: 'ERR-PCE',
        PST_FULL: 'ERR-PFL',
        HIJOS: 'ERR-HIJ',
        CLON: 'ERR-CLO',
        FILA: 'ERR-FIL',
        COL: 'ERR-COL',
        E_FILA: 'ERR-EFL',
        E_COL: 'ERR-ECL',
        DATOS: 'ERR-DAT',
        PIVOT: 'ERR-PVT',
        GENERAR: 'ERR-GEN',
        REGEN: 'ERR-RGN',
        IMPORT: 'ERR-IMP',
        SECCION: 'ERR-SEC',
    };

    // ============ CONFIG ============
    const CUADRO_ID = {{ $cuadro->cuadro_id }};
    const CSRF = '{{ csrf_token() }}';
    const BASE = '{{ url("/sgiem/admin/cuadros") }}/' + CUADRO_ID + '/dataset';
    const IS_DEV = @json(auth()->user()?->hasRole('Desarrollador') ?? false);
    function log(...args) { if (IS_DEV) console.log('[Dataset]', ...args); }

    let estado = @json($estadoInicial);
    let currentMode = 'diseno';

    // ============ DATA VIVOS (cell cache) ============
    const vivos = {};

    // ============ SELECTION STATE ============
    const sel = { active: false, startRi: -1, startCi: -1, endRi: -1, endCi: -1, anchorVi: null, anchorHi: null };
    const pointer = { down: false, startRi: -1, startCi: -1, startX: 0, startY: 0, dragging: false };
    let lastCell = null;

    // ============ UI HELPERS ============
    function alerta(msg, tipo) {
        document.getElementById('alerts').innerHTML =
            '<div class="alert alert-' + (tipo || 'danger') + ' alert-dismissible fade show">' + msg +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }

    function status(msg) {
        const el = document.getElementById('status-text');
        if (!el) return;
        el.textContent = msg || '';
        const bar = document.getElementById('status-bar');
        if (bar && msg) {
            bar.classList.add('status-flash');
            clearTimeout(bar._flashTimer);
            bar._flashTimer = setTimeout(() => bar.classList.remove('status-flash'), 2500);
        }
    }

    function esc(s) {
        if (!s) return '';
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function hexToRgba(hex, alpha) {
        if (!/^#[0-9a-f]{6}$/i.test(hex)) return null;
        return 'rgba(' + parseInt(hex.slice(1,3),16) + ',' + parseInt(hex.slice(3,5),16) + ',' + parseInt(hex.slice(5,7),16) + ',' + alpha + ')';
    }

    function saveAllBeforeAction() {
        const focused = document.querySelector('#dataset-table div:focus');
        if (focused) focused.blur();
    }

    function catActionsHtml(catId, esHijo, numHijos, esParent, esVertical) {
        var size = esHijo ? 'xs' : 'sm';
        var h = '<div class="cat-actions edit-only flex-shrink-0"><div class="btn-group btn-group-' + size + '">';
        if (!esHijo) {
            var c = esVertical ? 'success' : 'primary';
            h += '<button class="btn btn-' + c + '" title="Añadir hijo" onclick="window.agregarHijo(' + catId + ')"><i class="bi bi-plus-lg"></i></button>';
        }
        if (esParent && numHijos >= 2)
            h += '<button class="btn btn-info" title="Clonar categoría" onclick="window.clonarCategoria(' + catId + ')"><i class="bi bi-copy"></i></button>';
        h += '<button class="btn btn-danger" title="' + (esVertical ? 'Eliminar fila' : 'Eliminar columna') + '" onclick="window.' + (esVertical ? 'eliminarFila' : 'eliminarColumna') + '(' + catId + ')"><i class="bi bi-x-lg"></i></button>';
        h += '</div></div>';
        return h;
    }

    // ============ API ============
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

    // ============ MODE SWITCH ============
    window.switchMode = function(mode) {
        currentMode = mode;
        clearSelection();
        document.querySelectorAll('[data-mode]').forEach(btn =>
            btn.classList.toggle('active', btn.dataset.mode === mode)
        );
        const c = document.getElementById('grid-container');
        c.classList.toggle('mode-datos', mode === 'datos');
        c.classList.toggle('mode-diseno', mode === 'diseno');
        document.getElementById('mode-hint').textContent =
            mode === 'diseno'
                ? 'Diseño: estructura de filas, columnas y nombres'
                : 'Datos: editar celdas. También puede renombrar categorías y pivote';
        document.querySelectorAll('#dataset-table .cat-name, #dataset-table .pivot-label').forEach(el => el.contentEditable = 'true');
        document.querySelectorAll('#dataset-table td[data-vertical-id] > div').forEach(el => {
            el.contentEditable = mode === 'datos';
            if (mode === 'diseno') el.blur();
        });
        status(mode === 'diseno' ? 'Modo Diseño' : 'Modo Datos');
    };

    // ============ CELL COORDINATES ============
    function getCellCoords(el) {
        const th = el.closest('th');
        const td = el.closest('td');
        if (th && th.closest('thead')) {
            const catId = parseInt(th.dataset.categoriaId);
            if (catId) {
                const ci = estado.horizontales.findIndex(h => h.categoria_id === catId);
                if (ci >= 0) return { type: 'horizontal', ri: -1, ci, vId: null, hId: catId };
            }
            const colIdx = parseInt(th.dataset.colIndex);
            if (!isNaN(colIdx) && estado.horizontales[colIdx])
                return { type: 'horizontal', ri: -1, ci: colIdx, vId: null, hId: estado.horizontales[colIdx].categoria_id };
            return null;
        }
        if (th && th.closest('tbody')) {
            const catId = parseInt(th.dataset.categoriaId);
            if (catId) {
                const ri = estado.verticales.findIndex(v => v.categoria_id === catId);
                if (ri >= 0) return { type: 'vertical', ri, ci: -1, vId: catId, hId: null };
            }
            const rowIdx = parseInt(th.dataset.rowIndex);
            if (!isNaN(rowIdx) && estado.verticales[rowIdx])
                return { type: 'vertical', ri: rowIdx, ci: -1, vId: estado.verticales[rowIdx].categoria_id, hId: null };
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

    // ============ SELECTION ============
    function setSelection(minRi, minCi, maxRi, maxCi) {
        sel.active = true;
        sel.startRi = minRi; sel.startCi = minCi; sel.endRi = maxRi; sel.endCi = maxCi;
        sel.anchorVi = minRi >= 0 ? estado.verticales[minRi]?.categoria_id : null;
        sel.anchorHi = minCi >= 0 ? estado.horizontales[minCi]?.categoria_id : null;
        renderSelection();
    }

    function clearSelection() {
        sel.active = false;
        document.querySelectorAll('#dataset-table .cell-selected').forEach(el =>
            el.classList.remove('cell-selected', 'bg-primary', 'bg-opacity-10')
        );
    }

    function renderSelection() {
        document.querySelectorAll('#dataset-table .cell-selected').forEach(el =>
            el.classList.remove('cell-selected', 'bg-primary', 'bg-opacity-10')
        );
        if (!sel.active) return;
        const thead = document.getElementById('thead');
        const tbody = document.getElementById('tbody');
        if (!tbody) return;

        const highlightVP = ri => {
            const v = estado.verticales[ri];
            if (v?.padre_id) tbody.querySelector('th[data-categoria-id="' + v.padre_id + '"]')?.classList.add('cell-selected','bg-primary','bg-opacity-10');
        };
        const highlightHP = ci => {
            const h = estado.horizontales[ci];
            if (h?.padre_id) thead?.querySelector('th[data-categoria-id="' + h.padre_id + '"]')?.classList.add('cell-selected','bg-primary','bg-opacity-10');
        };

        // Column selection
        if (sel.startRi === -1 && sel.startCi >= 0) {
            thead?.querySelectorAll('th[data-col-index]').forEach(th => {
                const ci = parseInt(th.dataset.colIndex);
                if (ci >= sel.startCi && ci <= sel.endCi) th.classList.add('cell-selected','bg-primary','bg-opacity-10');
            });
            for (let ri = 0; ri < estado.data.length; ri++) {
                const tr = tbody.children[ri];
                if (!tr) break;
                for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
                    const hId = estado.horizontales[ci]?.categoria_id;
                    if (!hId) continue;
                    tr.querySelector('td[data-horizontal-id="' + hId + '"]')?.classList.add('cell-selected','bg-primary','bg-opacity-10');
                    highlightHP(ci);
                }
            }
            return;
        }

        // Row selection
        if (sel.startCi === -1 && sel.startRi >= 0) {
            for (let ri = sel.startRi; ri <= sel.endRi; ri++) {
                const tr = tbody.children[ri];
                if (!tr) break;
                tr.querySelectorAll('th').forEach(th => th.classList.add('cell-selected','bg-primary','bg-opacity-10'));
                tr.querySelectorAll('td[data-horizontal-id]').forEach(td => td.classList.add('cell-selected','bg-primary','bg-opacity-10'));
                highlightVP(ri);
            }
            return;
        }

        // Cell selection
        if (thead) for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
            const hId = estado.horizontales[ci]?.categoria_id;
            if (!hId) continue;
            thead.querySelector('th[data-categoria-id="' + hId + '"]')?.classList.add('cell-selected','bg-primary','bg-opacity-10');
            highlightHP(ci);
        }
        for (let ri = sel.startRi; ri <= sel.endRi && ri < estado.data.length; ri++) {
            const tr = tbody.children[ri];
            if (!tr) break;
            tr.querySelectorAll('th').forEach(th => th.classList.add('cell-selected','bg-primary','bg-opacity-10'));
            highlightVP(ri);
            for (let ci = sel.startCi; ci <= sel.endCi; ci++) {
                const hId = estado.horizontales[ci]?.categoria_id;
                if (!hId) continue;
                tr.querySelector('td[data-horizontal-id="' + hId + '"]')?.classList.add('cell-selected','bg-primary','bg-opacity-10');
            }
        }
    }

    // ============ CELL SAVE / RENAME ============
    window.guardarCelda = function(el, vId, hId) {
        const val = el.textContent.trim();
        const dato = vivos[vId + '-' + hId];
        if (!dato) return;
        log('guardarCelda', { vId, hId, val });
        status('Guardando...');
        api('/celda/' + dato.dato_id, { method: 'PUT', body: { valor: val } })
            .then(j => { if (j.success) { dato.valor = val; status('✓ Guardado'); } else alerta(j.message); })
            .catch(() => alerta('Error de red [' + ERR.CELDA + ']'));
    };

    function renombrar(el, id) {
        const nombre = el.textContent.trim();
        if (!nombre) return;
        status('Guardando...');
        api('/categoria/' + id, { method: 'PUT', body: { nombre } })
            .then(j => {
                if (j.success) {
                    if (j.categoria?._renombrado) { el.textContent = j.categoria.nombre; alerta('Ya existía, se renombró a <strong>' + esc(j.categoria.nombre) + '</strong>', 'warning'); }
                    status('✓ Guardado');
                } else { alerta(j.message); renderGrid(estado); }
            })
            .catch(() => alerta('Error [' + ERR.RENOM + ']'));
    }
    window.renombrarHeader = renombrar;

    // ============ PASTE ANCHOR ============
    function getPasteAnchor() {
        if (!sel.active) return lastCell;
        if (sel.startRi === -1 && sel.startCi >= 0) {
            const mc = Math.min(sel.startCi, sel.endCi);
            return { type: 'horizontal', vId: null, hId: estado.horizontales[mc]?.categoria_id };
        }
        if (sel.startCi === -1 && sel.startRi >= 0) {
            const mr = Math.min(sel.startRi, sel.endRi);
            return { type: 'vertical', vId: estado.verticales[mr]?.categoria_id, hId: null };
        }
        const mr = Math.min(sel.startRi, sel.endRi), mc = Math.min(sel.startCi, sel.endCi);
        return { type: 'cell', vId: estado.verticales[mr]?.categoria_id, hId: estado.horizontales[mc]?.categoria_id };
    }

    function buildPasteBody(clipGrid, anchor) {
        const body = { grid: clipGrid };
        if (anchor?.vId) body.start_vertical_id = anchor.vId;
        if (anchor?.hId) body.start_horizontal_id = anchor.hId;
        body.verticales = estado.verticales.map(v => v.categoria_id);
        body.horizontales = estado.horizontales.map(h => h.categoria_id);
        body.seccion_id = estado.seccion_activa_id;
        return body;
    }

    function doPaste(anchor, clipGrid, errCode) {
        saveAllBeforeAction();
        status('Pegando...');
        api('/paste', { method: 'POST', body: buildPasteBody(clipGrid, anchor) })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ Pegado'); } else alerta(j.message); })
            .catch(() => alerta('Error de red [' + errCode + ']'));
    }

    // ============ SECCIONES ============
    function renderSecciones() {
        const list = document.getElementById('seccion-list');
        if (!list) return;
        list.innerHTML = '';
        const secciones = estado.secciones || [];
        secciones.forEach((s, idx) => {
            const active = s.seccion_id === estado.seccion_activa_id;
            const grp = document.createElement('div');
            grp.className = 'btn-group btn-group-sm';
            grp.setAttribute('role', 'group');
            // Rename button
            const renBtn = document.createElement('button');
            renBtn.className = 'btn btn-primary';
            renBtn.innerHTML = '<i class="bi bi-pencil"></i>';
            renBtn.title = 'Renombrar sección';
            renBtn.onclick = function(e) { e.stopPropagation(); window.renombrarSeccion(s.seccion_id, s.nombre); };
            // Name / select button
            const nameBtn = document.createElement('button');
            nameBtn.className = 'btn ' + (active ? 'btn-primary' : 'btn-outline-primary');
            nameBtn.textContent = s.nombre;
            nameBtn.title = 'Seleccionar sección';
            nameBtn.onclick = () => switchSeccion(s.seccion_id);
            // Append rename + name
            grp.appendChild(renBtn);
            grp.appendChild(nameBtn);
            // Basal: + button, others: reorder + delete
            if (idx === 0) {
                const addBtn = document.createElement('button');
                addBtn.className = 'btn btn-outline-primary';
                addBtn.innerHTML = '<i class="bi bi-plus-lg"></i>';
                addBtn.title = 'Agregar sección';
                addBtn.onclick = function(e) { e.stopPropagation(); window.agregarSeccion(); };
                grp.appendChild(addBtn);
            } else {
                const upBtn = document.createElement('button');
                upBtn.className = 'btn btn-outline-secondary';
                upBtn.innerHTML = '<i class="bi bi-caret-up-fill"></i>';
                upBtn.title = 'Subir';
                upBtn.onclick = function(e) { e.stopPropagation(); window.reordenarSeccion(s.seccion_id, 'up'); };
                grp.appendChild(upBtn);
                if (idx < secciones.length - 1) {
                    const dnBtn = document.createElement('button');
                    dnBtn.className = 'btn btn-outline-secondary';
                    dnBtn.innerHTML = '<i class="bi bi-caret-down-fill"></i>';
                    dnBtn.title = 'Bajar';
                    dnBtn.onclick = function(e) { e.stopPropagation(); window.reordenarSeccion(s.seccion_id, 'down'); };
                    grp.appendChild(dnBtn);
                }
                const delBtn = document.createElement('button');
                delBtn.className = 'btn btn-danger';
                delBtn.innerHTML = '<i class="bi bi-trash3"></i>';
                delBtn.title = 'Eliminar sección';
                delBtn.onclick = function(e) { e.stopPropagation(); window.eliminarSeccion(s.seccion_id); };
                grp.appendChild(delBtn);
            }
            list.appendChild(grp);
        });
    }

    function switchSeccion(seccionId) {
        if (seccionId === estado.seccion_activa_id) return;
        status('Cargando...');
        api('/seccion/' + seccionId + '/data')
            .then(j => { if (j.success) { clearSelection(); renderGrid(estado); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.SECCION + ']'));
    }

    // ============ RENDER GRID ============
    function renderGrid(d) {
        if (!d.tiene_dataset) {
            document.getElementById('grid-container').style.display = 'none';
            const st = document.getElementById('seccion-tabs');
            if (st) st.style.display = 'none';
            const es = document.getElementById('empty-state');
            if (es) es.style.display = '';
            return;
        }
        document.getElementById('grid-container').style.display = '';
        const st = document.getElementById('seccion-tabs');
        if (st) st.style.display = '';
        const es = document.getElementById('empty-state');
        if (es) es.style.display = 'none';

        renderSecciones();

        const { verticales, horizontales, headers, labels, data } = d;
        const thead = document.getElementById('thead');
        const tbody = document.getElementById('tbody');

        for (const k in vivos) delete vivos[k];
        for (const row of data) for (const cel of row)
            if (cel.dato_id) vivos[cel.cat_vertical_id + '-' + cel.cat_horizontal_id] = cel;

        document.getElementById('dimension-badge').textContent = verticales.length + ' × ' + horizontales.length;
        const numLabelCols = labels.length ? Math.max(...labels.map(r => r.length), 1) : 1;

        // — HEADERS —
        let theadHtml = '';
        if (headers.length === 0) {
            theadHtml = '<tr><th class="text-center align-middle" style="background:#f0f2f5">'
                + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.limpiarDataset()" title="Limpiar todo"><i class="bi bi-trash3"></i></button></th>'
                + '<th class="text-center" style="background:#f0f2f5">'
                + '<button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.agregarColumna()" title="Agregar columna"><i class="bi bi-plus"></i></button></th></tr>';
        } else {
            for (let ri = 0, numHR = headers.length; ri < numHR; ri++) {
                theadHtml += '<tr>';
                for (const cell of headers[ri]) {
                    if (cell.tipo === 'corner') {
                        theadHtml += '<th rowspan="' + (cell.rowspan || numHR) + '" colspan="' + numLabelCols + '" class="text-center align-middle" style="background:#f0f2f5">'
                            + '<div class="d-flex flex-column align-items-center gap-1">'
                            + '<button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.limpiarDataset()" title="Limpiar todo"><i class="bi bi-trash3"></i></button>'
                            + '<span class="pivot-label editable-header text-muted small" style="font-size:0.65rem" onblur="window.guardarPivot(this)">' + esc(estado.pivot_label || 'PIVOTE') + '</span></div></th>';
                    } else if (cell.tipo === 'parent') {
                        theadHtml += '<th colspan="' + cell.colspan + '" data-categoria-id="' + cell.categoria_id + '" data-col-index="' + cell.col_index + '" data-es-hijo="' + (cell.es_hijo ? 1 : 0) + '" class="cat-cell align-middle text-center" style="background:#e2e6ea;font-weight:600">'
                            + '<div class="d-flex align-items-center w-100 cat-inner">'
                            + '<div contenteditable="true" data-categoria-id="' + cell.categoria_id + '" data-es-hijo="' + (cell.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + cell.categoria_id + ')" class="cat-name flex-grow-1 fw-semibold">' + esc(cell.nombre) + '</div>'
                            + catActionsHtml(cell.categoria_id, cell.es_hijo, cell.num_hijos || 0, true, false)
                            + '</div></th>';
                    } else {
                        theadHtml += '<th data-categoria-id="' + cell.categoria_id + '" data-col-index="' + cell.col_index + '" data-es-hijo="' + (cell.es_hijo ? 1 : 0) + '" class="cat-cell align-middle text-center" style="background:#f0f2f5">'
                            + '<div class="d-flex align-items-center w-100 cat-inner">'
                            + '<div contenteditable="true" data-categoria-id="' + cell.categoria_id + '" data-es-hijo="' + (cell.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + cell.categoria_id + ')" class="cat-name flex-grow-1">' + esc(cell.nombre) + '</div>'
                            + catActionsHtml(cell.categoria_id, cell.es_hijo, 0, false, false)
                            + '</div></th>';
                    }
                }
                if (ri === 0) theadHtml += '<th rowspan="' + numHR + '" class="text-center"><button class="btn btn-sm btn-outline-primary rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.agregarColumna()" title="Agregar columna"><i class="bi bi-plus"></i></button></th>';
                theadHtml += '</tr>';
            }
        }
        thead.innerHTML = theadHtml;

        // — BODY —
        let tbodyHtml = '';
        for (let ri = 0; ri < labels.length; ri++) {
            tbodyHtml += '<tr>';
            for (const label of labels[ri]) {
                if (label.tipo === 'parent' && label.rowspan > 1) {
                    tbodyHtml += '<th rowspan="' + label.rowspan + '" data-categoria-id="' + label.categoria_id + '" data-row-index="' + label.row_index + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" class="cat-cell align-middle text-center" style="background:#f8f9fa;font-weight:600">'
                        + '<div class="d-flex align-items-center w-100 cat-inner">'
                        + '<div contenteditable="true" data-categoria-id="' + label.categoria_id + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + label.categoria_id + ')" class="cat-name flex-grow-1 fw-semibold">' + esc(label.nombre) + '</div>'
                        + catActionsHtml(label.categoria_id, label.es_hijo, label.num_hijos || 0, true, true)
                        + '</div></th>';
                } else if (label.tipo === 'parent') {
                    tbodyHtml += '<th data-categoria-id="' + label.categoria_id + '" data-row-index="' + label.row_index + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" class="cat-cell align-middle text-center" style="background:#f8f9fa;font-weight:600">'
                        + '<div class="d-flex align-items-center w-100 cat-inner">'
                        + '<div contenteditable="true" data-categoria-id="' + label.categoria_id + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + label.categoria_id + ')" class="cat-name flex-grow-1 fw-semibold">' + esc(label.nombre) + '</div>'
                        + catActionsHtml(label.categoria_id, label.es_hijo, 0, false, true)
                        + '</div></th>';
                } else {
                    tbodyHtml += '<th data-categoria-id="' + label.categoria_id + '" data-row-index="' + label.row_index + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" class="cat-cell align-middle text-center" style="background:#f8f9fa">'
                        + '<div class="d-flex align-items-center w-100 cat-inner">'
                        + '<div contenteditable="true" data-categoria-id="' + label.categoria_id + '" data-es-hijo="' + (label.es_hijo ? 1 : 0) + '" onblur="window.renombrarHeader(this, ' + label.categoria_id + ')" class="cat-name flex-grow-1">' + esc(label.nombre) + '</div>'
                        + catActionsHtml(label.categoria_id, label.es_hijo, 0, false, true)
                        + '</div></th>';
                }
            }
            for (const cel of (data[ri] || []))
                tbodyHtml += '<td data-vertical-id="' + cel.cat_vertical_id + '" data-horizontal-id="' + cel.cat_horizontal_id + '" data-dato-id="' + (cel.dato_id || '') + '">'
                    + '<div contenteditable="true" onblur="window.guardarCelda(this, ' + cel.cat_vertical_id + ', ' + cel.cat_horizontal_id + ')">' + esc(cel.valor || '') + '</div></td>';
            tbodyHtml += '<td></td></tr>';
        }
        tbodyHtml += '<tr class="table-light"><td colspan="' + numLabelCols + '"><button class="btn btn-sm btn-outline-success rounded-circle p-0 d-inline-flex align-items-center justify-content-center edit-only" style="width:24px;height:24px;font-size:0.65rem" onclick="window.agregarFila()" title="Agregar fila"><i class="bi bi-plus"></i></button> <span class="edit-only small text-muted">Fila</span></td><td colspan="' + (horizontales.length + 1) + '"></td></tr>';
        tbody.innerHTML = tbodyHtml;

        // — Theme color —
        const color = (window.estado && window.estado.tema_color) || null;
        let styleEl = document.getElementById('tema-color-style');
        if (color && hexToRgba(color, 0.5)) {
            const childBg = hexToRgba(color, 0.5), cellBg = hexToRgba(color, 0.12);
            if (!styleEl) { styleEl = document.createElement('style'); styleEl.id = 'tema-color-style'; document.head.appendChild(styleEl); }
            styleEl.textContent =
                '#dataset-table .cat-cell .cat-name{background:' + color + ';color:#fff;border-radius:3px;padding:1px 6px}'
                + '#dataset-table .cat-cell .cat-name[data-es-hijo="1"]{background:' + childBg + ';border-radius:3px;padding:1px 6px}'
                + '#dataset-table tbody td{background:' + cellBg + '}';
        } else if (styleEl) styleEl.textContent = '';
        renderSelection();
    }

    // ============ POINTER EVENTS FOR CELL SELECTION ============
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('dataset-table');
        const tbody = document.getElementById('tbody');

        table.addEventListener('pointerdown', function(e) {
            if (e.target.closest('button, a, input')) return;
            document.querySelectorAll('#dataset-table .cell-paste-target').forEach(el => el.classList.remove('cell-paste-target'));
            const cell = e.target.closest('td, th');
            if (!cell) return;
            const coords = getCellCoords(cell);
            if (!coords) return;
            if (currentMode === 'datos' && coords.type !== 'cell') return;

            if (e.shiftKey) {
                e.preventDefault();
                if (currentMode === 'datos' && coords.type !== 'cell') return;
                const type = coords.type;
                const prevType = sel.active ? (sel.startRi === -1 ? 'horizontal' : sel.startCi === -1 ? 'vertical' : 'cell') : type;
                if (type !== prevType) return;
                if (type === 'horizontal') {
                    const mc = sel.active ? Math.min(sel.startCi, coords.ci) : coords.ci;
                    setSelection(-1, mc, -1, sel.active ? Math.max(sel.startCi, coords.ci) : coords.ci);
                } else if (type === 'vertical') {
                    const mr = sel.active ? Math.min(sel.startRi, coords.ri) : coords.ri;
                    setSelection(mr, -1, sel.active ? Math.max(sel.startRi, coords.ri) : coords.ri, -1);
                } else {
                    const mr = sel.active ? Math.min(sel.startRi, coords.ri) : coords.ri;
                    const xr = sel.active ? Math.max(sel.startRi, coords.ri) : coords.ri;
                    const mc = sel.active ? Math.min(sel.startCi, coords.ci) : coords.ci;
                    setSelection(mr, mc, xr, sel.active ? Math.max(sel.startCi, coords.ci) : coords.ci);
                }
                return;
            }

            lastCell = { type: coords.type, vId: coords.vId, hId: coords.hId };
            if (currentMode === 'datos' && coords.type === 'cell' && coords.vId && coords.hId) {
                const vCat = estado.verticales.find(v => v.categoria_id === coords.vId);
                const hCat = estado.horizontales.find(h => h.categoria_id === coords.hId);
                if (vCat && hCat) status('Fila: "' + vCat.nombre + '" | Columna: "' + hCat.nombre + '"');
            }
            if (currentMode === 'diseno' && coords.type !== 'cell') {
                document.querySelectorAll('#dataset-table .cell-selected').forEach(c => c.classList.remove('cell-selected'));
                cell.classList.add('cell-selected');
            }
            pointer.down = true;
            pointer.startRi = coords.ri; pointer.startCi = coords.ci;
            pointer.startX = e.clientX; pointer.startY = e.clientY;
            pointer.dragging = false;
        });

        document.addEventListener('pointermove', function(e) {
            if (!pointer.down) return;
            if (!pointer.dragging && (Math.abs(e.clientX - pointer.startX) > 4 || Math.abs(e.clientY - pointer.startY) > 4)) {
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
            if (coords.type !== startType || (currentMode === 'datos' && startType !== 'cell')) return;
            const mc = Math.min(pointer.startCi, coords.ci), xc = Math.max(pointer.startCi, coords.ci);
            const mr = Math.min(pointer.startRi, coords.ri), xr = Math.max(pointer.startRi, coords.ri);
            if (startType === 'horizontal') setSelection(-1, mc, -1, xc);
            else if (startType === 'vertical') setSelection(mr, -1, xr, -1);
            else setSelection(mr, mc, xr, xc);
        });

        document.addEventListener('pointerup', function(e) {
            if (pointer.dragging) {
                document.body.style.userSelect = '';
                pointer.down = false; pointer.dragging = false;
                if (sel.active) {
                    if (sel.startRi === -1) status('Selección: ' + (sel.endCi - sel.startCi + 1) + ' columnas');
                    else if (sel.startCi === -1) status('Selección: ' + (sel.endRi - sel.startRi + 1) + ' filas');
                    else status('Selección: ' + (sel.endRi - sel.startRi + 1) + '×' + (sel.endCi - sel.startCi + 1) + ' celdas');
                }
            } else if (pointer.down) { pointer.down = false; clearSelection(); }
        });

        // Click outside table -> save focused cell
        document.addEventListener('click', function(e) {
            const table = document.getElementById('dataset-table');
            if (table && !table.contains(e.target)) {
                const focused = document.querySelector('#dataset-table div:focus');
                if (focused) focused.blur();
            }
        });

        // ============ PASTE ============
        function highlightPasteTarget(anchor) {
            document.querySelectorAll('#dataset-table .cell-paste-target').forEach(el => el.classList.remove('cell-paste-target'));
            if (!anchor) return;
            let el = null;
            if (anchor.type === 'horizontal' && anchor.hId)
                el = document.querySelector('#thead th[data-categoria-id="' + anchor.hId + '"]');
            else if (anchor.type === 'vertical' && anchor.vId)
                el = document.querySelector('#tbody th[data-categoria-id="' + anchor.vId + '"]');
            else if (anchor.type === 'cell' && anchor.vId && anchor.hId) {
                el = document.querySelector('#tbody td[data-vertical-id="' + anchor.vId + '"][data-horizontal-id="' + anchor.hId + '"]');
                if (!el) el = document.querySelector('#tbody th[data-categoria-id="' + anchor.vId + '"]');
            }
            if (el) el.classList.add('cell-paste-target');
        }

        table.addEventListener('paste', function(e) {
            const text = (e.clipboardData || window.clipboardData).getData('text');
            if (!text.trim()) return;
            e.preventDefault();
            const clipGrid = text.split('\n').filter(r => r.trim()).map(r => r.split('\t').map(c => c.trim()));
            if (clipGrid.length === 0) return;

            const dims = clipGrid.length + '×' + (clipGrid[0]?.length || 1);

            // Datos mode: solo celdas
            if (currentMode === 'datos') {
                const anchor = sel.active ? getPasteAnchor() : lastCell;
                if (!anchor || anchor.type !== 'cell' || !anchor.vId || !anchor.hId) {
                    status('Clipboard: ' + dims + ' — Seleccioná una celda para pegar');
                    return;
                }
                highlightPasteTarget(anchor);
                status('Pegando ' + dims + ' desde ' + esc(clipGrid[0][0] || '') + '...');
                doPaste(anchor, clipGrid, ERR.PST_DATOS);
                return;
            }

            const anchor = getPasteAnchor();
            log('paste', { clipGrid, anchor });

            if (anchor?.type === 'horizontal' && anchor.hId) {
                const valores = clipGrid[0] || [];
                if (!valores.length) return;
                saveAllBeforeAction();
                highlightPasteTarget(anchor);
                status('Pegando ' + valores.length + ' columna(s) desde ' + esc(valores[0] || '') + '...');
                api('/paste-categorias', { method: 'POST', body: { eje: 'horizontal', start_categoria_id: anchor.hId, valores } })
                    .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ ' + valores.length + ' columna(s) renombrada(s)'); } else alerta(j.message); })
                    .catch(() => alerta('Error de red [' + ERR.PST_HCAT + ']'));
            } else if (anchor?.type === 'vertical' && anchor.vId) {
                const valores = clipGrid.map(r => r[0]).filter(v => v != null);
                if (!valores.length) return;
                saveAllBeforeAction();
                highlightPasteTarget(anchor);
                status('Pegando ' + valores.length + ' fila(s) desde ' + esc(valores[0] || '') + '...');
                api('/paste-categorias', { method: 'POST', body: { eje: 'vertical', start_categoria_id: anchor.vId, valores } })
                    .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ ' + valores.length + ' fila(s) renombrada(s)'); } else alerta(j.message); })
                    .catch(() => alerta('Error de red [' + ERR.PST_VCAT + ']'));
            } else if (anchor?.type === 'cell' && anchor.vId && anchor.hId) {
                highlightPasteTarget(anchor);
                status('Pegando ' + dims + ' en celda ' + esc(clipGrid[0][0] || '') + '...');
                doPaste(anchor, clipGrid, ERR.PST_CEL);
            } else if (clipGrid.length >= 2) {
                if (!confirm('Clipboard: ' + dims + '. ¿Reemplazar todo el dataset?')) return;
                status('Reemplazando dataset con ' + dims + '...');
                doPaste(null, clipGrid, ERR.PST_FULL);
            } else {
                status('Clipboard: ' + dims + ' — Seleccioná una categoría o celda para pegar');
            }
        });

        // ============ KEYBOARD NAVIGATION ============
        table.addEventListener('keydown', function(e) {
            if (e.target.closest('button, a, input')) return;

            if (currentMode === 'datos') {
                const td = e.target.closest('td[data-vertical-id]');
                if (!td) return;
                const vId = parseInt(td.dataset.verticalId), hId = parseInt(td.dataset.horizontalId);
                if (!vId || !hId) return;
                let ri = estado.verticales.findIndex(v => v.categoria_id === vId);
                let ci = estado.horizontales.findIndex(h => h.categoria_id === hId);
                if (ri < 0 || ci < 0) return;
                const k = e.key;
                if (!['ArrowUp','ArrowDown','ArrowLeft','ArrowRight','Tab','Enter'].includes(k)) return;
                e.preventDefault();
                if (k === 'ArrowRight') ci++; else if (k === 'ArrowLeft') ci--;
                else if (k === 'ArrowDown') ri++; else if (k === 'ArrowUp') ri--;
                else if (k === 'Tab') e.shiftKey ? ci-- : ci++;
                else if (k === 'Enter') e.shiftKey ? ri-- : ri++;
                ri = Math.max(0, Math.min(ri, estado.verticales.length - 1));
                ci = Math.max(0, Math.min(ci, estado.horizontales.length - 1));
                const t = document.querySelector('#tbody td[data-vertical-id="' + estado.verticales[ri].categoria_id + '"][data-horizontal-id="' + estado.horizontales[ci].categoria_id + '"]');
                const div = t?.querySelector('div[contenteditable]');
                if (!div) return;
                lastCell = { type: 'cell', vId: estado.verticales[ri].categoria_id, hId: estado.horizontales[ci].categoria_id };
                clearSelection(); setSelection(ri, ci, ri, ci); div.focus();
                const r = document.createRange(), s = window.getSelection();
                r.selectNodeContents(div); r.collapse(false); s.removeAllRanges(); s.addRange(r);
                const vCat = estado.verticales[ri], hCat = estado.horizontales[ci];
                if (vCat && hCat) status('Fila: "' + vCat.nombre + '" | Columna: "' + hCat.nombre + '"');
                return;
            }

            // Diseño mode: navigate ALL categories (padres, hijos, pivote)
            if (currentMode === 'diseno') {
                const pivotLabel = document.querySelector('#thead .pivot-label');
                const pivotTh = pivotLabel?.closest('th');
                const horizHeaders = Array.from(document.querySelectorAll('#thead th[data-categoria-id]'));
                const vertLabels = Array.from(document.querySelectorAll('#tbody th[data-categoria-id]'));
                const allFocusable = [pivotTh, ...horizHeaders, ...vertLabels].filter(Boolean);

                const active = document.activeElement;
                const activeTh = active?.closest('th');
                const k = e.key;
                if (!['ArrowUp','ArrowDown','ArrowLeft','ArrowRight','Tab','Enter'].includes(k)) return;
                e.preventDefault();

                function focusTh(el) {
                    if (!el || el === activeTh) return;
                    clearSelection();
                    document.querySelectorAll('#dataset-table .cell-selected').forEach(c => c.classList.remove('cell-selected'));
                    el.classList.add('cell-selected');
                    const span = el.querySelector('.cat-name, .pivot-label');
                    if (span) {
                        span.focus();
                        const r = document.createRange(), s = window.getSelection();
                        r.selectNodeContents(span); r.collapse(false); s.removeAllRanges(); s.addRange(r);
                    } else { el.focus(); }
                    status((el.querySelector('.pivot-label') ? 'Pivote' : 'Cat') + ': "' + (el.textContent || '').trim() + '"');
                }

                // If active element is not a known navigable th, focus the first one
                if (!activeTh || (!allFocusable.includes(activeTh))) {
                    focusTh(allFocusable[0]);
                    return;
                }

                const idxH = horizHeaders.indexOf(activeTh);
                const idxV = vertLabels.indexOf(activeTh);
                const isPivot = activeTh === pivotTh;
                let next = null;

                if (k === 'ArrowRight' || k === 'ArrowLeft') {
                    const right = (k === 'ArrowRight');
                    if (isPivot) {
                        next = right ? horizHeaders[0] : vertLabels[vertLabels.length - 1];
                    } else if (idxH >= 0) {
                        if (right && idxH < horizHeaders.length - 1) next = horizHeaders[idxH + 1];
                        else if (right && idxH === horizHeaders.length - 1) next = vertLabels[0];
                        else if (!right && idxH > 0) next = horizHeaders[idxH - 1];
                        else if (!right && idxH === 0) next = pivotTh;
                    } else if (idxV >= 0) {
                        if (right) next = horizHeaders[0];
                        else next = pivotTh;
                    }
                } else if (k === 'ArrowDown' || k === 'ArrowUp') {
                    const down = (k === 'ArrowDown');
                    if (isPivot) {
                        next = down ? vertLabels[0] : vertLabels[vertLabels.length - 1];
                    } else if (idxH >= 0) {
                        next = down
                            ? vertLabels[Math.min(idxH, vertLabels.length - 1)]
                            : (pivotTh || vertLabels[vertLabels.length - 1]);
                    } else if (idxV >= 0) {
                        if (down && idxV < vertLabels.length - 1) next = vertLabels[idxV + 1];
                        else if (down && idxV === vertLabels.length - 1) next = horizHeaders[0];
                        else if (!down && idxV > 0) next = vertLabels[idxV - 1];
                        else if (!down && idxV === 0) next = pivotTh;
                    }
                } else if (k === 'Tab' || k === 'Enter') {
                    const curIdx = allFocusable.indexOf(activeTh);
                    const step = (k === 'Tab' && e.shiftKey) ? -1 : 1;
                    next = allFocusable[(curIdx + step + allFocusable.length) % allFocusable.length];
                }

                focusTh(next);
            }
        }, true);
    });

    // ============ GLOBAL ACTION FUNCTIONS ============
    window.agregarHijo = function(padreId) {
        saveAllBeforeAction();
        api('/hijo', { method: 'POST', body: { padre_id: padreId } })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Hijo agregado'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.HIJOS + ']'));
    };

    window.clonarCategoria = function(categoriaId) {
        saveAllBeforeAction();
        if (!confirm('¿Clonar esta categoría con todos sus hijos?')) return;
        api('/clonar/' + categoriaId, { method: 'POST' })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Categoría clonada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.CLON + ']'));
    };

    window.agregarFila = function() {
        saveAllBeforeAction();
        api('/fila', { method: 'POST' })
            .then(j => { if (j.success) { estado = j.data; renderGrid(estado); status('Fila agregada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.FILA + ']'));
    };

    window.agregarColumna = function() {
        saveAllBeforeAction();
        api('/columna', { method: 'POST' })
            .then(j => { if (j.success) { estado = j.data; renderGrid(estado); status('Columna agregada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.COL + ']'));
    };

    window.eliminarFila = function(id) {
        saveAllBeforeAction();
        if (!confirm('¿Eliminar esta fila?')) return;
        api('/fila/' + id, { method: 'DELETE' })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Fila eliminada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.E_FILA + ']'));
    };

    window.eliminarColumna = function(id) {
        saveAllBeforeAction();
        if (!confirm('¿Eliminar esta columna?')) return;
        api('/columna/' + id, { method: 'DELETE' })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Columna eliminada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.E_COL + ']'));
    };

    window.limpiarDataset = function() {
        saveAllBeforeAction();
        new bootstrap.Modal(document.getElementById('modalRegenerar')).show();
    };

    window.limpiarDatos = function() {
        if (!confirm('¿Limpiar todos los valores de la sección activa? Se conservarán las categorías.')) return;
        saveAllBeforeAction();
        status('Limpiando datos...');
        api('/datos?seccion_id=' + estado.seccion_activa_id, { method: 'DELETE' })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('✓ Datos limpiados'); } else alerta(j.message); })
            .catch(() => alerta('Error de red [' + ERR.DATOS + ']'));
    };

    window.guardarPivot = function(el) {
        const val = el.textContent.trim() || 'PIVOTE';
        estado.pivot_label = val;
        status('Guardando pivote...');
        api('/pivot', { method: 'PUT', body: { label: val } })
            .then(j => { if (j.success) status('Pivote: ' + val); else alerta(j.message); })
            .catch(() => alerta('Error de red [' + ERR.PIVOT + ']'));
    };

    window.agregarSeccion = function() {
        const nombre = prompt('Nombre de la nueva sección:');
        if (!nombre) return;
        saveAllBeforeAction();
        status('Creando sección...');
        api('/seccion', { method: 'POST', body: { nombre } })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Sección creada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.SECCION + ']'));
    };

    window.renombrarSeccion = function(seccionId, nombreActual) {
        const nombre = prompt('Renombrar sección:', nombreActual);
        if (!nombre || nombre === nombreActual) return;
        saveAllBeforeAction();
        status('Renombrando...');
        api('/seccion/' + seccionId, { method: 'PUT', body: { nombre } })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Sección renombrada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.SECCION + ']'));
    };

    window.reordenarSeccion = function(seccionId, direccion) {
        saveAllBeforeAction();
        status('Reordenando...');
        api('/seccion/' + seccionId + '/reordenar', { method: 'POST', body: { direccion } })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Sección reordenada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.SECCION + ']'));
    };

    window.eliminarSeccion = function(seccionId) {
        const seccion = (estado.secciones || []).find(s => s.seccion_id === seccionId);
        if (!seccion) return;
        if (!confirm('¿Eliminar la sección "' + seccion.nombre + '" y todos sus datos?')) return;
        saveAllBeforeAction();
        status('Eliminando...');
        api('/seccion/' + seccionId, { method: 'DELETE' })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Sección eliminada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.SECCION + ']'));
    };

    // ============ GENERATE / REGENERATE ============
    document.getElementById('btn-generar')?.addEventListener('click', function() {
        const filas = parseInt(document.getElementById('input-filas').value) || 5;
        const cols = parseInt(document.getElementById('input-columnas').value) || 5;
        status('Generando...');
        api('/generar', { method: 'POST', body: { filas, columnas: cols } })
            .then(j => { if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Grilla generada'); } else alerta(j.message); })
            .catch(() => alerta('Error [' + ERR.GENERAR + ']'));
    });

    document.getElementById('btn-regenerar')?.addEventListener('click', function() {
        const filas = parseInt(document.getElementById('modal-input-filas').value) || 5;
        const cols = parseInt(document.getElementById('modal-input-columnas').value) || 5;
        status('Regenerando...');
        api('/generar', { method: 'POST', body: { filas, columnas: cols } })
            .then(j => {
                if (j.success) { estado = j.data; clearSelection(); renderGrid(estado); status('Cuadrícula regenerada'); bootstrap.Modal.getInstance(document.getElementById('modalRegenerar'))?.hide(); }
                else alerta(j.message);
            })
            .catch(() => alerta('Error [' + ERR.REGEN + ']'));
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
            }).catch(() => alerta('Error [' + ERR.IMPORT + ']'));
        input.value = '';
    }

    function initLastCell() {
        if (estado.verticales?.length && estado.horizontales?.length)
            lastCell = { type: 'cell', vId: estado.verticales[0].categoria_id, hId: estado.horizontales[0].categoria_id };
        else if (estado.verticales?.length)
            lastCell = { type: 'vertical', vId: estado.verticales[0].categoria_id, hId: null };
        else if (estado.horizontales?.length)
            lastCell = { type: 'horizontal', vId: null, hId: estado.horizontales[0].categoria_id };
    }

    // ============ INIT ============
    if (estado.tiene_dataset) {
        initLastCell();
        renderGrid(estado);
        switchMode('diseno');
    }
})();
</script>
