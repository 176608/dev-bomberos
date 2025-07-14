<!--<div class="modal fade" id="viewHidranteModal{{ $hidrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Reporte del Hidrante #{{ $hidrante->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Estatus</dt>
                    <dd class="col-sm-8">{{ $hidrante->stat }}</dd>
                    <dt class="col-sm-4">Fecha Inspección</dt>
                    <dd class="col-sm-8">{{ $hidrante->fecha_inspeccion }}</dd>
                    <dt class="col-sm-4">N° Estación</dt>
                    <dd class="col-sm-8">{{ $hidrante->numero_estacion }}</dd>
                    <dt class="col-sm-4">Calle</dt>
                    <dd class="col-sm-8">{{ $hidrante->callePrincipal?->Nomvial ?? $hidrante->calle }}</dd>
                    <dt class="col-sm-4">Y Calle</dt>
                    <dd class="col-sm-8">{{ $hidrante->calleSecundaria?->Nomvial ?? $hidrante->y_calle }}</dd>
                    <dt class="col-sm-4">Colonia</dt>
                    <dd class="col-sm-8">{{ $hidrante->coloniaLocacion?->NOMBRE ?? $hidrante->colonia }}</dd>
                    <dt class="col-sm-4">Llave Hidrante</dt>
                    <dd class="col-sm-8">{{ $hidrante->llave_hidrante }}</dd>
                    <dt class="col-sm-4">Presión Agua</dt>
                    <dd class="col-sm-8">{{ $hidrante->presion_agua }}</dd>
                    <dt class="col-sm-4">Llave Fosa</dt>
                    <dd class="col-sm-8">{{ $hidrante->llave_fosa }}</dd>
                    <dt class="col-sm-4">Ubicación Fosa</dt>
                    <dd class="col-sm-8">{{ $hidrante->ubicacion_fosa }}</dd>
                    <dt class="col-sm-4">Conectado a Tubo</dt>
                    <dd class="col-sm-8">{{ $hidrante->hidrante_conectado_tubo }}</dd>
                    <dt class="col-sm-4">Estado</dt>
                    <dd class="col-sm-8">{{ $hidrante->estado_hidrante }}</dd>
                    <dt class="col-sm-4">Marca</dt>
                    <dd class="col-sm-8">{{ $hidrante->marca }}</dd>
                    <dt class="col-sm-4">Año</dt>
                    <dd class="col-sm-8">{{ $hidrante->anio }}</dd>
                    <dt class="col-sm-4">Oficial</dt>
                    <dd class="col-sm-8">{{ $hidrante->oficial }}</dd>
                    <dt class="col-sm-4">Observaciones</dt>
                    <dd class="col-sm-8">{{ $hidrante->observaciones }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div> -->

<!-- Modal -->
<div class="modal fade" id="viewHidranteModal{{ $hidrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Encabezado -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Departamento de Bomberos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <!-- Subtítulo -->
                <h6 class="text-center mb-3">Registro de Hidrante #{{ $hidrante->id }}</h6>

                <!-- Información General -->
                <div class="mb-3">
                    <label class="form-label">Fecha de Inspección (mm/dd/aaaa)</label>
                    <input type="text" class="form-control" value="{{ $hidrante->fecha_inspeccion }}" readonly>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Número de Estación</label>
                        <input type="text" class="form-control" value="{{ $hidrante->numero_estacion }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Número de Hidrante</label>
                        <input type="text" class="form-control" value="{{ $hidrante->id }}" readonly>
                    </div>
                </div>

                <!-- Ubicación -->
                <h6 class="mt-4 mb-3">Ubicación</h6>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Calle</label>
                        <input type="text" class="form-control" value="{{ $hidrante->callePrincipal?->Nomvial ?? $hidrante->calle }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Y Calle</label>
                        <input type="text" class="form-control" value="{{ $hidrante->calleSecundaria?->Nomvial ?? $hidrante->y_calle }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Colonia</label>
                    <input type="text" class="form-control" value="{{ $hidrante->coloniaLocacion?->NOMBRE ?? $hidrante->colonia }}" readonly>
                </div>

                <!-- Detalles Técnicos -->
                <h6 class="mt-4 mb-3">Detalles Técnicos</h6>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Llave de Hidrante</label>
                        <input type="text" class="form-control" value="{{ $hidrante->llave_hidrante }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Presión del Agua</label>
                        <input type="text" class="form-control" value="{{ $hidrante->presion_agua }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Color</label>
                        <input type="text" class="form-control" value="{{ $hidrante->color }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Ubicación de Fosa</label>
                        <input type="text" class="form-control" value="{{ $hidrante->ubicacion_fosa }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Llave de Fosa</label>
                        <input type="text" class="form-control" value="{{ $hidrante->llave_fosa }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Hidrante Conectado a Tubo de:</label>
                        <input type="text" class="form-control" value="{{ $hidrante->hidrante_conectado_tubo }}" readonly>
                    </div>
                </div>

                <!-- Datos Adicionales -->
                <h6 class="mt-4 mb-3">Datos Adicionales</h6>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Año</label>
                        <input type="text" class="form-control" value="{{ $hidrante->anio }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Estado del Hidrante</label>
                        <input type="text" class="form-control" value="{{ $hidrante->estado_hidrante }}" readonly>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label class="form-label">Marca</label>
                        <input type="text" class="form-control" value="{{ $hidrante->marca }}" readonly>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Oficial</label>
                        <input type="text" class="form-control" value="{{ $hidrante->oficial }}" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" rows="3" readonly>{{ $hidrante->observaciones }}</textarea>
                </div>
            </div>

            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos generales */
.modal-body {
    font-size: 14px;
}

.form-label {
    font-weight: bold;
}

/* Estilo para el encabezado */
.modal-header {
    background-color: #0d6efd !important; /* Azul principal de Bootstrap */
    color: white;
}

/* Estilo para los inputs */
.form-control {
    background-color: #f8f9fa; /* Fondo claro para los campos */
    border: 1px solid #ced4da;
}

/* Estilo para las secciones */
h6 {
    font-weight: bold;
    color: #333;
}
</style>