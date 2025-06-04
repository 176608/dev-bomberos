@extends('layouts.app')More actions

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

    .button-text {
        transition: opacity 0.3s ease;
    }

    .btn:disabled .button-text {
        opacity: 0.5;
    }
</style>

<div class="container mt-4">
    <!-- Primera card con botones -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-6 d-flex align-items-center">
                    <img src="{{ asset('img/logo/Escudo_Ciudad_Juarez.png') }}" alt="Escudo" class="img-fluid custom-image-size">
                </div>
                <div class="col-6 d-flex flex-column justify-content-center">
                    <button class="btn btn-primary mb-2" id="btnNuevoHidrante">
                        <i class="bi bi-plus-square"></i>
                        <span class="button-text">Alta de hidrante</span>
                        <span class="spinner-border spinner-border-sm ms-1 d-none" role="status" aria-hidden="true"></span>
                    </button>
                    <button class="btn btn-secondary mb-2">
                        <i class="bi bi-gear-fill"></i> Editar parámetros del reporte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda card con la tabla -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">Reporte de Hidrantes</h5>
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
                                <td>{{ $hidrante->fecha_inspeccion }}</td>
                                <td>{{ $hidrante->callePrincipal ? $hidrante->callePrincipal->Nomvial : 'Calle Principal no especificada' }}</td>
                                <td>{{ $hidrante->calleSecundaria ? $hidrante->calleSecundaria->Nomvial : 'Calle Secundaria no especificada' }}</td>
                                <td>{{ $hidrante->coloniaLocacion ? $hidrante->coloniaLocacion->NOMBRE : 'Colonia no especificada' }}</td>
                                <td>{{ $hidrante->marca ? $hidrante->marca : 'S/A' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-hidrante" data-bs-toggle="modal" 
                                            data-hidrante-id="{{ $hidrante->id }}">
                                        Editar <i class="bi bi-pen-fill"></i>
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


@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // DataTable initialization
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

    // Manejador para el botón de nuevo hidrante
    $('#btnNuevoHidrante').click(function() {
        const button = $(this);
        const buttonText = button.find('.button-text');
        const spinner = button.find('.spinner-border');

        button.prop('disabled', true);
        buttonText.text('Cargando...');
        spinner.removeClass('d-none');

        $.get("{{ route('hidrantes.create') }}")
            .done(function(response) {
                $('.modal, .modal-backdrop').remove();
                $('body').append(response);

                const modalElement = document.getElementById('crearHidranteModal');
                const modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });

                modalInstance.show();

                $('#formCrearHidrante').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                modalInstance.hide();
                                location.reload();
                                alert('Hidrante creado exitosamente');
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            alert('Error al crear el hidrante');
                        }
                    });
                });
            })
            .fail(function(xhr) {
                console.error('Error:', xhr);
                alert('Error al cargar el formulario');
            })
            .always(function() {
                button.prop('disabled', false);
                buttonText.text('Alta de hidrante');
                spinner.addClass('d-none');
            });
    });

    // Manejador para el botón de editar hidrante
    $(document).on('click', '.edit-hidrante', function(e) {
        e.preventDefault();
        const hidranteId = $(this).data('hidrante-id');
        const button = $(this);
        
        button.prop('disabled', true)
             .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Espere');
        
        $.ajax({
            url: `${window.location.origin}/bev-bomberos/public/hidrantes/${hidranteId}/edit`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.modal, .modal-backdrop').remove();
                $('body').append(response);
                
                const modalElement = document.getElementById(`editarHidranteModal${hidranteId}`);
                const modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                
                modalInstance.show();
                
                $(`#editarHidranteModal${hidranteId} form`).on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    
                    $.ajax({
                        url: form.attr('action'),
                        method: 'POST',
                        data: form.serialize(),
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if(response.success) {
                                modalInstance.hide();
                                location.reload();
                                alert('Hidrante actualizado exitosamente');
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            alert('Error al actualizar el hidrante');
                        }
                    });
                });
            },
            error: function(xhr) {
                console.error('Error loading:', xhr);
                alert('Error al cargar los datos del hidrante');
            },
            complete: function() {
                button.prop('disabled', false)
                      .html('Editar <i class="bi bi-pen-fill"></i>');
            }
        });
    });
});
</script>
@endsection