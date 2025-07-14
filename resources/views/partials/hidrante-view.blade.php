<div class="modal fade" id="viewHidranteModal{{ $hidrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white align-items-center">
                <div class="w-100 text-center">
                    <h5 class="modal-title mb-0">Departamento de Bomberos</h5>
                    <div class="small">Registro de Hidrantes</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <img src="{{ asset('img/logo/Escudo_Ciudad_Juarez_smn.png') }}" alt="Escudo Ciudad Juárez" style="height:90px;">
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label class="form-label fw-bold mb-0">Fecha de Inspección:</label>
                                    <span class="ms-1">{{ \Carbon\Carbon::parse($hidrante->fecha_inspeccion)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-12">
                                    <label class="form-label fw-bold mb-0">N° Estación:</label>
                                    <span class="ms-1">{{ $hidrante->numero_estacion }}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label fw-bold mb-0">N° Hidrante:</label>
                                    <span class="ms-1">{{ $hidrante->id }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Ubicación en horizontal -->
                    <div class="row  mb-2 align-items-center">
                        <div class="col-md-6 offset-md-5">
                            <label class="form-label fw-bold mb-1">Ubicacion</label>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Calle:</label>
                            <span class="ms-1">{{ $hidrante->callePrincipal?->Nomvial ?? $hidrante->calle }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Y Calle:</label>
                            <span class="ms-1">{{ $hidrante->calleSecundaria?->Nomvial ?? $hidrante->y_calle }}</span>
                        </div>
                    </div>

                    <div class="row mb-2 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Colonia:</label>
                            <span class="ms-1">{{ $hidrante->coloniaLocacion?->NOMBRE ?? $hidrante->colonia }}</span>
                        </div>
                    </div>
                    <hr>
                    <!-- Llave, presión y color en horizontal -->
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Estado de Hidrante:</label>
                            <span class="ms-1">{{ $hidrante->estado_hidrante }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Presión del Agua:</label>
                            <span class="ms-1">{{ $hidrante->presion_agua }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Marca:</label>
                            <span class="ms-1">{{ $hidrante->marca }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Año:</label>
                            <span class="ms-1">{{ $hidrante->anio }}</span>
                        </div>
                    </div>
                    <hr>
                    <!-- Ubicación de fosa y llave de fosa -->
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Llave de Hidrante:</label>
                            <span class="ms-1">{{ $hidrante->llave_hidrante }}</span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Llave de Fosa:</label>
                            <span class="ms-1">{{ $hidrante->llave_fosa }}</span>
                        </div>
                    </div>
                    <hr>
                    <!-- Tubo, año, estado, marca en horizontal -->
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Ubicación de Fosa:</label>
                            <span class="ms-1">
                                <?php
                                    // Verificar si ubicacion_fosa es numérico y está entre 1 y 100
                                    $valor = $hidrante->ubicacion_fosa;
                                    if (is_numeric($valor) && $valor >= 1 && $valor <= 100) {
                                        echo "A {$valor} Metros.";
                                    } else {
                                        echo $valor;
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold mb-0">Hidrante Conectado a Tubo de:</label>
                            <span class="ms-1">{{ $hidrante->hidrante_conectado_tubo }}</span>
                        </div>
                    </div>
                    <hr>
                    <!-- Observaciones y oficial -->
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-12">
                            <label class="form-label fw-bold mb-0">Observaciones:</label>
                            <span class="ms-1">{{ $hidrante->observaciones }}</span>
                        </div> 
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-12">
                            <label class="form-label fw-bold mb-0">Oficial:</label>
                            <span class="ms-1">{{ $hidrante->oficial }}</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>