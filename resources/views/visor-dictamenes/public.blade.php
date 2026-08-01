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
    <div class="row mb-3 g-2">
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
    <div class="mb-4">
        <div class="chart-wrapper">
            <div class="chart-container-sm">
                <h5 class="mb-3"><i class="bi bi-bar-chart"></i> Número de dictámenes recibidos por mes</h5>
                <div style="height: 250px; position: relative; overflow: hidden;">
                    <canvas id="chartMeses"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros (server-side, se aplican al seleccionar; solo dictámenes ENVIADOS) -->
    <div class="card mb-3" style="border-left: 4px solid #2f7064;">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('visor-dictamenes.public') }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <span class="badge bg-success me-2">Solo estatus: ENVIADO</span>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0 me-2" for="anioFilter"><strong>Año:</strong></label>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm filter-select" id="anioFilter" name="anio" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach($anios as $a)
                            <option value="{{ $a }}" @selected(request('anio') === (string) $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0 me-2" for="revisadoFilter"><strong>Revisado por:</strong></label>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm filter-select" id="revisadoFilter" name="revisado_por" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach($revisadosPor as $r)
                            <option value="{{ $r }}" @selected(request('revisado_por') === $r)>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0 me-2" for="dependenciaFilter"><strong>Dependencia:</strong></label>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm filter-select" id="dependenciaFilter" name="dependencia" onchange="this.form.submit()">
                        <option value="">Todas</option>
                        @foreach($dependencias as $dep)
                            <option value="{{ $dep }}" @selected(request('dependencia') === $dep)>{{ $dep }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0 me-2" for="nombrePuestoFilter"><strong>Nombre/Puesto:</strong></label>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm filter-select" id="nombrePuestoFilter" name="nombre_puesto" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach($nombresPuestos as $np)
                            <option value="{{ $np }}" @selected(request('nombre_puesto') === $np)>{{ $np }}</option>
                        @endforeach
                    </select>
                </div>
                @if(request()->hasAny(['anio', 'revisado_por', 'dependencia', 'nombre_puesto']))
                    <div class="col-auto">
                        <a href="{{ route('visor-dictamenes.public') }}" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table id="dictamenes-table" class="table table-hover nowrap">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Año</th>
                    <th># Oficio</th>
                    <th>Dependencia</th>
                    <th>Asunto</th>
                    <th>Estatus</th>
                    <th>Archivo</th>
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
                    <td>
                        @php
                            $estadoA = $d->estado_archivo ?? 'sin_clave';
                            $encontradosA = $d->archivos_encontrados ?? [];
                        @endphp
                        @if($estadoA === 'encontrado')
                            <span class="badge bg-primary" title="Encontrado en el servidor: {{ $encontradosA[0] ?? '' }}">
                                <i class="bi bi-check-circle"></i> Encontrado
                            </span>
                        @elseif($estadoA === 'no_encontrado')
                            <span class="badge bg-danger" title="No se encontró ningún archivo con la clave {{ $d->clave_documento }} en el servidor">
                                <i class="bi bi-x-circle"></i> No encontrado
                            </span>
                        @elseif($estadoA === 'multiples')
                            <span class="badge bg-warning text-dark" title="{{ implode(PHP_EOL, $encontradosA) }}">
                                <i class="bi bi-exclamation-triangle"></i> {{ count($encontradosA) }} coincidencias
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
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

    $('.filter-select').select2({
        width: '200px',
        dropdownAutoWidth: true
    });

    const ctx = document.getElementById('chartMeses');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($meses ?? []) !!},
                datasets: [
                    {
                        label: 'Solicitudes',
                        data: {!! json_encode($solicitudes ?? []) !!},
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
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
});
</script>
@endsection
