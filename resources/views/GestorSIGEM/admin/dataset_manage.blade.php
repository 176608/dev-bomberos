<style>
    [x-cloak] { display: none !important; }
</style>
<div class="container-fluid py-4"
     x-data="datasetEditor()"
     x-init="initEditor({{ $cuadro->cuadro_id }}, {{ json_encode($estadoInicial) }})">

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
                <p class="text-muted">Genera una grilla inicial para empezar a trabajar. Puedes agregar categorías jerárquicas según sea necesario.</p>

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
            <!-- Toolbar -->
            <div class="card shadow-sm mb-3">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button class="btn btn-outline-success btn-sm" @click="addRoot('vertical')" :disabled="saving">
                            <i class="bi bi-plus-lg"></i> Raíz V
                        </button>
                        <button class="btn btn-outline-primary btn-sm" @click="addRoot('horizontal')" :disabled="saving">
                            <i class="bi bi-plus-lg"></i> Raíz H
                        </button>
                        <div class="vr mx-2"></div>
                        <button class="btn btn-outline-info btn-sm" @click="togglePivotRow" :disabled="saving">
                            <i class="bi bi-pin"></i> <span x-text="dataset.vertical.pivote ? 'Quitar pivote' : 'Añadir pivote'"></span>
                        </button>
                        <div class="vr mx-2"></div>
                        <button class="btn btn-outline-secondary btn-sm" @click="$refs.importInput2.click()">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                        <input type="file" x-ref="importInput2" accept=".csv,.txt" hidden @change="importFile($event.target)">
                        <div class="vr mx-2"></div>
                        <button class="btn btn-outline-danger btn-sm" @click="limpiarTodo" :disabled="saving">
                            <i class="bi bi-trash3"></i> Limpiar todo
                        </button>
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

            <!-- Chart -->
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
                            <p class="text-muted small mb-0">Índices empiezan en 0.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content: tree sidebar + grid -->
            <div class="row">
                <!-- Vertical tree sidebar -->
                <div class="col-auto" style="min-width:220px;max-width:300px">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header py-1 d-flex justify-content-between align-items-center">
                            <small class="fw-bold"><i class="bi bi-list-ul"></i> Vertical</small>
                            <button class="btn btn-sm btn-outline-success py-0 px-1" @click="addRoot('vertical')" title="Agregar raíz vertical">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <div class="card-body p-1" style="max-height:60vh;overflow-y:auto">
                            <template x-for="node in dataset.vertical.arbol" :key="node.categoria_id">
                                <div>
                                    <template x-if="node.hijos && node.hijos.length > 0">
                                        <div>
                                            <div class="tree-node tree-parent d-flex align-items-center py-1 px-1 border-bottom"
                                                 :class="{'bg-light': selectedCat === node.categoria_id}"
                                                 @click="selectCat('vertical', node.categoria_id, node.nombre, node.tipo)">
                                                <i class="bi bi-folder me-1 text-warning small"></i>
                                                <span class="small flex-grow-1" x-text="node.nombre"></span>
                                                <span class="badge bg-secondary" x-text="node.tipo" style="font-size:0.6rem"></span>
                                                <button class="btn btn-sm py-0 px-1" @click.stop="addChild(node.categoria_id)" title="Agregar hijo">
                                                    <i class="bi bi-plus-circle text-success" style="font-size:0.7rem"></i>
                                                </button>
                                                <button class="btn btn-sm py-0 px-1" @click.stop="deleteCat(node.categoria_id)" title="Eliminar">
                                                    <i class="bi bi-x-circle text-danger" style="font-size:0.7rem"></i>
                                                </button>
                                            </div>
                                            <div style="padding-left:1rem">
                                                <template x-for="child in node.hijos" :key="child.categoria_id">
                                                    <div>
                                                        <template x-if="child.hijos && child.hijos.length > 0">
                                                            <div>
                                                                <div class="tree-node tree-parent d-flex align-items-center py-1 px-1 border-bottom"
                                                                     :class="{'bg-light': selectedCat === child.categoria_id}"
                                                                     @click="selectCat('vertical', child.categoria_id, child.nombre, child.tipo)">
                                                                    <i class="bi bi-folder me-1 text-warning small"></i>
                                                                    <span class="small flex-grow-1" x-text="child.nombre"></span>
                                                                    <button class="btn btn-sm py-0 px-1" @click.stop="addChild(child.categoria_id)">
                                                                        <i class="bi bi-plus-circle text-success" style="font-size:0.7rem"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template x-if="!child.hijos || child.hijos.length === 0">
                                                            <div class="tree-node tree-leaf d-flex align-items-center py-1 px-1 border-bottom"
                                                                 :class="{'bg-light': selectedCat === child.categoria_id, 'text-muted fst-italic': child.tipo === 'pivote'}"
                                                                 @click="selectCat('vertical', child.categoria_id, child.nombre, child.tipo)">
                                                                <i class="bi" :class="child.tipo === 'pivote' ? 'bi-pin-angle' : 'bi-arrow-right-short' me-1 small"></i>
                                                                <span class="small flex-grow-1" x-text="child.nombre"></span>
                                                                <span class="badge" :class="child.tipo === 'pivote' ? 'bg-info' : 'bg-secondary'" style="font-size:0.6rem" x-text="child.tipo"></span>
                                                                <button class="btn btn-sm py-0 px-1" @click.stop="deleteCat(child.categoria_id)" title="Eliminar">
                                                                    <i class="bi bi-x-circle text-danger" style="font-size:0.7rem"></i>
                                                                </button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!node.hijos || node.hijos.length === 0">
                                        <div class="tree-node tree-leaf d-flex align-items-center py-1 px-1 border-bottom"
                                             :class="{'bg-light': selectedCat === node.categoria_id, 'text-muted fst-italic': node.tipo === 'pivote'}"
                                             @click="selectCat('vertical', node.categoria_id, node.nombre, node.tipo)">
                                            <i class="bi bi-arrow-right-short me-1 small"></i>
                                            <span class="small flex-grow-1" x-text="node.nombre"></span>
                                            <span class="badge" :class="node.tipo === 'pivote' ? 'bg-info' : 'bg-secondary'" style="font-size:0.6rem" x-text="node.tipo"></span>
                                            <button class="btn btn-sm py-0 px-1" @click.stop="addChild(node.categoria_id)" title="Agregar hijo">
                                                <i class="bi bi-plus-circle text-success" style="font-size:0.7rem"></i>
                                            </button>
                                            <button class="btn btn-sm py-0 px-1" @click.stop="deleteCat(node.categoria_id)" title="Eliminar">
                                                <i class="bi bi-x-circle text-danger" style="font-size:0.7rem"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Data grid -->
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height:65vh; overflow:auto;">
                                <table class="table table-sm table-bordered mb-0" style="font-size:0.82rem;"
                                       @paste.prevent="handlePaste($event)"
                                       @keydown.escape="cancelEdit"
                                       @keydown.enter="onEnter"
                                       @keydown.tab="onTab"
                                       @keydown.shift.tab="onShiftTab">
                                    <thead>
                                        <template x-for="(hrow, hi) in (dataset.tabla_headers && dataset.tabla_headers.length > 0 ? dataset.tabla_headers : [dataset.tabla[0]])" :key="'hr' + hi">
                                            <tr>
                                                <template x-for="(cell, ci) in hrow" :key="'hc' + hi + '_' + ci">
                                                    <th class="text-center align-middle"
                                                        :class="{'bg-light': cell.tipo === 'corner', 'bg-info bg-opacity-10': cell.tipo_cat === 'pivote'}"
                                                        :style="cell.colspan ? 'min-width:' + (cell.colspan * 80) + 'px;' : 'min-width:80px;'"
                                                        :colspan="cell.colspan || 1"
                                                        :rowspan="cell.rowspan || 1">
                                                        <template x-if="cell.tipo === 'corner'">
                                                            <span class="text-muted small"><i class="bi bi-arrow-right"></i></span>
                                                        </template>
                                                        <template x-if="cell.tipo === 'header' && ci > 0">
                                                            <div class="position-relative">
                                                                <span @click="startRename('h', cell.categoria_id, cell.valor)"
                                                                      class="d-block cursor-pointer" style="cursor:pointer;"
                                                                      title="Click para renombrar">
                                                                    <span x-text="cell.valor || 'Sin nombre'"></span>
                                                                </span>
                                                            </div>
                                                        </template>
                                                    </th>
                                                </template>
                                            </tr>
                                        </template>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, ri) in dataRows" :key="'r' + ri">
                                            <tr :class="{'table-info': isPivotRow(row)}">
                                                <template x-for="(cell, ci) in row" :key="'c' + ri + '_' + ci">
                                                    <template x-if="ci === 0">
                                                        <th class="text-nowrap align-middle py-1"
                                                            :style="'background:#f8f9fa;min-width:120px;position:relative;padding-left:' + (12 + (cell.profundidad || 0) * 16) + 'px'">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi" :class="{'bi-pin-angle-fill text-info me-1': cell.tipo_cat === 'pivote', 'bi-chevron-right text-muted me-1': cell.profundidad > 0}"></i>
                                                                <span @click="startRename('v', cell.categoria_id, cell.valor)"
                                                                      class="d-block cursor-pointer flex-grow-1" style="cursor:pointer;"
                                                                      title="Click para renombrar">
                                                                    <span x-text="cell.valor || 'Sin nombre'"></span>
                                                                </span>
                                                                <button class="btn btn-sm text-danger p-0 ms-1"
                                                                        @click="deleteCat(cell.categoria_id)"
                                                                        title="Eliminar">
                                                                    <i class="bi bi-x-circle" style="font-size:0.7rem"></i>
                                                                </button>
                                                            </div>
                                                        </th>
                                                    </template>
                                                    <template x-if="ci > 0">
                                                        <td class="align-middle p-1"
                                                            :class="{'bg-light': isPivotRow(row), 'bg-warning bg-opacity-10': cell.es_grupo}"
                                                            style="min-width:70px;">
                                                            <template x-if="isPivotRow(row)">
                                                                <span class="small fst-italic" x-text="cell.valor ?? ''"></span>
                                                            </template>
                                                            <template x-if="!isPivotRow(row) && !cell.es_grupo">
                                                                <div>
                                                                    <template x-if="!isEditing(ri, ci)">
                                                                        <div @click="startEdit(ri, ci, cell.dato_id, cell.valor, cell.cat_vertical_id, cell.cat_horizontal_id)"
                                                                             class="px-1 cell-display"
                                                                             style="min-height:26px;cursor:pointer;">
                                                                            <span x-text="cell.valor ?? ''" class="small"></span>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="isEditing(ri, ci)">
                                                                        <input type="text" class="form-control form-control-sm editing-input"
                                                                               x-model="editValue"
                                                                               @blur="saveEdit"
                                                                               @click.stop>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                            <template x-if="cell.es_grupo">
                                                                <span class="text-muted small">—</span>
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
            </div>
        </div>
    </template>

    <!-- Rename dialog -->
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
         style="z-index:1055;background:rgba(0,0,0,0.4)"
         x-show="showRename" x-cloak
         @click.self="cancelRename">
        <div class="bg-white rounded shadow p-3" style="min-width:300px" @click.stop>
            <h6 class="mb-2"><i class="bi bi-pencil"></i> Renombrar categoría</h6>
            <input type="text" class="form-control form-control-sm" x-model="renameValue"
                   @keydown.enter="saveRename" @keydown.escape="cancelRename"
                   x-ref="renameInput">
            <div class="d-flex justify-content-end gap-2 mt-2">
                <button class="btn btn-sm btn-secondary" @click="cancelRename">Cancelar</button>
                <button class="btn btn-sm btn-primary" @click="saveRename">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" defer></script>
<script>
function datasetEditor() {
    return {
        cuadroId: null,
        dataset: {
            tiene_dataset: false,
            vertical: { arbol: [], hojas: [], pivote: null },
            horizontal: { arbol: [], hojas: [], pivote: null },
            tabla: [],
            tabla_headers: [],
        },
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
        editRow: -1,
        editCol: -1,

        selectedCat: null,
        selectedEje: null,
        selectedCatName: '',
        selectedCatTipo: '',

        showRename: false,
        renameCatId: null,
        renameEje: null,
        renameValue: '',

        // Computed: data rows (exclude header row, include pivot)
        get dataRows() {
            if (!this.dataset.tabla || this.dataset.tabla.length < 2) return [];
            return this.dataset.tabla.slice(1);
        },

        initEditor(id, estado) {
            this.cuadroId = id;
            this.dataset = estado;
            this.chartParams.x = this.getParam('x', '');
            this.chartParams.y = this.getParam('y', '');
            this.chartParams.m = this.getParam('m', 'g');
            this.loading = false;

        },

        getParam(name, fallback) {
            const p = new URLSearchParams(window.location.search).get(name);
            return p ?? fallback;
        },

        isPivotRow(row) {
            return row && row[0] && row[0].tipo_cat === 'pivote';
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
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al generar la grilla'; }
            this.generating = false;
        },

        // ============ TREE MANAGEMENT ============

        selectCat(eje, catId, nombre, tipo) {
            this.selectedCat = catId;
            this.selectedEje = eje;
            this.selectedCatName = nombre;
            this.selectedCatTipo = tipo;
        },

        async addRoot(eje) {
            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/raiz', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ eje: eje, nombre: '', tipo: 'dato' }),
                });
                const json = await r.json();
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al agregar raíz'; }
            this.saving = false;
        },

        async addChild(padreId) {
            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/' + padreId + '/hijo', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
                const json = await r.json();
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al agregar hijo'; }
            this.saving = false;
        },

        async deleteCat(catId) {
            if (!confirm('¿Eliminar esta categoría y todos sus datos?')) return;
            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/categoria/' + catId, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
                const json = await r.json();
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al eliminar'; }
            this.saving = false;
        },

        async togglePivotRow() {
            if (this.dataset.vertical.pivote) {
                // Delete existing pivot
                await this.deleteCat(this.dataset.vertical.pivote.categoria_id);
            } else {
                // Add pivot row as a vertical category
                this.saving = true;
                try {
                    const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/raiz', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ eje: 'vertical', nombre: 'Pivote', tipo: 'pivote' }),
                    });
                    const json = await r.json();
                    if (json.success) this.dataset = json.data;
                    else this.error = json.message;
                } catch (e) { this.error = 'Error al añadir pivote'; }
                this.saving = false;
            }
        },

        // ============ RENAME ============

        startRename(eje, catId, currentName) {
            this.renameEje = eje;
            this.renameCatId = catId;
            this.renameValue = currentName || '';
            this.showRename = true;
            this.$nextTick(() => {
                if (this.$refs.renameInput) this.$refs.renameInput.focus();
            });
        },

        cancelRename() {
            this.showRename = false;
            this.renameCatId = null;
        },

        async saveRename() {
            if (!this.renameCatId || !this.renameValue.trim()) return;
            const catId = this.renameCatId;
            const nombre = this.renameValue.trim();
            this.cancelRename();
            this.saveStatus = 'Guardando...';
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/categoria/' + catId, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ nombre: nombre }),
                });
                const json = await r.json();
                if (json.success) {
                    await this.fetchEstado();
                    this.saveStatus = 'Guardado';
                } else this.error = json.message;
            } catch (e) { this.error = 'Error al renombrar'; }
            setTimeout(() => { this.saveStatus = ''; }, 1500);
        },

        async fetchEstado() {
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/estado');
                const json = await r.json();
                if (json.success) this.dataset = json.data;
            } catch(e) { console.error('Error fetching estado', e); }
        },

        // ============ CELL EDITING ============

        isEditing(ri, ci) {
            return this.editing && this.editRow === ri && this.editCol === ci;
        },

        startEdit(ri, ci, datoId, valor, catVId, catHId) {
            this.editing = true;
            this.editRow = ri;
            this.editCol = ci;
            this.editDatoId = datoId;
            this.editValue = valor ?? '';
            this.$nextTick(() => {
                const el = this.$el.querySelector('.editing-input');
                if (el) el.focus();
            });
        },

        async saveEdit() {
            if (!this.editing || !this.editDatoId) return;
            const datoId = this.editDatoId;
            const valor = this.editValue;
            const row = this.editRow;
            const col = this.editCol;
            this.editing = false;
            this.editRow = -1;
            this.editCol = -1;
            this.editDatoId = null;
            this.saveStatus = 'Guardando...';
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/celda/' + datoId, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ valor: valor }),
                });
                const json = await r.json();
                if (json.success) {
                    const tr = row + 1;
                    if (this.dataset.tabla[tr] && this.dataset.tabla[tr][col]) {
                        this.dataset.tabla[tr][col].valor = valor;
                    }
                    this.saveStatus = 'Guardado';
                } else this.error = json.message;
            } catch (e) { this.error = 'Error al guardar'; }
            setTimeout(() => { this.saveStatus = ''; }, 1500);
        },

        cancelEdit() {
            this.editing = false;
            this.editRow = -1;
            this.editCol = -1;
            this.editDatoId = null;
        },

        onEnter(e) {
            if (!this.editing) return;
            e.preventDefault();
            this.saveAndMoveNext();
        },

        onTab(e) {
            if (!this.editing) return;
            e.preventDefault();
            this.saveAndMoveNext();
        },

        onShiftTab(e) {
            if (!this.editing) return;
            e.preventDefault();
            this.saveAndMovePrev();
        },

        async saveAndMoveNext() {
            if (!this.editing) return;
            const cr = this.editRow, cc = this.editCol;
            await this.saveEdit();

            const rows = this.dataRows;
            const maxR = rows.length - 1;
            let headerLen = this.dataset.tabla[0]?.length ?? 0;
            let firstDataCol = 0;
            // Find first data column
            for (let i = 0; i < headerLen; i++) {
                if (this.dataset.tabla[0][i]?.tipo === 'header') { firstDataCol = i; break; }
            }
            if (firstDataCol === 0) firstDataCol = 1;
            const maxC = headerLen - 1;

            let nr = cr, nc = cc + 1;
            if (nc > maxC) { nr = cr + 1; nc = firstDataCol; }
            if (nr > maxR) { nr = 0; nc = firstDataCol; }

            const realRow = nr + 1;
            const cell = this.dataset.tabla[realRow]?.[nc];
            if (cell && cell.tipo === 'celda' && !cell.es_grupo && cell.dato_id) {
                this.startEdit(nr, nc, cell.dato_id, cell.valor, cell.cat_vertical_id, cell.cat_horizontal_id);
            }
        },

        async saveAndMovePrev() {
            if (!this.editing) return;
            const cr = this.editRow, cc = this.editCol;
            await this.saveEdit();

            const headerLen = this.dataset.tabla[0]?.length ?? 0;
            let firstDataCol = 1;
            const maxC = headerLen - 1;
            const maxR = this.dataRows.length - 1;

            let nr = cr, nc = cc - 1;
            if (nc < firstDataCol) { nr = cr - 1; nc = maxC; }
            if (nr < 0) { nr = maxR; nc = maxC; }

            const realRow = nr + 1;
            const cell = this.dataset.tabla[realRow]?.[nc];
            if (cell && cell.tipo === 'celda' && !cell.es_grupo && cell.dato_id) {
                this.startEdit(nr, nc, cell.dato_id, cell.valor, cell.cat_vertical_id, cell.cat_horizontal_id);
            }
        },

        async limpiarTodo() {
            if (!confirm('¿Eliminar todo el dataset? Esta acción no se puede deshacer.')) return;
            this.saving = true;
            try {
                const r = await fetch('{{ url("/sgiem/admin/cuadros") }}/' + this.cuadroId + '/dataset/limpiar', {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                });
                const json = await r.json();
                if (json.success) this.dataset = json.data;
                else this.error = json.message;
            } catch (e) { this.error = 'Error al limpiar dataset'; }
            this.saving = false;
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
                } else this.error = json.message;
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
                } else this.error = json.message;
            } catch (e) { this.error = 'Error al importar archivo'; }
            this.saving = false;
            input.value = '';
            setTimeout(() => { this.saveStatus = ''; }, 3000);
        },

        // ============ CHART ============

        renderChart() {
            if (!this.$refs.chart || this.dataset.tabla.length < 2) return;

            const dataRows = this.dataRows.filter(r => !this.isPivotRow(r));
            if (dataRows.length < 2) return;

            const xIdx = this.parseIndices(this.chartParams.x);
            const yIdx = this.parseIndices(this.chartParams.y);

            const maxCol = this.dataset.tabla[0].length - 1;
            const labels = [];
            const datasetsMap = {};

            const useX = xIdx.length > 0 ? xIdx : [0];
            const useY = yIdx.length > 0 ? yIdx : Array.from({length: maxCol}, (_, i) => i + 1);

            const header = this.dataset.tabla[0];

            for (const row of dataRows) {
                let label = useX.map(i => row[i]?.valor ?? '').join(' — ');
                if (!label) label = 'Fila';
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
