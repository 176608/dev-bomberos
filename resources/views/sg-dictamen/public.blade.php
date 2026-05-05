@extends('layouts.app')

@section('title', 'Dictámenes - IMIP Ciudad Juárez')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        text-align: center;
        padding: 20px 10px;
    }
    .stat-number {
        font-size: 2.8rem;
        font-weight: 700;
        color: #2f7064;
        margin: 10px 0;
    }
    .stat-label {
        font-size: 0.9rem;
        color: #666;
    }
    .chart-container-sm {
        height: 300px;
        margin-bottom: 30px;
        position: relative;
    }
    /* Espacio entre la gráfica y la tabla */
    #dictamenes-table_wrapper {
        margin-top: 20px;
    }
    /* Evitar que los controles se encimen */
    #dictamenes-table_wrapper .row:first-child {
        margin-bottom: 10px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        text-align: center;
        padding: 20px 10px;
    }
    .stat-number {
        font-size: 2.8rem;
        font-weight: 700;
        color: #2f7064;
        margin: 10px 0;
    }
    .stat-label {
        font-size: 0.9rem;
        color: #666;
    }
    .chart-container-sm {
        height: 300px;
        margin-bottom: 30px;
        position: relative;
    }
    #dictamenes-table_wrapper {
        margin-top: 20px;
    }
    #dictamenes-table_wrapper .row:first-child {
        margin-bottom: 10px;
    }
    
    /* Estilos para badges de colores */
    .badge {
        font-weight: 500;
        padding: 4px 8px;
        font-size: 0.75rem;
        border-radius: 4px;
        display: inline-block;
    }
    .badge-enviado { background: #28a745; color: white; }
    .badge-aprobado { background: #17a2b8; color: white; }
    .badge-publicado { background: #6f42c1; color: white; }
    .badge-regreso { background: #dc3545; color: white; }
    .badge-borrador { background: #ffc107; color: #000; }
    .badge-revision { background: #6c757d; color: white; }
    .badge-no-aplica { background: #6c757d; color: white; }
</style>
@endpush

@section('content')

<div class="container mt-4">
    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-number">{{ $nuevo ?? 0 }}</div>
                <div class="stat-label">Dictámenes Enviados</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="stat-number">{{ $total ?? 0 }}</div>
                <div class="stat-label">Total de dictámenes</div>
            </div>
        </div>
    </div>

    <!-- Gráfica -->
    <div class="chart-container-sm">
        <h5 class="mb-3">Número de dictámenes recibidos por mes</h5>
        <canvas id="chartMeses"></canvas>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table id="dictamenes-table" class="table table-hover nowrap">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
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
                    <td>{{ $d->oficio ?? '—' }}</td>
                    <td>{{ $d->dependencia_empres ?? '—' }}</td>
                    <td title="{{ $d->asunto ?? '' }}">{{ \Illuminate\Support\Str::limit($d->asunto ?? '', 60) }}</td>
                    <td>
                        @php
                            $s = $d->estatus ?? '';
                            $s_lower = strtolower($s);
                            $badgeClass = 'badge-no-aplica';
                            if (str_contains($s_lower, 'enviado')) {
                                $badgeClass = 'badge-enviado';
                            } elseif (str_contains($s_lower, 'aprobado')) {
                                $badgeClass = 'badge-aprobado';
                            } elseif (str_contains($s_lower, 'publicado')) {
                                $badgeClass = 'badge-publicado';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}" title="{{ $d->estatus }}">
                            {{ strlen($s) > 25 ? substr($s, 0, 22).'...' : $s }}
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/*$(document).ready(function() {
    // Inicializar DataTables solo si no existe
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
        "language": {
            "search": "Buscar:",
            "paginate": { "previous": "‹", "next": "›" },
            "emptyTable": "No hay dictámenes",
            "zeroRecords": "No se encontró nada"
        }
    });
    
    // Gráfica de Chart.js
    const ctx = document.getElementById('chartMeses');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($meses ?? ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']) !!},
                datasets: [
                    {
                        label: 'Solicitudes',
                        data: {!! json_encode($solicitudes ?? []) !!},
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Días hábiles',
                        data: {!! json_encode($diasHabiles ?? []) !!},
                        type: 'line',
                        borderColor: 'rgb(28, 32, 34)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});*/
</script>
@endsection