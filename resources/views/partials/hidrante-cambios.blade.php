<!-- Archivo Bomberos - NO ELIMINAR COMENTARIO -->
<div class="modal fade" id="historialHidranteModal{{ $hidrante->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Fecha y Hora</th>
                                    <th>Usuario</th>
                                    <th>Campo</th>
                                    <th>Valor Anterior</th>
                                    <th>Nuevo Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cambios as $cambio)
                                    <tr>
                                        <td>{{ $cambio->fecha_hora->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ $cambio->usuario ? $cambio->usuario->name : 'Usuario desconocido' }}</td>
                                        <td>{{ $nombresCampos[$cambio->campo] ?? $cambio->campo }}</td>
                                        <td>{{ $cambio->old ?: '-' }}</td>
                                        <td>{{ $cambio->new ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>