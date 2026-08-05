@extends('layouts.app')

@section('title', 'Historial de Cambios - Gestor de Dictámenes')

@push('styles')
<style>
table {
    font-size: 0.85rem;
}
</style>
@endpush

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history"></i> Historial de Cambios</h2>
        <a href="{{ route('gestor-dictamenes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @php
        $accionColores = [
            'CREAR' => 'success',
            'MODIFICAR' => 'primary',
            'DESHABILITAR' => 'danger',
            'RESTAURAR' => 'info',
        ];
    @endphp

    <div class="table-responsive">
        <table id="historial-table" class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Acción</th>
                    <th>Dictamen</th>
                    <th>Estatus</th>
                    <th>Núm. Oficio</th>
                    <th>Dependencia</th>
                    <th>Asunto</th>
                    <th>Usuario (ID)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cambios as $c)
                <tr>
                    <td data-order="{{ $c->created_at }}">{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>
                        <span class="badge bg-{{ $accionColores[$c->accion ?? ''] ?? 'secondary' }}">
                            {{ $c->accion ?? '—' }}
                        </span>
                    </td>
                    <td>#{{ $c->dictamen_id }}</td>
                    <td>{{ $c->estatus ?? '—' }}</td>
                    <td>{{ $c->numero_oficio_raw ?? '—' }}</td>
                    <td>{{ $c->dependencia_empres ?? '—' }}</td>
                    <td title="{{ $c->asunto ?? '' }}">{{ \Illuminate\Support\Str::limit($c->asunto ?? '', 60) }}</td>
                    <td>{{ $c->deleted_by ?? $c->updated_by ?? $c->created_by ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No hay cambios registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(document).ready(function() {
    $('#historial-table').DataTable({
        "paging": true,
        "lengthMenu": [[10, 25, 50, 100, -1], ['10', '25', '50', '100', 'Todos los registros']],
        "pageLength": 25,
        "searching": true,
        "info": false,
        "ordering": true,
        "order": [[0, 'desc']],
        "scrollX": true,
        "autoWidth": false,
        "language": {
            "search": "Buscar:",
            "paginate": { "previous": "‹", "next": "›" },
            "emptyTable": "No hay cambios registrados",
            "zeroRecords": "No se encontró nada"
        }
    });

    $('#historial-table_length').addClass('mb-3');
});
</script>
@endsection
