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
                                            <span id="iconoExclamacionNumero_estacion"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        Número de Estación:</label>
                                        <select class="form-select" name="numero_estacion" required>
                                            <option value="S/I" selected>Sin el dato...</option>
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

                                <!--<hr class="my-2">

                                <div class="row">
                                    
                                </div>-->

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
                                                <option value="">Buscar calle...</option>
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
                                                <option value="">Buscar calle...</option>
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
                                    <div class="col-md-6 mb-3 offset-md-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionEstado_hidrante"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Estado Hidrante:</label>
                                        <select class="form-select" name="estado_hidrante" required>
                                            <option value="S/I" selected >Dejar pendiente</option>
                                            <option value="EN SERVICIO">En servicio</option>
                                            <option value="FUERA DE SERVICIO">Fuera de servicio</option>
                                            <option value="SOLO BASE">Solo base</option>
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
                                            <span id="iconoExclamacionAnio"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span> 
                                        Año:</label>
                                        <input type="text" class="form-control" name="anio" placeholder="Año de inicio del servicio del hidrante" required>
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
                                            <span id="iconoExclamacionLlave_Hidrante"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Llave Hidrante:</label>
                                        <select class="form-select" name="llave_hidrante" required>
                                            <option value="S/I" selected>Sin el dato</option>
                                            <option value="PENTAGONO">Pentágono</option>
                                            <option value="CUADRO">Cuadro</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionPresion_agua"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Presión de Agua:</label>
                                        <select class="form-select" name="presion_agua" required>
                                            <option value="S/I" selected>Sin el dato</option>
                                            <option value="NULA">Nula</option>
                                            <option value="BAJA">Baja</option>
                                            <option value="REGULAR">Regular</option>
                                            <option value="ALTA">Alta</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionLlave_fosa"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
                                        Llave Fosa:</label>
                                        <select class="form-select" name="llave_fosa" required>
                                            <option value="S/I" selected >Sin dato</option>
                                            <option value="CUADRO">Cuadro</option>
                                            <option value="VOLANTE">Volante</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <span id="iconoExclamacionHidrante_conectado_tubo"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span> 
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
                                            <span id="iconoExclamacionUbicacion_fosa"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        Ubicación Fosa:</label>
                                        <input type="text" class="form-control" name="ubicacion_fosa" placeholder="A + N + metros, $Texto despues de numero$" required>
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
                          data-bs-content="Debe seleccionar una calle (o marcar como pendiente) y definir el Estado del Hidrante.">
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
            { name: 'numero_estacion', icon: 'iconoExclamacionNumero_estacion', tipo: 'select' },
            { name: 'llave_hidrante', icon: 'iconoExclamacionLlave_Hidrante', tipo: 'select' },
            { name: 'presion_agua', icon: 'iconoExclamacionPresion_agua', tipo: 'select' },
            { name: 'llave_fosa', icon: 'iconoExclamacionLlave_fosa', tipo: 'select' },
            { name: 'hidrante_conectado_tubo', icon: 'iconoExclamacionHidrante_conectado_tubo', tipo: 'select' },
            { name: 'estado_hidrante', icon: 'iconoExclamacionEstado_hidrante', tipo: 'select' },
            { name: 'marca', icon: 'iconoExclamacionMarca', tipo: 'input' },
            { name: 'anio', icon: 'iconoExclamacionAnio', tipo: 'input' },
            { name: 'oficial', icon: 'iconoExclamacionOficial', tipo: 'input' },
            { name: 'ubicacion_fosa', icon: 'iconoExclamacionUbicacion_fosa', tipo: 'input' },
            { name: 'id_calle', icon: 'iconoExclamacionCalle', tipo: 'select' },
            { name: 'id_y_calle', icon: 'iconoExclamacionYCalle', tipo: 'select' },
            { name: 'id_colonia', icon: 'iconoExclamacionColonia', tipo: 'select' }
        ]
    };

    // --- FUNCIÓN PARA ICONOS ---
    // Esta función muestra u oculta el icono de advertencia según el valor del campo, esto es lo que hay que revisar
    function toggleExclamationIcon(iconId, value) {
        const icon = $(iconId);
        // Incluir 'S/I' como un valor que activa el icono de advertencia
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
                $select.val('0').trigger('change');
                $icon.addClass('d-none');
                hidden.forEach(name => {
                    if (!$(`input[name="${name}"][type="hidden"]`).length) {
                        $('<input>').attr({type: 'hidden', name: name, value: name.startsWith('id_') ? '0' : 'Pendiente'}).appendTo(CONFIG.formId);
                    }
                });
            } else {
                $select.val('').trigger('change');
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
    function updateSaveButtonState() {
        // Verifica si el campo calle está cubierto (valor válido o switch activo)
        let calleOk = false;
        if ($('#switchNoCalle').is(':checked')) {
            calleOk = true;
        } else if ($('#id_calle').val() && $('#id_calle').val() !== '' && $('#id_calle').val() !== null) {
            calleOk = true;
        }

        // Verifica si el estado de hidrante está definido (no es 'S/I')
        let estadoOk = false;
        const estadoVal = $('select[name="estado_hidrante"]').val();
        if (estadoVal && estadoVal !== 'S/I') {
            estadoOk = true;
        }

        if (calleOk && estadoOk) {
            $('#btnRegistrarHidrante').prop('disabled', false);
            $('#popoverRegistrarHidrante').removeAttr('data-bs-toggle data-bs-trigger data-bs-content');
            if (bootstrap.Popover.getInstance(document.getElementById('popoverRegistrarHidrante'))) {
                bootstrap.Popover.getInstance(document.getElementById('popoverRegistrarHidrante')).dispose();
            }
        } else {
            $('#btnRegistrarHidrante').prop('disabled', true);
            let mensaje = 'Debe seleccionar una calle (o marcar como pendiente) y definir el Estado del Hidrante.';
            $('#popoverRegistrarHidrante')
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', mensaje);
            if (typeof initPopover === 'function') initPopover();
        }
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

    // --- SOLO BASE: LÓGICA DE BLOQUEO Y LIMPIEZA ---
    function handleSoloBaseStateCreate(isSoloBase) {
        // Campos de Estado y Características + Técnicas (excepto Estado Hidrante)
        const campos = [
            'marca', 'anio',
            'llave_hidrante', 'presion_agua', 'llave_fosa',
            'hidrante_conectado_tubo', 'ubicacion_fosa'
        ];
        // Iconos de exclamación de esas secciones
        const iconos = [
            '#iconoExclamacionMarca',
            '#iconoExclamacionAnio',
            '#iconoExclamacionLlave_Hidrante',
            '#iconoExclamacionPresion_agua',
            '#iconoExclamacionLlave_fosa',
            '#iconoExclamacionHidrante_conectado_tubo',
            '#iconoExclamacionUbicacion_fosa'
        ];
        campos.forEach(function(name) {
            const $input = $(`[name="${name}"]`);
            if (isSoloBase) {
                if ($input.is('select')) {
                    $input.val('S/I').trigger('change');
                } else {
                    if (name === 'marca' || name === 'ubicacion_fosa') $input.val('S/I');
                    if (name === 'anio') $input.val('S/I');
                }
            }
        });
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

    // --- EVENTOS ---
    function initEventHandlers() {
        // Switches de ubicación
        CONFIG.switches.forEach(setupSwitchHandler);

        // Iconos de advertencia
        setupIcons();

        // Select2
        initSelect2();

        // Guardar: validación
        $(CONFIG.formId).on('submit', function(e) {
            // Solo validamos calle y estado_hidrante
            if ($('#switchNoCalle').is(':checked')) {
                $('#id_calle').val('0');
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
            // Validación de estado_hidrante
            const estadoVal = $('select[name="estado_hidrante"]').val();
            if (!estadoVal || estadoVal === 'S/I') {
                e.preventDefault();
                alert('Debes definir el Estado del Hidrante.');
                return false;
            }
        });

        // Actualizar botón registrar cuando cambian campos clave
        $('#id_calle, #switchNoCalle, select[name="estado_hidrante"]').on('change', updateSaveButtonState);

        // Limpia todos los inputs ocultos de ubicación antes de agregar los necesarios
        $('input[type="hidden"][name="id_calle"], input[type="hidden"][name="calle"], input[type="hidden"][name="id_y_calle"], input[type="hidden"][name="y_calle"], input[type="hidden"][name="id_colonia"], input[type="hidden"][name="colonia"]').remove();

        // Estado Hidrante: SOLO BASE
        $('select[name="estado_hidrante"]').on('change', function() {
            const isSoloBase = $(this).val() === 'SOLO BASE';
            handleSoloBaseStateCreate(isSoloBase);
        });
    }

    // Al abrir el modal, aplicar si ya está en SOLO BASE
    $(CONFIG.modalId).on('shown.bs.modal', function() {
        const isSoloBase = $('select[name="estado_hidrante"]').val() === 'SOLO BASE';
        handleSoloBaseStateCreate(isSoloBase);
    });

    // --- INICIALIZACIÓN DEL MODAL ---
    $(CONFIG.modalId)
        .on('shown.bs.modal', function() {
            initEventHandlers();
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