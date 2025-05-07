@extends('layouts.app')

@section('title', 'Dashboard - Hidrantes')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Listado de Hidrantes</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="hidrantesTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha Inspección</th>
                        <th>Número Hidrante</th>
                        <th>Calle</th>
                        <th>Y Calle</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registros as $registro)
                        <tr>
                            <td>{{ $registro->id }}</td>
                            <td>{{ $registro->fecha_inspeccion }}</td>
                            <td>{{ $registro->numero_hidrante }}</td>
                            <td>{{ $registro->calle }}</td>
                            <td>{{ $registro->y_calle }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Ver</a>
                                <a href="#" class="btn btn-sm btn-warning">Editar</a>
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
<script>
    $(document).ready(function() {
        $('#hidrantesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            pageLength: 25,
            order: [[0, 'desc']],
            columns: [
                {data: 'id'},
                {data: 'fecha_inspeccion'},
                {data: 'numero_hidrante'},
                {data: 'calle'},
                {data: 'y_calle'},
                {data: 'acciones', orderable: false, searchable: false}
            ]
        });
    });
</script>
@endsection