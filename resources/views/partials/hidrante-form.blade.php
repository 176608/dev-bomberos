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
                                        <label class="form-label">
                                            <span id="edit_iconoExclamacion{{ $hidrante->id }}"><i class="bi bi-exclamation-diamond-fill text-warning"></i></span>
                                            Fecha tentativa de Mantenimiento
                                        </label>
                                        <div class="d-grid gap-2 mb-2 {{ $hidrante->fecha_tentativa ? 'd-none' : '' }}" id="edit_contenedorGenerarFecha{{ $hidrante->id }}">
                                            <button type="button" class="btn btn-primary" id="edit_btnGenerarFecha{{ $hidrante->id }}">
                                                Generar fecha tentativa
                                            </button>
                                        </div>
                                        <div class="btn-group w-100 mb-2 d-none" id="edit_opcionesPlazo{{ $hidrante->id }}">
                                            <button type="button" class="btn btn-outline-primary" data-plazo="corto">Corto plazo</button>
                                            <button type="button" class="btn btn-outline-primary" data-plazo="largo">Largo plazo</button>
                                            <button type="button" class="btn btn-outline-secondary" id="edit_btnRegresarGenerar{{ $hidrante->id }}">
                                                <i class="bi bi-arrow-left"></i>
                                            </button>
                                        </div>
                                        <div class="mb-2 {{ $hidrante->fecha_tentativa ? '' : 'd-none' }}" id="edit_contenedorFechaGenerada{{ $hidrante->id }}">
                                            <input type="date" class="form-control" name="fecha_tentativa" id="edit_fecha_tentativa{{ $hidrante->id }}"
                                                @if($hidrante->fecha_tentativa)
                                                    value="{{ $hidrante->fecha_tentativa instanceof \Carbon\Carbon ? $hidrante->fecha_tentativa->format('Y-m-d') : $hidrante->fecha_tentativa }}"
                                                @endif
                                            >
                                            <button type="button" class="btn btn-outline-secondary mt-2 btn-sm" id="edit_btnResetFecha{{ $hidrante->id }}">
                                                <i class="bi bi-arrow-left"></i> Cambiar plazo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="row">
                                    <div class="col-md-6 mb-3 offset-md-3">
                                        <label class="form-label">Número de Estación
                                            <span id="edit_iconoExclamacionNEstacion{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="numero_estacion">
                                            @foreach(['S/I', '01', '02', '03', '04', '05', '06', '07', '08', '09'] as $num)
                                                <option value="{{ $num }}" {{ $hidrante->numero_estacion == $num ? 'selected' : '' }}>{{ $num }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
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
                                    <!-- Calle Principal -->
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
                                        <small class="form-text text-muted">Calle guardada: <span id="calle_actual">{{ $hidrante->calle ?: 'Sin definir' }}</span></small>
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
                                        <small class="form-text text-muted">y Calle guardada: <span id="y_calle_actual">{{ $hidrante->y_calle ?: 'Sin definir' }}</span></small>
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
                                        <small class="form-text text-muted">Colonia guardada: <span id="colonia_actual">{{ $hidrante->colonia ?: 'Sin definir' }}</span></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Tercera Sección - Características Técnicas -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-primary text-white">
                                Características Técnicas
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Llave Hidrante
                                            <span id="edit_iconoExclamacionLlaveHi{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="llave_hidrante">
                                            <option value="S/I" {{ $hidrante->llave_hidrante == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                            <option value="Pentagono" {{ $hidrante->llave_hidrante == 'Pentagono' ? 'selected' : '' }}>Pentagono</option>
                                            <option value="Cuadro" {{ $hidrante->llave_hidrante == 'Cuadro' ? 'selected' : '' }}>Cuadro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Presión de Agua
                                            <span id="edit_iconoExclamacionPresionA{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="presion_agua">
                                            <option value="S/I" {{ $hidrante->presion_agua == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                            <option value="Mala" {{ $hidrante->presion_agua == 'Mala' ? 'selected' : '' }}>Mala</option>
                                            <option value="Buena" {{ $hidrante->presion_agua == 'Buena' ? 'selected' : '' }}>Buena</option>
                                            <option value="Sin agua" {{ $hidrante->presion_agua == 'Sin agua' ? 'selected' : '' }}>Sin agua</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Llave Fosa
                                            <span id="edit_iconoExclamacionLlaveFosa{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="llave_fosa">
                                            <option value="S/I" {{ $hidrante->llave_fosa == 'S/I' ? 'selected' : '' }}>Información Pendiente</option>
                                            <option value="Cuadro" {{ $hidrante->llave_fosa == 'Cuadro' ? 'selected' : '' }}>Cuadro</option>
                                            <option value="Volante" {{ $hidrante->llave_fosa == 'Volante' ? 'selected' : '' }}>Volante</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Conectado a Tubo de
                                            <span id="edit_iconoExclamacionHCT{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
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
                                            <span id="edit_iconoExclamacionUbiFosa{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        </label>
                                        <input type="text" class="form-control" name="ubicacion_fosa" required
                                               value="{{ $hidrante->ubicacion_fosa ?? '' }}" placeholder="Sin Definir">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Cuarta Sección - Estado y Características -->
                    <div class="row mb-4">
                        <div class="card text-center p-0">
                            <div class="card-header bg-success text-white">
                                Estado y Características
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                        Marca:
                                        <span id="edit_iconoExclamacionMarca{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        </label>
                                        <input type="text" class="form-control" name="marca"
                                               value="{{ $hidrante->marca ?? '' }}" placeholder="Sin Definir">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                        Año:
                                        <span id="edit_iconoExclamacionYY{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        </label>
                                        <input type="number" class="form-control" name="anio"
                                               value="{{ $hidrante->anio ?? '' }}" placeholder="Sin Definir">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Estado Hidrante
                                            <span id="edit_iconoExclamacionEstadoH{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="estado_hidrante">
                                            <option value="S/I" {{ $hidrante->estado_hidrante == 'S/I' ? 'selected' : '' }}> Pendiente</option>
                                            <option value="Servicio" {{ $hidrante->estado_hidrante == 'Servicio' ? 'selected' : '' }}>Servicio</option>
                                            <option value="Fuera de servicio" {{ $hidrante->estado_hidrante == 'Fuera de servicio' ? 'selected' : '' }}>Fuera de servicio</option>
                                            <option value="Solo Base" {{ $hidrante->estado_hidrante == 'Solo Base' ? 'selected' : '' }}>Solo Base</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Color
                                            <span id="edit_iconoExclamacionColor{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-warning"></i></span>
                                        </label>
                                        <select class="form-select" name="color">
                                            <option value="S/I" {{ $hidrante->color == 'S/I' ? 'selected' : '' }}> Pendiente</option>
                                            <option value="Rojo" {{ $hidrante->color == 'Rojo' ? 'selected' : '' }}>Rojo</option>
                                            <option value="Amarillo" {{ $hidrante->color == 'Amarillo' ? 'selected' : '' }}>Amarillo</option>
                                            <option value="Otro" {{ $hidrante->color == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
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
                                               value="{{ $hidrante->oficial ?? '' }}" placeholder="Sin Definir">
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
                        data-bs-content="Falta generar una fecha tentativa de mantenimiento.">
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
            { name: 'numero_estacion', icon: 'edit_iconoExclamacionNEstacion', tipo: 'select' },
            { name: 'llave_hidrante', icon: 'edit_iconoExclamacionLlaveHi', tipo: 'select' },
            { name: 'presion_agua', icon: 'edit_iconoExclamacionPresionA', tipo: 'select' },
            { name: 'llave_fosa', icon: 'edit_iconoExclamacionLlaveFosa', tipo: 'select' },
            { name: 'hidrante_conectado_tubo', icon: 'edit_iconoExclamacionHCT', tipo: 'select' },
            { name: 'estado_hidrante', icon: 'edit_iconoExclamacionEstadoH', tipo: 'select' },
            { name: 'color', icon: 'edit_iconoExclamacionColor', tipo: 'select' },
            { name: 'marca', icon: 'edit_iconoExclamacionMarca', tipo: 'input' },
            { name: 'anio', icon: 'edit_iconoExclamacionYY', tipo: 'input' },
            { name: 'oficial', icon: 'edit_iconoExclamacionOficial', tipo: 'input' },
            { name: 'ubicacion_fosa', icon: 'edit_iconoExclamacionUbiFosa', tipo: 'input' },
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
    let fechaTentativaGenerada = !!$('#edit_fecha_tentativa' + CONFIG.hidranteId).val();

    function updateSaveButtonState() {
        // Verifica si la fecha tentativa ya fue generada
        let fechaOk = fechaTentativaGenerada;
        // Verifica si el campo calle está cubierto (valor válido o switch activo)
        let calleOk = false;
        if ($('#edit_switchNoCalle' + CONFIG.hidranteId).is(':checked')) {
            calleOk = true;
        } else if ($('#edit_id_calle').val() && $('#edit_id_calle').val() !== '' && $('#edit_id_calle').val() !== null) {
            calleOk = true;
        }
        if (fechaOk && calleOk) {
            $('#edit_btnGuardarHidrante' + CONFIG.hidranteId).prop('disabled', false);
            $('#edit_popoverGuardarHidrante' + CONFIG.hidranteId).removeAttr('data-bs-toggle data-bs-trigger data-bs-content');
            if (bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante' + CONFIG.hidranteId))) {
                bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante' + CONFIG.hidranteId)).dispose();
            }
        } else {
            $('#edit_btnGuardarHidrante' + CONFIG.hidranteId).prop('disabled', true);
            $('#edit_popoverGuardarHidrante' + CONFIG.hidranteId)
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', 'Debe generar la fecha tentativa y seleccionar una calle.');
            if (typeof edit_initPopover === 'function') edit_initPopover();
        }
    }

    // --- FECHA TENTATIVA: FLUJO DE PASOS ---
    function mostrarPasoGenerar() {
        $('#edit_contenedorGenerarFecha' + CONFIG.hidranteId).removeClass('d-none');
        $('#edit_opcionesPlazo' + CONFIG.hidranteId).addClass('d-none');
        $('#edit_contenedorFechaGenerada' + CONFIG.hidranteId).addClass('d-none');
        $('#edit_iconoExclamacion' + CONFIG.hidranteId).removeClass('d-none');
        fechaTentativaGenerada = false;
        updateSaveButtonState();
    }
    function mostrarPasoPlazo() {
        $('#edit_contenedorGenerarFecha' + CONFIG.hidranteId).addClass('d-none');
        $('#edit_opcionesPlazo' + CONFIG.hidranteId).removeClass('d-none');
        $('#edit_contenedorFechaGenerada' + CONFIG.hidranteId).addClass('d-none');
        $('#edit_iconoExclamacion' + CONFIG.hidranteId).removeClass('d-none');
        fechaTentativaGenerada = false;
        updateSaveButtonState();
    }
    function mostrarPasoFechaGenerada() {
        $('#edit_contenedorGenerarFecha' + CONFIG.hidranteId).addClass('d-none');
        $('#edit_opcionesPlazo' + CONFIG.hidranteId).addClass('d-none');
        $('#edit_contenedorFechaGenerada' + CONFIG.hidranteId).removeClass('d-none');
        $('#edit_iconoExclamacion' + CONFIG.hidranteId).addClass('d-none');
        fechaTentativaGenerada = true;
        updateSaveButtonState();
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

        // Fecha tentativa: flujo de pasos
        $('#edit_btnGenerarFecha' + CONFIG.hidranteId).click(mostrarPasoPlazo);
        $('#edit_opcionesPlazo' + CONFIG.hidranteId + ' button[data-plazo]').click(function() {
            const plazo = $(this).data('plazo');
            const fechaBase = new Date();
            if (plazo === 'corto') {
                fechaBase.setMonth(fechaBase.getMonth() + 6);
            } else {
                fechaBase.setFullYear(fechaBase.getFullYear() + 1);
            }
            $('#edit_fecha_tentativa' + CONFIG.hidranteId).val(fechaBase.toISOString().split('T')[0]);
            mostrarPasoFechaGenerada();
        });
        $('#edit_btnRegresarGenerar' + CONFIG.hidranteId).click(mostrarPasoGenerar);
        $('#edit_btnResetFecha' + CONFIG.hidranteId).click(mostrarPasoPlazo);

        // Guardar: validación
        $('form').on('submit', function(e) {
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

    // --- INICIALIZACIÓN DEL MODAL ---
    $(CONFIG.modalId)
        .on('shown.bs.modal', function() {
            initEventHandlers();
            autoActivarSwitchUbicacion();
            // Flujo de fecha tentativa
            const fechaTentativaVal = $('#edit_fecha_tentativa' + CONFIG.hidranteId).val();
            if (fechaTentativaVal) {
                mostrarPasoFechaGenerada();
                $('#edit_fecha_tentativa' + CONFIG.hidranteId).val(fechaTentativaVal);
            } else {
                mostrarPasoGenerar();
            }
            setTimeout(edit_initPopover, 200);
        })
        .on('hidden.bs.modal', function() {
            $('.select2-search').select2('destroy');
        });

    // Inicialización directa si el modal ya está abierto
    if ($(CONFIG.modalId).is(':visible')) {
        initEventHandlers();
        autoActivarSwitchUbicacion();
    }
});
</script>
