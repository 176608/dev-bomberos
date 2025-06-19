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

    .card .table-responsive {
        overflow-y: visible !important;
        max-height: none !important;
    }

    .dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }

    .dataTables_scrollBody {
        max-height: none !important;
        overflow-y: visible !important;
    }

    /* Si hay scroll horizontal, asegurarnos que sea suave */
    .dataTables_wrapper .dataTables_scroll {
        overflow-x: auto;
    }

    /* Asegurar que el modal no afecte el scroll */
    body.modal-open {
        overflow: auto !important;
        padding-right: 0 !important;
    }


    #hidrantesConfigTable td {
        padding: 0.75rem;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 120px;
        transition: width 0.2s ease;
    }

    /* Estilos específicos para encabezados */
    #hidrantesConfigTable thead th {
        color: white;
        font-weight: 500;
        position: relative;
        resize: horizontal;
    }

    /* Ajustes para columnas específicas */
    #hidrantesConfigTable th:first-child,
    #hidrantesConfigTable td:first-child {
        min-width: 80px;
        width: 80px;
    }

    #hidrantesConfigTable th:last-child,
    #hidrantesConfigTable td:last-child {
        min-width: 100px;
        width: 100px;
    }

    /* Contenedor de DataTables */
    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
    }

    /* Ajustes responsive */
    .dataTables_wrapper {
        padding: 0;
    }

    .dataTables_scrollBody {
        min-height: 400px;
    }

    /* Personalización de DataTables */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
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
                    <button class="btn btn-info mb-2" id="btnVerTabla">
                        <i class="bi bi-table"></i> Ver la tabla
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="tablaHidrantesContainer" style="display:none;">
        <!-- Aquí se cargará la tabla con AJAX -->
    </div>
</div>

<!-- Modal para configuración de tabla -->
<div class="modal fade modal-params" id="configuracionModal" tabindex="-1">
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
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="calle" id="col_calle" checked>
                                <label class="form-check-label" for="col_calle">Calle Principal</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="y_calle" id="col_y_calle" checked>
                                <label class="form-check-label" for="col_y_calle">Calle Secundaria</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input column-toggle" type="checkbox" value="colonia" id="col_colonia" checked>
                                <label class="form-check-label" for="col_colonia">Colonia</label>
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
    // Obtener headerNames del servidor
    const headerNames = @json($headerNames);

    // Modificar la inicialización de DataTables
    var configTable = $('#hidrantesConfigTable').DataTable({
        language: {
            url: "{{ asset('js/datatables/i18n/es-ES.json') }}"
        },
        order: [[0, 'desc']],
        paging: true,
        searching: true,
        info: true,
        autoWidth: false,
        scrollX: true,
        responsive: true,
        pageLength: 25,
        lengthMenu: [[25, 50, 100, 500], [25, 50, 100,  500]],
        drawCallback: function() {
            //$('#tableLoader').addClass('d-none');
            $(window).trigger('resize');
            this.api().columns.adjust();
        }
    });

    var configTable; // Variable para la tabla configurada

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
                $('.modal-create, .modal-edit, .modal-backdrop').remove();
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
                                cargarTablaHidrantes();
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
                $('.modal-create, .modal-edit, .modal-backdrop').remove();
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
        $('body').removeClass('modal-open').removeAttr('style');
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

    // Función para actualizar los headers de la tabla configurada
    function updateConfiguredTableHeaders(configuracion) {
        const headerRow = $('#configuredHeaders');
        headerRow.find('th:not(:first-child):not(:last-child)').remove();
        
        // Ahora headerNames está disponible aquí
        const lastHeader = headerRow.find('th:last');
        configuracion.forEach(column => {
            $('<th>', {
                text: headerNames[column] || column
            }).insertBefore(lastHeader);
        });
    }

    // Modificar el guardado de configuración
    $('#guardarConfiguracion').click(function() {
        const configuracion = $('.column-toggle:checked').map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: "{{ route('configuracion.save') }}",
            method: 'POST',
            data: { configuracion: configuracion },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Cerrar modal
                    const modalElement = document.getElementById('configuracionModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();
                    // Recargar página
                    //window.location.reload();
                    cargarTablaHidrantes();
                }
            },
            error: function(xhr) {
                console.error('Error al guardar:', xhr);
                alert('Error al guardar la configuración');
            }
        });
    });

    // Manejar el cierre del modal
    $('#configuracionModal').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').removeAttr('style');
    });

    // Agregar este evento para cargar la configuración cuando se abre el modal
    $('#configuracionModal').on('show.bs.modal', function () {
        // Cargar configuración actual del usuario
        $.get("{{ route('configuracion.get') }}")
            .done(function(response) {
                // Desmarcar todos los checkboxes primero
                $('.column-toggle').prop('checked', false);
                
                if (response.configuracion && Array.isArray(response.configuracion)) {
                    // Marcar los checkboxes según la configuración guardada
                    response.configuracion.forEach(function(columnName) {
                        $(`#col_${columnName}`).prop('checked', true);
                    });
                } else {
                    // Configuración por defecto si no hay configuración guardada
                    const defaultColumns = [
                        'fecha_inspeccion',
                        'calle',
                        'y_calle'
                    ];
                    defaultColumns.forEach(function(columnName) {
                        $(`#col_${columnName}`).prop('checked', true);
                    });
                }
            })
            .fail(function(error) {
                console.error('Error al cargar configuración:', error);
                alert('Error al cargar la configuración');
            })
            .always(function() {
                //$('#tableLoader').addClass('d-none');
            });
    });

    // Mostrar la tabla al dar click en "Ver la tabla", "Alta de hidrante" o "Editar parámetros"
    function cargarTablaHidrantes() {
        $('#tablaHidrantesContainer').show().html('');
        $('#tablaHidrantesContainer').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><div>Cargando tabla...</div></div>');
        $.get("{{ route('capturista.panel') }}", { tabla: 1 }, function(response) {
            // Renderiza el partial de la tabla
            $('#tablaHidrantesContainer').html(response);
            inicializarDataTableServerSide();
        });
    }

    // Nuevo botón "Ver la tabla"
    $('#btnVerTabla').click(function() {
        cargarTablaHidrantes();
    });

    // También puedes llamar cargarTablaHidrantes() después de crear o editar hidrante, si lo deseas

    // Inicializa DataTable con server-side
    function inicializarDataTableServerSide() {
        let columnas = window.hidrantesTableConfig || [];
        let headerNames = window.hidrantesHeaderNames || {};
        let dtColumns = [
            { data: 'id', name: 'id', className: 'text-center align-middle' }
        ];
        columnas.forEach(function(col) {
            if(col !== 'id' && col !== 'acciones') {
                dtColumns.push({
                    data: col,
                    name: col,
                    className: 'text-center align-middle'
                });
            }
        });
        dtColumns.push({
            data: 'acciones',
            name: 'acciones',
            orderable: false,
            searchable: false,
            className: 'text-center align-middle'
        });

        let table = $('#hidrantesConfigTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('hidrantes.data') }}",
            columns: dtColumns,
            language: {
                url: "{{ asset('js/datatables/i18n/es-ES.json') }}"
            },
            order: [[0, 'desc']],
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: true,
            responsive: true,
            pageLength: 25,
            lengthMenu: [[25, 50, 100, 500], [25, 50, 100,  500]],
            drawCallback: function() {
                $('#tablaLoader').hide();
                $('.table-responsive').show();
            }
        });

        // Reasigna eventos de editar hidrante
        $('#hidrantesConfigTable').on('click', '.edit-hidrante', function(e) {
            // ...tu código de editar hidrante aquí...
        });
    }

    // Si quieres que la tabla se muestre automáticamente después de crear/editar hidrante,
    // llama cargarTablaHidrantes() en el success de esos AJAX.

    // Opcional: Si quieres que la tabla se muestre automáticamente al dar click en "Alta de hidrante" o "Editar parámetros",
    // llama cargarTablaHidrantes() en el success de esos eventos también.
});
</script>
@endsection