<div class="container-fluid py-4"
     x-data="datasetEditor()"
     x-init="init({{ $cuadro->cuadro_id }}, @json($estadoInicial))">

    <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
        <span>
            <i class="bi bi-pencil-square"></i>
            <strong>Modo edición</strong> — Gestión del dataset de
            <code>{{ $cuadro->codigo_cuadro }}</code>
            <strong>{{ $cuadro->c_titulo }}</strong>
            @if(!empty($cuadro->c_subtitulo))
            <br><small class="text-muted">{{ $cuadro->c_subtitulo }}</small>
            @endif
        </span>
        <a href="{{ route('sgiem.admin.cuadros.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <template x-if="loading">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando...</p>
        </div>
    </template>

    <template x-if="error">
        <div class="alert alert-danger" x-text="error"></div>
    </template>

    <template x-if="!loading && !dataset.tiene_dataset">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-table" style="font-size: 4rem; color: #dee2e6;"></i>
                <h5 class="mt-3">Este cuadro no tiene dataset</h5>
                <p class="text-muted">Genera una grilla inicial para empezar a trabajar. Puedes agregar o eliminar filas/columnas después según sea necesario.</p>

                <div class="row justify-content-center mt-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filas</label>
                        <input type="number" class="form-control text-center" x-model="generateFilas" min="1" max="50">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Columnas</label>
                        <input type="number" class="form-control text-center" x-model="generateColumnas" min="1" max="50">
                    </div>
                </div>

                <button class="btn btn-success btn-lg mt-4" @click="generarGrilla" :disabled="generating">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    <span x-text="generating ? 'Generando...' : 'Generar grilla'"></span>
                </button>

                <hr class="my-4">
                <p class="text-muted small">
                    <i class="bi bi-info-circle"></i>
                    También puedes <a href="#" @click.prevent="$refs.importInput.click()" class="text-decoration-none">importar un archivo CSV</a> si ya tienes los datos.
                    <input type="file" x-ref="importInput" accept=".csv,.txt" hidden @change="importFile($event.target)">
                </p>
            </div>
        </div>
    </template>

    <template x-if="!loading && dataset.tiene_dataset">
        <div>
            <div class="card shadow-sm mb-3">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button class="btn btn-outline-success btn-sm" @click="addRow" :disabled="saving">
                            <i class="bi bi-plus-lg"></i> Fila
                        </button>
                        <button class="btn btn-outline-primary btn-sm" @click="addColumn" :disabled="saving">
                            <i class="bi bi-plus-lg"></i> Columna
                        </button>
                        <div class="vr mx-2"></div>
                        <button class="btn btn-outline-secondary btn-sm" @click="$refs.importInput2.click()">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                        <input type="file" x-ref="importInput2" accept=".csv,.txt" hidden @change="importFile($event.target)">
                        <div class="vr mx-2"></div>
                        <button class="btn btn-sm" :class="showChart ? 'btn-success' : 'btn-outline-success'"
                                @click="showChart = !showChart; $nextTick(() => showChart && renderChart())">
                            <i class="bi bi-bar-chart"></i> <span x-text="showChart ? 'Ocultar gráfica' : 'Ver gráfica'"></span>
                        </button>
                        <small class="text-muted ms-auto">
                            <span x-text="dataset.max_filas"></span> filas × <span x-text="dataset.max_columnas"></span> columnas
                        </small>
                    </div>
                </div>
            </div>

            <div class="row" x-show="showChart" x-transition>
                <div class="col-md-8">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-graph-up"></i> Visualización</span>
                            <select class="form-select form-select-sm" style="width:auto"
                                    x-model="chartType" @change="renderChart">
                                <option value="bar">Barras</option>
                                <option value="line">Líneas</option>
                                <option value="pie">Pastel</option>
                                <option value="doughnut">Dona</option>
                                <option value="radar">Radar</option>
                                <option value="polarArea">Área Polar</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <canvas x-ref="chart" style="max-height:350px"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header"><i class="bi bi-sliders"></i> Configuración gráfica</div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Columnas X (etiquetas)</label>
                                <input type="text" class="form-control form-control-sm" x-model="chartParams.x" @input.debounce="renderChart" placeholder="ej: 1,3,5">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Columnas Y (datos)</label>
                                <input type="text" class="form-control form-control-sm" x-model="chartParams.y" @input.debounce="renderChart" placeholder="ej: 2,4">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Agrupación</label>
                                <select class="form-select form-select-sm" x-model="chartParams.m" @change="renderChart">
                                    <option value="g">Agrupado</option>
                                    <option value="s">Apilado</option>
                                </select>
                            </div>
                            <p class="text-muted small mb-0">Índices empiezan en 0 (columna 0 = encabezado de fila).</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:70vh; overflow:auto;">
                        <table class="table table-sm table-bordered mb-0" style="font-size:0.85rem;"
                               @paste.prevent="handlePaste($event)"
                               @dblclick="handleDblClick($event)"
                               @keydown.escape="cancelEdit"
                               @keydown.enter.prevent="saveEdit">
                            <thead>
                                <tr>
                                    <template x-for="(cell, ci) in dataset.tabla[0]" :key="'h' + ci">
                                        <th class="text-center align-middle" style="min-width:100px;background:#e9ecef;position:relative;">
                                            <template x-if="ci === 0">
                                                <span class="text-muted small">
                                                    <i class="bi bi-arrow-right"></i>
                                                </span>
                                            </template>
                                            <template x-if="ci > 0">
                                                <div>
                                                    <template x-if="!isEditing('header_h') || editCol !== ci">
                                                        <span @click="startHeaderEdit('h', 0, ci, cell.categoria_id, cell.valor)"
                                                              class="d-block cursor-pointer" style="cursor:pointer;"
                                                              title="Click para renombrar">
                                                            <span x-text="cell.valor || 'Sin nombre'"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="isEditing('header_h') && editCol === ci">
                                                        <input type="text" class="form-control form-control-sm text-center"
                                                               x-model="editValue"
                                                               @blur="saveHeaderEdit"
                                                               x-ref="headerInput_h_0_ci"
                                                               @click.stop>
                                                    </template>
                                                    <button class="btn btn-sm text-danger p-0 position-absolute top-0 end-0"
                                                            @click="deleteColumn(cell.categoria_id)"
                                                            title="Eliminar columna">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, ri) in dataset.tabla.slice(1)" :key="'r' + ri">
                                    <tr>
                                        <template x-for="(cell, ci) in row" :key="'c' + ri + '_' + ci">
                                            <template x-if="ci === 0">
                                                <th class="text-nowrap align-middle" style="background:#f8f9fa;min-width:120px;position:relative;">
                                                    <template x-if="!isEditing('header_v', ri)">
                                                        <span @click="startHeaderEdit('v', ri, 0, cell.categoria_id, cell.valor)"
                                                              class="d-block cursor-pointer" style="cursor:pointer;"
                                                              title="Click para renombrar">
                                                            <span x-text="cell.valor || 'Sin nombre'"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="isEditing('header_v', ri)">
                                                        <input type="text" class="form-control form-control-sm"
                                                               x-model="editValue"
                                                               @blur="saveHeaderEdit"
                                                               x-ref="headerInput_v_ri"
                                                               @click.stop>
                                                    </template>
                                                    <button class="btn btn-sm text-danger p-0 position-absolute top-0 start-100 translate-middle"
                                                            @click="deleteRow(cell.categoria_id)"
                                                            title="Eliminar fila">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </th>
                                            </template>
                                            <template x-if="ci > 0">
                                                <td class="align-middle p-1" style="min-width:80px;">
                                                    <template x-if="!isEditing('celda', ri, ci)">
                                                        <div @dblclick="startEdit(ri + 1, ci, cell.dato_id, cell.valor)"
                                                             class="px-1 cell-display"
                                                             style="min-height:28px;cursor:pointer;">
                                                            <span x-text="cell.valor ?? ''" class="small"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="isEditing('celda', ri, ci)">
                                                        <input type="text" class="form-control form-control-sm"
                                                               x-model="editValue"
                                                               @blur="saveEdit"
                                                               @click.stop
                                                               x-ref="celdaInput_ri_ci">
                                                    </template>
                                                </td>
                                            </template>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-muted small text-end" x-show="saveStatus" x-text="saveStatus"></div>
            </div>
        </div>
    </template>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" defer></script>
<script>
function datasetEditor() {
    return {
        cuadroId: null,
        dataset: { tiene_dataset: false, verticales: [], horizontales: [], tabla: [] },
        loading: true,
        error: null,
        generating: false,
        saving: false,
        saveStatus: '',

        generateFilas: 10,
        generateColumnas: 10,

        showChart: false,
        chartType: 'bar',
        chartInstance: null,
        chartParams: { x: '', y: '', m: 'g' },

        editing: null,
        editValue: '',
        editDatoId: null,
        editCatId: null,
        editRow: -1,
        editCol: -1,

        init(id, estado) {
            this.cuadroId = id;
            this.dataset = estado;
            this.chartParams.x = this.getParam('x', '');
            this.chartParams.y = this.getParam('y', '');
            this.chartParams.m = this.getParam('m', 'g');
            this.loading = false;
        },

        initChartData() {
            this.showChart = false;
            this.$nextTick(() => {
                if (this.showChart) this.renderChart();
            });
        },

        getParam(name, fallback) {
            const p = new URLSearchParams(window.location.search).get(name);
            return p ?? fallback;
        },

        // ============ GENERATE ============

        async generarGrilla() {
            this.generating = true;
            this.error = null;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/generar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ filas: this.generateFilas, columnas: this.generateColumnas }),
                });
                const json = await r.json();
                if (json.success) {
                    this.dataset = json.data;
                } else {
                    this.error = json.message;
                }
            } catch (e) {
                this.error = 'Error al generar la grilla';
            }
            this.generating = false;
        },

        // ============ ADD / DELETE ============

        async addRow() {
            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/fila', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
                const json = await r.json();
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al agregar fila'; }
            this.saving = false;
        },

        async addColumn() {
            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/columna', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
                const json = await r.json();
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al agregar columna'; }
            this.saving = false;
        },

        async deleteRow(catId) {
            if (!confirm('¿Eliminar esta fila y todos sus datos?')) return;
            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/fila/' + catId, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
                const json = await r.json();
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al eliminar fila'; }
            this.saving = false;
        },

        async deleteColumn(catId) {
            if (!confirm('¿Eliminar esta columna y todos sus datos?')) return;
            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/columna/' + catId, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
                const json = await r.json();
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al eliminar columna'; }
            this.saving = false;
        },

        // ============ CELL EDITING ============

        isEditing(tipo, ri, ci) {
            if (!this.editing) return false;
            if (tipo === 'header_h') return this.editing.tipo === 'header_h' && this.editCol === ci;
            if (tipo === 'header_v') return this.editing.tipo === 'header_v' && this.editRow === ri;
            if (tipo === 'celda') return this.editing.tipo === 'celda' && this.editRow === ri && this.editCol === ci;
            return false;
        },

        startEdit(ri, ci, datoId, valor) {
            this.editing = { tipo: 'celda' };
            this.editRow = ri;
            this.editCol = ci;
            this.editDatoId = datoId;
            this.editValue = valor ?? '';
            this.$nextTick(() => {
                const key = 'celdaInput_' + ri + '_' + ci;
                if (this.$refs[key]) this.$refs[key].focus();
            });
        },

        async saveEdit() {
            if (!this.editing || this.editing.tipo !== 'celda') return;
            const datoId = this.editDatoId;
            const valor = this.editValue;
            this.editing = null;
            this.saveStatus = 'Guardando...';
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/celda/' + datoId, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ valor: valor }),
                });
                const json = await r.json();
                if (json.success) {
                    this.dataset.tabla[this.editRow][this.editCol].valor = valor;
                    this.saveStatus = 'Guardado';
                } else {
                    this.error = json.message;
                }
            } catch (e) { this.error = 'Error al guardar'; }
            this.editRow = -1;
            this.editCol = -1;
            this.editDatoId = null;
            setTimeout(() => { this.saveStatus = ''; }, 1500);
        },

        cancelEdit() {
            this.editing = null;
            this.editRow = -1;
            this.editCol = -1;
            this.editDatoId = null;
        },

        handleDblClick(e) {
            const td = e.target.closest('td');
            if (!td) return;
        },

        // ============ HEADER EDITING ============

        startHeaderEdit(eje, ri, ci, catId, valor) {
            this.editing = { tipo: 'header_' + eje };
            this.editRow = ri;
            this.editCol = ci;
            this.editCatId = catId;
            this.editValue = valor || '';
            this.$nextTick(() => {
                const key = 'headerInput_' + eje + '_' + ri + '_' + ci;
                if (this.$refs[key]) this.$refs[key].focus();
            });
        },

        async saveHeaderEdit() {
            if (!this.editing || !['header_h', 'header_v'].includes(this.editing.tipo)) return;
            const catId = this.editCatId;
            const nombre = this.editValue;
            this.editing = null;
            this.saveStatus = 'Guardando...';
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/categoria/' + catId, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ nombre: nombre }),
                });
                const json = await r.json();
                if (json.success) {
                    const tabla = this.dataset.tabla;
                    if (this.editing?.tipo === 'header_h') {
                        tabla[0][this.editCol].valor = nombre;
                    } else {
                        tabla[this.editRow + 1][0].valor = nombre;
                    }
                    this.saveStatus = 'Guardado';
                } else {
                    this.error = json.message;
                }
            } catch (e) { this.error = 'Error al guardar'; }
            this.editRow = -1;
            this.editCol = -1;
            this.editCatId = null;
            setTimeout(() => { this.saveStatus = ''; }, 1500);
        },

        // ============ PASTE ============

        async handlePaste(e) {
            const data = e.clipboardData.getData('text');
            if (!data.trim()) return;

            const rows = data.split('\n').map(r => r.split('\t').map(c => c.trim()));
            if (rows.length < 2) {
                this.error = 'Los datos copiados deben tener al menos 2 filas (encabezados + datos)';
                return;
            }

            if (!confirm('Se reemplazará todo el dataset con los datos copiados (' + rows.length + ' filas × ' + rows[0].length + ' columnas). ¿Continuar?')) return;

            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/paste', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ grid: rows }),
                });
                const json = await r.json();
                if (json.success) {
                    this.dataset = json.data;
                    this.saveStatus = 'Datos importados desde el portapapeles';
                } else {
                    this.error = json.message;
                }
            } catch (e) { this.error = 'Error al pegar datos'; }
            this.saving = false;
            setTimeout(() => { this.saveStatus = ''; }, 3000);
        },

        // ============ IMPORT ============

        async importFile(input) {
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('dataset', file);

            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/importar', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData,
                });
                const json = await r.json();
                if (json.success) {
                    this.dataset = json.data;
                    this.saveStatus = 'Archivo importado correctamente';
                } else {
                    this.error = json.message;
                }
            } catch (e) { this.error = 'Error al importar archivo'; }
            this.saving = false;
            input.value = '';
            setTimeout(() => { this.saveStatus = ''; }, 3000);
        },

        // ============ CHART ============

        renderChart() {
            if (!this.$refs.chart || this.dataset.tabla.length < 2) return;

            const tabla = this.dataset.tabla;
            const xIdx = this.parseIndices(this.chartParams.x);
            const yIdx = this.parseIndices(this.chartParams.y);

            if (xIdx.length === 0 && yIdx.length === 0) return;

            const maxCol = tabla[0].length - 1;
            const labels = [];
            const datasetsMap = {};

            const useX = xIdx.length > 0 ? xIdx : [0];
            const useY = yIdx.length > 0 ? yIdx : Array.from({length: maxCol}, (_, i) => i + 1);

            const header = tabla[0];

            for (let ri = 1; ri < tabla.length; ri++) {
                const row = tabla[ri];
                let label = useX.map(i => row[i]?.valor ?? '').join(' — ');
                if (!label) label = 'Fila ' + ri;
                labels.push(label);

                for (const ci of useY) {
                    if (ci > maxCol) continue;
                    const colName = header[ci]?.valor ?? 'Col ' + ci;
                    if (!datasetsMap[colName]) {
                        datasetsMap[colName] = { label: colName, data: [], borderWidth: 2 };
                    }
                    const val = parseFloat(String(row[ci]?.valor ?? '').replace(/[$,%]/g, '')) || 0;
                    datasetsMap[colName].data.push(val);
                }
            }

            const colors = [
                '#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0',
                '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2'
            ];
            const datasets = Object.values(datasetsMap).map((ds, i) => ({
                ...ds,
                backgroundColor: this.chartType === 'line'
                    ? colors[i % colors.length]
                    : colors[i % colors.length] + '80',
                borderColor: colors[i % colors.length],
            }));

            const isPie = ['pie', 'doughnut', 'polarArea'].includes(this.chartType);
            const isRadar = this.chartType === 'radar';

            if (this.chartInstance) this.chartInstance.destroy();

            const config = {
                type: this.chartType,
                data: {
                    labels: isPie ? datasets.map(d => d.label) : labels,
                    datasets: isPie ? [{
                        label: 'Distribución',
                        data: datasets.map(d => d.data.reduce((a, b) => a + b, 0)),
                        backgroundColor: colors.slice(0, datasets.length),
                    }] : datasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { position: 'bottom' } },
                    scales: isPie || isRadar ? undefined : {
                        y: { beginAtZero: true, stacked: this.chartParams.m === 's' },
                        x: { stacked: this.chartParams.m === 's' },
                    },
                },
            };

            this.chartInstance = new Chart(this.$refs.chart, config);
        },

        parseIndices(str) {
            if (!str || !str.trim()) return [];
            return str.split(',').map(s => parseInt(s.trim())).filter(n => !isNaN(n) && n >= 0);
        },
    };
}
</script>
