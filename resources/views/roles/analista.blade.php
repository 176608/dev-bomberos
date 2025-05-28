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
                <button class="btn btn-secondary mb-2">Editar parametros del reporte</button>
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
                                    <th>Calle</th>
                                    <th>Y Calle</th>
                                    <th>Colonia</th>
                                    <th>Marca</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hidrantes as $hidrante)
                                    <tr>
                                        <td>{{ $hidrante->id }}</td>
                                        <td>{{ $hidrante->fecha_alta }}</td>
                                        <td>{{ $hidrante->callePrincipal ? $hidrante->callePrincipal->Nomvial : 'Calle Principal no especificada' }}</td>
                                        <td>{{ $hidrante->calleSecundaria ? $hidrante->calleSecundaria->Nomvial : 'Calle Secundaria no especificada' }}</td>
                                        <td>{{ $hidrante->coloniaLocacion ? $hidrante->coloniaLocacion->NOMBRE : 'Colonia no especificada' }}</td>
                                        <td>{{ $hidrante->marca ? $hidrante->marca : 'S/A' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-hidrante" data-bs-toggle="modal" 
                                                data-hidrante-id="{{ $hidrante->id }}">
                                                Editar
                                            </button>
                                        </td>
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
@include('partials.hidrante-form')

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

        // Manejador para el botón de editar
        $('.edit-hidrante').on('click', function() {
            const hidranteId = $(this).data('hidrante-id');
            
            // Hacer la petición AJAX para obtener los datos del hidrante y el formulario
            $.get(`/hidrantes/${hidranteId}/edit`, function(data) {
                // Crear un div temporal para insertar el formulario
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;
                
                // Agregar el formulario al body y mostrar el modal
                document.body.appendChild(tempDiv);
                
                const modalId = `#editarHidranteModal${hidranteId}`;
                $(modalId).modal('show');
                
                // Limpiar el modal cuando se cierre
                $(modalId).on('hidden.bs.modal', function() {
                    tempDiv.remove();
                });
            });
        });
    });
</script>
@endsection