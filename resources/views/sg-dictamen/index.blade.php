@extends('layouts.app')

@section('title', 'Dictámenes - IMIP Ciudad Juárez')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dictamenes.css') }}">
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

    <!-- Gráfica con límite de viewport -->
    <div class="chart-wrapper">
        <div class="chart-container-sm">
            <h5 class="mb-3"><i class="bi bi-bar-chart"></i> Número de dictámenes recibidos por mes</h5>
            <!-- Gráfica con límite de viewport <canvas id="chartMeses"></canvas>-->
        </div>
    </div>

    <!-- Botón Agregar (solo Administrador Dictamenes y Desarrollador) -->
    @if(auth()->check() && auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createModal">
        <i class="bi bi-plus-circle"></i> Agregar nuevo dictamen
    </button>
    
    <!-- Ver eliminados (solo Administrador Dictamenes y Desarrollador) -->
    <a href="{{ route('sg-dictamen.deleted') }}" class="btn btn-outline-danger mb-3">
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
                    @if(auth()->check())
                        <th>Nombre / Puesto</th>
                        <th>Revisado por</th>
                        <th>Núm. Oficio</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    @endif
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
                        @php
                            $s = $d->estatus ?? '';
                            $s_lower = strtolower($s);
                            $badgeClass = 'badge-no-aplica';
                            if (str_contains($s_lower, 'enviado')) {
                                $badgeClass = 'badge-enviado';
                            } elseif (str_contains($s_lower, 'regreso') || str_contains($s_lower, 'detenido')) {
                                $badgeClass = 'badge-regreso';
                            } elseif (str_contains($s_lower, 'borrador') || str_contains($s_lower, 'informativo')) {
                                $badgeClass = 'badge-borrador';
                            }
                        @endphp
                        <span class="badge {{ $badgeClass }}" title="{{ $d->estatus }}">
                            {{ strlen($s) > 25 ? substr($s, 0, 22).'...' : $s }}
                        </span>
                    </td>

                    @if(auth()->check())
                        <td>{{ $d->nombre_puesto ?? '—' }}</td>
                        <td>{{ $d->revisado_por ?? '—' }}</td>
                        <td>{{ $d->numero_oficio ?? '—' }}</td>
                        <td>{{ $d->observaciones ?? '—' }}</td>
                        <td>
                            <!-- Editar: Admin Dictamenes, Editor Dictamenes y Desarrollador -->
                            @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Editor Dictamenes', 'Desarrollador']))
                                <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $d->id }}" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class="bi bi-pencil"></i> Editar
                                </button>
                            @endif

                            <!-- Eliminar: solo Administrador Dictamenes y Desarrollador -->
                            @if(auth()->user()->hasAnyRole(['Administrador Dictamenes', 'Desarrollador']))
                                <form id="delete-form-{{ $d->id }}" action="{{ route('sg-dictamen.destroy', $d->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button class="btn btn-sm btn-danger delete-btn"
                                        onclick="if(confirm('¿Estás seguro que deseas eliminar este dictamen?')) document.getElementById('delete-form-{{ $d->id }}').submit();">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            @endif
                        </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modals (Crear) -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Dictamen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createForm" method="POST" action="{{ route('sg-dictamen.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Fecha</label>
                        <input type="date" class="form-control" name="fecha" required>
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
                        <textarea class="form-control" name="asunto"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Núm. Oficio</label>
                        <input type="text" class="form-control" name="numero_oficio">
                    </div>
                    <div class="mb-3">
                        <label>Revisado por</label>
                        <input type="text" class="form-control" name="revisado_por">
                    </div>
                    <div class="mb-3">
                        <label>Estatus</label>
                        <select class="form-control" name="estatus" required>
                            <option value="">Seleccione un estatus...</option>
                            <option value="ENVIADO">ENVIADO</option>
                            <option value="BORRADOR">BORRADOR</option>
                            <option value="PENDIENTE">PENDIENTE</option>
                            <option value="EN PROCESO">EN PROCESO</option>
                              <option value="EN REVISION">EN REVISION</option>
                            <option value="DETENIDO">DETENIDO</option>
                            <option value="SE REGRESO">SE REGRESO</option>
                            <option value="INFORMATIVO">INFORMATIVO</option>
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

<!-- Modals (Editar) -->
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
                        <textarea class="form-control" id="asunto_edit" name="asunto"></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Núm. Oficio</label>
                        <input type="text" class="form-control" id="numero_oficio_edit" name="numero_oficio">
                    </div>
                    <div class="mb-3">
                        <label>Revisado por</label>
                        <input type="text" class="form-control" id="revisado_por_edit" name="revisado_por">
                    </div>
                   <div class="mb-3">
    <label>Estatus</label>
    <select class="form-control" id="estatus_edit" name="estatus" required>
        <option value="">Seleccione un estatus...</option>
        <option value="ENVIADO">ENVIADO</option>
        <option value="BORRADOR">BORRADOR</option>
        <option value="PENDIENTE">PENDIENTE</option>
        <option value="EN PROCESO">EN PROCESO</option>
        <option value="EN REVISION">EN REVISION</option>
        <option value="DETENIDO">DETENIDO</option>
        <option value="SE REGRESO">SE REGRESO</option>
        <option value="INFORMATIVO">INFORMATIVO</option>
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
        [5, 10,15,20,50,100, 150, 10000],
        ['5','10','15','20','50','100', '150', 'Todas']
    ],
    "pageLength": 0,
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

    // EDITAR - Cargar datos desde atributos de la fila (SIN AJAX)
    $('#dictamenes-table').on('click', '.edit-btn', function() {
        const $row = $(this).closest('tr');
        const id = $(this).data('id');
        
        if (!id) {
            alert('Error: No se encontró el ID del dictamen');
            return;
        }
        
        // Cargar datos directamente desde data-* attributes
        $('#fecha_edit').val($row.data('fecha') || '');
        $('#oficio_edit').val($row.data('oficio') || '');
        $('#nombre_puesto_edit').val($row.data('nombre-puesto') || '');
        $('#dependencia_empres_edit').val($row.data('dependencia') || '');
        $('#asunto_edit').val($row.data('asunto') || '');
        $('#numero_oficio_edit').val($row.data('numero-oficio') || '');
        $('#revisado_por_edit').val($row.data('revisado-por') || '');
        $('#estatus_edit').val($row.data('estatus') || '');
        $('#observaciones_edit').val($row.data('observaciones') || '');
        
        // Configurar action del formulario usando Blade para generar URL segura
        $('#editForm').attr('action', `/admin/dictamenes/${id}`);
        
        // Mostrar modal
        $('#editModal').modal('show');
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

});
</script>
@endsection