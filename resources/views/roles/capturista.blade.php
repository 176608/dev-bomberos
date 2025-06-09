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

    .button-text {
        transition: opacity 0.3s ease;
    }

    .btn:disabled .button-text {
        opacity: 0.5;
    }

    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
    }
    
    .table {
        width: 100% !important;
    }
    
    .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: auto !important;
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
                    <button class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#configuracionModal">
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
                            <th data-column="id">ID</th>
                            <th data-column="fecha_inspeccion">Fecha Inspección</th>
                            <th data-column="calle">Calle Principal</th>
                            <th data-column="y_calle">Calle Secundaria</th>
                            <th data-column="colonia">Colonia</th>
                            <th data-column="marca">Marca</th>
                            <th data-column="acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hidrantes as $hidrante)
                            <tr>
                                <td>{{ $hidrante->id }}</td>
                                <td>{{ $hidrante->fecha_inspeccion ? $hidrante->fecha_inspeccion->format('Y-m-d') : 'No especificado' }}</td>
                                <td>{{ $hidrante->callePrincipal ? $hidrante->callePrincipal->Nomvial : 'No especificada' }}</td>
                                <td>{{ $hidrante->calleSecundaria ? $hidrante->calleSecundaria->Nomvial : 'No especificada' }}</td>
                                <td>{{ $hidrante->coloniaLocacion ? $hidrante->coloniaLocacion->NOMBRE : 'No especificada' }}</td>
                                <td>{{ $hidrante->marca ?? 'S/A' }}</td>
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

<!-- Modal para configuración de tabla -->
<div class="modal fade" id="configuracionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configuración de Tabla</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formConfiguracion">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Información Básica</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="fecha_inspeccion" id="col_fecha_inspeccion" checked>
                                <label class="form-check-label" for="col_fecha_inspeccion">Fecha de Inspección</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="fecha_tentativa" id="col_fecha_tentativa">
                                <label class="form-check-label" for="col_fecha_tentativa">Fecha Tentativa</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="numero_estacion" id="col_numero_estacion">
                                <label class="form-check-label" for="col_numero_estacion">Número de Estación</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="numero_hidrante" id="col_numero_hidrante">
                                <label class="form-check-label" for="col_numero_hidrante">Número de Hidrante</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Ubicación</h6>
                            <div class="row">
                                    <div class="form-check mb-2 col-6">
                                    <input class="form-check-input column-toggle" type="checkbox" value="calle" id="col_calle" checked>
                                    <label class="form-check-label" for="col_calle">Calle Principal(Cadena)</label>
                                </div>
                                <div class="form-check mb-2 col-6">
                                    <input class="form-check-input column-toggle" type="checkbox" value="id_calle" id="col_id_calle" checked>
                                    <label class="form-check-label" for="col_id_calle">Calle Principal(Clave ID)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-check mb-2">
                                    <input class="form-check-input column-toggle" type="checkbox" value="y_calle" id="col_y_calle" checked>
                                    <label class="form-check-label" for="col_y_calle">Calle Secundaria(Cadena)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input column-toggle" type="checkbox" value="id_y_calle" id="col_id_y_calle" checked>
                                    <label class="form-check-label" for="col_id_y_calle">Calle Secundaria(Clave ID)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-check mb-2">
                                    <input class="form-check-input column-toggle" type="checkbox" value="colonia" id="col_colonia" checked>
                                    <label class="form-check-label" for="col_colonia">Colonia(Cadena)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input column-toggle" type="checkbox" value="id_colonia" id="col_id_colonia" checked>
                                    <label class="form-check-label" for="col_id_colonia">Colonia(Clave ID)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Características</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="llave_hidrante" id="col_llave_hidrante">
                                <label class="form-check-label" for="col_llave_hidrante">Llave Hidrante</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="presion_agua" id="col_presion_agua">
                                <label class="form-check-label" for="col_presion_agua">Presión de Agua</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="color" id="col_color">
                                <label class="form-check-label" for="col_color">Color</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="estado_hidrante" id="col_estado_hidrante">
                                <label class="form-check-label" for="col_estado_hidrante">Estado</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Información Adicional</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="marca" id="col_marca" checked>
                                <label class="form-check-label" for="col_marca">Marca</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="anio" id="col_anio">
                                <label class="form-check-label" for="col_anio">Año</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="oficial" id="col_oficial">
                                <label class="form-check-label" for="col_oficial">Oficial</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="observaciones" id="col_observaciones">
                                <label class="form-check-label" for="col_observaciones">Observaciones</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Información del Sistema</h6>
                            <div class="row">
                                <div class="form-check mb-2 col-6">
                                    <input class="form-check-input column-toggle" type="checkbox" value="create_user_id" id="col_create_user">
                                    <label class="form-check-label" for="col_create_user">Usuario Alta</label>
                                </div>
                                <div class="form-check mb-2 col-6">
                                    <input class="form-check-input column-toggle" type="checkbox" value="update_user_id" id="col_update_user">
                                    <label class="form-check-label" for="col_update_user">Usuario Actualización</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-check mb-2 col-6">
                                    <input class="form-check-input column-toggle" type="checkbox" value="created_at" id="col_created_at">
                                    <label class="form-check-label" for="col_created_at">Fecha de Alta</label>
                                </div>
                                <div class="form-check mb-2 col-6">
                                    <input class="form-check-input column-toggle" type="checkbox" value="updated_at" id="col_updated_at">
                                    <label class="form-check-label" for="col_updated_at">Fecha de Actualización</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarConfiguracion">Guardar cambios</button>
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
            url: "{{ asset('js/datatables/i18n/es-ES.json') }}"
        },
        pageLength: 10,
        order: [[0, 'desc']],
        responsive: true,
        scrollX: true,
        autoWidth: true,
        columnDefs: [
            {
                targets: 6,
                orderable: false,
                searchable: false
            }
        ],
        drawCallback: function(settings) {
            // Ajustar columnas cada vez que se redibuja la tabla
            $(window).trigger('resize');
            this.api().columns.adjust();
        }
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

    // Función para limpiar modales
    function cleanupModal() {
        $('.modal').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    }

    // Manejador para el botón de configuración
    $('#btnConfiguracion').click(function() {
        cleanupModal();
        const modal = new bootstrap.Modal(document.getElementById('configuracionModal'));
        modal.show();
    });

    // Evento cuando se cierra el modal
    $('#configuracionModal').on('hidden.bs.modal', function () {
        cleanupModal();
    });

    // Cargar configuración guardada
    function loadTableConfig() {
        $.get("{{ route('configuracion.get') }}")
            .done(function(response) {
                if (response.success && response.configuracion) {
                    // Primero, ocultar todas las columnas excepto ID y Acciones
                    table.columns().every(function() {
                        const columnName = $(this.header()).data('column');
                        if (columnName && !['id', 'acciones'].includes(columnName)) {
                            this.visible(false);
                            $(`#col_${columnName}`).prop('checked', false);
                        }
                    });

                    // Luego, mostrar solo las columnas configuradas
                    response.configuracion.forEach(function(columnName) {
                        const columnIndex = $(`th[data-column="${columnName}"]`).index();
                        if (columnIndex !== -1) {
                            table.column(columnIndex).visible(true);
                            $(`#col_${columnName}`).prop('checked', true);
                        }
                    });

                    // Ajustar columnas y redibujar
                    table.columns.adjust().draw();
                }
            })
            .fail(function() {
                // Configuración por defecto en caso de error
                const defaultColumns = ['id', 'fecha_inspeccion', 'calle', 'y_calle', 'colonia', 'marca', 'acciones'];
                table.columns().every(function() {
                    const columnName = $(this.header()).data('column');
                    const isVisible = defaultColumns.includes(columnName);
                    this.visible(isVisible);
                    if (columnName) {
                        $(`#col_${columnName}`).prop('checked', isVisible);
                    }
                });
                table.columns.adjust().draw();
            });
    }

    // Guardar configuración
    $('#guardarConfiguracion').click(function() {
        const configuracion = $('.column-toggle:checked').map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: "{{ route('configuracion.save') }}",
            method: 'POST',
            data: {
                configuracion: configuracion
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Ocultar el modal
                    $('#configuracionModal').modal('hide');
                    
                    // Actualizar visibilidad de columnas
                    table.columns().every(function() {
                        const columnName = $(this.header()).data('column');
                        if (columnName && !['id', 'acciones'].includes(columnName)) {
                            this.visible(configuracion.includes(columnName));
                        }
                    });

                    // Ajustar columnas y redibujar
                    table.columns.adjust().draw();
                    
                    // Limpiar modal
                    cleanupModal();
                    
                    // Notificar éxito
                    alert('Configuración guardada exitosamente');
                }
            },
            error: function(xhr) {
                alert('Error al guardar la configuración');
            }
        });
    });

    // Cargar configuración al iniciar
    loadTableConfig();

    // Agregar listener para ajuste de ventana
    $(window).on('resize', function() {
        table.columns.adjust();
    });
});
</script>
@endsection