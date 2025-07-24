<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
@extends('layouts.app')

@section('title', 'Bomberos')

@section('content')
<style>
    .custom-image-size {
        width: 40vw;
        height: auto;
        object-fit: contain;
    }

    .card-body {
        background-color: rgba(236, 236, 236, 0.96);
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

    /* Estilos para los toast */
    .toast {
        box-shadow: 0 .25rem .75rem rgba(0,0,0,.15);
        opacity: 1;
        backdrop-filter: blur(5px);
        background-color: rgba(255, 255, 255, 0.95) !important;
        border-left: 4px solid #0d6efd;
        max-width: 350px;
        font-size: 0.9rem;
    }

    .toast-success {
        border-left-color: #198754;
    }

    .toast-error {
        border-left-color: #dc3545;
    }

    .toast-warning {
        border-left-color: #ffc107;
    }

    .toast-info {
        border-left-color: #0dcaf0;
    }

    /* Corrección para evitar bordes adicionales en DataTables */
    #hidrantesConfigTable tbody tr {
        border-bottom: 1px solid #dee2e6 !important;
        border-top: none !important;
    }
    
    #hidrantesConfigTable tbody td {
        border-left: none !important;
        border-right: none !important;
    }
    
    /* Asegurar que los estilos de filas alternadas se mantienen consistentes */
    #hidrantesConfigTable tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9 !important;
    }
    
    #hidrantesConfigTable tbody tr:nth-of-type(even) {
        background-color: #ffffff !important;
    }
    
    /* Asegurar que los bordes horizontales sean ligeros */
    .dataTables_wrapper .dataTable {
        border-collapse: collapse !important;
    }
    
    /* Estilos para los botones de exportación */
    div.dt-buttons {
        margin-top: 1rem;
        margin-bottom: 1rem;
    }
    
    .dt-buttons .btn {
        margin-right: 5px;
        border-radius: 4px;
    }
    
    .dt-buttons .btn:hover {
        opacity: 0.85;
    }
    
    /* Para la tabla de resumen, centrar los botones */
    #tablaResumenHidrantes_wrapper .dt-buttons {
        text-align: center;
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
                    <button class="btn btn-success mb-2" id="btnResumen">
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

    <!-- Toast container para mensajes -->
    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

</div>

@endsection

@section('scripts')
<script>
// 1. Variables globales y configuración
$(document).ready(function() {
    const headerNames = @json($headerNames);
    var configTable;
    
    // 2. Inicialización y detección de parámetros URL
    initializeFromUrlParams();
    
    // 3. Manejadores de eventos para botones principales
    setupMainButtons();
    
    // 4. Manejadores para operaciones CRUD
    setupCrudHandlers();
    
    // Solución para el problema de superposición en el selector de registros
    $(document).on('init.dt', function(e, settings) {
        const select = $('.dataTables_length select');
        if (select.length) {
            select.css({
                'min-width': '70px',
                'padding-right': '25px',
                'text-align': 'center'
            });
        }
    });
});

// ========================
// FUNCIONES DE INICIALIZACIÓN
// ========================

/**
 * Inicializa la página según los parámetros de la URL
 */
function initializeFromUrlParams() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('mostrar_tabla') === '1') {
        cargarTablaHidrantes();
        // Limpiar parámetro de la URL
        const url = new URL(window.location);
        url.searchParams.delete('mostrar_tabla');
        window.history.replaceState({}, document.title, url);
    }
}

/**
 * Configura los botones principales de la interfaz
 */
function setupMainButtons() {
    // Botón Ver Tabla
    $('#btnVerTabla').click(function() {
        cargarTablaHidrantes();
        
        // Normalizar estilos después de que la tabla se haga visible
        setTimeout(normalizarEstilosTabla, 500);
    });
    
    // Botón Configuración
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
            $(modalElement).on('shown.bs.modal', function() {
                $('#spinnerConfiguracion').addClass('d-none');
            });
            // Limpia el modal del DOM al cerrarse para evitar duplicados
            $(modalElement).on('hidden.bs.modal', function () {
                $(this).remove();
                $('#spinnerConfiguracion').addClass('d-none');
            });
        });
    });
    
    // Botón Nuevo Hidrante
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
                                // Pasar el toast como callback a ejecutar después de recargar la tabla
                                recargarSoloTabla(function() {
                                    mostrarToast('Hidrante creado exitosamente');
                                });
                            } else {
                                mostrarToast('Error: ' + response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            // Reemplazar alert por mostrarToast
                            mostrarToast('Error al crear el hidrante', 'error');
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
    
    // Botón Resumen
    $('#btnResumen').on('click', function() {
        $('#resumenHidrantesContainer').show().html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><div>Cargando resumen...</div></div>');
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
}

/**
 * Configura los manejadores para operaciones CRUD
 */
function setupCrudHandlers() {
    // Manejador para el botón de editar hidrante
    $(document).on('click', '.edit-hidrante', function(e) {
        e.preventDefault();
        const hidranteId = $(this).data('hidrante-id');
        const button = $(this);
        
        button.prop('disabled', true)
             .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        
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
                
                // En el manejador de submit del formulario de edición
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
                                recargarSoloTabla(function() {
                                    mostrarToast('Hidrante actualizado exitosamente');
                                });
                            } else {
                                mostrarToast('Error: ' + response.message, 'error');
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
        
        console.log('=== DEBUG VIEW HIDRANTE ===');
        console.log('Hidrante ID:', hidranteId);
        
        if (!hidranteId) {
            console.error('ID de hidrante no encontrado');
            return;
        }
        
        $btn.prop('disabled', true);
        const originalHtml = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm"></span>');

        $.get("{{ url('/hidrantes') }}/" + hidranteId + "/view")
            .done(function(modalHtml) {
                console.log('=== MODAL HTML RECIBIDO ===');
                console.log('Tipo de respuesta:', typeof modalHtml);
                console.log('Longitud:', modalHtml.length);
                console.log('Primeros 200 caracteres:', modalHtml.substring(0, 200));
                
                // Verificar si es HTML válido
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = modalHtml;
                console.log('HTML parseado válido:', tempDiv.children.length > 0);
                
                // Verificar si contiene un modal
                const hasModal = modalHtml.includes('class="modal"') || modalHtml.includes("class='modal'");
                console.log('Contiene clase modal:', hasModal);
                
                // Eliminar modales anteriores completamente
                $('.modal-view').each(function() {
                    const modalInstance = bootstrap.Modal.getInstance(this);
                    if (modalInstance) {
                        console.log('Eliminando instancia modal anterior');
                        modalInstance.dispose();
                    }
                    $(this).remove();
                });
                
                // Eliminar cualquier backdrop residual
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                console.log('Modales anteriores eliminados');
                
                // Crear el modal y agregarlo al DOM
                const $modal = $(modalHtml).addClass('modal-view');

                // Asegurar que tiene los atributos necesarios
                $modal.attr({
                    'tabindex': '-1',
                    'aria-labelledby': 'modalLabel',
                    'aria-hidden': 'true'
                });

                $('body').append($modal);

                setTimeout(function() {
                    try {
                        const modalElement = $modal[0];
                        
                        // Verificación más exhaustiva
                        if (!modalElement) {
                            throw new Error('Modal element is null');
                        }
                        
                        if (!modalElement.classList) {
                            throw new Error('Modal element does not have classList');
                        }
                        
                        if (!modalElement.classList.contains('modal')) {
                            console.warn('Adding modal class manually');
                            modalElement.classList.add('modal');
                        }
                        
                        // Intentar inicializar
                        const modalInstance = new bootstrap.Modal(modalElement, {
                            backdrop: 'static',
                            keyboard: true,
                            focus: true
                        });
                        
                        modalInstance.show();
                        
                    } catch (error) {
                        console.error('Detailed error:', {
                            message: error.message,
                            stack: error.stack,
                            modalElement: modalElement,
                            modalHtml: modalHtml.substring(0, 500)
                        });
                    }
                }, 300);

                // Limpiar cuando se cierre
                $modal.on('hidden.bs.modal', function() {
                    console.log('Modal cerrado, limpiando...');
                    try {
                        modalInstance.dispose();
                    } catch (e) {
                        console.warn('Error al limpiar modal:', e);
                    }
                    $(this).remove();
                    
                    // Limpiar cualquier residuo
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                });
                
            })
            .fail(function(xhr) {
                console.error('=== ERROR EN PETICIÓN AJAX ===');
                console.error('Status:', xhr.status);
                console.error('Status Text:', xhr.statusText);
                console.error('Response Text:', xhr.responseText);
                mostrarToast('Error al cargar los detalles del hidrante', 'error');
            })
            .always(function() {
                $btn.prop('disabled', false).html(originalHtml);
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
                        recargarSoloTabla(function() {
                            mostrarToast('Hidrante dado de baja exitosamente', 'info');
                        });
                    } else {
                        mostrarToast('No se pudo dar de baja el hidrante', 'error');
                    }
                },
                error: function() {
                    mostrarToast('Error al dar de baja el hidrante', 'error');
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
                        recargarSoloTabla(function() {
                            mostrarToast('Hidrante activado exitosamente');
                        });
                    } else {
                        mostrarToast('No se pudo activar el hidrante', 'error');
                    }
                },
                error: function() {
                    alert('Error al activar el hidrante.');
                }
            });
        }
    });
}

/**
 * Función para normalizar los estilos de la tabla después de cambiar entre vistas
 */
function normalizarEstilosTabla() {
    // Eliminar bordes adicionales
    $('#hidrantesConfigTable tbody tr').css({
        'border-bottom': '1px solid #dee2e6',
        'border-top': 'none'
    });
    
    // Restaurar el estilo alternado de filas
    $('#hidrantesConfigTable tbody tr:nth-child(odd)').css('background-color', '#eee2e2ff');
    $('#hidrantesConfigTable tbody tr:nth-child(even)').css('background-color', '#ffffff');
    
    // Eliminar cualquier borde adicional en celdas
    $('#hidrantesConfigTable td').css({
        'border-left': 'none',
        'border-right': 'none',
        'border-bottom': 'none'
    });
}

/**
 * Inicializa DataTable with server-side processing
 */
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
    
    // Asegurarse que todas las columnas configuradas existen en los datos
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
            dataSrc: function(json) {
                console.log("DataTables response:", json);
                if (json.data && json.data.length > 0) {
                    console.log("Propiedades disponibles en los datos:", Object.keys(json.data[0]));
                }
                return json.data;
            },
            data: function(d) {
                const filtrosAdicionales = window.filtrosNoVisibles || {};
                if (Object.keys(filtrosAdicionales).length > 0) {
                    d.filtros_adicionales = JSON.stringify(filtrosAdicionales);
                }
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error("Error en la petición AJAX:", error, thrown, xhr);
            }
        },
        columns: dtColumns,
        columnDefs: [
            { targets: [1], className: 'no-export' }  // No exportar la columna de acciones
        ],
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
        
        // Incluir los botones en el DOM pero ocultarlos
        dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>" +
             "<'row d-none'<'col-sm-12'B>>", // Ocultar los botones originales
        buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'csvHtml5',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'excelHtml5',
                filename: function() {
                    const now = new Date();
                    return 'Hidrantes_' + now.getFullYear() + 
                           (now.getMonth() + 1).toString().padStart(2, '0') + 
                           now.getDate().toString().padStart(2, '0');
                },
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                },
                customize: function(doc) {
                    doc.defaultStyle.fontSize = 8;
                    doc.styles.tableHeader.fontSize = 9;
                    doc.pageMargins = [10, 10, 10, 10];
                }
            },
            {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.no-export)'
                }
            }
        ],
        drawCallback: function() {
            $('#tablaLoader').hide();
            $('.table-responsive').show();
            
            // Mover los botones al área del título después de que la tabla se inicialice
            setTimeout(moverBotonesAlTitulo, 100);
            
            scrollToTablaHidrantes();
        }
    });
    
    // Hacer disponible la tabla globalmente
    window.hidrantesTable = table;
    window.configTable = table; 
}

/**
 * Mueve los botones de exportación al área del título
 */
function moverBotonesAlTitulo() {
    // Buscar el contenedor de botones personalizado en el HTML
    const contenedorBotones = document.getElementById('exportButtonsContainer');
    
    if (contenedorBotones && window.hidrantesTable) {
        // Limpiar el contenedor si ya tiene botones
        contenedorBotones.innerHTML = '';
        
        // Crear los botones manualmente basándose en la configuración
        const botonesConfig = [
            {
                text: '<i class="bi bi-clipboard"></i> Copiar',
                className: 'btn btn-sm btn-outline-secondary',
                action: 'copy'
            },
            {
                text: '<i class="bi bi-filetype-csv"></i> CSV',
                className: 'btn btn-sm btn-outline-success',
                action: 'csv'
            },
            {
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                className: 'btn btn-sm btn-outline-success',
                action: 'excel'
            },
            {
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                className: 'btn btn-sm btn-outline-danger',
                action: 'pdf'
            },
            {
                text: '<i class="bi bi-printer"></i> Imprimir',
                className: 'btn btn-sm btn-outline-info',
                action: 'print'
            }
        ];
        
        botonesConfig.forEach(function(config, index) {
            const btnElement = document.createElement('button');
            btnElement.className = config.className;
            btnElement.innerHTML = config.text;
            btnElement.style.marginRight = '5px';
            
            // Agregar el evento click
            btnElement.addEventListener('click', function() {
                // Obtener el botón correspondiente de DataTables y activarlo
                try {
                    window.hidrantesTable.button(index).trigger();
                } catch (e) {
                    console.error('Error al activar botón:', e);
                }
            });
            
            contenedorBotones.appendChild(btnElement);
        });
    }
}

/**
 * Carga el panel auxiliar según el modo especificado
 */
function cargarPanelAuxiliar(modo, callback) {
    $('#AuxContainer').show().html('<div class="text-center my-3"><div class="spinner-border text-primary" role="status"></div><div>Cargando panel...</div></div>');
    
    $.get("{{ route('capturista.panel-auxiliar') }}", { modo: modo }, function(response) {
        $('#AuxContainer').html(response);
        
        // Si estamos en modo tabla, asignar la variable de tabla global para los filtros
        if (modo === 'tabla') {
            setTimeout(function() {
                // Recargar el panel auxiliar con los filtros
                aplicarFiltrosGuardados();
                
                // Ejecutar el callback si existe
                if (typeof callback === 'function') {
                    callback();
                }
            }, 200);
        } else if (typeof callback === 'function') {
            callback();
        }
    });
}

/**
 * Carga la tabla de hidrantes
 */
function cargarTablaHidrantes() {
    $('#tablaHidrantesContainer').show().html('');
    $('#tablaHidrantesContainer').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><div>Cargando tabla...</div></div>');
    $('#resumenHidrantesContainer').hide();
    
    // Primero cargamos el panel auxiliar
    cargarPanelAuxiliar('tabla');
    
    $.get("{{ route('capturista.panel') }}", { tabla: 1 }, function(response) {
        // Renderiza el partial de la tabla
        $('#tablaHidrantesContainer').html(response);
        inicializarDataTableServerSide();
    });
}

/**
 * Carga la tabla sin afectar el panel auxiliar
 */
function cargarSoloTablaHidrantes() {
    // Conservar el estado actual de visibilidad del panel auxiliar
    const auxVisible = $('#AuxContainer').is(':visible');
    
    $('#tablaHidrantesContainer').show().html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><div>Cargando tabla...</div></div>');
    $('#resumenHidrantesContainer').hide();
    
    $.get("{{ route('capturista.panel') }}", { tabla: 1 }, function(response) {
        // Renderiza el partial de la tabla
        $('#tablaHidrantesContainer').html(response);
        inicializarDataTableServerSide();
        
        // Aplicar filtros guardados si existen
        const filtros = recuperarEstadoFiltros();
        if (Object.keys(filtros).length > 0) {
            setTimeout(function() {
                aplicarFiltrosATabla(filtros);
            }, 200);
        }
    });
}

/**
 * Hace scroll a la tabla de hidrantes
 */
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

/**
 * Hace scroll al contenedor auxiliar
 */
function scrollToAuxContainer() {
    const auxContainer = document.getElementById('AuxContainer');
    if (auxContainer) {
        const navbar = document.querySelector('.navbar.fixed-top');
        const navbarHeight = navbar ? navbar.offsetHeight : 0;
        
        const auxTop = auxContainer.getBoundingClientRect().top;
        const scrollY = window.scrollY || window.pageYOffset;
        const destino = scrollY + auxTop - navbarHeight - 20; // 20px de margen
        
        window.scrollTo({
            top: destino,
            behavior: 'smooth'
        });
    }
}

/**
 * Recarga solo la tabla sin recargar la página completa ni el panel auxiliar
 * @param {Function} callback - Función a ejecutar después de que la tabla se recargue
 */
function recargarSoloTabla(callback) {
    // Guardar los filtros actuales
    const filtros = guardarEstadoFiltros();
    
    // Mostrar indicador de carga solo sobre la tabla
    $('#tablaHidrantesContainer').css('position', 'relative').append(
        '<div id="reloadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 1000">' +
        '<div class="text-center"><div class="spinner-border text-primary"></div><div class="mt-2">Actualizando tabla...</div></div>' +
        '</div>'
    );
    
    // Recargar SOLO la tabla, no el panel auxiliar
    $.get("{{ route('capturista.panel') }}", { tabla: 1 }, function(response) {
        // Actualizar solo el contenedor de la tabla
        $('#tablaHidrantesContainer').html(response);
        
        // Inicializar la tabla con DataTables
        inicializarDataTableServerSide();
        
        // Aplicar de nuevo los filtros guardados a la tabla recién cargada
        if (Object.keys(filtros).length > 0) {
            setTimeout(function() {
                aplicarFiltrosATabla(filtros, true); // true significa no hacer scroll
            }, 200);
        }
        
        // Normalizar estilos después de cargar la tabla
        setTimeout(normalizarEstilosTabla, 100);
        
        // Ejecutar callback si existe (p.ej. mostrar toast)
        if (typeof callback === 'function') {
            setTimeout(callback, 500);
        }
    });
}

// ========================
// FUNCIONES DE PERSISTENCIA DE FILTROS
// ========================

/**
 * Guarda el estado de los filtros aplicados
 */
function guardarEstadoFiltros() {
    const filtrosActivos = {};
    
    // Guardar los valores seleccionados de cada filtro
    $('.filtro-valor').each(function() {
        const campo = $(this).data('campo');
        const valor = $(this).val();
        if (valor) {
            filtrosActivos[campo] = valor;
        }
    });
    
    // Guardar en localStorage para persistencia
    localStorage.setItem('hidrantesFilterState', JSON.stringify(filtrosActivos));
    return filtrosActivos;
}

/**
 * Recupera el estado guardado de los filtros
 */
function recuperarEstadoFiltros() {
    const filtrosGuardados = localStorage.getItem('hidrantesFilterState');
    return filtrosGuardados ? JSON.parse(filtrosGuardados) : {};
}

/**
 * Aplica los filtros guardados a la tabla actual
 */
function aplicarFiltrosGuardados() {
    const filtros = recuperarEstadoFiltros();
    
    // Solo aplicar filtros si hay alguno guardado
    if (Object.keys(filtros).length > 0) {
        // Esperar a que los elementos del filtro estén disponibles
        const checkFilters = setInterval(function() {
            if ($('.filtro-valor').length > 0) {
                clearInterval(checkFilters);
                
                // Establecer los valores en los selectores
                $('.filtro-valor').each(function() {
                    const campo = $(this).data('campo');
                    if (filtros[campo]) {
                        $(this).val(filtros[campo]);
                    }
                });
                
                // Aplicar los filtros a la tabla
                if (window.configTable) {
                    aplicarFiltrosATabla(filtros);
                }
            }
        }, 100);
    }
}

/**
 * Aplica los filtros a la tabla
 * Esta función debe definirse en configuracion-param-auxiliar.blade.php
 * Agregada aquí como referencia
 */
function aplicarFiltrosATabla(filtros, noScroll = false) {
    // Guardar el estado de los filtros
    localStorage.setItem('hidrantesFilterState', JSON.stringify(filtros));
    
    // Actualizar visualmente los selectores de filtro para reflejar los filtros aplicados
    $('.filtro-valor').each(function() {
        const campo = $(this).data('campo');
        if (filtros[campo] !== undefined) {
            $(this).val(filtros[campo]);
        } else {
            $(this).val(''); // Si no hay filtro para este campo, seleccionar "Todos"
        }
    });
}

/**
 * Muestra un mensaje toast
 * @param {string} mensaje - El mensaje a mostrar
 * @param {string} tipo - El tipo de mensaje (success, error, warning, info)
 * @param {number} duracion - Duración en milisegundos (por defecto 3000ms)
 */
function mostrarToast(mensaje, tipo = 'success', duracion = 3000) {
    // Definir el icono según el tipo usando Bootstrap Icons
    let icono = 'check-circle';
    let colorClase = 'text-success';
    let borderClass = 'toast-success';
    
    switch(tipo) {
        case 'error':
            icono = 'exclamation-circle';
            colorClase = 'text-danger';
            borderClass = 'toast-error';
            break;
        case 'warning':
            icono = 'exclamation-triangle';
            colorClase = 'text-warning';
            borderClass = 'toast-warning';
            break;
        case 'info':
            icono = 'info-circle';
            colorClase = 'text-info';
            borderClass = 'toast-info';
            break;
    }
    
    // Crear el HTML del toast con Bootstrap Icons
    const toast = `<div class="toast align-items-center text-bg-light border-0 ${borderClass}" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${icono} ${colorClase} me-2"></i> ${mensaje}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>`;
    
    // Agregar el toast al DOM
    $('#toastContainer').append(toast);
    
    // Obtener el último toast agregado
    const toastEl = $('#toastContainer .toast').last()[0];
    
    // Inicializar y mostrar el toast
    const bsToast = new bootstrap.Toast(toastEl, { delay: duracion });
    bsToast.show();
    
    // Eliminar el toast del DOM después de ocultarse
    $(toastEl).on('hidden.bs.toast', function() {
        $(this).remove();
    });
}
</script>
@endsection