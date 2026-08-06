@extends('layouts.app')

@section('title', 'Visor de Dictámenes - IMIP Ciudad Juárez')

@push('styles')
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
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

<div class="container-fluid pt-4 bg-fonde" style="min-height: 100vh;">
 <div class="container pb-5">
    <!-- Estadísticas -->
    <div class="row mb-4" id="statsCards">
        <div class="col-md-6">
            <div style="background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; padding: 20px 10px;">
                <div style="font-size: 2.8rem; font-weight: 700; color: #2f7064; margin: 10px 0;">{{ $total }}</div>
                <div style="font-size: 0.9rem; color: #666;">Total de dictámenes</div>
            </div>
        </div>
        <div class="col-md-6">
            <div style="background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); text-align: center; padding: 20px 10px;">
                <div style="font-size: 2.8rem; font-weight: 700; color: #28a745; margin: 10px 0;">{{ $enviados }}</div>
                <div style="font-size: 0.9rem; color: #666;">Dictámenes Enviados</div>
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
    <div id="tablaCard">
        <table id="dictamenes-table" class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th># Oficio</th>
                    <th>Dependencia</th>
                    <th>Asunto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictamenes as $d)
                <tr>
                    <td data-order="{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : '0000-00-00' }}">
                        {{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('d/m/Y') : '—' }}
                    </td>
                    <td>{{ $d->oficio_recibido ?? '—' }}</td>
                    <td>{{ $d->dependencia_empres ?? '—' }}</td>
                    <td title="{{ $d->asunto ?? '' }}" data-search="{{ trim(($d->asunto ?? '') . ' ' . ($d->observaciones ?? '') . ' ' . ($d->tipo_dictamen ?? '') . ' ' . ($d->dependencia_empres ?? '') . ' ' . ($d->nombre_puesto ?? '') . ' ' . ($d->revisado_por ?? '') . ' ' . ($d->numero_oficio ?? '') . ' ' . ($d->oficio_recibido ?? '')) }}">{{ \Illuminate\Support\Str::limit($d->asunto ?? '', 60) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
 </div>
</div>

@endsection

@section('scripts')
@parent

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {

    // Limpieza de estado DataTables viejo al cambiar la estructura de columnas (se quitó la columna Estatus)
    if (!sessionStorage.getItem('dictamenes_state_v12')) {
        Object.keys(localStorage).forEach(function(k) {
            if (k.indexOf('DataTables_dictamenes-table') === 0) {
                localStorage.removeItem(k);
            }
        });
        sessionStorage.setItem('dictamenes_state_v12', '1');
    }

    function initDataTable() {
        if ($.fn.DataTable.isDataTable('#dictamenes-table')) {
            $('#dictamenes-table').DataTable().destroy();
        }
        $('#dictamenes-table').DataTable({
            "paging": true,
            "lengthMenu": [[5, 10, 15, 20, 50, 100, -1], ['5', '10', '15', '20', '50', '100', 'Todas']],
            "pageLength": 10,
            "searching": true,
            "info": true,
            "ordering": true,
            "order": [[0, 'desc']],
            "stateSave": true,
            "stateDuration": 60 * 60 * 24 * 30,
            "language": {
                "search": "Buscar:",
                "paginate": { "previous": "‹", "next": "›" },
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros (_MAX_ en total)",
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
