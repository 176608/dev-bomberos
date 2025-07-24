<div class="modal fade modal-create" id="crearHidranteModal" tabindex="-1">
    <div class="modal-dialog modal-xl"> <!-- Cambio de modal-lg a modal-xl -->
        <div class="modal-content">
            <form action="{{ route('hidrantes.store') }}" method="POST" id="formCrearHidrante">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Hidrante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: rgba(120, 255, 232, 0.8);">
                    
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
                                            <option value="S/I" selected>Sin definir, dejar pendiente...</option>
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
                                            <span id="iconoExclamacionCalle">
                                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                            </span>
                                            Entre <span id="calle_tipo_display" class="d-none fw-bold text-primary"></span>:
                                        </label>
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <select class="form-select select2-search" name="id_calle" id="id_calle">
                                                    <option value="">Buscar calle...</option>
                                                    @foreach($calles as $calle)
                                                        <option value="{{ $calle->IDKEY }}" data-tipo="{{ $calle->Tipovial }}">{{ $calle->Nomvial }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="calle" id="calle_manual" placeholder="O escribe manualmente si no aparece en la lista">
                                        <small class="form-text text-muted">
                                            <div id="calle_selected_container" class="d-none">
                                                Tipo y nombre: <span id="calle_selected_tipo" class="fw-bold"></span>
                                            </div>
                                        </small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Y <span id="y_calle_tipo_display" class="d-none fw-bold text-primary"></span>:
                                        </label>
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <select class="form-select select2-search" name="id_y_calle" id="id_y_calle">
                                                    <option value="">Buscar calle...</option>
                                                    @foreach($calles as $calle)
                                                        <option value="{{ $calle->IDKEY }}" data-tipo="{{ $calle->Tipovial }}">{{ $calle->Nomvial }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="y_calle" id="y_calle_manual" placeholder="O escribe manualmente si no aparece en la lista">
                                        <small class="form-text text-muted">
                                            <div id="y_calle_selected_container" class="d-none">
                                                Tipo y nombre: <span id="y_calle_selected_tipo" class="fw-bold"></span>
                                            </div>
                                        </small>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">
                                            En <span id="colonia_tipo_display" class="d-none fw-bold text-primary"></span>:
                                        </label>
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <select class="form-select select2-search" name="id_colonia" id="id_colonia">
                                                    <option value="">Buscar colonia...</option>
                                                    @foreach($colonias as $colonia)
                                                        <option value="{{ $colonia->IDKEY }}" data-tipo="{{ $colonia->TIPO }}">{{ $colonia->NOMBRE }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="colonia" id="colonia_manual" placeholder="O escribe manualmente si no aparece en la lista">
                                        <small class="form-text text-muted">
                                            <div id="colonia_selected_container" class="d-none">
                                                Tipo y nombre: <span id="colonia_selected_tipo" class="fw-bold"></span>
                                            </div>
                                        </small>
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
                                            <option value="S/I" selected >Dejar pendiente...</option>
                                            <option value="EN SERVICIO">En servicio</option>
                                            <option value="FUERA DE SERVICIO">Fuera de servicio</option>
                                            <option value="SOLO BASE">Solo base</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span id="iconoExclamacionMarca">
                                                    <i class="bi bi-exclamation-triangle-fill text-danger mx-1"></i>
                                                </span>
                                                Marca:
                                            </span>
                                            <input type="text" class="form-control" name="marca" placeholder="Ej. MUELLER" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span id="iconoExclamacionAnio">
                                                    <i class="bi bi-exclamation-triangle-fill text-danger mx-1"></i>
                                                </span>
                                                Año:
                                            </span>
                                            <input type="text" class="form-control" name="anio" placeholder="Año del modelo del hidrante" required>
                                        </div>
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
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span id="iconoExclamacionLlave_Hidrante">
                                                    <i class="bi bi-exclamation-triangle-fill text-warning mx-1"></i>
                                                </span>
                                                Llave Hidrante:
                                            </span>
                                            <select class="form-select" name="llave_hidrante" required>
                                                <option value="S/I" selected>Sin definir, dejar pendiente...</option>
                                                <option value="PENTAGONO">Pentágono</option>
                                                <option value="CUADRO">Cuadro</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span id="iconoExclamacionPresion_agua">
                                                    <i class="bi bi-exclamation-triangle-fill text-warning mx-1"></i>
                                                </span>
                                                Presión de Agua:
                                            </span>
                                            <select class="form-select" name="presion_agua" required>
                                                <option value="S/I" selected>Sin definir, dejar pendiente...</option>
                                                <option value="NULA">Nula</option>
                                                <option value="BAJA">Baja</option>
                                                <option value="REGULAR">Regular</option>
                                                <option value="ALTA">Alta</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span id="iconoExclamacionLlave_fosa">
                                                    <i class="bi bi-exclamation-triangle-fill text-warning mx-1"></i>
                                                </span>
                                                Llave Fosa:
                                            </span>
                                            <select class="form-select" name="llave_fosa" required>
                                                <option value="S/I" selected >Sin definir, dejar pendiente...</option>
                                                <option value="CUADRO">Cuadro</option>
                                                <option value="VOLANTE">Volante</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span id="iconoExclamacionHidrante_conectado_tubo">
                                                    <i class="bi bi-exclamation-triangle-fill text-warning mx-1"></i>
                                                </span>
                                                Conectado a Tubo de:
                                            </span>
                                            <select class="form-select" name="hidrante_conectado_tubo" required>
                                                <option value="S/I" selected >Sin definir, dejar pendiente...</option>
                                                <option value="4'">4'</option>
                                                <option value="6'">6'</option>
                                                <option value="8'">8'</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span id="iconoExclamacionUbicacion_fosa">
                                                    <i class="bi bi-exclamation-triangle-fill text-danger mx-1"></i>
                                                </span>
                                                Ubicación Fosa:
                                            </span>
                                            <input type="text" class="form-control" name="ubicacion_fosa" placeholder="Numero en metros o texto descriptivo" required>
                                        </div>
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
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <span id="iconoExclamacionOficial">
                                                    <i class="bi bi-exclamation-triangle-fill text-danger mx-1"></i>
                                                </span>
                                                Oficial:
                                            </span>
                                            <input type="text" class="form-control" name="oficial" placeholder="Nombre del oficial responsable de la inspección" required>
                                        </div>
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

.tipo-field {
    background-color: #f8f9fa !important;
    border-left: 0 !important;
    font-size: 0.85rem;
    color: #6c757d;
}

.manual-input {
    border-left: 3px solid #28a745;
}

.manual-input:focus {
    border-left-color: #20c997;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>

<script>
$(document).ready(function() {
    // --- CONFIGURACIÓN CENTRAL ---
    const CONFIG = {
        modalId: '#crearHidranteModal',
        formId: '#formCrearHidrante',
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
    function toggleExclamationIcon(iconId, value) {
        const icon = $(iconId);
        if (value === 'S/I' || value === '0' || value === '' || value === null) {
            icon.removeClass('d-none');
        } else {
            icon.addClass('d-none');
        }
    }

    // --- FUNCIÓN PARA MANEJAR SELECT2 + INPUT MANUAL ---
    function setupLocationField(selectId, manualId, tipoDisplayId, fieldType) {
        const $select = $(selectId);
        const $manual = $(manualId);
        const $tipoDisplay = $(tipoDisplayId);
        
        // Al seleccionar en Select2
        $select.on('select2:select', function(e) {
            const data = e.params.data;
            const selectedId = data.id;
            const selectedText = data.text;
            const tipo = $(this).find('option:selected').data('tipo');
            
            if (selectedId && selectedId !== '0') {
                // Deshabilitar input manual y limpiar su contenido
                $manual.prop('disabled', true).val('').addClass('input-disabled');
                
                // Mostrar tipo en el label
                $tipoDisplay.text(`(${tipo})`).removeClass('d-none');
                
                // Mostrar información en el contenedor pequeño
                const containerId = selectId.replace('#', '#') + '_selected_container';
                const tipoId = selectId.replace('#', '#') + '_selected_tipo';
                $(containerId).removeClass('d-none');
                $(tipoId).text(tipo + ' ' + selectedText);
            }
        });
        
        // Al limpiar Select2
        $select.on('select2:clear', function() {
            // Habilitar input manual y restaurar su valor anterior si lo tenía
            $manual.prop('disabled', false).removeClass('input-disabled');
            
            // Ocultar tipo en el label
            $tipoDisplay.addClass('d-none');
            
            // Ocultar información
            const containerId = selectId.replace('#', '#') + '_selected_container';
            $(containerId).addClass('d-none');
        });
        
        // Al escribir en input manual
        $manual.on('input', function() {
            const value = $(this).val().trim();
            if (value && !$select.val()) {
                // Si hay texto y no hay selección en Select2, es válido
                updateSaveButtonState();
            }
        });
        
        // Inicializar estado
        if (!$select.val()) {
            $manual.prop('disabled', false).removeClass('input-disabled');
        } else {
            $manual.prop('disabled', true).addClass('input-disabled');
        }
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
        // Verifica si el campo calle está cubierto
        let calleOk = false;
        const calleSelectVal = $('#id_calle').val();
        const calleManualVal = $('#calle_manual').val().trim();
        
        if ((calleSelectVal && calleSelectVal !== '') || calleManualVal) {
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
            let mensaje = 'Debe seleccionar una calle (o escribir manualmente) y definir el Estado del Hidrante.';
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
        const campos = [
            'marca', 'anio',
            'llave_hidrante', 'presion_agua', 'llave_fosa',
            'hidrante_conectado_tubo', 'ubicacion_fosa'
        ];
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
        // Iconos de advertencia
        setupIcons();

        // Select2
        initSelect2();

        // Configurar campos de ubicación
        setupLocationField('#id_calle', '#calle_manual', '#calle_tipo_display', 'calle');
        setupLocationField('#id_y_calle', '#y_calle_manual', '#y_calle_tipo_display', 'calle');
        setupLocationField('#id_colonia', '#colonia_manual', '#colonia_tipo_display', 'colonia');

        // Guardar: validación
        $(CONFIG.formId).on('submit', function(e) {
            // Validar calle (obligatoria)
            const calleSelectVal = $('#id_calle').val();
            const calleManualVal = $('#calle_manual').val().trim();
            
            if (!calleSelectVal && !calleManualVal) {
                e.preventDefault();
                alert('El campo Calle es obligatorio. Selecciona una opción o escribe manualmente.');
                return false;
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
        $('#id_calle, #calle_manual, select[name="estado_hidrante"]').on('change input', updateSaveButtonState);

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