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
                                            <span id="edit_iconoExclamacionNEstacion{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
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
                                            <span id="edit_iconoExclamacionCalle{{ $hidrante->id }}" class="d-none">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                            </span>
                                        </label>
                                        <div class="input-group justify-content-center">
                                            <select class="form-select select2-search" name="id_calle" id="edit_id_calle">
                                                <option value="">Buscar nueva calle principal...</option>
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
                                            <span id="edit_iconoExclamacionYCalle{{ $hidrante->id }}" class="d-none">
                                                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                            </span>
                                        </label>
                                        <div class="input-group justify-content-center">
                                            <select class="form-select select2-search" name="id_y_calle" id="edit_id_y_calle">
                                                <option value="">Buscar nueva calle secundaria...</option>
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
                                            <span id="edit_iconoExclamacionColonia{{ $hidrante->id }}" class="d-none">
                                                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                            </span>
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
                                        Marca
                                        <span id="edit_iconoExclamacionMarca{{ $hidrante->id }}"><i class="bi bi-exclamation-triangle-fill text-danger"></i></span>
                                        </label>
                                        <input type="text" class="form-control" name="marca"
                                               value="{{ $hidrante->marca ?? '' }}" placeholder="Sin Definir">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                        Año
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
                                            Oficial
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
    const MODAL_ID = '#editarHidranteModal{{ $hidrante->id }}';

    // --- ICONOS DE EXCLAMACIÓN Y VALIDACIÓN ---
    // Solo para y_calle y colonia: icono solo si valor es "Pendiente" (id=0)
    function actualizarIconoPendienteUbicacion() {
        // Y Calle
        if ($('#edit_id_y_calle').val() === '0') {
            $('#edit_iconoExclamacionYCalle{{ $hidrante->id }}').removeClass('d-none');
        } else {
            $('#edit_iconoExclamacionYCalle{{ $hidrante->id }}').addClass('d-none');
        }
        // Colonia
        if ($('#edit_id_colonia').val() === '0') {
            $('#edit_iconoExclamacionColonia{{ $hidrante->id }}').removeClass('d-none');
        } else {
            $('#edit_iconoExclamacionColonia{{ $hidrante->id }}').addClass('d-none');
        }
    }

    $('#edit_id_y_calle, #edit_id_colonia').on('change', actualizarIconoPendienteUbicacion);

    // Estado inicial al abrir modal
    actualizarIconoPendienteUbicacion();

    // --- SWITCHES DE UBICACIÓN INDIVIDUAL ---
    $('#edit_switchNoYCalle{{ $hidrante->id }}').change(function() {
        if ($(this).is(':checked')) {
            $('#edit_id_y_calle').prop('disabled', true).addClass('input-disabled').val('0').trigger('change');
            if (!$('input[name="id_y_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_y_calle', value: '0'}).appendTo('form');
            }
            if (!$('input[name="y_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'y_calle', value: 'Pendiente'}).appendTo('form');
            }
        } else {
            $('#edit_id_y_calle').prop('disabled', false).removeClass('input-disabled');
            if (!$('#edit_id_y_calle').val()) {
                $('#edit_iconoExclamacionYCalle{{ $hidrante->id }}').addClass('d-none');
            }
            $('input[name="id_y_calle"][type="hidden"]').remove();
            $('input[name="y_calle"][type="hidden"]').remove();
            $('#edit_id_y_calle').val('').trigger('change');
        }
        actualizarIconoPendienteUbicacion();
    });

    $('#edit_switchNoColonia{{ $hidrante->id }}').change(function() {
        if ($(this).is(':checked')) {
            $('#edit_id_colonia').prop('disabled', true).addClass('input-disabled').val('0').trigger('change');
            if (!$('input[name="id_colonia"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_colonia', value: '0'}).appendTo('form');
            }
            if (!$('input[name="colonia"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'colonia', value: 'Pendiente'}).appendTo('form');
            }
        } else {
            $('#edit_id_colonia').prop('disabled', false).removeClass('input-disabled');
            if (!$('#edit_id_colonia').val()) {
                $('#edit_iconoExclamacionColonia{{ $hidrante->id }}').addClass('d-none');
            }
            $('input[name="id_colonia"][type="hidden"]').remove();
            $('input[name="colonia"][type="hidden"]').remove();
            $('#edit_id_colonia').val('').trigger('change');
        }
        actualizarIconoPendienteUbicacion();
    });

    // Calle (principal): icono y switch como antes (no cambia)
    $('#edit_switchNoCalle{{ $hidrante->id }}').change(function() {
        if ($(this).is(':checked')) {
            $('#edit_id_calle').prop('disabled', true).addClass('input-disabled').val('0').trigger('change');
            $('#edit_iconoExclamacionCalle{{ $hidrante->id }}').removeClass('d-none');
            if (!$('input[name="id_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_calle', value: '0'}).appendTo('form');
            }
            if (!$('input[name="calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'calle', value: 'Pendiente'}).appendTo('form');
            }
        } else {
            $('#edit_id_calle').prop('disabled', false).removeClass('input-disabled');
            if (!$('#edit_id_calle').val()) {
                $('#edit_iconoExclamacionCalle{{ $hidrante->id }}').removeClass('d-none');
            }
            $('input[name="id_calle"][type="hidden"]').remove();
            $('input[name="calle"][type="hidden"]').remove();
            $('#edit_id_calle').val('').trigger('change');
        }
    });

    // Estado inicial de switches de ubicación
    function autoActivarSwitchUbicacion() {
        if ($('#edit_id_calle').val() === '0' || $('#calle_actual').text().trim() === 'Pendiente') {
            $('#edit_switchNoCalle{{ $hidrante->id }}').prop('checked', true).trigger('change');
        }
        if ($('#edit_id_y_calle').val() === '0' || $('#y_calle_actual').text().trim() === 'Pendiente') {
            $('#edit_switchNoYCalle{{ $hidrante->id }}').prop('checked', true).trigger('change');
        }
        if ($('#edit_id_colonia').val() === '0' || $('#colonia_actual').text().trim() === 'Pendiente') {
            $('#edit_switchNoColonia{{ $hidrante->id }}').prop('checked', true).trigger('change');
        }
    }
    autoActivarSwitchUbicacion();

    // --- UBICACIÓN ---
    function handleLocationField(field, action) {
        const select = $(`#edit_id_${field}`);
        const span = $(`#${field}_actual`);
        const form = select.closest('form');
        $(`input[name="${field}"][type="hidden"], input[name="id_${field}"][type="hidden"]`).remove();
        const config = CONFIG.actions[action];
        if (!config) return;
        select.val(null).trigger('change').prop('disabled', config.disabled);
        if (config.value) {
            span.text(config.value);
            $('<input>', {
                type: 'hidden',
                name: field,
                value: config.value
            }).add($('<input>', {
                type: 'hidden',
                name: `id_${field}`,
                value: config.id
            })).appendTo(form);
        }
    }

    // --- SELECT2 ---
    function initSelect2() {
        $('.select2-search').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $(`${MODAL_ID} .modal-body`),
            language: {
                noResults: function() { return "No se encontraron resultados"; },
                searching: function() { return "Buscando..."; }
            },
            placeholder: 'Comienza a escribir para buscar...',
            allowClear: true,
            minimumInputLength: 2,
            scrollAfterSelect: false,
            position: function(pos, $el) {
                pos.top += 5;
                return pos;
            }
        }).on('select2:open', function() {
            setTimeout(function() {
                $('.select2-search__field').get(0).focus();
            }, 10);
        });
    }

    // --- FECHA TENTATIVA ---
    let edit_fechaTentativaGenerada{{ $hidrante->id }} = false;

    function edit_mostrarPasoGenerar{{ $hidrante->id }}() {
        $('#edit_contenedorGenerarFecha{{ $hidrante->id }}').removeClass('d-none');
        $('#edit_opcionesPlazo{{ $hidrante->id }}').addClass('d-none');
        $('#edit_contenedorFechaGenerada{{ $hidrante->id }}').addClass('d-none');
        $('#edit_iconoExclamacion{{ $hidrante->id }}').removeClass('d-none');
        edit_fechaTentativaGenerada{{ $hidrante->id }} = false;
        edit_actualizarEstadoBotonGuardar{{ $hidrante->id }}();
    }

    function edit_mostrarPasoPlazo{{ $hidrante->id }}() {
        $('#edit_contenedorGenerarFecha{{ $hidrante->id }}').addClass('d-none');
        $('#edit_opcionesPlazo{{ $hidrante->id }}').removeClass('d-none');
        $('#edit_contenedorFechaGenerada{{ $hidrante->id }}').addClass('d-none');
        $('#edit_iconoExclamacion{{ $hidrante->id }}').removeClass('d-none');
        edit_fechaTentativaGenerada{{ $hidrante->id }} = false;
        edit_actualizarEstadoBotonGuardar{{ $hidrante->id }}();
    }

    function edit_mostrarPasoFechaGenerada{{ $hidrante->id }}() {
        $('#edit_contenedorGenerarFecha{{ $hidrante->id }}').addClass('d-none');
        $('#edit_opcionesPlazo{{ $hidrante->id }}').addClass('d-none');
        $('#edit_contenedorFechaGenerada{{ $hidrante->id }}').removeClass('d-none');
        $('#edit_iconoExclamacion{{ $hidrante->id }}').addClass('d-none');
        edit_fechaTentativaGenerada{{ $hidrante->id }} = true;
        edit_actualizarEstadoBotonGuardar{{ $hidrante->id }}();
    }

    function edit_initPopover{{ $hidrante->id }}() {
        const popoverTrigger = document.getElementById('edit_popoverGuardarHidrante{{ $hidrante->id }}');
        if (popoverTrigger) {
            if (bootstrap.Popover.getInstance(popoverTrigger)) {
                bootstrap.Popover.getInstance(popoverTrigger).dispose();
            }
            new bootstrap.Popover(popoverTrigger);
        }
    }

    function edit_actualizarEstadoBotonGuardar{{ $hidrante->id }}() {
        if (edit_fechaTentativaGenerada{{ $hidrante->id }}) {
            $('#edit_btnGuardarHidrante{{ $hidrante->id }}').prop('disabled', false);
            $('#edit_popoverGuardarHidrante{{ $hidrante->id }}').removeAttr('data-bs-toggle').removeAttr('data-bs-trigger').removeAttr('data-bs-content');
            if (bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante{{ $hidrante->id }}'))) {
                bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante{{ $hidrante->id }}')).dispose();
            }
        } else {
            $('#edit_btnGuardarHidrante{{ $hidrante->id }}').prop('disabled', true);
            $('#edit_popoverGuardarHidrante{{ $hidrante->id }}')
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', 'Falta generar una fecha tentativa de mantenimiento.');
            edit_initPopover{{ $hidrante->id }}();
        }
    }

    // --- EVENTOS ---
    function initEventHandlers() {
        // Ubicación pendiente
        $('#edit_ubicacionPendiente').change(function() {
            const action = $(this).is(':checked') ? 'pending' : 'enable';
            CONFIG.fields.forEach(field => handleLocationField(field, action));
        });

        // Botones de limpieza (si existen)
        $('.clear-field').change(function() {
            if ($(this).is(':checked')) {
                handleLocationField($(this).data('field'), 'clear');
                setTimeout(() => $(this).prop('checked', false), 100);
            }
        });

        // Fecha tentativa: flujo de pasos
        $('#edit_btnGenerarFecha{{ $hidrante->id }}').click(function() {
            edit_mostrarPasoPlazo{{ $hidrante->id }}();
        });
        $('#edit_opcionesPlazo{{ $hidrante->id }} button[data-plazo]').click(function() {
            const plazo = $(this).data('plazo');
            const fechaBase = new Date(); // Usar fecha actual
            if (plazo === 'corto') {
                fechaBase.setMonth(fechaBase.getMonth() + 6);
            } else {
                fechaBase.setFullYear(fechaBase.getFullYear() + 1);
            }
            $('#edit_fecha_tentativa{{ $hidrante->id }}').val(fechaBase.toISOString().split('T')[0]);
            edit_mostrarPasoFechaGenerada{{ $hidrante->id }}();
        });
        $('#edit_btnRegresarGenerar{{ $hidrante->id }}').click(function() {
            edit_mostrarPasoGenerar{{ $hidrante->id }}();
        });
        $('#edit_btnResetFecha{{ $hidrante->id }}').click(function() {
            edit_mostrarPasoPlazo{{ $hidrante->id }}();
        });
    }

    // --- INICIALIZACIÓN DEL MODAL ---
    $(MODAL_ID)
        .on('shown.bs.modal', function() {
            initSelect2();
            $(window).trigger('resize');
            // Flujo de fecha tentativa
            const fechaTentativaVal = $('#edit_fecha_tentativa{{ $hidrante->id }}').val();
            if (fechaTentativaVal) {
                // Si hay valor, mostrar el input y habilitar guardar
                edit_mostrarPasoFechaGenerada{{ $hidrante->id }}();
                $('#edit_fecha_tentativa{{ $hidrante->id }}').val(fechaTentativaVal); // Asegura que el valor se muestre
            } else {
                // Si no hay valor, iniciar desde el principio
                edit_mostrarPasoGenerar{{ $hidrante->id }}();
            }
            setTimeout(edit_initPopover{{ $hidrante->id }}, 200);
            edit_actualizarIconoCalle{{ $hidrante->id }}();
        })
        .on('hidden.bs.modal', function() {
            $('.select2-search').select2('destroy');
        });

    // Inicializar eventos
    initEventHandlers();

    // --- AUTOACTIVAR SWITCHES DE UBICACIÓN SI VALOR ES 0/Pendiente ---
    function autoActivarSwitchUbicacion() {
        // Calle
        if ($('#edit_id_calle').val() === '0' || $('#calle_actual').text().trim() === 'Pendiente') {
            $('#edit_switchNoCalle{{ $hidrante->id }}').prop('checked', true).trigger('change');
        }
        // Y Calle
        if ($('#edit_id_y_calle').val() === '0' || $('#y_calle_actual').text().trim() === 'Pendiente') {
            $('#edit_switchNoYCalle{{ $hidrante->id }}').prop('checked', true).trigger('change');
        }
        // Colonia
        if ($('#edit_id_colonia').val() === '0' || $('#colonia_actual').text().trim() === 'Pendiente') {
            $('#edit_switchNoColonia{{ $hidrante->id }}').prop('checked', true).trigger('change');
        }
    }

    // --- ICONOS DE EXCLAMACIÓN Y VALIDACIÓN ---
    const camposConExclamacion = [
        { name: 'numero_estacion', icon: 'edit_iconoExclamacionNEstacion{{ $hidrante->id }}', tipo: 'select' },
        { name: 'llave_hidrante', icon: 'edit_iconoExclamacionLlaveHi{{ $hidrante->id }}', tipo: 'select' },
        { name: 'presion_agua', icon: 'edit_iconoExclamacionPresionA{{ $hidrante->id }}', tipo: 'select' },
        { name: 'llave_fosa', icon: 'edit_iconoExclamacionLlaveFosa{{ $hidrante->id }}', tipo: 'select' },
        { name: 'hidrante_conectado_tubo', icon: 'edit_iconoExclamacionHCT{{ $hidrante->id }}', tipo: 'select' },
        { name: 'estado_hidrante', icon: 'edit_iconoExclamacionEstadoH{{ $hidrante->id }}', tipo: 'select' },
        { name: 'color', icon: 'edit_iconoExclamacionColor{{ $hidrante->id }}', tipo: 'select' },
        { name: 'marca', icon: 'edit_iconoExclamacionMarca{{ $hidrante->id }}', tipo: 'input' },
        { name: 'anio', icon: 'edit_iconoExclamacionYY{{ $hidrante->id }}', tipo: 'input' },
        { name: 'oficial', icon: 'edit_iconoExclamacionOficial{{ $hidrante->id }}', tipo: 'input' },
        { name: 'ubicacion_fosa', icon: 'edit_iconoExclamacionUbiFosa{{ $hidrante->id }}', tipo: 'input' },
        { name: 'id_calle', icon: 'edit_iconoExclamacionCalle{{ $hidrante->id }}', tipo: 'select' },
        { name: 'id_y_calle', icon: 'edit_iconoExclamacionYCalle{{ $hidrante->id }}', tipo: 'select' },
        { name: 'id_colonia', icon: 'edit_iconoExclamacionColonia{{ $hidrante->id }}', tipo: 'select' }
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

    // --- SWITCHES DE UBICACIÓN INDIVIDUAL ---
    $('#edit_switchNoCalle{{ $hidrante->id }}').change(function() {
        if ($(this).is(':checked')) {
            $('#edit_id_calle').prop('disabled', true).addClass('input-disabled').val('').trigger('change');
            $('#edit_iconoExclamacionCalle{{ $hidrante->id }}').addClass('d-none');
            if (!$('input[name="id_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_calle', value: '0'}).appendTo('form');
            }
            if (!$('input[name="calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'calle', value: 'Pendiente'}).appendTo('form');
            }
        } else {
            $('#edit_id_calle').prop('disabled', false).removeClass('input-disabled');
            if (!$('#edit_id_calle').val()) {
                $('#edit_iconoExclamacionCalle{{ $hidrante->id }}').removeClass('d-none');
            }
            $('input[name="id_calle"][type="hidden"]').remove();
            $('input[name="calle"][type="hidden"]').remove();
        }
        edit_actualizarEstadoBotonGuardar{{ $hidrante->id }}();
    });

    $('#edit_switchNoYCalle{{ $hidrante->id }}').change(function() {
        if ($(this).is(':checked')) {
            $('#edit_id_y_calle').prop('disabled', true).addClass('input-disabled').val('0').trigger('change');
            $('#edit_iconoExclamacionYCalle{{ $hidrante->id }}').addClass('d-none');
            if (!$('input[name="id_y_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_y_calle', value: '0'}).appendTo('form');
            }
            if (!$('input[name="y_calle"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'y_calle', value: 'Pendiente'}).appendTo('form');
            }
        } else {
            $('#edit_id_y_calle').prop('disabled', false).removeClass('input-disabled');
            if (!$('#edit_id_y_calle').val()) {
                $('#edit_iconoExclamacionYCalle{{ $hidrante->id }}').removeClass('d-none');
            }
            $('input[name="id_y_calle"][type="hidden"]').remove();
            $('input[name="y_calle"][type="hidden"]').remove();
        }
    });

    $('#edit_switchNoColonia{{ $hidrante->id }}').change(function() {
        if ($(this).is(':checked')) {
            $('#edit_id_colonia').prop('disabled', true).addClass('input-disabled').val('0').trigger('change');
            $('#edit_iconoExclamacionColonia{{ $hidrante->id }}').addClass('d-none');
            if (!$('input[name="id_colonia"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'id_colonia', value: '0'}).appendTo('form');
            }
            if (!$('input[name="colonia"][type="hidden"]').length) {
                $('<input>').attr({type: 'hidden', name: 'colonia', value: 'Pendiente'}).appendTo('form');
            }
        } else {
            $('#edit_id_colonia').prop('disabled', false).removeClass('input-disabled');
            if (!$('#edit_id_colonia').val()) {
                $('#edit_iconoExclamacionColonia{{ $hidrante->id }}').removeClass('d-none');
            }
            $('input[name="id_colonia"][type="hidden"]').remove();
            $('input[name="colonia"][type="hidden"]').remove();
        }
    });

    // --- HABILITAR/DESHABILITAR BOTÓN GUARDAR ---
    $('#edit_id_calle').on('change', function() {
        edit_actualizarIconoCalle{{ $hidrante->id }}();
        edit_actualizarEstadoBotonGuardar{{ $hidrante->id }}();
    });
    $('#edit_switchNoCalle{{ $hidrante->id }}').on('change', function() {
        edit_actualizarIconoCalle{{ $hidrante->id }}();
        edit_actualizarEstadoBotonGuardar{{ $hidrante->id }}();
    });
    function edit_actualizarEstadoBotonGuardar{{ $hidrante->id }}() {
        // Verifica si la fecha tentativa ya fue generada
        let fechaOk = edit_fechaTentativaGenerada{{ $hidrante->id }};

        // Verifica si el campo calle está cubierto (valor válido o switch activo)
        let calleOk = false;
        if ($('#edit_switchNoCalle{{ $hidrante->id }}').is(':checked')) {
            calleOk = true;
        } else if ($('#edit_id_calle').val() && $('#edit_id_calle').val() !== '' && $('#edit_id_calle').val() !== null) {
            calleOk = true;
        }

        if (fechaOk && calleOk) {
            $('#edit_btnGuardarHidrante{{ $hidrante->id }}').prop('disabled', false);
            $('#edit_popoverGuardarHidrante{{ $hidrante->id }}').removeAttr('data-bs-toggle').removeAttr('data-bs-trigger').removeAttr('data-bs-content');
            if (bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante{{ $hidrante->id }}'))) {
                bootstrap.Popover.getInstance(document.getElementById('edit_popoverGuardarHidrante{{ $hidrante->id }}')).dispose();
            }
        } else {
            $('#edit_btnGuardarHidrante{{ $hidrante->id }}').prop('disabled', true);
            $('#edit_popoverGuardarHidrante{{ $hidrante->id }}')
                .attr('data-bs-toggle', 'popover')
                .attr('data-bs-trigger', 'hover focus')
                .attr('data-bs-content', 'Debe generar la fecha tentativa y seleccionar una calle.');
            if (typeof edit_initPopover{{ $hidrante->id }} === 'function') edit_initPopover{{ $hidrante->id }}();
        }
    }

    function edit_actualizarIconoCalle{{ $hidrante->id }}() {
        // Si hay valor válido en el select de calle, oculta el icono
        // Si está vacío, null o 0 (pendiente), muestra el icono
        const val = $('#edit_id_calle').val();
        if (val && val !== '' && val !== null && val !== '0') {
            $('#edit_iconoExclamacionCalle{{ $hidrante->id }}').addClass('d-none');
        } else {
            $('#edit_iconoExclamacionCalle{{ $hidrante->id }}').removeClass('d-none');
        }
    }

    // Nueva función para actualizar iconos de ubicación
    function actualizarIconosUbicacion() {
        // CALLE
        const calleVal = $('#edit_id_calle').val();
        const switchCalle = $('#edit_switchNoCalle{{ $hidrante->id }}').is(':checked');
        const iconoCalle = $('#edit_iconoExclamacionCalle{{ $hidrante->id }}');
        iconoCalle.removeClass('text-warning text-danger d-none');
        if (calleVal === '0') {
            // Pendiente: warning
            iconoCalle.addClass('text-warning').removeClass('text-danger').removeClass('d-none');
        } else if (!calleVal && !switchCalle) {
            // Vacío y switch apagado: danger
            iconoCalle.addClass('text-danger').removeClass('text-warning').removeClass('d-none');
        } else {
            // ID válida o switch activado: sin icono
            iconoCalle.addClass('d-none');
        }

        // Y CALLE
        const yCalleVal = $('#edit_id_y_calle').val();
        const iconoYCalle = $('#edit_iconoExclamacionYCalle{{ $hidrante->id }}');
        iconoYCalle.removeClass('d-none');
        if (yCalleVal === '0') {
            // Pendiente: warning
            iconoYCalle.addClass('text-warning').removeClass('d-none');
        } else {
            // Vacío o ID válida: sin icono
            iconoYCalle.addClass('d-none').removeClass('text-warning');
        }

        // COLONIA
        const coloniaVal = $('#edit_id_colonia').val();
        const iconoColonia = $('#edit_iconoExclamacionColonia{{ $hidrante->id }}');
        iconoColonia.removeClass('d-none');
        if (coloniaVal === '0') {
            // Pendiente: warning
            iconoColonia.addClass('text-warning').removeClass('d-none');
        } else {
            // Vacío o ID válida: sin icono
            iconoColonia.addClass('d-none').removeClass('text-warning');
        }
    }

    // Llama a esta función en los eventos de cambio:
    $('#edit_id_calle, #edit_switchNoCalle{{ $hidrante->id }}, #edit_id_y_calle, #edit_id_colonia').on('change', actualizarIconosUbicacion);

    // Llama también al abrir el modal para estado inicial:
    actualizarIconosUbicacion();
});
</script>
