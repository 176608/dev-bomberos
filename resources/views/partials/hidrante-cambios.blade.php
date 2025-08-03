<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<div class="modal fade" id="historialHidranteModal{{ $hidrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Historial de Cambios - Hidrante #{{ $hidrante->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($cambios->isEmpty())
                    <div class="alert alert-info">
                        No se encontraron registros de cambios para este hidrante.
                    </div>
                @else
                    @php
                        // Agrupar cambios por fecha/hora y usuario
                        $cambiosAgrupados = $cambios->groupBy(function($item) {
                            return $item->fecha_hora->format('Y-m-d H:i') . '-' . $item->id_user;
                        });

                        // Campos relacionados para mostrar juntos
                        $camposRelacionados = [
                            'id_colonia' => 'colonia',
                            'id_calle' => 'calle',
                            'id_y_calle' => 'y_calle',
                        ];
                        
                        $primerGrupoKey = $cambiosAgrupados->keys()->first();
                    @endphp

                    <div class="accordion" id="accordionCambios">
                        @foreach($cambiosAgrupados as $grupo => $items)
                            @php
                                $primerItem = $items->first();
                                $fechaHora = $primerItem->fecha_hora->format('d/m/Y H:i:s');
                                $usuario = $primerItem->usuario ? $primerItem->usuario->name : 'Usuario desconocido';
                                $grupoId = 'grupo_' . str_replace(['-', ':', ' ', '.'], '_', $grupo);
                                
                                $esUltimoGrupo = ($grupo === $primerGrupoKey);
                            @endphp

                            <div class="accordion-item mb-3 border">
                                <h2 class="accordion-header" id="heading{{ $grupoId }}">
                                    <button class="accordion-button {{ $esUltimoGrupo ? '' : 'collapsed' }}" type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $grupoId }}" 
                                            aria-expanded="{{ $esUltimoGrupo ? 'true' : 'false' }}" 
                                            aria-controls="collapse{{ $grupoId }}">
                                        <strong>{{ $fechaHora }}</strong> - {{ $usuario }} ({{ $items->count() }} cambios)
                                    </button>
                                </h2>
                                <div id="collapse{{ $grupoId }}" 
                                     class="accordion-collapse collapse {{ $esUltimoGrupo ? 'show' : '' }}" 
                                     aria-labelledby="heading{{ $grupoId }}" 
                                     data-bs-parent="#accordionCambios">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover mb-0">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Campo</th>
                                                        <th>Valor Anterior</th>
                                                        <th>Nuevo Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        // Procesar campos relacionados primero
                                                        $procesados = [];
                                                        $itemsOrdenados = collect();
                                                    @endphp

                                                    @foreach($items as $cambio)
                                                        @php
                                                            // Saltarse los ya procesados
                                                            if(in_array($cambio->id_cambio_h, $procesados)) continue;

                                                            // Marcar como procesado
                                                            $procesados[] = $cambio->id_cambio_h;

                                                            // Buscar campo relacionado
                                                            $campoRelacionado = null;
                                                            $valorRelacionadoAntes = null;
                                                            $valorRelacionadoDespues = null;
                                                            $idCampoRelacionado = null;
                                                            
                                                            // Si es un campo ID, buscar su relacionado
                                                            if(array_key_exists($cambio->campo, $camposRelacionados)) {
                                                                $campoRelacionadoNombre = $camposRelacionados[$cambio->campo];
                                                                foreach($items as $itemRelacionado) {
                                                                    if($itemRelacionado->campo == $campoRelacionadoNombre) {
                                                                        $campoRelacionado = $itemRelacionado;
                                                                        $valorRelacionadoAntes = $itemRelacionado->old;
                                                                        $valorRelacionadoDespues = $itemRelacionado->new;
                                                                        $idCampoRelacionado = $itemRelacionado->id_cambio_h;
                                                                        $procesados[] = $idCampoRelacionado;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            
                                                            // Si es un valor y no un ID, buscamos su ID relacionado
                                                            $relacionInversa = array_search($cambio->campo, $camposRelacionados);
                                                            if($relacionInversa !== false) {
                                                                foreach($items as $itemRelacionado) {
                                                                    if($itemRelacionado->campo == $relacionInversa) {
                                                                        $campoRelacionado = $itemRelacionado;
                                                                        $valorRelacionadoAntes = $itemRelacionado->old;
                                                                        $valorRelacionadoDespues = $itemRelacionado->new;
                                                                        $idCampoRelacionado = $itemRelacionado->id_cambio_h;
                                                                        $procesados[] = $idCampoRelacionado;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        @endphp

                                                        <tr>
                                                            <td>
                                                                @if(array_key_exists($cambio->campo, $camposRelacionados) && $campoRelacionado)
                                                                    {{ $nombresCampos[$camposRelacionados[$cambio->campo]] ?? $camposRelacionados[$cambio->campo] }}
                                                                @elseif($relacionInversa !== false && $campoRelacionado)
                                                                    {{ $nombresCampos[$cambio->campo] ?? $cambio->campo }}
                                                                @else
                                                                    {{ $nombresCampos[$cambio->campo] ?? $cambio->campo }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(array_key_exists($cambio->campo, $camposRelacionados) && $campoRelacionado)
                                                                    {{ $valorRelacionadoAntes ?: '-' }}
                                                                    @if($cambio->old === '0')
                                                                        (ID: Manual)
                                                                    @elseif($cambio->old === '' || $cambio->old === null)
                                                                        (ID: Nulo)
                                                                    @else
                                                                        (ID: {{ $cambio->old }})
                                                                    @endif
                                                                @elseif($relacionInversa !== false && $campoRelacionado)
                                                                    {{ $cambio->old ?: '-' }}
                                                                    @if($valorRelacionadoAntes === '0')
                                                                        (ID: Manual)
                                                                    @elseif($valorRelacionadoAntes === '' || $valorRelacionadoAntes === null)
                                                                        (ID: Nulo)
                                                                    @else
                                                                        (ID: {{ $valorRelacionadoAntes }})
                                                                    @endif
                                                                @else
                                                                    {{ $cambio->old ?: '-' }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(array_key_exists($cambio->campo, $camposRelacionados) && $campoRelacionado)
                                                                    {{ $valorRelacionadoDespues ?: '-' }}
                                                                    @if($cambio->new === '0')
                                                                        (ID: Manual)
                                                                    @elseif($cambio->new === '' || $cambio->new === null)
                                                                        (ID: Nulo)
                                                                    @else
                                                                        (ID: {{ $cambio->new }})
                                                                    @endif
                                                                @elseif($relacionInversa !== false && $campoRelacionado)
                                                                    {{ $cambio->new ?: '-' }}
                                                                    @if($valorRelacionadoDespues === '0')
                                                                        (ID: Manual)
                                                                    @elseif($valorRelacionadoDespues === '' || $valorRelacionadoDespues === null)
                                                                        (ID: Nulo)
                                                                    @else
                                                                        (ID: {{ $valorRelacionadoDespues }})
                                                                    @endif
                                                                @else
                                                                    {{ $cambio->new ?: '-' }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Agregar este script al final -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Asegurarse de que el accordion funcione dentro del modal
    var modalEl = document.getElementById('historialHidranteModal{{ $hidrante->id }}');
    if (modalEl) {
        modalEl.addEventListener('shown.bs.modal', function() {
            var accordionButtons = this.querySelectorAll('.accordion-button');
            accordionButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var target = document.querySelector(this.getAttribute('data-bs-target'));
                    if (target) {
                        var bsCollapse = new bootstrap.Collapse(target, {
                            toggle: true
                        });
                    }
                });
            });
        });
    }
});
</script>

<style>
.accordion-item {
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 1rem;
}

.accordion-button {
    background-color: #f8f9fa;
    border-left: 4px solid #17a2b8;
}

.accordion-button:not(.collapsed) {
    background-color: #e7f5ff;
    color: #0c63e4;
}

.accordion-button:focus {
    box-shadow: none;
}
</style>