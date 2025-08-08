<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->

<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header bg-danger text-white text-center">
            <h5 class="modal-title mb-0">Departamento de Bomberos</h5>
            <div class="small">Registro de Hidrantes</div>
        </div>
        <div class="card-body">
            <div class="row mb-2 align-items-center">
                <div class="col-md-6 d-flex justify-content-center align-items-center">
                    <img src="{{ public_path('img/logo/Escudo_Ciudad_Juarez_smn.png') }}" alt="Escudo Ciudad Juárez" style="height:90px;">
                </div>
                <div class="col-md-6">
                    <div class="row mb-2">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-0">Fecha de Inspección:</label>
                            <span class="ms-1">{{ $hidrante->fecha_inspeccion ? \Carbon\Carbon::parse($hidrante->fecha_inspeccion)->format('d/m/Y') : 'No registrada' }}</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-0">N° Estación:</label>
                            <span class="ms-1">{{ $hidrante->numero_estacion ?: 'No registrada' }}</span>
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
            <div class="card text-center">
                <div class="card-header">
                    Ubicación del Hidrante
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            @php
                                $callePrincipal = $hidrante->callePrincipal 
                                    ? $hidrante->callePrincipal->Tipovial . ' ' . $hidrante->callePrincipal->Nomvial
                                    : ($hidrante->calle ? $hidrante->calle . '*' : null);
                                    
                                $calleSecundaria = $hidrante->calleSecundaria 
                                    ? $hidrante->calleSecundaria->Tipovial . ' ' . $hidrante->calleSecundaria->Nomvial
                                    : ($hidrante->y_calle ? $hidrante->y_calle . '*' : null);
                            @endphp

                            @if($callePrincipal)
                                @if($calleSecundaria)
                                    {{-- Caso: Ambas calles (pueden ser mixtas) --}} Entre {{ $callePrincipal }} y {{ $calleSecundaria }}.
                                @else
                                    {{-- Caso: Solo calle principal --}} Sobre {{ $callePrincipal }}.
                                @endif
                            @else
                                Ubicación no registrada.
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            @if($hidrante->coloniaLocacion)
                                {{-- Caso 1: Colonia con relación --}} En {{ $hidrante->coloniaLocacion->TIPO }} {{ $hidrante->coloniaLocacion->NOMBRE }}.
                            @elseif($hidrante->colonia && $hidrante->colonia !== NULL)
                                {{-- Caso 2: Solo campo colonia sin relación --}} En {{ $hidrante->colonia }}*.
                            @else
                                Colonia no registrada.
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer text-body-secondary">
                    Última actualización realizada: {{ $hidrante->updated_at ? \Carbon\Carbon::parse($hidrante->updated_at)->format('d/m/Y H:i') : 'N/A' }}
                </div>
            </div>

            <hr>
            <!-- Llave, presión y color en horizontal -->
            <div class="row mb-2 align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-0">Estado de Hidrante:</label>
                    <span class="ms-1">
                        @if($hidrante->estado_hidrante === 'S/I')
                            S/I
                        @elseif($hidrante->estado_hidrante == 'BUENO')
                            <span class="badge bg-success">BUENO</span>
                        @elseif($hidrante->estado_hidrante == 'REGULAR')
                            <span class="badge bg-warning text-dark">REGULAR</span>
                        @elseif($hidrante->estado_hidrante == 'MALO')
                            <span class="badge bg-danger">MALO</span>
                        @elseif($hidrante->estado_hidrante == 'NO FUNCIONA')
                            <span class="badge bg-secondary">NO FUNCIONA</span>
                        @else
                            {{ ucfirst(strtolower($hidrante->estado_hidrante)) ?: 'No registrado' }}
                        @endif
                    </span>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-0">Presión del Agua:</label>
                    <span class="ms-1">
                        @if($hidrante->presion_agua === 'S/I')
                            S/I
                        @else
                            {{ ucfirst(strtolower($hidrante->presion_agua)) ?: 'No registrada' }}
                        @endif
                    </span>
                </div>
            </div>
            <hr>
            <div class="row mb-2 align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-0">Marca:</label>
                    <span class="ms-1">{{ $hidrante->marca ?: 'No registrada' }}</span>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-0">Año:</label>
                    <span class="ms-1">{{ $hidrante->anio ?: 'No registrado' }}</span>
                </div>
            </div>
            <hr>
            <!-- Ubicación de fosa y llave de fosa -->
            <div class="row mb-2 align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-0">Llave de Hidrante:</label>
                    <span class="ms-1">
                        @if($hidrante->llave_hidrante === 'S/I')
                            S/I
                        @else
                            {{ ucfirst(strtolower($hidrante->llave_hidrante)) ?: 'No registrada' }}
                        @endif
                    </span>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-0">Llave de Fosa:</label>
                    <span class="ms-1">
                        @if($hidrante->llave_fosa === 'S/I')
                            S/I
                        @else
                            {{ ucfirst(strtolower($hidrante->llave_fosa)) ?: 'No registrada' }}
                        @endif
                    </span>
                </div>
            </div>
            <hr>
            <!-- Tubo, año, estado, marca en horizontal -->
            <div class="row mb-2 align-items-center">
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-0">Ubicación de Fosa:</label>
                    <span class="ms-1">
                        @if($hidrante->ubicacion_fosa === 'S/I')
                            S/I
                        @elseif(is_numeric($hidrante->ubicacion_fosa) && $hidrante->ubicacion_fosa >= 1 && $hidrante->ubicacion_fosa <= 100)
                            A {{ $hidrante->ubicacion_fosa }} Metros
                        @else
                            {{ ucfirst(strtolower($hidrante->ubicacion_fosa)) ?: 'No registrada' }}
                        @endif
                    </span>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold mb-0">Hidrante Conectado a Tubo de:</label>
                    <span class="ms-1">
                        @switch($hidrante->hidrante_conectado_tubo)
                            @case("S/I")
                                S/I
                                @break
                            @case("4'")
                                4 Pulgadas
                                @break
                            @case("6'")
                                6 Pulgadas
                                @break
                            @case("8'")
                                8 Pulgadas
                                @break
                            @default
                                {{ ucfirst(strtolower($hidrante->hidrante_conectado_tubo)) ?: 'No registrado' }}
                        @endswitch
                    </span>
                </div>
            </div>
            <hr>
            <!-- Observaciones y oficial -->
            <div class="row mb-2 align-items-center">
                <div class="col-md-12">
                    <label class="form-label fw-bold mb-0">Observaciones:</label>
                    <span class="ms-1">{{ $hidrante->observaciones ?: 'No hay observaciones registradas' }}</span>
                </div> 
            </div>
            <div class="row mb-2 align-items-center">
                <div class="col-md-12">
                    <label class="form-label fw-bold mb-0">Oficial:</label>
                    <span class="ms-1">{{ $hidrante->oficial ?: 'No registrado' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>