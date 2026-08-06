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
<div class="container-fluid pt-4 bg-fonde" style="min-height: 100vh;">
  <div class="container pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="bi bi-eye-slash"></i> Dictámenes Deshabilitados</h2>
        <a href="{{ route('gestor-dictamenes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Regresar a gestor
        </a>
    </div>

    <div id="tablaDeletedCard">
        <table id="deleted-table" class="table table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Número de Oficio Recibido</th>
                    <th>Deshabilitado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dictamenes as $log)
                <tr>
                    <td data-order="{{ $log->fecha }}">{{ $log->fecha ? \Carbon\Carbon::parse($log->fecha)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $log->oficio ?? 'S/N' }}</td>
                    <td>{{ $log->deleted_by }}</td>
                    <td>
                        <form action="{{ route('gestor-dictamenes.restore', $log->dictamen_id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Rehabilitar (estatus S/D) y regresarlo al gestor"
                                    onclick="return confirm('¿Deseas restaurar este dictamen? Quedará con estatus S/D y aparecerá en el gestor.');">
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
        "order": [[0, 'desc']],
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
