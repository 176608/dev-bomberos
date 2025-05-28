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

        // Manejador para el botón de editar
        $(document).on('click', '.edit-hidrante', function(e) {
            e.preventDefault();
            const hidranteId = $(this).data('hidrante-id');
            
            // Usar route() de Laravel para generar la URL correcta
            $.ajax({
                url: "{{ route('hidrantes.edit', ':id') }}".replace(':id', hidranteId),
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Remover modal existente si hay uno
                    $('.modal-backdrop').remove();
                    $('#editarHidranteModal' + hidranteId).remove();
                    
                    // Agregar el nuevo modal al DOM
                    $('body').append(response);
                    
                    // Mostrar el modal usando Bootstrap 5
                    const modal = new bootstrap.Modal(document.getElementById('editarHidranteModal' + hidranteId));
                    modal.show();
                },
                error: function(xhr) {
                    console.error('Error al cargar el hidrante:', xhr);
                    alert('Error al cargar los datos del hidrante. Por favor, intente nuevamente.');
                }
            });
        });

        // Limpiar modales cuando se cierren
        $(document).on('hidden.bs.modal', '.modal', function() {
            $(this).remove();
            $('.modal-backdrop').remove();
        });
    });
</script>
@endsection