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

                            <div class="card-header bg-success text-white d-flex justify-content-center align-items-center">
                                <span class="text-center w-100">Ubicación</span>
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
                                Estado y Características
                            </div>

                            <div class="card-body">

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

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionMarca"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span> 
                                        Marca:</label>
                                        <input type="text" class="form-control" name="marca" placeholder="MUELLER" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionYY"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span> 
                                        Año:</label>
                                        <input type="number" class="form-control" name="anio" placeholder="Año de inicio del servicio del hidrante" required>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="card text-center p-0">

                            <div class="card-header bg-success text-white">
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
                                        <input type="text" class="form-control" name="ubicacion_fosa" placeholder="(N MTS.) Ejemplo: 5 MTS." required>
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
                                        <input type="text" class="form-control" name="oficial" placeholder="Nombre del oficial responsable" required>
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
    // --- CONFIGURACIÓN CENTRAL ---
    const CONFIG = {
        modalId: '#crearHidranteModal',
        formId: '#formCrearHidrante',
        switches: [
            {
                field: 'calle',
                switchId: '#switchNoCalle',
                selectId: '#id_calle',
                iconId: '#iconoExclamacionCalle',
                hidden: ['id_calle', 'calle']
            },
            {
                field: 'y_calle',
                switchId: '#switchNoYCalle',
                selectId: '#id_y_calle',
                iconId: '#iconoExclamacionYCalle',
                hidden: ['id_y_calle', 'y_calle']
            },
            {
                field: 'colonia',
                switchId: '#switchNoColonia',
                selectId: '#id_colonia',
                iconId: '#iconoExclamacionColonia',
                hidden: ['id_colonia', 'colonia']
            }
        ],
        fieldsWithIcons: [
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
        ]
    };

    // --- FUNCIÓN PARA ICONOS ---
    function toggleExclamationIcon(iconId, value) {
        const icon = $(iconId);
        if (value === 'S/I' || value === '0' || value === '' || value === null) {
            icon.removeClass('d-none');
        } else {
            icon.addClass('d-none');
        }
    }

    // --- FUNCIÓN PARA SWITCHES ---
    function setupSwitchHandler({switchId, selectId, iconId, hidden}) {
        $(switchId).change(function() {
            const $select = $(selectId);
            const $icon = $(iconId);
            if ($(this).is(':checked')) {
                $select.prop('disabled', true).addClass('input-disabled').val('').trigger('change');
                $icon.addClass('d-none');
                hidden.forEach(name => {
                    if (!$(`input[name="${name}"][type="hidden"]`).length) {
                        $('<input>').attr({type: 'hidden', name: name, value: name.startsWith('id_') ? '0' : 'Pendiente'}).appendTo(CONFIG.formId);
                    }
                });
            } else {
                $select.prop('disabled', false).removeClass('input-disabled');
                hidden.forEach(name => $(`input[name="${name}"][type="hidden"]`).remove());
                if (!$select.val()) $icon.removeClass('d-none');
            }
            updateSaveButtonState();
        });
    }

    // --- SELECT2 ---
    function initSelect2() {
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $(`${CONFIG.modalId} .modal-body`),
            language: {
                noResults: () => "No se encontraron resultados",
                searching: () => "Buscando..."
            },
            placeholder: 'Comienza a escribir para buscar...',
            allowClear: true,
            minimumInputLength: 2
        }).on('select2:open', function() {
            setTimeout(function() {
                $('.select2-search__field').get(0).focus();
            }, 10);
        });
    }

    // --- ICONOS DE ADVERTENCIA ---
    function setupIcons() {
        CONFIG.fieldsWithIcons.forEach(({ name, icon, tipo }) => {
            if (tipo === 'select') {
                $(`select[name="${name}"]`).on('change', function() {
                    toggleExclamationIcon(`#${icon}`, $(this).val());
                });
                toggleExclamationIcon(`#${icon}`, $(`select[name="${name}"]`).val());
            } else if (tipo === 'input') {
                $(`input[name="${name}"]`).on('input', function() {
                    toggleExclamationIcon(`#${icon}`, $(this).val());
                });
                toggleExclamationIcon(`#${icon}`, $(`input[name="${name}"]`).val());
            }
        });
    }

    // --- BOTÓN REGISTRAR Y POPOVER ---
    let fechaTentativaGenerada = false;

    function updateSaveButtonState() {
        let fechaOk = fechaTentativaGenerada;
        let calleOk = false;
        if ($('#switchNoCalle').is(':checked')) {
            calleOk = true;
        } else if ($('#id_calle').val() && $('#id_calle').val() !== '' && $('#id_calle').val() !== null) {
            calleOk = true;
        }
        if (fechaOk && calleOk) {
            $('#btnRegistrarHidrante').prop('disabled', false);
            $('#popoverRegistrarHidrante').removeAttr('data-bs-toggle data-bs-trigger data-bs-content');
            if (bootstrap.Popover.getInstance(document.getElementById('popoverRegistrarHidrante'))) {
                bootstrap.Popover.getInstance(document.getElementById('popoverRegistrarHidrante')).dispose();
            }
        } else {
            $('#btnRegistrarHidrante').prop('disabled', true);
            $('#popoverRegistrarHidrante')
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', 'Debe generar la fecha tentativa y seleccionar una calle.');
            if (typeof initPopover === 'function') initPopover();
        }
    }

    // --- FECHA TENTATIVA: FLUJO DE PASOS ---
    function mostrarPasoGenerar() {
        $('#contenedorGenerarFecha').removeClass('d-none');
        $('#opcionesPlazo').addClass('d-none');
        $('#contenedorFechaGenerada').addClass('d-none');
        $('#iconoExclamacion').removeClass('d-none');
        fechaTentativaGenerada = false;
        updateSaveButtonState();
    }
    function mostrarPasoPlazo() {
        $('#contenedorGenerarFecha').addClass('d-none');
        $('#opcionesPlazo').removeClass('d-none');
        $('#contenedorFechaGenerada').addClass('d-none');
        $('#iconoExclamacion').removeClass('d-none');
        fechaTentativaGenerada = false;
        updateSaveButtonState();
    }
    function mostrarPasoFechaGenerada() {
        $('#contenedorGenerarFecha').addClass('d-none');
        $('#opcionesPlazo').addClass('d-none');
        $('#contenedorFechaGenerada').removeClass('d-none');
        $('#iconoExclamacion').addClass('d-none');
        fechaTentativaGenerada = true;
        updateSaveButtonState();
    }

    // --- POPOVER ---
    function initPopover() {
        const popoverTrigger = document.getElementById('popoverRegistrarHidrante');
        if (popoverTrigger) {
            if (bootstrap.Popover.getInstance(popoverTrigger)) {
                bootstrap.Popover.getInstance(popoverTrigger).dispose();
            }
            new bootstrap.Popover(popoverTrigger);
        }
    }

    // --- EVENTOS ---
    function initEventHandlers() {
        // Switches de ubicación
        CONFIG.switches.forEach(setupSwitchHandler);

        // Iconos de advertencia
        setupIcons();

        // Select2
        initSelect2();

        // Fecha tentativa: flujo de pasos
        $('#btnGenerarFecha').click(mostrarPasoPlazo);
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
        $('#btnRegresarGenerar').click(mostrarPasoGenerar);
        $('#btnResetFecha').click(mostrarPasoPlazo);

        // Guardar: validación
        $(CONFIG.formId).on('submit', function(e) {
            if (!fechaTentativaGenerada) {
                e.preventDefault();
                return false;
            }
            if ($('#switchNoCalle').is(':checked')) {
                $('#id_calle').prop('disabled', true).val('0');
                if (!$('input[name="id_calle"][type="hidden"]').length) {
                    $('<input>').attr({type: 'hidden', name: 'id_calle', value: '0'}).appendTo(this);
                }
                if (!$('input[name="calle"][type="hidden"]').length) {
                    $('<input>').attr({type: 'hidden', name: 'calle', value: 'Pendiente'}).appendTo(this);
                }
            } else {
                const val = $('#id_calle').val();
                if (!val || val === '' || val === null) {
                    e.preventDefault();
                    alert('Debes seleccionar una calle o marcar el switch de "No aparece la calle".');
                    return false;
                }
                $('input[name="id_calle"][type="hidden"]').remove();
                $('input[name="calle"][type="hidden"]').remove();
            }
            // Puedes agregar validaciones adicionales aquí...
        });

        // Actualizar botón registrar cuando cambian campos clave
        $('#id_calle, #switchNoCalle').on('change', updateSaveButtonState);

        // Limpia todos los inputs ocultos de ubicación antes de agregar los necesarios
        $('input[type="hidden"][name="id_calle"], input[type="hidden"][name="calle"], input[type="hidden"][name="id_y_calle"], input[type="hidden"][name="y_calle"], input[type="hidden"][name="id_colonia"], input[type="hidden"][name="colonia"]').remove();
    }

    // --- SOLO BASE: LÓGICA DE BLOQUEO Y LIMPIEZA ---
    function handleSoloBaseStateCreate(isSoloBase) {
        // Campos de Estado y Características + Técnicas (excepto Estado Hidrante)
        const campos = [
            'color', 'marca', 'anio',
            'llave_hidrante', 'presion_agua', 'llave_fosa',
            'hidrante_conectado_tubo', 'ubicacion_fosa'
        ];
        // Iconos de exclamación de esas secciones
        const iconos = [
            '#iconoExclamacionColor',
            '#iconoExclamacionMarca',
            '#iconoExclamacionYY',
            '#iconoExclamacionLlaveHi',
            '#iconoExclamacionPresionA',
            '#iconoExclamacionLlaveFosa',
            '#iconoExclamacionHCT',
            '#iconoExclamacionUbiFosa'
        ];
        // Deshabilitar/habilitar y limpiar/poner valores
        campos.forEach(function(name) {
            const $input = $(`[name="${name}"]`);
            if (isSoloBase) {
                if ($input.is('select')) {
                    $input.val('S/I').prop('disabled', true).addClass('input-disabled').trigger('change');
                } else {
                    if (name === 'marca' || name === 'ubicacion_fosa') $input.val('S/I');
                    if (name === 'anio') $input.val('0');
                    $input.prop('disabled', true).addClass('input-disabled');
                }
            } else {
                $input.prop('disabled', false).removeClass('input-disabled');
            }
        });
        // Iconos
        iconos.forEach(function(sel) {
            if (isSoloBase) {
                $(sel).addClass('d-none');
            } else {
                // Se reevalúa el icono según la lógica normal
                const field = sel.replace('#iconoExclamacion', '').toLowerCase();
                const $input = $(`[name="${field}"]`);
                if ($input.length) {
                    toggleExclamationIcon(sel, $input.val());
                }
            }
        });
    }

    // Evento de cambio en Estado Hidrante
    $('select[name="estado_hidrante"]').on('change', function() {
        const isSoloBase = $(this).val() === 'Solo Base';
        handleSoloBaseStateCreate(isSoloBase);
    });

    // Al abrir el modal, aplicar si ya está en Solo Base
    $(CONFIG.modalId).on('shown.bs.modal', function() {
        const isSoloBase = $('select[name="estado_hidrante"]').val() === 'Solo Base';
        handleSoloBaseStateCreate(isSoloBase);
    });


    // --- INICIALIZACIÓN DEL MODAL ---
    $(CONFIG.modalId)
        .on('shown.bs.modal', function() {
            $('#fecha_tentativa').val('');
            initEventHandlers();
            mostrarPasoGenerar();
            setTimeout(initPopover, 200);
        })
        .on('hidden.bs.modal', function() {
            $('.select2-search').select2('destroy');
        });

    // Inicialización directa si el modal ya está abierto
    if ($(CONFIG.modalId).is(':visible')) {
        initEventHandlers();
    }
});
</script>