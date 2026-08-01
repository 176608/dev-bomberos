@extends('layouts.app')

@section('title', 'Gestor de Dictámenes - IMIP Ciudad Juárez')

@push('styles')
<style>
:root {
    --imip-blue: #2f7064;
}

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
    color: var(--imip-blue) !important;
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
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="stat-number" style="color:#6c757d;">{{ $total }}</div>
                <div class="stat-label">Total</div>
            </div>
        </div>
        @foreach($conteoEstatus as $estatus => $count)
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="stat-number" style="color: {{ $badgeColores[$estatus] ?? '#6c757d' }};">{{ $count }}</div>
                <div class="stat-label">{{ $estatus }}</div>
            </div>
        </div>
        @endforeach
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

    <!-- Filtro por estatus (server-side) -->
    <div class="card mb-3" style="border-left: 4px solid #2f7064;">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('gestor-dictamenes.index') }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 me-2" for="estatusFilter"><strong>Filtrar por estatus:</strong></label>
                </div>
                <div class="col-auto">
                    <select class="form-select form-select-sm" id="estatusFilter" name="estatus" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach(\App\Models\GestorDictamenes\Dictamen::STATUSES as $estatus)
                            <option value="{{ $estatus }}" @selected(request('estatus') === $estatus)>{{ $estatus }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Buscar..." value="{{ request('search') }}" style="min-width: 260px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Filtrar</button>
                    @if(request()->hasAny(['estatus', 'search']))
                        <a href="{{ route('gestor-dictamenes.index') }}" class="btn btn-sm btn-link">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Botón Agregar + Ver Eliminados (solo Administrador Dictamenes y Desarrollador) -->
    @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle"></i> Agregar nuevo dictamen
        </button>
        <a href="{{ route('gestor-dictamenes.deleted') }}" class="btn btn-outline-danger mb-3">
            <i class="bi bi-trash"></i> Ver Eliminados
        </a>
    @endif

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
                    <th>Nombre / Puesto</th>
                    <th>Revisado por</th>
                    <th>Núm. Oficio</th>
                    <th>Observaciones</th>
                    <th>Archivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictamenes as $d)
                <tr
                    data-fecha="{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') : '' }}"
                    data-oficio="{{ $d->oficio ?? '' }}"
                    data-nombre-puesto="{{ $d->nombre_puesto ?? '' }}"
                    data-dependencia="{{ $d->dependencia_empres ?? '' }}"
                    data-asunto="{{ $d->asunto ?? '' }}"
                    data-numero-oficio="{{ $d->numero_oficio ?? '' }}"
                    data-revisado-por="{{ $d->revisado_por ?? '' }}"
                    data-estatus="{{ $d->estatus ?? '' }}"
                    data-observaciones="{{ $d->observaciones ?? '' }}"
                >
                    <td>{{ $d->fecha ? \Carbon\Carbon::parse($d->fecha)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $d->oficio ?? '—' }}</td>
                    <td>{{ $d->dependencia_empres ?? '—' }}</td>
                    <td title="{{ $d->asunto ?? '' }}">{{ \Illuminate\Support\Str::limit($d->asunto ?? '', 60) }}</td>
                    <td>
                        <span class="badge" style="background-color: {{ $badgeColores[$d->estatus ?? ''] ?? '#6c757d' }}; color: {{ ($d->estatus ?? '') === 'BORRADOR PARA FIRMA' ? '#212529' : 'white' }}; font-weight: 500; padding: 4px 8px; font-size: 0.75rem; border-radius: 4px; display: inline-block;">
                            {{ $d->estatus ?? 'S/D' }}
                        </span>
                    </td>
                    <td>{{ $d->nombre_puesto ?? '—' }}</td>
                    <td>{{ $d->revisado_por ?? '—' }}</td>
                    <td>{{ $d->numero_oficio ?? '—' }}</td>
                    <td>{{ $d->observaciones ?? '—' }}</td>
                    <td>
                        @if(!empty($d->archivo))
                            <span class="badge bg-info text-dark" title="Nombre físico en servidor">{{ $d->archivo }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Editor Dictamenes', 'Desarrollador']))
                            <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $d->id }}" data-route="{{ route('gestor-dictamenes.update', $d->id) }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                        @endif

                        @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
                            <form id="delete-form-{{ $d->id }}" action="{{ route('gestor-dictamenes.destroy', $d->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button class="btn btn-sm btn-danger delete-btn"
                                    onclick="if(confirm('¿Estás seguro que deseas eliminar este dictamen?')) document.getElementById('delete-form-{{ $d->id }}').submit();">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Dictamen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('gestor-dictamenes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Fecha</label>
                        <input type="date" class="form-control" name="fecha" required>
                    </div>
                    <div class="mb-3">
                        <label>Núm. Oficio</label>
                        <input type="text" class="form-control" name="numero_oficio" required>
                    </div>
                    <div class="mb-3">
                        <label># Oficio</label>
                        <input type="text" class="form-control" name="oficio">
                    </div>
                    <div class="mb-3">
                        <label>Nombre / Puesto</label>
                        <input type="text" class="form-control" name="nombre_puesto">
                    </div>
                    <div class="mb-3">
                        <label>Dependencia</label>
                        <input type="text" class="form-control" name="dependencia_empres">
                    </div>
                    <div class="mb-3">
                        <label>Asunto</label>
                        <textarea class="form-control" name="asunto" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Revisado por</label>
                        <input type="text" class="form-control" name="revisado_por">
                    </div>
                    <div class="mb-3">
                        <label>Estatus</label>
                        <select class="form-control" name="estatus" required>
                            <option value="">Seleccione un estatus...</option>
                            @foreach(\App\Models\GestorDictamenes\Dictamen::STATUSES as $estatus)
                                <option value="{{ $estatus }}">{{ $estatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Observaciones</label>
                        <textarea class="form-control" name="observaciones"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Dictamen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST" onsubmit="return confirm('¿Seguro que deseas editar este dictamen?');">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Fecha</label>
                        <input type="date" class="form-control" id="fecha_edit" name="fecha" required>
                    </div>
                    <div class="mb-3">
                        <label>Núm. Oficio</label>
                        <input type="text" class="form-control" id="numero_oficio_edit" name="numero_oficio" required>
                    </div>
                    <div class="mb-3">
                        <label># Oficio</label>
                        <input type="text" class="form-control" id="oficio_edit" name="oficio">
                    </div>
                    <div class="mb-3">
                        <label>Nombre / Puesto</label>
                        <input type="text" class="form-control" id="nombre_puesto_edit" name="nombre_puesto">
                    </div>
                    <div class="mb-3">
                        <label>Dependencia</label>
                        <input type="text" class="form-control" id="dependencia_empres_edit" name="dependencia_empres">
                    </div>
                    <div class="mb-3">
                        <label>Asunto</label>
                        <textarea class="form-control" id="asunto_edit" name="asunto" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Revisado por</label>
                        <input type="text" class="form-control" id="revisado_por_edit" name="revisado_por">
                    </div>
                    <div class="mb-3">
                        <label>Estatus</label>
                        <select class="form-control" id="estatus_edit" name="estatus" required>
                            <option value="">Seleccione un estatus...</option>
                            @foreach(\App\Models\GestorDictamenes\Dictamen::STATUSES as $estatus)
                                <option value="{{ $estatus }}">{{ $estatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Observaciones</label>
                        <textarea class="form-control" id="observaciones_edit" name="observaciones"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
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
        "lengthMenu": [
            [10, 25, 50, 100, 150, -1],
            ['10', '25', '50', '100', '150', 'Todas']
        ],
        "pageLength": -1,
        "searching": true,
        "info": false,
        "ordering": true,
        "order": [],
        "scrollX": true,
        "autoWidth": false,
        "language": {
            "search": "Buscar:",
            "paginate": { "previous": "‹", "next": "›" },
            "emptyTable": "No hay dictámenes",
            "zeroRecords": "No se encontró nada"
        }
    });

    $('#dictamenes-table_length').addClass('mb-3');

    // EDITAR - Cargar datos desde atributos de la fila (SIN AJAX)
    $('#dictamenes-table').on('click', '.edit-btn', function() {
        const $row = $(this).closest('tr');
        const id = $(this).data('id');
        const route = $(this).data('route');

        if (!id) {
            alert('Error: No se encontró el ID del dictamen');
            return;
        }

        $('#fecha_edit').val($row.data('fecha') || '');
        $('#oficio_edit').val($row.data('oficio') || '');
        $('#nombre_puesto_edit').val($row.data('nombre-puesto') || '');
        $('#dependencia_empres_edit').val($row.data('dependencia') || '');
        $('#asunto_edit').val($row.data('asunto') || '');
        $('#numero_oficio_edit').val($row.data('numero-oficio') || '');
        $('#revisado_por_edit').val($row.data('revisado-por') || '');
        $('#estatus_edit').val($row.data('estatus') || '');
        $('#observaciones_edit').val($row.data('observaciones') || '');

        $('#editForm').attr('action', route);
        $('#editModal').modal('show');
    });

    // Gráfica de Chart.js
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
});
</script>
@endsection
