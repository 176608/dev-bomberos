@extends('layouts.app')

@section('title', 'Bomberos')

@section('content')

<style>
    .custom-image-size {
        width: 40vw;
        height: auto;
        object-fit: contain;
    }

    .card-title {
        margin-left: 1rem;
        font-weight: bold;
    }
</style>

<div class="container mt-4">

<div class="card mb-4">
    <div class="card-body">

        <!-- Uso de row y col-6 -->
        <div class="row g-3 align-items-center">

            <!-- Columna izquierda (6/12) - Imagen -->
            <div class="col-6 d-flex align-items-center">
                <img src="{{ asset('img/logo/Escudo_Ciudad_Juarez.png') }}" alt="Escudo" class="img-fluid custom-image-size">
            </div>

            <!-- Columna derecha (6/12) - Botones -->
            <div class="col-6 d-flex flex-column justify-content-center">
                <button class="btn btn-primary mb-2">Alta de hidrante</button>
                <button class="btn btn-secondary mb-2">Editar información de hidrante</button>
                <button class="btn btn-success" id="verReporteBtn" data-bs-toggle="collapse" data-bs-target="#tabla-hidrantes">
                    Ver reporte de hidrantes <i class="bi bi-chevron-down"></i>
                </button>
            </div>

        </div>
    </div>
</div>

    <!-- Accordion con tabla -->
    <div class="accordion" id="accordionHidrantes">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tabla-hidrantes" aria-expanded="false">
                    Mostrar/Ocultar Reporte de Hidrantes
                </button>
            </h2>
            <div id="tabla-hidrantes" class="accordion-collapse collapse">
                <div class="accordion-body">
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

</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Inicializar DataTable
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

        // Funcionalidad del botón "Ver reporte"
        $('#verReporteBtn').on('click', function () {
            const target = document.getElementById('tabla-hidrantes');
            if (target.classList.contains('show')) {
                $('html, body').animate({ scrollTop: $('#accordionHidrantes').offset().top }, 1000);
            } else {
                setTimeout(() => {
                    $('html, body').animate({ scrollTop: $('#accordionHidrantes').offset().top }, 1000);
                }, 300); // Pequeño retraso para esperar que el accordion se abra
            }
        });
    });
</script>
@endsection