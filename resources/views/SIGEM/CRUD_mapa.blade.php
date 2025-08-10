<!-- Header del CRUD -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-map"></i> Panel CRUD de Mapas</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">Administra los mapas del sistema SIGEM. Aquí puedes crear, editar y eliminar registros de la tabla <strong>"mapas"</strong>.</p>
                <small class="text-muted">Modelo: <code>Mapa.php</code></small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalAgregarMapa">
                    <i class="bi bi-plus-circle"></i> Nuevo Mapa
                </button>
                <small class="text-muted d-block mt-2">Total de registros: <strong>{{ count($mapas ?? []) }}</strong></small>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de datos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-table"></i> Listado de Mapas</h6>
            </div>
            <div class="card-body">
                @if(isset($mapas) && count($mapas) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Sección</th>
                                    <th>Nombre Mapa</th>
                                    <th>Descripción</th>
                                    <th>Enlace</th>
                                    <th>Icono</th>
                                    <th>Código</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mapas as $mapa)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $mapa->mapa_id }}</span></td>
                                    <td>{{ $mapa->nombre_seccion ?? 'Sin sección' }}</td>
                                    <td><strong>{{ $mapa->nombre_mapa }}</strong></td>
                                    <td>
                                        @if(strlen($mapa->descripcion ?? '') > 50)
                                            <span title="{{ $mapa->descripcion }}">
                                                {{ substr($mapa->descripcion, 0, 50) }}...
                                            </span>
                                        @else
                                            {{ $mapa->descripcion ?? 'Sin descripción' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($mapa->enlace)
                                            <a href="{{ $mapa->enlace }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">Sin enlace</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($mapa->icono)
                                            <img src="{{ asset('storage/iconos/' . $mapa->icono) }}" alt="Icono" 
                                                 class="img-thumbnail" style="max-width: 30px; max-height: 30px;" 
                                                 onerror="this.src='{{ asset('images/no-icon.png') }}'">
                                        @else
                                            <i class="bi bi-image text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $mapa->codigo_mapa ?? 'Sin código' }}</code>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-info" 
                                                    title="Ver detalles" 
                                                    onclick="verMapa({{ $mapa->mapa_id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning" 
                                                    title="Editar" 
                                                    onclick="editarMapa({{ $mapa->mapa_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    title="Eliminar" 
                                                    onclick="eliminarMapa({{ $mapa->mapa_id }}, '{{ addslashes($mapa->nombre_mapa) }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No hay mapas registrados</h5>
                        <p class="text-muted">Comienza agregando tu primer mapa haciendo clic en el botón "Nuevo Mapa".</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarMapa">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Mapa
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar nuevo mapa -->
<div class="modal fade" id="modalAgregarMapa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Mapa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarMapa" method="POST" action="#" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_seccion" class="form-label">Nombre de Sección</label>
                                <input type="text" class="form-control" id="nombre_seccion" name="nombre_seccion" 
                                       placeholder="Ej: Cartografía Básica">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_mapa" class="form-label">Nombre del Mapa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nombre_mapa" name="nombre_mapa" 
                                       placeholder="Ej: Mapa de División Política" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Describe brevemente el contenido del mapa..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="enlace" class="form-label">Enlace</label>
                                <input type="url" class="form-control" id="enlace" name="enlace" 
                                       placeholder="https://ejemplo.com/mapa.html">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="codigo_mapa" class="form-label">Código del Mapa</label>
                                <input type="text" class="form-control" id="codigo_mapa" name="codigo_mapa" 
                                       placeholder="MP001">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="icono" class="form-label">Icono (PNG)</label>
                        <input type="file" class="form-control" id="icono" name="icono" accept=".png">
                        <small class="form-text text-muted">Solo archivos PNG. Tamaño recomendado: 64x64px</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Guardar Mapa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript mínimo para acciones -->
<script>
function verMapa(id) {
    alert('Ver detalles del mapa ID: ' + id + '\n(Funcionalidad pendiente)');
}

function editarMapa(id) {
    alert('Editar mapa ID: ' + id + '\n(Funcionalidad pendiente)');
}

function eliminarMapa(id, nombre) {
    if (confirm('¿Estás seguro de eliminar el mapa "' + nombre + '"?')) {
        alert('Eliminar mapa ID: ' + id + '\n(Funcionalidad pendiente)');
    }
}
</script>