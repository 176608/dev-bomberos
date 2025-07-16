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

    /* Ajuste para la columna Acciones (ahora segunda columna) */
    #hidrantesConfigTable th:nth-child(2),
    #hidrantesConfigTable td:nth-child(2) {
        min-width: 120px;
        width: 120px;
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
                    <button class="btn btn-secondary mb-2" id="btnConfiguracion">
                        <i class="bi bi-gear-fill"></i>
                        <span class="button-text">Editar parámetros de la tabla</span>
                        <span class="spinner-border spinner-border-sm ms-1 d-none" id="spinnerConfiguracion" role="status" aria-hidden="true"></span>
                    </button>
                    <button class="btn btn-success mb-2" id="btnVerTabla">
                        <i class="bi bi-table"></i> Ver la tabla
                    </button>
                    <button class="btn btn-info mb-2" id="btnResumen">
                        <i class="bi bi-gear-fill"></i>
                        <span class="button-text">Ver resumen de hidrantes</span>
                        <span class="spinner-border spinner-border-sm ms-1 d-none" id="spinnerResumen" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="AuxContainer" style="display:none;">
        <!-- **Aquí se cargará la card auxiliar** -->
    </div>

    <div id="tablaHidrantesContainer" style="display:none;">
        <!-- Aquí se cargará la tabla con AJAX -->
    </div>

    <div id="resumenHidrantesContainer" style="display:none;">
        <!-- Aquí se cargaria la tabla con AJAX -->
    </div>

</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const headerNames = @json($headerNames);
    var configTable;

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
                                alert('Hidrante creado exitosamente');
                                window.location = window.location.pathname + '?mostrar_tabla=1';
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
             .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span');
        
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
                                //location.reload();
                                alert('Hidrante actualizado exitosamente');
                                window.location = window.location.pathname + '?mostrar_tabla=1';
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
                      .html('<i class="bi bi-pen-fill"></i>');
            }
        });
    });

    // Manejador para el botón de ver hidrante
    $(document).on('click', '.view-hidrante', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const hidranteId = $btn.data('hidrante-id');
        $btn.prop('disabled', true);
        const originalHtml = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm"></span');

        // Usa la función route de Laravel para generar la URL correcta:
        $.get("{{ url('/hidrantes') }}/" + hidranteId + "/view", function(modalHtml) {
            // Elimina cualquier modal anterior de vista
            $('.modal-view').remove();
            // Agrega el modal al body
            const $modal = $(modalHtml).addClass('modal-view');
            $('body').append($modal);

            // Inicializa el modal (sin backdrop estático)
            const modalInstance = new bootstrap.Modal($modal[0], {
                backdrop: true, // backdrop normal (no 'static')
                keyboard: true
            });
            modalInstance.show();

            // Al cerrar, elimina el modal del DOM
            $modal.on('hidden.bs.modal', function() {
                $modal.remove();
            });
        }).always(function() {
            $btn.prop('disabled', false).html(originalHtml);
        });
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

    // Inicializa DataTable con server-side
    function inicializarDataTableServerSide() {
        let columnas = window.hidrantesTableConfig || [];
        let headerNames = window.hidrantesHeaderNames || {};
        let dtColumns = [
            { data: 'id', name: 'id', className: 'text-center align-middle' },
            { 
                data: 'acciones',
                name: 'acciones',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle'
            },
            { 
                data: 'stat',
                name: 'stat',
                className: 'text-center align-middle',
                render: function(data, type, row) {
                    // Si stat es '000', muestra badge
                    if (data === '000') {
                        return `<span class="badge rounded-pill bg-danger">Dado de Baja</span>`;
                    }
                    // Si stat es porcentaje (ej: '25', '70', etc)
                    let percent = parseInt(data, 10);
                    let color = 'bg-success';
                    if (percent <= 40) {
                        color = 'bg-danger';
                    } else if (percent <= 70) {
                        color = 'bg-primary';
                    }
                    return `
                        <div class="progress" style="height: 22px;">
                          <div class="progress-bar progress-bar-striped ${color}" role="progressbar"
                            style="width: ${percent}%"
                            aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100">
                            ${percent}%
                          </div>
                        </div>
                    `;
                }
            }
        ];
        columnas.forEach(function(col) {
            if(col !== 'id' && col !== 'acciones' && col !== 'stat') {
                dtColumns.push({
                    data: col,
                    name: col,
                    className: 'text-center align-middle',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return data;
                        }
                        if (type === 'filter' || type === 'search') {
                            // Elimina el asterisco para la búsqueda y agrega variantes
                            const cleanData = data ? data.replace('*', '') : '';
                            return cleanData + ' ' + cleanData + '*';
                        }
                        return data;
                    }
                });
            }
        });

        let table = $('#hidrantesConfigTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('hidrantes.data') }}",
                data: function(d) {
                    // Añadir filtros adicionales si existen
                    const filtrosAdicionales = window.filtrosNoVisibles || {};
                    if (Object.keys(filtrosAdicionales).length > 0) {
                        d.filtros_adicionales = JSON.stringify(filtrosAdicionales);
                    }
                    return d;
                }
            },
            language: {
                url: "{{ asset('js/datatables/i18n/es-ES.json') }}"
            },
            order: [[0, 'desc']],
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: true,
            responsive: false,
            pageLength: 25,
            lengthMenu: [[25, 50, 100, 500], [25, 50, 100, 500]],
            drawCallback: function() {
                $('#tablaLoader').hide();
                $('.table-responsive').show();
                scrollToTablaHidrantes();
            }
        });
        
        // Hacer disponible la tabla globalmente
        window.hidrantesTable = table;
    }

    function scrollToTablaHidrantes() {
    const tabla = document.getElementById('tablaHidrantesContainer');
    if (tabla) {
        const navbar = document.querySelector('.navbar.fixed-top');
        const navbarHeight = navbar ? navbar.offsetHeight : 0;

        const tablaTop = tabla.getBoundingClientRect().top;

        const scrollY = window.scrollY || window.pageYOffset;

        const destino = scrollY + tablaTop - navbarHeight;

        window.scrollTo({
            top: destino,
            behavior: 'smooth'
        });
    }
}

    // Función para cargar el panel auxiliar
function cargarPanelAuxiliar(modo) {
    $('#AuxContainer').show().html('<div class="text-center my-3"><div class="spinner-border text-primary" role="status"></div><div>Cargando panel...</div></div>');
    
    $.get("{{ route('capturista.panel-auxiliar') }}", { modo: modo }, function(response) {
        $('#AuxContainer').html(response);
        
        // Si estamos en modo tabla, asignar la variable de tabla global para los filtros
        if (modo === 'tabla') {
            window.configTable = $('#hidrantesConfigTable').DataTable();
        }
    });
}

// Mostrar la tabla al dar click en "Ver la tabla", "Alta de hidrante" o "Editar parámetros"
function cargarTablaHidrantes() {
    $('#tablaHidrantesContainer').show().html('');
    $('#tablaHidrantesContainer').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><div>Cargando tabla...</div></div>');
    
    // Primero cargamos el panel auxiliar
    cargarPanelAuxiliar('tabla');
    
    $.get("{{ route('capturista.panel') }}", { tabla: 1 }, function(response) {
        // Renderiza el partial de la tabla
        $('#tablaHidrantesContainer').html(response);
        inicializarDataTableServerSide();
        //scrollToTablaHidrantes();
    });
}

// Display the table when the page loads
$('#btnVerTabla').click(function() {
    window.location = window.location.pathname + '?mostrar_tabla=1';
});

// Detecta el parámetro mostrar_tabla en la URL y carga la tabla automáticamente
const params = new URLSearchParams(window.location.search);
if (params.get('mostrar_tabla') === '1') {
    cargarTablaHidrantes();
    // Opcional: limpia el parámetro de la URL para evitar recargar la tabla si el usuario refresca
    const url = new URL(window.location);
    url.searchParams.delete('mostrar_tabla');
    window.history.replaceState({}, document.title, url);
}

// Mostrar spinner al hacer click en el botón
$('#btnConfiguracion').on('click', function() {
    $('#spinnerConfiguracion').removeClass('d-none');
    // Elimina cualquier modal anterior para evitar duplicados
    $('#configuracionModal').remove();

    $.get("{{ route('capturista.configuracion-modal') }}", function(modalHtml) {
        $('body').append(modalHtml);
        const modalElement = document.getElementById('configuracionModal');
        const modalInstance = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        modalInstance.show();

        // Oculta el spinner cuando el modal se muestre
        $(modalElement).on('shown.bs.modal', function () {
            $('#spinnerConfiguracion').addClass('d-none');
        });
        // Limpia el modal del DOM al cerrarse para evitar duplicados
        $(modalElement).on('hidden.bs.modal', function () {
            $(this).remove();
            $('#spinnerConfiguracion').addClass('d-none');
        });
    });
});
    

    // Manejador para desactivar hidrante
    $(document).on('click', '.desactivar-hidrante', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const hidranteId = $btn.data('hidrante-id');
        if (confirm('¿Está seguro de dar de baja este hidrante?')) {
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            $.ajax({
                url: "{{ url('/hidrantes') }}/" + hidranteId + "/desactivar",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        window.location = window.location.pathname + '?mostrar_tabla=1';
                    } else {
                        alert('No se pudo desactivar el hidrante.');
                    }
                },
                error: function() {
                    alert('Error al desactivar el hidrante.');
                }
            });
        }
    });

    // Manejador para activar hidrante
    $(document).on('click', '.activar-hidrante', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const hidranteId = $btn.data('hidrante-id');
        if (confirm('¿Está seguro de activar este hidrante?')) {
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            $.ajax({
                url: "{{ url('/hidrantes') }}/" + hidranteId + "/activar",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        window.location = window.location.pathname + '?mostrar_tabla=1';
                    } else {
                        alert('No se pudo activar el hidrante.');
                    }
                },
                error: function() {
                    alert('Error al activar el hidrante.');
                }
            });
        }
    });

    $('#btnResumen').on('click', function() {
        $('#resumenHidrantesContainer').show().html('');
        $('#spinnerResumen').removeClass('d-none');
        $('#tablaHidrantesContainer').hide();
        
        // Cargar panel auxiliar para resumen
        cargarPanelAuxiliar('resumen');
        
        $.get("{{ route('hidrantes.resumen') }}", function(response) {
            $('#resumenHidrantesContainer').html(response);
            $('#spinnerResumen').addClass('d-none');
            scrollToTablaHidrantes();
        });
    });
});
</script>
@endsection