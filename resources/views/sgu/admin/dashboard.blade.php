@extends('sgu.layouts.admin')

@section('title', 'SGU v2 — Dashboard de Métricas')

@section('sgu_content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Dashboard de Métricas</h4>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <form method="GET" action="{{ route('sgu.admin.index') }}" class="d-flex gap-2 align-items-center">
            <input type="date" name="desde" value="{{ $desde }}" class="form-control form-control-sm">
            <span>—</span>
            <input type="date" name="hasta" value="{{ $hasta }}" class="form-control form-control-sm">
            <button type="submit" class="btn btn-sm btn-success">Aplicar</button>
            <a href="{{ route('sgu.admin.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
        </form>
        <div class="btn-group" role="group">
            <a href="{{ route('sgu.admin.gestor.usuarios') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-person-gear"></i> Gestor
            </a>
            <a href="{{ route('sgu.admin.auditor.accesos') }}" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-shield-lock-fill"></i> Auditor
            </a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border-0 shadow-sm text-center p-2">
            <i class="bi bi-activity display-6 text-success"></i>
            <h4 class="mt-1 mb-0">{{ number_format($eventos) }}</h4>
            <small class="text-muted">Eventos en el periodo</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border-0 shadow-sm text-center p-2">
            <i class="bi bi-person-fill display-6 text-primary"></i>
            <h4 class="mt-1 mb-0">{{ number_format($visitantesTotales) }}</h4>
            <small class="text-muted">Visitantes únicos</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border-0 shadow-sm text-center p-2">
            <i class="bi bi-person-plus-fill display-6 text-info"></i>
            <h4 class="mt-1 mb-0">{{ number_format($visitantesNuevos) }}</h4>
            <small class="text-muted">Nuevos en el periodo</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border-0 shadow-sm text-center p-2">
            <i class="bi bi-person-check-fill display-6 text-teal"></i>
            <h4 class="mt-1 mb-0">{{ number_format($visitantesActivos) }}</h4>
            <small class="text-muted">Activos en el periodo</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border-0 shadow-sm text-center p-2">
            <i class="bi bi-robot display-6 text-secondary"></i>
            <h4 class="mt-1 mb-0">{{ number_format($bots) }}</h4>
            <small class="text-muted">Bots / {{ number_format($humanos) }} humanos</small>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card h-100 border-0 shadow-sm text-center p-2">
            <i class="bi bi-image-fill display-6 text-warning"></i>
            <h4 class="mt-1 mb-0">{{ number_format($pngDescargas) }}</h4>
            <small class="text-muted">Descargas PNG</small>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">Eventos por día</div>
            <div class="card-body">
                <canvas id="chartDias" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">Top acciones</div>
            <div class="card-body">
                <canvas id="chartAcciones" height="140"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">Páginas visitadas</div>
            <div class="card-body">
                <canvas id="chartPaginas" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">Origen de las visitas</div>
            <div class="card-body">
                <canvas id="chartOrigenes" height="150"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">Tipos de gráfica</div>
            <div class="card-body">
                <canvas id="chartTipos" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">Top cuadros consultados</div>
    <div class="card-body">
        <canvas id="chartCuadros" height="110"></canvas>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">Últimas visitas (50)</div>
    <div class="card-body table-responsive">
        <table class="table table-striped table-sm table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Visitante</th>
                    <th>Cuadro</th>
                    <th>Acción</th>
                    <th>Detalle</th>
                    <th>Origen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimasVisitas as $visita)
                <tr>
                    <td class="text-nowrap">{{ \Carbon\Carbon::parse($visita->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td title="{{ $visita->visitante?->vuid }}">{{ $visita->visitante_id }}</td>
                    <td>{{ $visita->cuadro?->codigo_cuadro ?? '—' }}</td>
                    <td>{{ \App\Http\Controllers\SGU\DashboardController::ETIQUETAS_ACCION[$visita->accion] ?? $visita->accion }}</td>
                    <td>{{ $visita->detalle ?? '—' }}</td>
                    <td>{{ $visita->origen ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">Sin eventos en el periodo</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
$(document).ready(function () {
    const paleta = ['#2c5f4a', '#4a7c63', '#e9a03a', '#5b8def', '#8e6fbf', '#d95d5d', '#4fb8b8', '#9a9a9a'];

    const diasLabels = @json($eventosPorDia->keys()->map(fn ($f) => \Carbon\Carbon::parse($f)->format('d/m'))->values());
    const diasData = @json($eventosPorDia->values());

    if (diasLabels.length) {
        new Chart(document.getElementById('chartDias'), {
            type: 'line',
            data: {
                labels: diasLabels,
                datasets: [{
                    label: 'Eventos',
                    data: diasData,
                    borderColor: '#2c5f4a',
                    backgroundColor: 'rgba(44, 95, 74, 0.15)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }

    const acciones = @json($topAcciones);
    if (acciones.length) {
        new Chart(document.getElementById('chartAcciones'), {
            type: 'bar',
            data: {
                labels: acciones.map(a => a.label),
                datasets: [{
                    label: 'Eventos',
                    data: acciones.map(a => a.total),
                    backgroundColor: paleta
                }]
            },
            options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }

    const paginas = @json($paginas);
    if (paginas.length) {
        new Chart(document.getElementById('chartPaginas'), {
            type: 'bar',
            data: {
                labels: paginas.map(p => p.label),
                datasets: [{
                    label: 'Visitas',
                    data: paginas.map(p => p.total),
                    backgroundColor: '#4a7c63'
                }]
            },
            options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }

    const origenes = @json($origenes);
    if (origenes.length) {
        new Chart(document.getElementById('chartOrigenes'), {
            type: 'pie',
            data: {
                labels: origenes.map(o => o.origen),
                datasets: [{ data: origenes.map(o => o.total), backgroundColor: paleta }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    }

    const tipos = @json($tiposGrafica);
    if (tipos.length) {
        new Chart(document.getElementById('chartTipos'), {
            type: 'pie',
            data: {
                labels: tipos.map(t => t.detalle),
                datasets: [{ data: tipos.map(t => t.total), backgroundColor: paleta }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    }

    const cuadros = @json($topCuadros->map(fn ($c) => ['label' => $c->codigo_cuadro . ' — ' . mb_strimwidth($c->c_titulo ?? '', 0, 40, '…'), 'total' => (int) $c->total])->reverse()->values());
    if (cuadros.length) {
        new Chart(document.getElementById('chartCuadros'), {
            type: 'bar',
            data: {
                labels: cuadros.map(c => c.label),
                datasets: [{
                    label: 'Consultas',
                    data: cuadros.map(c => c.total),
                    backgroundColor: '#5b8def'
                }]
            },
            options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }
});
</script>
@endsection
