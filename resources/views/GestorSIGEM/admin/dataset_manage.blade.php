<div class="container-fluid py-4" id="app-dataset">

    <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
        <span>
            <i class="bi bi-pencil-square"></i>
            <strong>Dataset</strong> —
            <code>{{ $cuadro->codigo_cuadro }}</code>
            <strong>{{ $cuadro->c_titulo }}</strong>
        </span>
        <a href="{{ route('sgiem.admin.cuadros.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div id="alerts"></div>

    @if(!$estadoInicial['tiene_dataset'])
        <div class="card shadow-sm p-4 text-center" id="empty-state">
            <h5>Generar grilla</h5>
            <div class="row justify-content-center mt-3">
                <div class="col-auto">
                    <label>Filas</label>
                    <input type="number" class="form-control text-center" id="input-filas" value="5" min="1" max="50">
                </div>
                <div class="col-auto">
                    <label>Columnas</label>
                    <input type="number" class="form-control text-center" id="input-columnas" value="5" min="1" max="50">
                </div>
            </div>
            <button class="btn btn-success mt-3" id="btn-generar">Generar</button>
            <hr class="my-3">
            <button class="btn btn-outline-secondary btn-sm" id="btn-importar-vacio">Importar CSV</button>
            <input type="file" id="input-csv-vacio" accept=".csv,.txt" hidden>
        </div>
    @endif

    <div id="grid-container" @if(!$estadoInicial['tiene_dataset']) style="display:none" @endif>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height:75vh">
                    <table class="table table-sm table-bordered mb-0" style="font-size:0.82rem" id="dataset-table">
                        <thead id="thead"></thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-end small text-muted" id="status-bar"></div>
        </div>
    </div>
</div>

<script>
(function() {
    const CUADRO_ID = {{ $cuadro->cuadro_id }};
    const CSRF = '{{ csrf_token() }}';
    const BASE = '{{ url("/sgiem/admin/cuadros") }}/' + CUADRO_ID + '/dataset';

    let estado = @json($estadoInicial);

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
        document.getElementById('status-bar').textContent = msg || '';
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
            if (j.success) { dato.valor = val; status('✓'); }
            else alerta(j.message);
        }).catch(() => alerta('Error de red'));
        setTimeout(() => status(''), 1200);
    }

    function renombrar(el, id) {
        const nombre = el.textContent.trim();
        if (!nombre) return;
        status('Guardando...');
        api('/categoria/' + id, {
            method: 'PUT',
            body: { nombre: nombre },
        }).then(j => {
            if (j.success) status('✓');
            else alerta(j.message);
        }).catch(() => alerta('Error'));
        setTimeout(() => status(''), 1200);
    }

    function renderGrid(d) {
        if (!d.tiene_dataset) {
            document.getElementById('grid-container').style.display = 'none';
            return;
        }
        document.getElementById('grid-container').style.display = '';
        if (document.getElementById('empty-state')) {
            document.getElementById('empty-state').style.display = 'none';
        }

        const { verticales, horizontales, tabla } = d;
        const thead = document.getElementById('thead');
        const tbody = document.getElementById('tbody');

        // Build map of dato_id by vId-hId
        for (const k in vivos) delete vivos[k];
        for (const row of tabla.slice(1)) {
            for (const cel of row.slice(1)) {
                if (cel.dato_id) {
                    vivos[cel.cat_vertical_id + '-' + cel.cat_horizontal_id] = cel;
                }
            }
        }

        // Render
        let html = '<tr><th style="min-width:40px" class="text-center">' +
            '<button class="btn btn-sm btn-outline-danger py-0 px-1" onclick="limpiarDataset()" title="Limpiar todo"><i class="bi bi-trash3"></i></button>' +
            '</th>';

        for (const h of horizontales) {
            html += '<th style="min-width:90px;background:#e9ecef" class="align-middle position-relative">' +
                '<div contenteditable="true" onblur="renombrarHeader(this, ' + h.categoria_id + ')" class="px-1">' + esc(h.nombre) + '</div>' +
                '<button class="btn btn-sm text-danger p-0 position-absolute top-0 end-0" onclick="eliminarColumna(' + h.categoria_id + ')" title="Eliminar"><i class="bi bi-x-circle" style="font-size:0.7rem"></i></button>' +
                '</th>';
        }
        html += '<th style="min-width:40px" class="text-center"><button class="btn btn-sm btn-outline-primary py-0 px-1" onclick="agregarColumna()" title="Agregar columna"><i class="bi bi-plus-lg"></i></button></th></tr>';
        thead.innerHTML = html;

        html = '';
        for (const v of verticales) {
            html += '<tr>' +
                '<th style="background:#f8f9fa;min-width:120px" class="position-relative">' +
                '<div contenteditable="true" onblur="renombrarHeader(this, ' + v.categoria_id + ')" class="px-1">' + esc(v.nombre) + '</div>' +
                '<button class="btn btn-sm text-danger p-0 position-absolute top-0 start-100 translate-middle" onclick="eliminarFila(' + v.categoria_id + ')" title="Eliminar"><i class="bi bi-x-circle" style="font-size:0.7rem"></i></button>' +
                '</th>';
            for (const h of horizontales) {
                const cel = vivos[v.categoria_id + '-' + h.categoria_id] || {};
                html += '<td style="min-width:70px"><div contenteditable="true" onblur="guardarCelda(this, ' + v.categoria_id + ', ' + h.categoria_id + ')" class="px-1" style="min-height:26px">' + esc(cel.valor || '') + '</div></td>';
            }
            html += '<td class="text-center"><button class="btn btn-sm text-danger py-0 px-1" onclick="eliminarFila(' + v.categoria_id + ')" title="Eliminar"><i class="bi bi-x-circle"></i></button></td></tr>';
        }

        html += '<tr><td><button class="btn btn-sm btn-outline-success py-0" onclick="agregarFila()"><i class="bi bi-plus-lg"></i> Fila</button></td>' +
            '<td colspan="' + (horizontales.length + 1) + '" class="text-muted small">' +
            verticales.length + ' × ' + horizontales.length +
            ' <span class="vr mx-2"></span> ' +
            '<a href="#" onclick="importarCSV(); return false">Importar CSV</a>' +
            '<input type="file" id="input-csv" accept=".csv,.txt" style="display:none">' +
            ' <span class="vr mx-2"></span> ' +
            '<span class="text-muted">Ctrl+V para pegar</span></td></tr>';

        tbody.innerHTML = html;
    }

    function esc(s) {
        if (!s) return '';
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // === PASTE ===
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('dataset-table').addEventListener('paste', function(e) {
            const text = (e.clipboardData || window.clipboardData).getData('text');
            if (!text.trim()) return;
            e.preventDefault();
            const rows = text.split('\n').map(r => r.split('\t').map(c => c.trim()));
            if (rows.length < 2 || !confirm('¿Reemplazar dataset (' + rows.length + '×' + rows[0].length + ')?')) return;
            status('Pegando...');
            api('/paste', { method: 'POST', body: { grid: rows } }).then(j => {
                if (j.success) { estado = j.data; renderGrid(estado); status('Pegado'); }
                else alerta(j.message);
            }).catch(() => alerta('Error'));
            setTimeout(() => status(''), 2000);
        });
    });

    // === GLOBAL FUNCTIONS (called from onclick) ===
    window.guardarCelda = guardarCelda;

    window.renombrarHeader = function(el, id) {
        renombrar(el, id);
    };

    window.agregarFila = function() {
        api('/fila', { method: 'POST' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.agregarColumna = function() {
        api('/columna', { method: 'POST' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.eliminarFila = function(id) {
        if (!confirm('¿Eliminar fila?')) return;
        api('/fila/' + id, { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.eliminarColumna = function(id) {
        if (!confirm('¿Eliminar columna?')) return;
        api('/columna/' + id, { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    window.limpiarDataset = function() {
        if (!confirm('¿Eliminar todo el dataset?')) return;
        api('/limpiar', { method: 'DELETE' }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); }
            else alerta(j.message);
        }).catch(() => alerta('Error'));
    };

    // === GENERATE ===
    document.getElementById('btn-generar')?.addEventListener('click', function() {
        const filas = parseInt(document.getElementById('input-filas').value) || 5;
        const cols = parseInt(document.getElementById('input-columnas').value) || 5;
        status('Generando...');
        api('/generar', { method: 'POST', body: { filas: filas, columnas: cols } }).then(j => {
            if (j.success) { estado = j.data; renderGrid(estado); }
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
                if (j.success) { estado = j.data; renderGrid(estado); status('Importado'); }
                else alerta(j.message);
            }).catch(() => alerta('Error'));
        input.value = '';
        setTimeout(() => status(''), 2000);
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
