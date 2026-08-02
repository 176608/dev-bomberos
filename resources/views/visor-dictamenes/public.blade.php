@extends('layouts.app')

@section('title', 'Visor de Dictámenes - IMIP Ciudad Juárez')

@push('styles')
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
    color: #333;
}

.stat-card {
    background: white !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
    text-align: center !important;
    padding: 16px 10px !important;
    height: 100%;
}

.stat-number {
    font-size: 2rem !important;
    font-weight: 700 !important;
    color: #2f7064 !important;
    margin: 6px 0 !important;
}

.stat-label {
    font-size: 0.85rem !important;
    color: #666 !important;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.chart-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.chart-container-sm {
    width: 100%;
    max-width: 1000px;
    height: 300px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.chart-container-sm canvas {
    width: 100% !important;
    height: 100% !important;
}

table {
    font-size: 0.85rem;
}

th {
    font-weight: 600;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

tr:hover td {
    background-color: #f8fafd;
}

/* Placeholder mientras se dibuja la gráfica */
.chart-loading {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.65);
    transition: opacity 0.25s ease;
}

.chart-loading.hidden {
    opacity: 0;
    pointer-events: none;
}
</style>
@endpush

@section('content')

@php
    $badgeColores = [
        'ENVIADO' => '#28a745',
        'BORRADOR PARA FIRMA' => '#ffc107',
        'EN REVISION' => '#007bff',
        'INFORMATIVO' => '#8a2be2',
        'S/D' => '#6c757d',
    ];
@endphp

<div class="container mt-4">

    <!-- Estadísticas -->
    <div class="row mb-3 g-2" id="statsCards">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-number">{{ $enviados }}</div>
                <div class="stat-label">Dictámenes Enviados</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-number">{{ $total }}</div>
                <div class="stat-label">Total de dictámenes</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-number" style="color:#8a2be2;">{{ $enviados ? round($enviados * 100 / $total, 1) : 0 }}%</div>
                <div class="stat-label">Enviados / Total</div>
            </div>
        </div>
    </div>

    <!-- Gráfica -->
    <div class="card mb-3" id="graficaCard" style="border-left: 4px solid #2f7064;">
        <div class="card-body">
            <h5 class="mb-3"><i class="bi bi-bar-chart"></i> Número de dictámenes recibidos por mes</h5>
            <div style="height: 250px; position: relative;">
                <canvas id="chartMeses"></canvas>
                <div class="chart-loading" id="chartLoading">
                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                    <span class="ms-2 text-muted">Generando gráfica…</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive" id="tablaCard">
        <table id="dictamenes-table" class="table table-hover nowrap">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Año</th>
                    <th># Oficio</th>
                    <th>Dependencia</th>
                    <th>Asunto</th>
                    <th>Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictamenes as $d)
                <tr>
                    <td data-order="{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : '0000-00-00' }}">
                        {{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        @if($d->anio)
                            <span class="badge bg-dark">{{ $d->anio }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>{{ $d->oficio ?? '—' }}</td>
                    <td>{{ $d->dependencia_empres ?? '—' }}</td>
                    <td title="{{ $d->asunto ?? '' }}">{{ \Illuminate\Support\Str::limit($d->asunto ?? '', 60) }}</td>
                    <td>
                        <span class="badge" style="background-color: {{ $badgeColores[$d->estatus ?? ''] ?? '#6c757d' }}; color: {{ ($d->estatus ?? '') === 'BORRADOR PARA FIRMA' ? '#212529' : 'white' }}; font-weight: 500; padding: 4px 8px; font-size: 0.75rem; border-radius: 4px; display: inline-block;">
                            {{ $d->estatus ?? 'S/D' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
@parent

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#dictamenes-table')) {
            $('#dictamenes-table').DataTable().destroy();
        }
        $('#dictamenes-table').DataTable({
            "paging": true,
            "lengthMenu": [[5, 10, 15, 20, 50, 100, -1], ['5', '10', '15', '20', '50', '100', 'Todas']],
            "pageLength": 10,
            "searching": true,
            "info": false,
            "ordering": true,
            "order": [[0, 'desc']],
            "scrollX": true,
            "autoWidth": false,
            "stateSave": true,
            "stateDuration": 60 * 60 * 24 * 30,
            "language": {
                "search": "Buscar:",
                "paginate": { "previous": "‹", "next": "›" },
                "emptyTable": "No hay dictámenes",
                "zeroRecords": "No se encontró nada"
            }
        });
        $('#dictamenes-table_length').addClass('mb-3');
    }

    initDataTable();

    // Gráfica de Chart.js (client-side; todos los dictámenes ENVIADOS)
    const DATOS_GRAFICA = @json($datosGrafica ?? []);
    const MESES_ESP = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    const ctx = document.getElementById('chartMeses');
    if (ctx) {
        const chart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Solicitudes',
                        data: [],
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        function aplicarGrafica() {
            const datos = DATOS_GRAFICA.filter(function (d) {
                return d.f;
            });

            const buckets = {};
            const llaves = [];
            datos.forEach(function (d) {
                const k = d.f.substring(0, 7);
                buckets[k] = (buckets[k] || 0) + 1;
                if (llaves.indexOf(k) === -1) {
                    llaves.push(k);
                }
            });
            llaves.sort();

            chart.data.labels = llaves.map(function (k) {
                const partes = k.split('-');
                return MESES_ESP[parseInt(partes[1], 10) - 1] + ' ' + partes[0];
            });
            chart.data.datasets[0].data = llaves.map(function (k) {
                return buckets[k] || 0;
            });
            chart.update();
            ocultarCargaGrafica();
        }

        aplicarGrafica();
        window.aplicarGrafica = aplicarGrafica;
    } else {
        ocultarCargaGrafica();
    }

    function ocultarCargaGrafica() {
        const loadingEl = document.getElementById('chartLoading');
        if (loadingEl) {
            loadingEl.style.display = 'none';
            loadingEl.classList.add('d-none');
        }
    }
});
</script>
@endsection
