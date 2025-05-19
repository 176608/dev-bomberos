@extends('layouts.app')

@section('title', 'Panel de Desarrollador')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Gestión de Colonias</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="coloniasTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Fecha reg IMIP</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>IDKEY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($colonias as $colonia)
                        <tr>
                            <td>{{ $colonia->FECHAUBICAIMIP }}</td>
                            <td>{{ $colonia->NOMBRE }}</td>
                            <td>{{ $colonia->TIPO }}</td>
                            <td>{{ $colonia->IDKEY }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#coloniasTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[0, 'asc']]
        });
    });
</script>
@endsection