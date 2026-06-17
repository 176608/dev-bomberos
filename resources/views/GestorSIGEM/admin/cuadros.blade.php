<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-table me-2"></i>Cuadros Estadísticos V2</h4>
        <a href="{{ route('sgiem.admin.cuadros-v2.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Nuevo Cuadro V2
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(isset($cuadros) && $cuadros->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tabla-cuadros-v2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Subtema</th>
                            <th>Publicado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuadros as $cuadro)
                        <tr>
                            <td>{{ $cuadro->cuadro_id }}</td>
                            <td>{{ $cuadro->codigo_cuadro }}</td>
                            <td>{{ $cuadro->c_titulo }}</td>
                            <td>{{ $cuadro->subtema->subtema_titulo ?? 'N/A' }}</td>
                            <td>
                                @if($cuadro->publicado)
                                    <span class="badge bg-success">Sí</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('sgiem.admin.cuadros-v2.edit', $cuadro->cuadro_id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('sgiem.admin.cuadros-v2.show', $cuadro->cuadro_id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-table" style="font-size: 3rem;"></i>
                <p class="mt-3">No hay cuadros V2 registrados.</p>
                <a href="{{ route('sgiem.admin.cuadros-v2.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Crear primer cuadro
                </a>
            </div>
        @endif
    </div>
</div>
