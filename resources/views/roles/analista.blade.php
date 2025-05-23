@extends('layouts.app')

@section('title', 'Bomberos')

@section('content')
<div class="container mt-4">
    <!-- Contenedor superior -->
    <div class="d-flex align-items-center mb-4">
        <!-- Mitad izquierda: imagen -->
        <div class="col-md-6 d-flex justify-content-start">
            <img src="{{ asset('images/hidrante.png') }}" alt="Hidrante" width="100" height="100">
        </div>

        <!-- Mitad derecha: botones -->
        <div class="col-md-6 d-flex flex-column justify-content-end">
            <button class="btn btn-primary mb-2">Alta de hidrante</button>
            <button class="btn btn-secondary mb-2">Editar información de hidrante</button>
            <button class="btn btn-success mb-2" id="verReporteBtn">Ver reporte de hidrantes</button>
        </div>
    </div>

    <!-- Tabla oculta - Spoiler -->
    <div id="tabla-hidrantes" class="accordion-collapse collapse">
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
                                <th>Fecha Alta</th>
                                <th>Colonia</th>
                                <th>ID Colonia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registros as $registro)
                                <tr>
                                    <td>{{ $registro->id }}</td>
                                    <td>{{ $registro->fecha_alta }}</td>
                                    <td>{{ $registro->colonia }}</td>
                                    <td>{{ $registro->id_colonia }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializa DataTable
        var table = $('#hidrantesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            paging: true,
            searching: true,
            info: true,
            autoWidth: false
        });

        // Funcionalidad del botón "Ver reporte de hidrantes"
        $('#verReporteBtn').on('click', function () {
            $('#tabla-hidrantes').collapse('show'); // Muestra el accordion
            $('html, body').animate({ scrollTop: $('#tabla-hidrantes').offset().top }, 1000); // Centra la vista
        });
    });
</script>
@endsection