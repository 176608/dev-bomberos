<!-- Modal -->
<div class="modal fade" id="viewHidranteModal{{ $hidrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Encabezado -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title w-100 text-center">Departamento de Bomberos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <!-- Subtítulo -->
                <h6 class="text-center mb-3">Registro de Hidrantes</h6>

                <!-- Información General -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Inspección (mm/dd/aaaa)</label>
                        <input type="text" class="form-control" value="{{ $hidrante->fecha_inspeccion }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Número de Estación</label>
                        <input type="text" class="form-control" value="{{ $hidrante->numero_estacion }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Número de Hidrante</label>
                        <input type="text" class="form-control" value="{{ $hidrante->id }}" readonly>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Ubicación -->
                <h6 class="section-title">Ubicación</h6>
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Calle</label>
                        <input type="text" class="form-control" value="{{ $hidrante->callePrincipal?->Nomvial ?? $hidrante->calle }}" readonly>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Y Calle</label>
                        <input type="text" class="form-control" value="{{ $hidrante->calleSecundaria?->Nomvial ?? $hidrante->y_calle }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Colonia</label>
                        <input type="text" class="form-control" value="{{ $hidrante->coloniaLocacion?->NOMBRE ?? $hidrante->colonia }}" readonly>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Detalles Técnicos -->
                <h6 class="section-title">Detalles Técnicos</h6>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Llave de Hidrante</label>
                        <input type="text" class="form-control" value="{{ $hidrante->llave_hidrante }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Presión del Agua</label>
                        <input type="text" class="form-control" value="{{ $hidrante->presion_agua }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ubicación de Fosa</label>
                        <input type="text" class="form-control" value="{{ $hidrante->ubicacion_fosa }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Llave de Fosa</label>
                        <input type="text" class="form-control" value="{{ $hidrante->llave_fosa }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hidrante Conectado a Tubo de:</label>
                        <input type="text" class="form-control" value="{{ $hidrante->hidrante_conectado_tubo }}" readonly>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Datos Adicionales -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Año</label>
                        <input type="text" class="form-control" value="{{ $hidrante->anio }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado del Hidrante</label>
                        <input type="text" class="form-control" value="{{ $hidrante->estado_hidrante }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Marca</label>
                        <input type="text" class="form-control" value="{{ $hidrante->marca }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Oficial</label>
                        <input type="text" class="form-control" value="{{ $hidrante->oficial }}" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" rows="2" readonly>{{ $hidrante->observaciones }}</textarea>
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
.section-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
    margin-top: 10px;
    text-decoration: underline;
}
.form-label {
    font-weight: bold;
}
.form-control[readonly], textarea[readonly] {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
}
hr {
    border-top: 2px solid #bbb;
}
</style>