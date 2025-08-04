<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<div class="container-fluid p-0">
    <div class="row">
        <!-- Información básica -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información Básica</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Fecha de Inspección:</th>
                            <td>{{ $hidrante->fecha_inspeccion ? $hidrante->fecha_inspeccion->format('d/m/Y') : 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Estación:</th>
                            <td>{{ $hidrante->numero_estacion ?: 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Estado:</th>
                            <td>
                                @if($hidrante->estado_hidrante == 'BUENO')
                                    <span class="badge bg-success">BUENO</span>
                                @elseif($hidrante->estado_hidrante == 'REGULAR')
                                    <span class="badge bg-warning text-dark">REGULAR</span>
                                @elseif($hidrante->estado_hidrante == 'MALO')
                                    <span class="badge bg-danger">MALO</span>
                                @elseif($hidrante->estado_hidrante == 'NO FUNCIONA')
                                    <span class="badge bg-secondary">NO FUNCIONA</span>
                                @else
                                    <span class="badge bg-secondary">{{ $hidrante->estado_hidrante ?: 'NO REGISTRADO' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Marca:</th>
                            <td>{{ $hidrante->marca ?: 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Año:</th>
                            <td>{{ $hidrante->anio ?: 'No registrado' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Ubicación -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Ubicación</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Colonia:</th>
                            <td>{{ $hidrante->colonia ?: 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Sobre vía:</th>
                            <td>{{ $hidrante->calle ?: 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Y vía:</th>
                            <td>{{ $hidrante->y_calle ?: 'No registrada' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Características -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Características Técnicas</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Llave Hidrante:</th>
                            <td>{{ $hidrante->llave_hidrante ?: 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Presión Agua:</th>
                            <td>{{ $hidrante->presion_agua ?: 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Llave Fosa:</th>
                            <td>{{ $hidrante->llave_fosa ?: 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Ubicación Fosa:</th>
                            <td>{{ $hidrante->ubicacion_fosa ?: 'No registrada' }}</td>
                        </tr>
                        <tr>
                            <th>Conectado al Tubo:</th>
                            <td>
                                @if($hidrante->hidrante_conectado_tubo == 'SI')
                                    <span class="badge bg-success">SÍ</span>
                                @elseif($hidrante->hidrante_conectado_tubo == 'NO')
                                    <span class="badge bg-danger">NO</span>
                                @else
                                    <span class="badge bg-secondary">No registrado</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Observaciones -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Observaciones</h5>
                </div>
                <div class="card-body">
                    <div class="p-2 bg-light rounded">
                        {{ $hidrante->observaciones ?: 'No hay observaciones registradas.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>