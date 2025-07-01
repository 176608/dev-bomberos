<div class="modal fade modal-create" id="crearHidranteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('hidrantes.store') }}" method="POST" id="formCrearHidrante">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Hidrante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: rgba(201, 201, 201, 0.8);">
                    
                    <div class="row">
                        
                        <div class="card text-center p-0">

                            <div class="card-header bg-primary text-white">
                                Información Básica
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de Inspección:</label>
                                        <input type="date" class="form-control" name="fecha_inspeccion" 
                                            id="fecha_inspeccion"
                                            value="{{ date('Y-m-d') }}" required>
                                            <small class="form-text text-muted">Formato: DD-MM-YYYY</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacion"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                            Fecha tentativa de Mantenimiento:
                                        </label>
                                        <div class="d-grid gap-2 mb-2" id="contenedorGenerarFecha">
                                            <button type="button" class="btn btn-primary" id="btnGenerarFecha">
                                                Generar fecha tentativa
                                            </button>
                                        </div>
                                        <div class="btn-group d-none w-100 mb-2" id="opcionesPlazo">
                                            <button type="button" class="btn btn-outline-primary" data-plazo="corto">Corto plazo</button>
                                            <button type="button" class="btn btn-outline-primary" data-plazo="largo">Largo plazo</button>
                                            <button type="button" class="btn btn-outline-secondary" id="btnRegresarGenerar">
                                                <i class="bi bi-arrow-left"></i>
                                            </button>
                                        </div>
                                        <div class="d-none mb-2" id="contenedorFechaGenerada">
                                            <input type="date" class="form-control" name="fecha_tentativa" id="fecha_tentativa">
                                            <button type="button" class="btn btn-outline-secondary mt-2 btn-sm" id="btnResetFecha">
                                                <i class="bi bi-arrow-left"></i> Cambiar plazo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-6 mb-3 offset-md-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionNEstacion"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        Número de Estación:</label>
                                        <select class="form-select" name="numero_estacion" required>
                                            <option value="S/I" selected>Seleccione estación...</option>
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="card text-center p-0">

                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <span>Ubicación</span>
                            </div>

                            <div class="card-body">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionCalle"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        Calle:</label>
                                        <div class="input-group justify-content-center">
                                            <select class="form-select select2-search" name="id_calle" id="id_calle">
                                                <option value="">Buscar calle principal...</option>
                                                @foreach($calles as $calle)
                                                    <option value="{{ $calle->IDKEY }}">{{ $calle->Nomvial }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-text bg-white border-0">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="switchNoCalle">
                                                    <label class="form-check-label small ms-2" for="switchNoCalle">No aparece la calle</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionYCalle"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        Y Calle:</label>
                                        <div class="input-group justify-content-center">
                                            <select class="form-select select2-search" name="id_y_calle" id="id_y_calle">
                                                <option value="">Buscar calle secundaria...</option>
                                                @foreach($calles as $calle)
                                                    <option value="{{ $calle->IDKEY }}">{{ $calle->Nomvial }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-text bg-white border-0">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="switchNoYCalle">
                                                    <label class="form-check-label small ms-2" for="switchNoYCalle">No aparece la calle</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">
                                            <span id="iconoExclamacionColonia"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        Colonia:</label>
                                        <div class="input-group justify-content-center">
                                            <select class="form-select select2-search" name="id_colonia" id="id_colonia">
                                                <option value="">Buscar colonia...</option>
                                                @foreach($colonias as $colonia)
                                                    <option value="{{ $colonia->IDKEY }}">{{ $colonia->NOMBRE }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-text bg-white border-0">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="switchNoColonia">
                                                    <label class="form-check-label small ms-2" for="switchNoColonia">No aparece la colonia</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            
                        </div>
                    </div>

                    <hr class="my-4">

                    
                    <div class="row">
                        <div class="card text-center p-0">

                            <div class="card-header bg-primary text-white">
                                Características Técnicas
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionLlaveHi"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Llave Hidrante:</label>
                                        <select class="form-select" name="llave_hidrante" required>
                                            <option value="S/I" selected>Sin definir, dejar pendiente...</option>
                                            <option value="Pentagono">Pentágono</option>
                                            <option value="Cuadro">Cuadro</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionPresionA"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Presión de Agua:</label>
                                        <select class="form-select" name="presion_agua" required>
                                            <option value="S/I" selected>Sin definir, dejar pendiente...</option>
                                            <option value="Mala">Mala</option>
                                            <option value="Buena">Buena</option>
                                            <option value="Sin agua">Sin agua</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionLlaveFosa"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Llave Fosa:</label>
                                        <select class="form-select" name="llave_fosa" required>
                                            <option value="S/I" selected >Sin definir, dejar pendiente...</option>
                                            <option value="Cuadro">Cuadro</option>
                                            <option value="Volante">Volante</option>
                                            <option value="Otra">Otra</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionHCT"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Conectado a Tubo de:</label>
                                        <select class="form-select" name="hidrante_conectado_tubo" required>
                                            <option value="S/I" selected >Sin definir, dejar pendiente...</option>
                                            <option value="4'">4'</option>
                                            <option value="6'">6'</option>
                                            <option value="8'">8'</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionUbiFosa"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        Ubicación Fosa:</label>
                                        <input type="text" class="form-control" name="ubicacion_fosa" id="ubicacion_fosa" placeholder="(N MTS.) Ejemplo: 5 MTS." required>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="card text-center p-0">

                            <div class="card-header bg-success text-white">
                                Estado y Características
                            </div>

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionMarca"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span> 
                                        Marca:</label>
                                        <input type="text" class="form-control" name="marca" id="marca" placeholder="MUELLER" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionYY"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span> 
                                        Año:</label>
                                        <input type="number" class="form-control" name="anio" id="anio" placeholder="Año de inicio del servicio del hidrante" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionEstadoH"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Estado Hidrante:</label>
                                        <select class="form-select" name="estado_hidrante" required>
                                            <option value="S/I" selected >Sin definir, dejar pendiente...</option>
                                            <option value="Servicio">Servicio</option>
                                            <option value="Fuera de servicio">Fuera de servicio</option>
                                            <option value="Solo Base">Solo Base</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionColor"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Color:</label>
                                        <select class="form-select" name="color" required>
                                            <option value="S/I" selected>Sin definir, dejar pendiente..</option>
                                            <option value="Rojo">Rojo</option>
                                            <option value="Amarillo">Amarillo</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-secondary text-white">
                                Información Adicional
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Observaciones:</label>
                                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Escriba observaciones aquí..."></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">
                                            <span id="iconoExclamacionOficial"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span> 
                                        Oficial:</label>
                                        <input type="text" class="form-control" name="oficial" id="oficial" placeholder="Nombre del oficial responsable" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="modal-footer">
                    <span class="d-inline-block" tabindex="0" id="popoverRegistrarHidrante"
                          data-bs-toggle="popover"
                          data-bs-trigger="hover focus"
                          data-bs-placement="top"
                          title="¡Atención!"
                          data-bs-content="Falta generar una fecha tentativa de mantenimiento.">
                        <button type="submit" class="btn btn-danger" id="btnRegistrarHidrante" disabled>
                            Registrar Hidrante
                        </button>
                    </span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
}

.select2-container--bootstrap-5 .select2-search__field:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #0d6efd;
    color: white;
}

.select2-container--bootstrap-5 .select2-results__option {
    padding: 6px 12px;
}

.modal .select2-container {
    z-index: 1056;
}

/* Ajustes para el posicionamiento del dropdown */
.select2-container--bootstrap-5.select2-container--open {
    z-index: 1060 !important; /* Mayor que el z-index del modal */
}

.select2-container--bootstrap-5 .select2-dropdown {
    border-color: #dee2e6;
    border-radius: 0.375rem;
}

/* Asegurar que el dropdown esté contenido correctamente */
.select2-container--bootstrap-5 .select2-dropdown--below {
    margin-top: 2px;
}

/* Corregir el ancho del contenedor */
.select2.select2-container {
    width: 100% !important;
}

/* Ajustar el padding del contenedor del modal */
.modal-body .select2-container {
    display: block;
}

/* Personaliza el fondo y texto del título del popover */
.popover-header {
    background-color: #dc3545 !important;
    color: #fff !important;
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid #fff;
}

/* Personaliza el contenido del popover */
.popover-body {
    color: #212529 !important;
    font-size: 1rem;
    text-align: center;
}

.popover {
    border: 2px solid #dc3545;
}

.input-disabled {
    background-color: #e9ecef !important;
    opacity: 0.7;
    pointer-events: none;
}
</style>

<script>
$(document).ready(function() {
    let fechaTentativaGenerada = false;    

    function initSelect2Modal() {
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('#crearHidranteModal .modal-body'), // Cambio importante aquí
            language: {
                noResults: function() {
                    return "No se encontraron resultados";
                },
                searching: function() {
                    return "Buscando...";
                }
            },
            placeholder: 'Comienza a escribir para buscar...',
            allowClear: true,
            minimumInputLength: 2,
            scrollAfterSelect: false, // Prevenir scroll automático
            position: function(pos, $el) {
                pos.top += 5; // Ajuste fino del posicionamiento
                return pos;
            }
        }).on('select2:open', function() {
            setTimeout(function() {
                $('.select2-search__field').get(0).focus();
            }, 10);
        });
    }

    // Reinicializar Select2 cuando se abre el modal
    $('#crearHidranteModal').on('shown.bs.modal', function () {
        $('#fecha_tentativa').val('');
        initSelect2Modal();
        $(window).trigger('resize');
        mostrarPasoGenerar();
        setTimeout(initPopover, 200);
    });

    // Limpiar y destruir Select2 cuando se cierra el modal
    $('#crearHidranteModal').on('hidden.bs.modal', function () {
        $('.select2-search').select2('destroy');
    });

    // --- FECHA TENTATIVA FLUJO ---
    function mostrarPasoGenerar() {
        $('#contenedorGenerarFecha').removeClass('d-none');
        $('#opcionesPlazo').addClass('d-none');
        $('#contenedorFechaGenerada').addClass('d-none');
        $('#iconoExclamacion').removeClass('d-none');
        fechaTentativaGenerada = false;
        actualizarEstadoBotonRegistrar();
    }

    function mostrarPasoPlazo() {
        $('#contenedorGenerarFecha').addClass('d-none');
        $('#opcionesPlazo').removeClass('d-none');
        $('#contenedorFechaGenerada').addClass('d-none');
        $('#iconoExclamacion').removeClass('d-none');
        fechaTentativaGenerada = false;
        actualizarEstadoBotonRegistrar();
    }

    function mostrarPasoFechaGenerada() {
        $('#contenedorGenerarFecha').addClass('d-none');
        $('#opcionesPlazo').addClass('d-none');
        $('#contenedorFechaGenerada').removeClass('d-none');
        $('#iconoExclamacion').addClass('d-none');
        fechaTentativaGenerada = true;
        actualizarEstadoBotonRegistrar();
    }

    // Paso 1: Mostrar opciones de plazo
    $('#btnGenerarFecha').click(function() {
        mostrarPasoPlazo();
    });

    // Paso 2: Selección de plazo y generación de fecha
    $('#opcionesPlazo button[data-plazo]').click(function() {
        const plazo = $(this).data('plazo');
        const fechaInspeccion = new Date($('#fecha_inspeccion').val());

        if (plazo === 'corto') {
            fechaInspeccion.setMonth(fechaInspeccion.getMonth() + 6);
        } else {
            fechaInspeccion.setFullYear(fechaInspeccion.getFullYear() + 1);
        }

        $('#fecha_tentativa').val(fechaInspeccion.toISOString().split('T')[0]);
        mostrarPasoFechaGenerada();
    });

    // Botón para regresar de selección de plazo a "Generar fecha tentativa"
    $('#btnRegresarGenerar').click(function() {
        mostrarPasoGenerar();
    });

    // Botón para regresar de fecha generada a selección de plazo
    $('#btnResetFecha').click(function() {
        mostrarPasoPlazo();
    });

    // --- SWITCHES DE UBICACIÓN INDIVIDUAL ---
    $('#switchNoCalle').change(function() {
        if ($(this).is(':checked')) {
            $('#id_calle').prop('disabled', true).addClass('input-disabled').val('').trigger('change');
            $('#iconoExclamacionCalle').addClass('d-none'); // Oculta icono
            // Agrega campo oculto para enviar id_calle = 0
            if (!$('input[name="id_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_calle', value: '0'}).appendTo('#formCrearHidrante');
            }
            if (!$('input[name="calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'calle', value: 'Pendiente'}).appendTo('#formCrearHidrante');
            }
        } else {
            $('#id_calle').prop('disabled', false).removeClass('input-disabled');
            // Si el campo sigue vacío, muestra el icono
            if (!$('#id_calle').val()) {
                $('#iconoExclamacionCalle').removeClass('d-none');
            }
            $('input[name="id_calle"][type="hidden"]').remove();
            $('input[name="calle"][type="hidden"]').remove();
        }
    });

    $('#switchNoYCalle').change(function() {
        if ($(this).is(':checked')) {
            $('#id_y_calle').prop('disabled', true).addClass('input-disabled').val('').trigger('change');
            $('#iconoExclamacionYCalle').addClass('d-none');
            if (!$('input[name="id_y_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_y_calle', value: '0'}).appendTo('#formCrearHidrante');
            }
            if (!$('input[name="y_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'y_calle', value: 'Pendiente'}).appendTo('#formCrearHidrante');
            }
        } else {
            $('#id_y_calle').prop('disabled', false).removeClass('input-disabled');
            if (!$('#id_y_calle').val()) {
                $('#iconoExclamacionYCalle').removeClass('d-none');
            }
            $('input[name="id_y_calle"][type="hidden"]').remove();
            $('input[name="y_calle"][type="hidden"]').remove();
        }
    });

    $('#switchNoColonia').change(function() {
        if ($(this).is(':checked')) {
            $('#id_colonia').prop('disabled', true).addClass('input-disabled').val('').trigger('change');
            $('#iconoExclamacionColonia').addClass('d-none');
            if (!$('input[name="id_colonia"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_colonia', value: '0'}).appendTo('#formCrearHidrante');
            }
            if (!$('input[name="colonia"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'colonia', value: 'Pendiente'}).appendTo('#formCrearHidrante');
            }
        } else {
            $('#id_colonia').prop('disabled', false).removeClass('input-disabled');
            if (!$('#id_colonia').val()) {
                $('#iconoExclamacionColonia').removeClass('d-none');
            }
            $('input[name="id_colonia"][type="hidden"]').remove();
            $('input[name="colonia"][type="hidden"]').remove();
        }
    });

    // --- SUBMIT FORMULARIO ---
    $('#formCrearHidrante').submit(function(e) {
        if (!fechaTentativaGenerada) {
            e.preventDefault();
            return false;
        }

        const fields = ['calle', 'y_calle', 'colonia'];
        const pendienteChecked = $('#ubicacionPendiente').is(':checked');
        
        if (!pendienteChecked) {
            fields.forEach(field => {
                const selectValue = $(`#id_${field}`).val();
                if (!selectValue) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: field,
                        value: 'Sin definir'
                    }).appendTo(this);
                    
                    $('<input>').attr({
                        type: 'hidden',
                        name: `id_${field}`,
                        value: ''
                    }).appendTo(this);
                }
            });
        }
    });

    // --- POPOVER BOTÓN REGISTRAR ---
    function initPopover() {
        const popoverTrigger = document.getElementById('popoverRegistrarHidrante');
        if (popoverTrigger) {
            if (bootstrap.Popover.getInstance(popoverTrigger)) {
                bootstrap.Popover.getInstance(popoverTrigger).dispose();
            }
            new bootstrap.Popover(popoverTrigger);
        }
    }

    function actualizarEstadoBotonRegistrar() {
        if (fechaTentativaGenerada) {
            $('#btnRegistrarHidrante').prop('disabled', false);
            $('#popoverRegistrarHidrante').removeAttr('data-bs-toggle').removeAttr('data-bs-trigger').removeAttr('data-bs-content');
            if (bootstrap.Popover.getInstance(document.getElementById('popoverRegistrarHidrante'))) {
                bootstrap.Popover.getInstance(document.getElementById('popoverRegistrarHidrante')).dispose();
            }
        } else {
            $('#btnRegistrarHidrante').prop('disabled', true);
            $('#popoverRegistrarHidrante')
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', 'Falta generar la fecha tentativa de mantenimiento.');
            initPopover();
        }
    }

    // Inicializa el popover al abrir el modal
    $('#crearHidranteModal').on('shown.bs.modal', function () {
        mostrarPasoGenerar();
        $('#fecha_tentativa').val('');
        setTimeout(initPopover, 200); // Espera a que el DOM esté listo
    });

    const camposConExclamacion = [
        { name: 'numero_estacion', icon: 'iconoExclamacionNEstacion', tipo: 'select' },
        { name: 'llave_hidrante', icon: 'iconoExclamacionLlaveHi', tipo: 'select' },
        { name: 'presion_agua', icon: 'iconoExclamacionPresionA', tipo: 'select' },
        { name: 'llave_fosa', icon: 'iconoExclamacionLlaveFosa', tipo: 'select' },
        { name: 'hidrante_conectado_tubo', icon: 'iconoExclamacionHCT', tipo: 'select' },
        { name: 'estado_hidrante', icon: 'iconoExclamacionEstadoH', tipo: 'select' },
        { name: 'color', icon: 'iconoExclamacionColor', tipo: 'select' },
        { name: 'marca', icon: 'iconoExclamacionMarca', tipo: 'input' },
        { name: 'anio', icon: 'iconoExclamacionYY', tipo: 'input' },
        { name: 'oficial', icon: 'iconoExclamacionOficial', tipo: 'input' },
        { name: 'ubicacion_fosa', icon: 'iconoExclamacionUbiFosa', tipo: 'input' },
        { name: 'id_calle', icon: 'iconoExclamacionCalle', tipo: 'select' },
        { name: 'id_y_calle', icon: 'iconoExclamacionYCalle', tipo: 'select' },
        { name: 'id_colonia', icon: 'iconoExclamacionColonia', tipo: 'select' }
    ];

    camposConExclamacion.forEach(function(campo) {
        if (campo.tipo === 'select') {
            $(`select[name="${campo.name}"]`).on('change', function() {
                if ($(this).val() === 'S/I' || $(this).val() === '' || $(this).val() === null) {
                    $(`#${campo.icon}`).removeClass('d-none');
                } else {
                    $(`#${campo.icon}`).addClass('d-none');
                }
            });
            // Estado inicial
            if ($(`select[name="${campo.name}"]`).val() === 'S/I' || $(`select[name="${campo.name}"]`).val() === '' || $(`select[name="${campo.name}"]`).val() === null) {
                $(`#${campo.icon}`).removeClass('d-none');
            } else {
                $(`#${campo.icon}`).addClass('d-none');
            }
        } else if (campo.tipo === 'input') {
            $(`input[name="${campo.name}"]`).on('input', function() {
                if ($(this).val() === '' || $(this).val() === null) {
                    $(`#${campo.icon}`).removeClass('d-none');
                } else {
                    $(`#${campo.icon}`).addClass('d-none');
                }
            });
            // Estado inicial
            if ($(`input[name="${campo.name}"]`).val() === '' || $(`input[name="${campo.name}"]`).val() === null) {
                $(`#${campo.icon}`).removeClass('d-none');
            } else {
                $(`#${campo.icon}`).addClass('d-none');
            }
        }
    });

    function camposUbicacionFaltantes() {
        let faltantes = [];
        if ($('#id_calle').is(':enabled') && !$('#id_calle').val()) faltantes.push('Calle');
        if ($('#ubicacion_fosa').is(':enabled') && !$('#ubicacion_fosa').val()) faltantes.push('Ubicación Fosa');
        if ($('#oficial').is(':enabled') && !$('#oficial').val()) faltantes.push('Oficial');
        if ($('#marca').is(':enabled') && !$('#marca').val()) faltantes.push('Marca');
        if ($('#anio').is(':enabled') && !$('#anio').val()) faltantes.push('Año');
        return faltantes;
    }

    $('#btnGuardarHidrante').on('mouseenter focus', function() {
        let faltantes = camposUbicacionFaltantes();
        let popoverMsg = '';
        if (!fechaTentativaGenerada) {
            popoverMsg = 'Falta generar una fecha tentativa de mantenimiento.';
        } else if (faltantes.length > 0) {
            popoverMsg = 'Faltan campos obligatorios: ' + faltantes.join(', ');
        }
        if (popoverMsg) {
            $(this)
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', popoverMsg);
            if (bootstrap.Popover.getInstance(this)) {
                bootstrap.Popover.getInstance(this).setContent({ '.popover-body': popoverMsg });
            } else {
                new bootstrap.Popover(this);
            }
        } else {
            $(this).removeAttr('data-bs-toggle data-bs-trigger data-bs-content');
            if (bootstrap.Popover.getInstance(this)) {
                bootstrap.Popover.getInstance(this).dispose();
            }
        }
    });
});
</script>