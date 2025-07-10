<div class="modal fade" id="viewHidranteModal{{ $hidrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Visualizar Hidrante #{{ $hidrante->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Estatus</dt>
                    <dd class="col-sm-8">{{ $hidrante->stat }}</dd>
                    <dt class="col-sm-4">Fecha Inspección</dt>
                    <dd class="col-sm-8">{{ $hidrante->fecha_inspeccion }}</dd>
                    <dt class="col-sm-4">Fecha Tentativa</dt>
                    <dd class="col-sm-8">{{ $hidrante->fecha_tentativa }}</dd>
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
</div>