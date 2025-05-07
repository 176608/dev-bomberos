@extends('layouts.app')

@section('title', 'Dashboard - Hidrantes')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Listado de Hidrantes</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
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
                    @forelse($registros as $registro)
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
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay registros de hidrantes disponibles</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection