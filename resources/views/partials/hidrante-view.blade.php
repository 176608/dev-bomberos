<div class="modal fade" id="viewHidranteModal{{ $hidrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title w-100 text-center">Departamento de Bomberos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <h6 class="text-center mb-3">Registro de Hidrantes</h6>
                <form>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha de Inspección</label>
                            <input type="text" class="form-control" value="{{ $hidrante->fecha_inspeccion }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fecha Tentativa de Mantenimiento</label>
                            <input type="text" class="form-control" value="{{ $hidrante->fecha_mantenimiento ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">N° Estación</label>
                            <input type="text" class="form-control" value="{{ $hidrante->numero_estacion }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Número de Hidrante</label>
                            <input type="text" class="form-control" value="{{ $hidrante->id }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <h6 class="fw-bold">Ubicación</h6>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Calle</label>
                            <input type="text" class="form-control" value="{{ $hidrante->callePrincipal?->Nomvial ?? $hidrante->calle }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Y Calle</label>
                            <input type="text" class="form-control" value="{{ $hidrante->calleSecundaria?->Nomvial ?? $hidrante->y_calle }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Colonia</label>
                            <input type="text" class="form-control" value="{{ $hidrante->coloniaLocacion?->NOMBRE ?? $hidrante->colonia }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Llave de Hidrante</label>
                            <input type="text" class="form-control" value="{{ $hidrante->llave_hidrante }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Presión del Agua</label>
                            <input type="text" class="form-control" value="{{ $hidrante->presion_agua }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Color</label>
                            <input type="text" class="form-control" value="{{ $hidrante->color ?? '' }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ubicación de Fosa</label>
                            <input type="text" class="form-control" value="{{ $hidrante->ubicacion_fosa }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Llave de Fosa</label>
                            <input type="text" class="form-control" value="{{ $hidrante->llave_fosa }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Hidrante Conectado a Tubo de</label>
                            <input type="text" class="form-control" value="{{ $hidrante->hidrante_conectado_tubo }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Año</label>
                            <input type="text" class="form-control" value="{{ $hidrante->anio }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estado de Hidrante</label>
                            <input type="text" class="form-control" value="{{ $hidrante->estado_hidrante }}" readonly>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Marca</label>
                            <input type="text" class="form-control" value="{{ $hidrante->marca }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Observaciones</label>
                            <input type="text" class="form-control" value="{{ $hidrante->observaciones }}" readonly>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Atendido Maquina Num</label>
                            <input type="text" class="form-control" value="{{ $hidrante->maquina_num ?? '' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Oficial</label>
                            <input type="text" class="form-control" value="{{ $hidrante->oficial }}" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>