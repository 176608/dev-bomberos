@extends('layouts.app')

@section('title', 'Panel de Analista')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Panel de Analista</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="hidrantesTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha Alta</th>
                        <th>Colonia</th>
                        <th>Calle</th>
                        <th>Y Calle</th>
                        <th>Oficial</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registros as $registro)
                        <tr>
                            <td>{{ $registro->id }}</td>
                            <td>{{ $registro->fecha_alta }}</td>
                            <td>{{ $registro->colonia }}</td>
                            <td>{{ $registro->calle }}</td>
                            <td>{{ $registro->y_calle }}</td>
                            <td>{{ $registro->oficial }}</td>
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
            order: [[0, 'desc']]
        });
    });
</script>
@endsection