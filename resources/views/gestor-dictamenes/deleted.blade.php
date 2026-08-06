@extends('layouts.app')

@section('title', 'Dictámenes Deshabilitados - Gestor de Dictámenes')

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
        <h2><i class="bi bi-eye-slash"></i> Dictámenes Deshabilitados</h2>
        <a href="{{ route('gestor-dictamenes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="table-responsive">
        <table id="deleted-table" class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th># Registro</th>
                    <th>Fecha</th>
                    <th># Oficio</th>
                    <th>Dependencia</th>
                    <th>Nombre / Puesto</th>
                    <th>Asunto</th>
                    <th>Núm. Oficio</th>
                    <th>Revisado por</th>
                    <th>Observaciones</th>
                    <th>Archivo</th>
                    <th>Estatus Anterior</th>
                    <th>Eliminado Por (ID)</th>
                    <th>Fecha de Eliminación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictamenes as $log)
                <tr>
                    <td>{{ $log->dictamen_id }}</td>
                    <td>{{ $log->fecha ? \Carbon\Carbon::parse($log->fecha)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $log->oficio ?? 'S/N' }}</td>
                    <td>{{ $log->dependencia_empres ?? 'N/A' }}</td>
                    <td>{{ $log->nombre_puesto ?? 'N/A' }}</td>
                    <td title="{{ $log->asunto }}">{{ \Illuminate\Support\Str::limit($log->asunto, 50) }}</td>
                    <td>{{ $log->numero_oficio ?? 'N/A' }}</td>
                    <td>{{ $log->revisado_por ?? 'N/A' }}</td>
                    <td>{{ $log->observaciones ? \Illuminate\Support\Str::limit($log->observaciones, 30) : 'N/A' }}</td>
                    <td>
                        @if($log->archivo)
                            <span class="badge bg-info text-dark">{{ $log->archivo }} archivo(s) ligado(s)</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-danger">{{ $log->estatus }}</span>
                    </td>
                    <td>{{ $log->deleted_by }}</td>
                    <td>{{ $log->deleted_at ? \Carbon\Carbon::parse($log->deleted_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td>
                        <form action="{{ route('gestor-dictamenes.restore', $log->dictamen_id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('¿Estás seguro que deseas restaurar este dictamen?');">
                                <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                            </button>
                        </form>
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
<script>
$(document).ready(function() {
    $('#deleted-table').DataTable({
        "paging": true,
        "lengthMenu": [[10, 25, 50, 100, -1], ['10', '25', '50', '100', 'Todos los registros']],
        "pageLength": 25,
        "searching": true,
        "info": false,
        "ordering": true,
        "order": [[12, 'desc']],
        "scrollX": true,
        "autoWidth": false,
        "language": {
            "search": "Buscar:",
            "paginate": { "previous": "‹", "next": "›" },
            "emptyTable": "No hay dictámenes deshabilitados",
            "zeroRecords": "No se encontró nada"
        }
    });

    $('#deleted-table_length').addClass('mb-3');
});
</script>
@endsection
