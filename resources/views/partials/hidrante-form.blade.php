<div class="modal fade modal-edit" id="editarHidranteModal{{ $hidrante->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('hidrantes.update', $hidrante->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Hidrante #{{ $hidrante->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: rgba(201, 201, 201, 0.8);">
                    <!-- Primera Sección - Información Básica -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-primary text-white">
                                Información Básica
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de Inspección</label>
                                        <input type="date" class="form-control" name="fecha_inspeccion" 
                                               id="edit_fecha_inspeccion"
                                               value="{{ $hidrante->fecha_inspeccion ? date('Y-m-d', strtotime($hidrante->fecha_inspeccion)) : date('Y-m-d') }}" 
                                               required>
                                        <small class="form-text text-muted">Formato: DD-MM-YYYY</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Número de Estación
                                            <span id="edit_iconoExclamacionNumero_estacion{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="numero_estacion">
                                            @foreach(['S/I', '01', '02', '03', '04', '05', '06', '07', '08', '09'] as $num)
                                                <option value="{{ $num }}" {{ $hidrante->numero_estacion == $num ? 'selected' : '' }}>{{ $num }}</option>
                                            @endforeach
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

                    <!-- Segunda Sección - Ubicación -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-success text-white d-flex justify-content-center align-items-center">
                                <span class="text-center w-100">Ubicación</span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Calle -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Calle
                                            <span id="edit_iconoExclamacionCalle{{ $hidrante->id }}">
                                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                            </span>
                                        </label>
                                        <div class="input-group justify-content-center">
                                            <select class="form-select select2-search" name="id_calle" id="edit_id_calle">
                                                <option value="">Buscar nueva calle ...</option>
                                                @foreach($calles as $calle)
                                                    <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                        {{ $calle->Nomvial }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-text bg-white border-0">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="edit_switchNoCalle{{ $hidrante->id }}">
                                                    <label class="form-check-label small ms-2" for="edit_switchNoCalle{{ $hidrante->id }}">No aparece la calle</label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Calle guardada: <span id="calle_actual">{{ $hidrante->calle ?: 'N/A' }}</span></small>
                                    </div>
                                    <!-- Y Calle -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Y Calle
                                        </label>
                                        <div class="input-group justify-content-center">
                                            <select class="form-select select2-search" name="id_y_calle" id="edit_id_y_calle">
                                                <option value="">Buscar nueva calle ...</option>
                                                @foreach($calles as $calle)
                                                    <option value="{{ $calle->IDKEY }}" {{ $hidrante->id_y_calle == $calle->IDKEY ? 'selected' : '' }}>
                                                        {{ $calle->Nomvial }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-text bg-white border-0">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="edit_switchNoYCalle{{ $hidrante->id }}">
                                                    <label class="form-check-label small ms-2" for="edit_switchNoYCalle{{ $hidrante->id }}">No aparece la calle</label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">y Calle guardada: <span id="y_calle_actual">{{ $hidrante->y_calle ?: 'N/A' }}</span></small>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <!-- Colonia -->
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">
                                            Colonia
                                        </label>
                                        <div class="input-group justify-content-center">
                                            <select class="form-select select2-search" name="id_colonia" id="edit_id_colonia">
                                                <option value="">Buscar nueva colonia...</option>
                                                @foreach($colonias as $colonia)
                                                    <option value="{{ $colonia->IDKEY }}" {{ $hidrante->id_colonia == $colonia->IDKEY ? 'selected' : '' }}>
                                                        {{ $colonia->NOMBRE }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-text bg-white border-0">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="edit_switchNoColonia{{ $hidrante->id }}">
                                                    <label class="form-check-label small ms-2" for="edit_switchNoColonia{{ $hidrante->id }}">No aparece la colonia</label>
                                                </div>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Colonia guardada: <span id="colonia_actual">{{ $hidrante->colonia ?: 'N/A' }}</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Tercera Sección - Estado y Características -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-primary text-white">
                                Estado y Características
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3 offset-md-3">
                                        <label class="form-label">
                                            Estado Hidrante
                                            <span id="edit_iconoExclamacionEstado_Hidrante{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="estado_hidrante">
                                            <option value="S/I" {{ $hidrante->estado_hidrante == 'S/I' ? 'selected' : '' }}> Pendiente</option>
                                            <option value="EN SERVICIO" {{ $hidrante->estado_hidrante == 'EN SERVICIO' ? 'selected' : '' }}>En servicio</option>
                                            <option value="FUERA DE SERVICIO" {{ $hidrante->estado_hidrante == 'FUERA DE SERVICIO' ? 'selected' : '' }}>Fuera de servicio</option>
                                            <option value="SOLO BASE" {{ $hidrante->estado_hidrante == 'SOLO BASE' ? 'selected' : '' }}>Solo Base</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                        Marca:
                                        <span id="edit_iconoExclamacionMarca{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        </label>
                                        <input type="text" class="form-control" name="marca"
                                               value="{{ $hidrante->marca ?? '' }}" placeholder="N/A">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                        Año:
                                        <span id="edit_iconoExclamacionAnio{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        </label>
                                        <input type="text" class="form-control" name="anio"
                                               value="{{ $hidrante->anio ?? '' }}" placeholder="N/A">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Cuarta Sección - Características Técnicas -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-success text-white">
                                Características Técnicas
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Llave Hidrante
                                            <span id="edit_iconoExclamacionLlave_Hidrante{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="llave_hidrante">
                                            <option value="S/I" {{ $hidrante->llave_hidrante == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                            <option value="PENTAGONO" {{ $hidrante->llave_hidrante == 'PENTAGONO' ? 'selected' : '' }}>Pentagono</option>
                                            <option value="CUADRO" {{ $hidrante->llave_hidrante == 'CUADRO' ? 'selected' : '' }}>Cuadro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Presión de Agua
                                            <span id="edit_iconoExclamacionPresion_agua{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="presion_agua">
                                            <option value="S/I" {{ $hidrante->presion_agua == 'S/I' ? 'selected' : '' }}>Sin el dato</option>
                                            <option value="NULA" {{ $hidrante->presion_agua == 'NULA' ? 'selected' : '' }}>Nula</option>
                                            <option value="BAJA" {{ $hidrante->presion_agua == 'BAJA' ? 'selected' : '' }}>Baja</option>
                                            <option value="REGULAR" {{ $hidrante->presion_agua == 'REGULAR' ? 'selected' : '' }}>Regular</option>
                                            <option value="ALTA" {{ $hidrante->presion_agua == 'ALTA' ? 'selected' : '' }}>Alta</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Llave Fosa
                                            <span id="edit_iconoExclamacionLlave_Fosa{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="llave_fosa">
                                            <option value="S/I" {{ $hidrante->llave_fosa == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                            <option value="CUADRO" {{ $hidrante->llave_fosa == 'CUADRO' ? 'selected' : '' }}>Cuadro</option>
                                            <option value="VOLANTE" {{ $hidrante->llave_fosa == 'VOLANTE' ? 'selected' : '' }}>Volante</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Conectado a Tubo de
                                            <span id="edit_iconoExclamacionHidrante_conectado_tubo{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="hidrante_conectado_tubo">
                                            <option value="S/I" {{ $hidrante->hidrante_conectado_tubo == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                            <option value="4'" {{ $hidrante->hidrante_conectado_tubo == "4'" ? 'selected' : '' }}>4'</option>
                                            <option value="6'" {{ $hidrante->hidrante_conectado_tubo == "6'" ? 'selected' : '' }}>6'</option>
                                            <option value="8'" {{ $hidrante->hidrante_conectado_tubo == "8'" ? 'selected' : '' }}>8'</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 mb-3">
                                        <label class="form-label">
                                            Ubicación Fosa (N MTS.)
                                            <span id="edit_iconoExclamacionUbicacion_fosa{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        </label>
                                        <input type="text" class="form-control" name="ubicacion_fosa" required
                                               value="{{ $hidrante->ubicacion_fosa ?? '' }}" placeholder="N/A">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Quinta Sección - Información Adicional -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-secondary text-white">
                                Información Adicional
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Observaciones</label>
                                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Sin observaciones">{{ $hidrante->observaciones ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 mb-3 offset-md-2">
                                        <label class="form-label">
                                            Oficial:
                                            <span id="edit_iconoExclamacionOficial{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        </label>
                                        <input type="text" class="form-control" name="oficial"
                                               value="{{ $hidrante->oficial ?? '' }}" placeholder="N/A">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <span class="d-inline-block" tabindex="0" id="edit_popoverGuardarHidrante{{ $hidrante->id }}"
                        data-bs-toggle="popover"
                        data-bs-trigger="hover focus"
                        data-bs-placement="top"
                        title="¡Atención!"
                        data-bs-content="Debe seleccionar una calle (o marcar como pendiente) y definir el Estado del Hidrante.">
                        <button type="submit" class="btn btn-danger" id="edit_btnGuardarHidrante{{ $hidrante->id }}" disabled>
                            Guardar Cambios
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

.select2-container--bootstrap-5.select2-container--open {
    z-index: 1060 !important;
}

.select2-container--bootstrap-5 .select2-dropdown {
    border-color: #dee2e6;
    border-radius: 0.375rem;
}

.select2-container--bootstrap-5 .select2-dropdown--below {
    margin-top: 2px;
}

select2.select2-container {
    width: 100% !important;
}

.modal-body .select2-container {
    display: block;
}

/* Personaliza el fondo y texto del título del popover */
.popover-header {
    background-color: #dc3545 !important; /* Rojo Bootstrap */
    color: #fff !important;               /* Letras blancas */
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid #fff;
}

/* Personaliza el contenido del popover */
.popover-body {
    color: #212529 !important;            /* Texto oscuro */
    font-size: 1rem;
    text-align: center;
}

/* Opcional: cambia el borde del popover */
.popover {
    border: 2px solid #dc3545;
}
</style>

<script>
$(document).ready(function() {
    // --- CONFIGURACIÓN CENTRAL ---
    const CONFIG = {
        hidranteId: "{{ $hidrante->id }}",
        modalId: '#editarHidranteModal{{ $hidrante->id }}',
        switches: [
            {
                field: 'calle',
                switchId: '#edit_switchNoCalle',
                selectId: '#edit_id_calle',
                iconId: '#edit_iconoExclamacionCalle',
                hidden: ['id_calle', 'calle']
            },
            {
                field: 'y_calle',
                switchId: '#edit_switchNoYCalle',
                selectId: '#edit_id_y_calle',
                iconId: '#edit_iconoExclamacionYCalle',
                hidden: ['id_y_calle', 'y_calle']
            },
            {
                field: 'colonia',
                switchId: '#edit_switchNoColonia',
                selectId: '#edit_id_colonia',
                iconId: '#edit_iconoExclamacionColonia',
                hidden: ['id_colonia', 'colonia']
            }
        ],
        fieldsWithIcons: [
            { name: 'numero_estacion', icon: 'edit_iconoExclamacionNumero_estacion', tipo: 'select' },
            { name: 'llave_hidrante', icon: 'edit_iconoExclamacionLlave_Hidrante', tipo: 'select' },
            { name: 'presion_agua', icon: 'edit_iconoExclamacionPresion_agua', tipo: 'select' },
            { name: 'llave_fosa', icon: 'edit_iconoExclamacionLlave_Fosa', tipo: 'select' },
            { name: 'hidrante_conectado_tubo', icon: 'edit_iconoExclamacionHidrante_conectado_tubo', tipo: 'select' },
            { name: 'estado_hidrante', icon: 'edit_iconoExclamacionEstado_Hidrante', tipo: 'select' },
            { name: 'marca', icon: 'edit_iconoExclamacionMarca', tipo: 'input' },
            { name: 'anio', icon: 'edit_iconoExclamacionAnio', tipo: 'input' },
            { name: 'oficial', icon: 'edit_iconoExclamacionOficial', tipo: 'input' },
            { name: 'ubicacion_fosa', icon: 'edit_iconoExclamacionUbicacion_fosa', tipo: 'input' },
            { name: 'id_calle', icon: 'edit_iconoExclamacionCalle', tipo: 'select' }
        ]
    };

    // --- FUNCIÓN PARA ICONOS ---
    function toggleExclamationIcon(iconId, value) {
        const icon = $(`${iconId}${CONFIG.hidranteId}`);
        if (value === 'S/I' || value === '0' || value === '' || value === null) {
            icon.removeClass('d-none');
        } else {
            icon.addClass('d-none');
        }
    }

    // --- FUNCIÓN PARA SWITCHES ---
    function setupSwitchHandler({switchId, selectId, iconId, hidden}) {
        $(`${switchId}${CONFIG.hidranteId}`).change(function() {
            const $select = $(selectId);
            const $icon = $(`${iconId}${CONFIG.hidranteId}`);
            if ($(this).is(':checked')) {
                $select.prop('disabled', true).addClass('input-disabled').val('0').trigger('change');
                $icon.addClass('d-none');
                hidden.forEach(name => {
                    if (!$(`input[name="${name}"][type="hidden"]`).length) {
                        $('<input>').attr({type: 'hidden', name: name, value: name.startsWith('id_') ? '0' : 'Pendiente'}).appendTo('form');
                    }
                });
            } else {
                $select.prop('disabled', false).removeClass('input-disabled').val('').trigger('change');
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
        });
    }

    // --- ICONOS DE ADVERTENCIA ---
    function setupIcons() {
        // Detecta el valor actual de estado_hidrante al cargar
        const estado = $('select[name="estado_hidrante"]').val();
        const esSoloBase = estado === 'SOLO BASE';

        CONFIG.fieldsWithIcons.forEach(({ name, icon, tipo }) => {
            const iconSelector = `#${icon}${CONFIG.hidranteId}`;
            if (esSoloBase) {
                // Oculta todos los iconos pertinentes si es SOLO BASE
                $(iconSelector).addClass('d-none');
            } else {
                // Lógica normal: muestra/oculta según el valor del campo
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
            }
        });
    }

    // --- AUTOACTIVAR SWITCHES DE UBICACIÓN SI VALOR ES 0/Pendiente ---
    function autoActivarSwitchUbicacion() {
        CONFIG.switches.forEach(({switchId, selectId}) => {
            const $switch = $(`${switchId}${CONFIG.hidranteId}`);
            const $select = $(selectId);
            const $actual = $(`#${$select.attr('name').replace('id_', '')}_actual`);
            if ($select.val() === '0' || ($actual.length && $actual.text().trim() === 'Pendiente')) {
                $switch.prop('checked', true).trigger('change');
            }
        });
    }

    // --- BOTÓN GUARDAR Y POPOVER ---
    function updateSaveButtonState() {
        // Verifica si el campo calle está cubierto (valor válido o switch activo)
        let calleOk = false;
        if ($('#edit_switchNoCalle' + CONFIG.hidranteId).is(':checked')) {
            calleOk = true;
        } else if ($('#edit_id_calle').val() && $('#edit_id_calle').val() !== '' && $('#edit_id_calle').val() !== null) {
            calleOk = true;
        }
        // Verifica si el estado de hidrante está definido (no es 'S/I')
        let estadoOk = false;
        const estadoVal = $(`select[name="estado_hidrante"]`).val();
        if (estadoVal && estadoVal !== 'S/I') {
            estadoOk = true;
        }

        if (calleOk && estadoOk) {
            $('#edit_btnGuardarHidrante' + CONFIG.hidranteId).prop('disabled', false);
            $('#edit_popoverGuardarHidrante' + CONFIG.hidranteId).removeAttr('data-bs-toggle data-bs-trigger data-bs-content');
            if (bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante' + CONFIG.hidranteId))) {
                bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante' + CONFIG.hidranteId)).dispose();
            }
        } else {
            $('#edit_btnGuardarHidrante' + CONFIG.hidranteId).prop('disabled', true);
            let mensaje = 'Debe seleccionar una calle (o marcar como pendiente) y definir el Estado del Hidrante.';
            $('#edit_popoverGuardarHidrante' + CONFIG.hidranteId)
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', mensaje);
            if (typeof edit_initPopover === 'function') edit_initPopover();
        }
    }

    // --- POPOVER ---
    function edit_initPopover() {
        const popoverTrigger = document.getElementById('edit_popoverGuardarHidrante' + CONFIG.hidranteId);
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

        // Guardar: validación
        $('form').on('submit', function(e) {
            // Limpia todos los inputs ocultos de ubicación antes de agregar los necesarios
            $('input[type="hidden"][name="id_calle"], input[type="hidden"][name="calle"], input[type="hidden"][name="id_y_calle"], input[type="hidden"][name="y_calle"], input[type="hidden"][name="id_colonia"], input[type="hidden"][name="colonia"]').remove();

            if ($('#edit_switchNoCalle' + CONFIG.hidranteId).is(':checked')) {
                $('#edit_id_calle').prop('disabled', true).val('0');
                if (!$('input[name="id_calle"][type="hidden"]').length) {
                    $('<input>').attr({type: 'hidden', name: 'id_calle', value: '0'}).appendTo(this);
                }
                if (!$('input[name="calle"][type="hidden"]').length) {
                    $('<input>').attr({type: 'hidden', name: 'calle', value: 'Pendiente'}).appendTo(this);
                }
            } else {
                const val = $('#edit_id_calle').val();
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

        // Actualizar botón guardar cuando cambian campos clave
        $('#edit_id_calle, #edit_switchNoCalle' + CONFIG.hidranteId).on('change', updateSaveButtonState);
    }

    // --- SOLO BASE: LÓGICA DE BLOQUEO Y LIMPIEZA ---
    function handleSoloBaseState(isSoloBase) {
        // Campos de Estado y Características + Características Técnicas
        const fields = [
            'marca', 'anio',
            'llave_hidrante', 'presion_agua', 'llave_fosa',
            'hidrante_conectado_tubo', 'ubicacion_fosa'
        ];
        // Iconos de exclamación de esas secciones
        const iconos = [
            '#edit_iconoExclamacionMarca',
            '#edit_iconoExclamacionAnio',
            '#edit_iconoExclamacionLlave_Hidrante',
            '#edit_iconoExclamacionPresion_agua',
            '#edit_iconoExclamacionLlave_Fosa',
            '#edit_iconoExclamacionHidrante_conectado_tubo',
            '#edit_iconoExclamacionUbicacion_fosa'
        ];
        fields.forEach(function(name) {
            const $input = $(`[name="${name}"]`);
            if (isSoloBase) {
                if ($input.is('select')) {
                    $input.val('S/I').trigger('change');
                } else {
                    if (name === 'marca' || name === 'ubicacion_fosa') $input.val('S/I');
                    if (name === 'anio') $input.val('S/I'); // Cambiado de '0' a 'S/I'
                }
            } else {
            }
        });
        // Iconos
        iconos.forEach(function(sel) {
            if (isSoloBase) {
                $(`${sel}${CONFIG.hidranteId}`).addClass('d-none');
            } else {
                // Se reevalúa el icono según la lógica normal
                const field = sel.replace('#edit_iconoExclamacion', '').replace(CONFIG.hidranteId, '').toLowerCase();
                const $input = $(`[name="${field}"]`);
                if ($input.length) {
                    if ($input.is('select')) {
                        toggleExclamationIcon(sel, $input.val());
                    } else {
                        toggleExclamationIcon(sel, $input.val());
                    }
                }
            }
        });
    }

    // --- EVENTO DE CAMBIO EN ESTADO HIDRANTE ---
    $(`select[name="estado_hidrante"]`).on('change', function() {
        const isSoloBase = $(this).val() === 'SOLO BASE';
        handleSoloBaseState(isSoloBase);
    });

    // --- Al abrir el modal, aplicar si ya está en SOLO BASE ---
    $(CONFIG.modalId).on('shown.bs.modal', function() {
        const isSoloBase = $(`select[name="estado_hidrante"]`).val() === 'SOLO BASE';
        handleSoloBaseState(isSoloBase);
    });

    // --- INICIALIZACIÓN DEL MODAL ---
    $(CONFIG.modalId)
        .on('shown.bs.modal', function() {
            initEventHandlers();
            autoActivarSwitchUbicacion();
            setupIcons();
            updateSaveButtonState(); // <-- AQUI
            setTimeout(edit_initPopover, 200);
        })
        .on('hidden.bs.modal', function() {
            $('.select2-search').select2('destroy');
            // Redirige al cerrar/cancelar el modal
            //window.location = window.location.pathname + '?mostrar_tabla=1';
            recargarSoloTabla();
        });

    // Inicialización directa si el modal ya está abierto
    if ($(CONFIG.modalId).is(':visible')) {
        initEventHandlers();
        autoActivarSwitchUbicacion();
        setupIcons();
        updateSaveButtonState(); // <-- AQUI
    }
});
</script>
