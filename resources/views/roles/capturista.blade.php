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


    <!-- Card adicional para el reporte configurado -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Reporte Hidrantes Configurado</h5>
            <div class="table-responsive">
                <table id="hidrantesConfigTable" class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr id="configuredHeaders">
                            <!-- El ID siempre estará presente -->
                            <th>ID</th>
                            <!-- Los headers dinámicos se insertarán aquí -->
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- El tbody quedará vacío por ahora -->
                    </tbody>
                </table>
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
                                <td>{{ $hidrante->fecha_inspeccion->format('Y-m-d') }}</td>
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
    // Definir headerNames al inicio para que esté disponible en todo el scope
    const headerNames = {
        'fecha_inspeccion': 'Fecha Inspección',
        'fecha_tentativa': 'Fecha Tentativa', 
        'numero_estacion': 'N° Estación',
        'numero_hidrante': 'N° Hidrante',
        'calle': 'Calle Principal',
        'y_calle': 'Calle Secundaria',
        'colonia': 'Colonia',
        'llave_hidrante': 'Llave Hidrante',
        'presion_agua': 'Presión Agua',
        'color': 'Color',
        'estado_hidrante': 'Estado',
        'marca': 'Marca',
        'anio': 'Año',
        'oficial': 'Oficial',
        'observaciones': 'Observaciones'
    };

    // DataTable initialization
    var table = $('#hidrantesTable').DataTable({
        language: {
            url: "{{ asset('js/datatables/i18n/es-ES.json') }}"
        },
        order: [[0, 'desc']],
        paging: true,
        searching: true,
        info: true,
        autoWidth: true,
        scrollX: true,
        scrollY: false,        // Deshabilitar scroll vertical
        scrollCollapse: false, // Deshabilitar colapso de scroll
        responsive: true,
        columnDefs: [
            {
                targets: 'no-sort',
                orderable: false
            }
        ],
        drawCallback: function() {
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

    // Cargar configuración guardada
    function loadTableConfig() {
        $.get("{{ route('configuracion.get') }}")
            .done(function(response) {
                let configuracion = response.configuracion && Array.isArray(response.configuracion) ? 
                    response.configuracion : 
                    ['fecha_inspeccion', 'calle', 'y_calle', 'colonia', 'marca'];

                // Actualizar headers
                updateConfiguredTableHeaders(configuracion);

                // Definir mapeo de relaciones
                const relationMapping = {
                    'calle': (row) => row.callePrincipal ? row.callePrincipal.Nomvial : 'Sin especificar',
                    'y_calle': (row) => row.calleSecundaria ? row.calleSecundaria.Nomvial : 'Sin especificar',
                    'colonia': (row) => row.coloniaLocacion ? row.coloniaLocacion.NOMBRE : 'Sin especificar'
                };

                // Definir mapeo de fechas
                const dateMapping = {
                    'fecha_inspeccion': true,
                    'fecha_tentativa': true
                };

                // Configurar columnas para DataTable
                const columns = [
                    { 
                        data: 'id',
                        title: 'ID',
                        className: 'text-center'
                    }
                ];

                // Agregar columnas configuradas
                configuracion.forEach(column => {
                    columns.push({
                        data: null,
                        title: headerNames[column] || column,
                        className: 'text-center',
                        render: function(data, type, row) {
                            // Si es una relación
                            if (relationMapping[column]) {
                                return relationMapping[column](row);
                            }
                            // Si es una fecha
                            if (dateMapping[column]) {
                                return row[column] ? moment(row[column]).format('YYYY-MM-DD') : 'N/A';
                            }
                            // Campos normales
                            return row[column] || 'N/A';
                        }
                    });
                });

                // Agregar columna de acciones
                columns.push({
                    data: null,
                    title: 'Acciones',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return `<button class="btn btn-sm btn-warning edit-hidrante" 
                                data-hidrante-id="${row.id}" disabled>
                                Editar <i class="bi bi-pen-fill"></i>
                            </button>`;
                    }
                });

                // Destruir tabla existente si ya está inicializada
                if (configTable) {
                    configTable.destroy();
                }

                // Inicializar la tabla configurada
                configTable = $('#hidrantesConfigTable').DataTable({
                    data: {!! json_encode($hidrantes) !!},
                    columns: columns,
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
                    columnDefs: [{
                        targets: '_all',
                        defaultContent: 'N/A'
                    }],
                    drawCallback: function() {
                        $(window).trigger('resize');
                        this.api().columns.adjust();
                    }
                });

                // Actualizar checkboxes
                $('.column-toggle').prop('checked', false);
                configuracion.forEach(function(columnName) {
                    $(`#col_${columnName}`).prop('checked', true);
                });
            })
            .fail(function(error) {
                console.error('Error al cargar configuración:', error);
                alert('Error al cargar la configuración');
            });
    }

    // Modificar el evento para cargar el modal
    $('[data-bs-target="#configuracionModal"]').on('click', function() {
        // Cargar configuración antes de mostrar el modal
        $.get("{{ route('configuracion.get') }}")
            .done(function(response) {
                console.log('Configuración recuperada:', response);
                
                // Desmarcamos todos los checkboxes primero
                $('.column-toggle').prop('checked', false);
                
                if (response.success && response.configuracion) {
                    // Si hay configuración guardada, usarla
                    response.configuracion.forEach(function(columnName) {
                        $(`#col_${columnName}`).prop('checked', true);
                    });
                    console.log('Usando configuración guardada:', response.configuracion);
                } else {
                    // Usar configuración por defecto desde el modelo
                    const defaultColumns = [
                        'fecha_inspeccion',
                        'calle',
                        'y_calle',
                        'colonia',
                        'marca'
                    ];
                    
                    $('.column-toggle').each(function() {
                        const columnName = $(this).val();
                        $(this).prop('checked', defaultColumns.includes(columnName));
                    });
                    console.log('Usando configuración por defecto:', defaultColumns);
                }
            })
            .fail(function(error) {
                console.error('Error cargando configuración:', error);
                alert('Error al cargar la configuración de columnas');
            });
    });

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
                    loadTableConfig(); // Recargar la tabla con la nueva configuración
                    
                    const modalElement = document.getElementById('configuracionModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();
                    
                    setTimeout(() => {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').removeAttr('style');
                        document.documentElement.style.overflow = '';
                        document.body.style.overflow = '';
                        alert('Configuración guardada exitosamente');
                    }, 300);
                }
            },
            error: function(xhr) {
                console.error('Error al guardar:', xhr);
                alert('Error al guardar la configuración');
            }
        });
    });

    // Cargar configuración al iniciar
    loadTableConfig();

    $(window).on('resize', function() {
        if (table) {
            table.columns.adjust();
        }
    });

    // Manejar el cierre del modal
    $('#configuracionModal').on('hidden.bs.modal', function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').removeAttr('style');
    });
});
</script>
@endsection