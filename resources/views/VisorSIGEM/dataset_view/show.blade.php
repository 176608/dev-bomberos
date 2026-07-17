@extends('VisorSIGEM.layouts.visor')

@section('visor_title', $cuadro['c_titulo'] . ' — Visor de Datasets')

@section('visor_content')
<div class="container-fluid py-4"
     x-data="visor()"
     x-init="init({{ $cuadro['cuadro_id'] }})">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('sigem.v2.index') }}">SIGEM v2</a></li>
            @if($from === 'catalogo')
            <li class="breadcrumb-item"><a href="{{ route('sigem.v2.catalogo') }}">Catálogo</a></li>
            @elseif($from === 'estadistica')
            <li class="breadcrumb-item"><a href="{{ route('sigem.v2.estadistica') }}">Estadística</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $cuadro['c_titulo'] }}</li>
        </ol>
    </nav>

    <template x-if="loading">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando datos del cuadro...</p>
        </div>
    </template>

    <template x-if="error">
        <div class="alert alert-danger" x-text="error"></div>
    </template>

    <template x-if="!loading && !error">
        <div>
            <div class="row mb-3">
                <div class="col-12">
                    <h2 class="h4 mb-1" x-text="cuadro.c_titulo"></h2>
                    <p class="text-muted small" x-text="cuadro.c_subtitulo"></p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-graph-up"></i> Visualización</span>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" style="width:auto"
                                        x-model="chartType" @change="renderChart">
                                    <option value="bar">Barras</option>
                                    <option value="line">Líneas</option>
                                    <option value="pie">Pastel</option>
                                    <option value="doughnut">Dona</option>
                                    <option value="radar">Radar</option>
                                    <option value="polarArea">Área Polar</option>
                                </select>
                                <button class="btn btn-sm btn-outline-secondary"
                                        @click="copyShareUrl"
                                        title="Copiar URL con configuración actual">
                                    <i class="bi bi-share"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas x-ref="chart" style="max-height:400px"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-sliders"></i> Configuración
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Columnas X (etiquetas)</label>
                                <input type="text" class="form-control form-control-sm"
                                       x-model="params.x"
                                       @input.debounce="applyParams"
                                       placeholder="ej: 1,3,5">
                                <div class="form-text">Índices de fila separados por coma.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Columnas Y (datos)</label>
                                <input type="text" class="form-control form-control-sm"
                                       x-model="params.y"
                                       @input.debounce="applyParams"
                                       placeholder="ej: 2,4">
                                <div class="form-text">Índices de columna separados por coma.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Agrupación</label>
                                <select class="form-select form-select-sm" x-model="params.m" @change="renderChart">
                                    <option value="g">Agrupado</option>
                                    <option value="s">Apilado</option>
                                </select>
                            </div>
                            <div class="alert alert-info small mb-0">
                                <i class="bi bi-info-circle"></i>
                                Los índices empiezan en 0. Cada fila es una etiqueta X, cada columna una serie de datos Y.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-table"></i> Datos</span>
                    <small class="text-muted">
                        <span x-text="tabla.length - 1"></span> filas ×
                        <span x-text="tabla[0] ? tabla[0].length - 1 : 0"></span> columnas
                    </small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <template x-for="(cell, ci) in tabla[0]" :key="ci">
                                        <th x-text="cell"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, ri) in tabla.slice(1)" :key="ri">
                                    <tr>
                                        <template x-for="(cell, ci) in row" :key="ci">
                                            <td x-text="cell ?? ''"></td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('visor_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js" defer></script>
<script>
function visor() {
    return {
        loading: true,
        error: null,
        cuadro: {},
        tabla: [],
        chartType: 'bar',
        chartInstance: null,
        params: { x: '', y: '', m: 'g' },

        init(cuadroId) {
            this.params.x = this.getParam('x', '');
            this.params.y = this.getParam('y', '');
            this.params.m = this.getParam('m', 'g');
            this.fetchData(cuadroId);
        },

        getParam(name, fallback) {
            const p = new URLSearchParams(window.location.search).get(name);
            return p ?? fallback;
        },

        fetchData(cuadroId) {
            fetch('{{ route("sigem.v2.cuadro.api", ":id") }}'.replace(':id', cuadroId))
                .then(r => {
                    if (!r.ok) throw new Error('Cuadro no encontrado');
                    return r.json();
                })
                .then(data => {
                    this.cuadro = data.cuadro;
                    this.tabla = data.tabla;
                    this.loading = false;
                    this.$nextTick(() => this.renderChart());
                })
                .catch(err => {
                    this.error = err.message;
                    this.loading = false;
                });
        },

        applyParams() {
            this.updateUrl();
            this.renderChart();
        },

        updateUrl() {
            const p = new URLSearchParams();
            if (this.params.x) p.set('x', this.params.x);
            if (this.params.y) p.set('y', this.params.y);
            if (this.params.m && this.params.m !== 'g') p.set('m', this.params.m);
            const qs = p.toString();
            const url = window.location.pathname + (qs ? '?' + qs : '');
            window.history.replaceState({}, '', url);
        },

        renderChart() {
            if (!this.$refs.chart || this.tabla.length < 2) return;

            const xIdx = this.parseIndices(this.params.x);
            const yIdx = this.parseIndices(this.params.y);

            if (xIdx.length === 0 && yIdx.length === 0) return;

            const maxCol = this.tabla[0].length - 1;
            const labels = [];
            const datasetsMap = {};

            const useX = xIdx.length > 0 ? xIdx : [0];
            const useY = yIdx.length > 0 ? yIdx : Array.from({length: maxCol}, (_, i) => i + 1)
                .filter(i => !useX.includes(i));

            const header = this.tabla[0];

            for (let ri = 1; ri < this.tabla.length; ri++) {
                const row = this.tabla[ri];
                let label = useX.map(i => row[i] ?? '').join(' — ');
                if (!label) label = `Fila ${ri}`;
                labels.push(label);

                for (const ci of useY) {
                    if (ci > maxCol) continue;
                    const colName = header[ci] ?? `Col ${ci}`;
                    if (!datasetsMap[colName]) {
                        datasetsMap[colName] = { label: colName, data: [], borderWidth: 2 };
                    }
                    const val = parseFloat(String(row[ci] ?? '').replace(/[$,%]/g, '')) || 0;
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
                    : colors.map(c => c + '80')[i % colors.length] || colors[i % colors.length] + '80',
                borderColor: colors[i % colors.length],
            }));

            const isPie = ['pie', 'doughnut', 'polarArea'].includes(this.chartType);
            const isRadar = this.chartType === 'radar';

            if (this.chartInstance) this.chartInstance.destroy();

            const config = {
                type: this.chartType,
                data: {
                    labels: isPie ? datasets.map(d => d.label) : labels,
                    datasets: isPie
                        ? [{
                            label: 'Distribución',
                            data: datasets.map(d => d.data.reduce((a, b) => a + b, 0)),
                            backgroundColor: colors.slice(0, datasets.length),
                        }]
                        : datasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                    scales: isPie || isRadar ? undefined : {
                        y: {
                            beginAtZero: true,
                            stacked: this.params.m === 's',
                        },
                        x: {
                            stacked: this.params.m === 's',
                        }
                    },
                },
            };

            this.chartInstance = new Chart(this.$refs.chart, config);
        },

        parseIndices(str) {
            if (!str || !str.trim()) return [];
            return str.split(',')
                .map(s => parseInt(s.trim()))
                .filter(n => !isNaN(n) && n >= 0);
        },

        copyShareUrl() {
            this.updateUrl();
            navigator.clipboard.writeText(window.location.href)
                .then(() => alert('URL copiada al portapapeles'));
        },
    }
}
</script>
@endpush
