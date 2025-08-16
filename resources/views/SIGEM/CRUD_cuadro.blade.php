<!-- Header del CRUD -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Panel CRUD de Cuadros Estadísticos</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Errores de validación:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <button type="button" class="btn btn-info btn-lg" data-bs-toggle="modal" data-bs-target="#modalAgregarCuadro">
                    <i class="bi bi-plus-circle"></i> Nuevo Cuadro
                </button>
                <small class="text-muted d-block mt-2">Total de registros: <strong>{{ count($cuadros ?? []) }}</strong></small>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de datos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-table"></i> Listado de Cuadros Estadísticos</h6>
            </div>
            <div class="card-body">
                @if(isset($cuadros) && count($cuadros) > 0)
                    <div class="table-responsive">
                        <table id="tablaCuadros" class="table table-striped table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Subtítulo</th>
                                    <th>Tema</th>
                                    <th>Subtema</th>
                                    <th>Archivos</th>
                                    <th>Gráfica</th>
                                    <th width="120">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cuadros as $cuadro)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $cuadro->cuadro_estadistico_id }}</span></td>
                                    <td>
                                        <code class="text-primary">{{ $cuadro->codigo_cuadro }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $cuadro->cuadro_estadistico_titulo }}</strong>
                                    </td>
                                    <td>
                                        @if($cuadro->cuadro_estadistico_subtitulo)
                                            @if(strlen($cuadro->cuadro_estadistico_subtitulo) > 30)
                                                <span title="{{ $cuadro->cuadro_estadistico_subtitulo }}">
                                                    <small class="text-muted">{{ Str::limit($cuadro->cuadro_estadistico_subtitulo, 30) }}</small>
                                                </span>
                                            @else
                                                <small class="text-muted">{{ $cuadro->cuadro_estadistico_subtitulo }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cuadro->subtema && $cuadro->subtema->tema)
                                            <span class="badge bg-success">{{ $cuadro->subtema->tema->tema_titulo }}</span>
                                        @else
                                            <span class="text-danger">Sin tema</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cuadro->subtema)
                                            <span class="badge bg-warning text-dark">{{ $cuadro->subtema->subtema_titulo }}</span>
                                        @else
                                            <span class="text-danger">Sin subtema</span>
                                        @endif
                                    </td>
                                    <td data-order="{{ ($cuadro->excel_file ? 1 : 0) + ($cuadro->pdf_file ? 1 : 0) + ($cuadro->img_name ? 1 : 0) }}">
                                        <div class="d-flex gap-1">
                                            @if($cuadro->excel_file)
                                                <span class="badge bg-success" title="Excel disponible">
                                                    <i class="bi bi-file-earmark-excel"></i>
                                                </span>
                                            @endif
                                            @if($cuadro->pdf_file)
                                                <span class="badge bg-danger" title="PDF disponible">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </span>
                                            @endif
                                            @if($cuadro->img_name)
                                                <span class="badge bg-primary" title="Imagen disponible">
                                                    <i class="bi bi-image"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-order="{{ $cuadro->permite_grafica ? 1 : 0 }}">
                                        @if($cuadro->permite_grafica)
                                            <span class="badge bg-info" title="Permite gráfica: {{ $cuadro->tipo_grafica_permitida }}">
                                                <i class="bi bi-graph-up"></i> {{ ucfirst($cuadro->tipo_grafica_permitida) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin gráfica</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-warning" 
                                                    title="Editar" 
                                                    onclick="editarCuadro({{ $cuadro->cuadro_estadistico_id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    title="Eliminar" 
                                                    onclick="eliminarCuadro({{ $cuadro->cuadro_estadistico_id }}, '{{ addslashes($cuadro->cuadro_estadistico_titulo) }}')">
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
                        <i class="bi bi-table text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No hay cuadros estadísticos registrados</h5>
                        <p class="text-muted">Comienza agregando tu primer cuadro haciendo clic en el botón "Nuevo Cuadro".</p>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalAgregarCuadro">
                            <i class="bi bi-plus-circle"></i> Agregar Primer Cuadro
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar nuevo cuadro -->
<div class="modal fade" id="modalAgregarCuadro" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Nuevo Cuadro Estadístico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarCuadro" method="POST" action="{{ route('sigem.admin.cuadros.crear') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="codigo_cuadro" class="form-label">Código del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('codigo_cuadro') is-invalid @enderror" 
                                       id="codigo_cuadro" name="codigo_cuadro" 
                                       placeholder="Ej: CUA001" value="{{ old('codigo_cuadro') }}" required>
                                @error('codigo_cuadro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="cuadro_estadistico_titulo" class="form-label">Título del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cuadro_estadistico_titulo') is-invalid @enderror" 
                                       id="cuadro_estadistico_titulo" name="cuadro_estadistico_titulo" 
                                       placeholder="Ej: Población por Edad y Sexo 2024" 
                                       value="{{ old('cuadro_estadistico_titulo') }}" required>
                                @error('cuadro_estadistico_titulo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cuadro_estadistico_subtitulo" class="form-label">Subtítulo del Cuadro</label>
                        <input type="text" class="form-control @error('cuadro_estadistico_subtitulo') is-invalid @enderror" 
                               id="cuadro_estadistico_subtitulo" name="cuadro_estadistico_subtitulo" 
                               placeholder="Descripción adicional del cuadro (opcional)"
                               value="{{ old('cuadro_estadistico_subtitulo') }}">
                        @error('cuadro_estadistico_subtitulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tema_id_select" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select @error('tema_id') is-invalid @enderror" 
                                        id="tema_id_select" name="tema_id" required>
                                    <option value="">Seleccionar tema...</option>
                                    @if(isset($temas))
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->tema_id }}" 
                                                    {{ old('tema_id') == $tema->tema_id ? 'selected' : '' }}>
                                                {{ $tema->tema_titulo }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('tema_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select @error('subtema_id') is-invalid @enderror" 
                                        id="subtema_id" name="subtema_id" required>
                                    <option value="">Seleccionar subtema...</option>
                                </select>
                                @error('subtema_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Archivos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-files"></i> Archivos Asociados</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="excel_file" class="form-label">Archivo Excel</label>
                                        <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                               id="excel_file" name="excel_file" accept=".xlsx,.xls">
                                        <small class="form-text text-muted">Formato: .xlsx o .xls (Max: 5MB)</small>
                                        @error('excel_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pdf_file" class="form-label">Archivo PDF</label>
                                        <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" 
                                               id="pdf_file" name="pdf_file" accept=".pdf">
                                        <small class="form-text text-muted">Formato: .pdf (Max: 5MB)</small>
                                        @error('pdf_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Gráficas -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-graph-up"></i> Configuración de Gráficas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" 
                                               id="permite_grafica" name="permite_grafica" value="1"
                                               {{ old('permite_grafica') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permite_grafica">
                                            Permite gráficas
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="tipo_grafica_permitida" class="form-label">Tipo de Gráfica</label>
                                        <select class="form-select" id="tipo_grafica_permitida" 
                                                name="tipo_grafica_permitida" disabled>
                                            <option value="">Seleccionar...</option>
                                            <option value="bar" {{ old('tipo_grafica_permitida') == 'bar' ? 'selected' : '' }}>Barras</option>
                                            <option value="line" {{ old('tipo_grafica_permitida') == 'line' ? 'selected' : '' }}>Líneas</option>
                                            <option value="pie" {{ old('tipo_grafica_permitida') == 'pie' ? 'selected' : '' }}>Pastel</option>
                                            <option value="doughnut" {{ old('tipo_grafica_permitida') == 'doughnut' ? 'selected' : '' }}>Dona</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" 
                                               id="invertir_eje_vertical_horizontal" 
                                               name="invertir_eje_vertical_horizontal" value="1"
                                               {{ old('invertir_eje_vertical_horizontal') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="invertir_eje_vertical_horizontal">
                                            Invertir ejes
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="eje_vertical_mchart" class="form-label">Etiqueta Eje Vertical</label>
                                        <input type="text" class="form-control" id="eje_vertical_mchart" 
                                               name="eje_vertical_mchart" placeholder="Ej: Población" 
                                               value="{{ old('eje_vertical_mchart') }}" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="pie_pagina" class="form-label">Pie de Página</label>
                                        <input type="text" class="form-control" id="pie_pagina" 
                                               name="pie_pagina" placeholder="Nota o fuente del cuadro"
                                               value="{{ old('pie_pagina') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-save"></i> Guardar Cuadro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar cuadro -->
<div class="modal fade" id="modalEditarCuadro" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Editar Cuadro Estadístico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarCuadro" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_cuadro_estadistico_id" name="cuadro_estadistico_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_codigo_cuadro" class="form-label">Código del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_codigo_cuadro" name="codigo_cuadro" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_cuadro_estadistico_titulo" class="form-label">Título del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_cuadro_estadistico_titulo" name="cuadro_estadistico_titulo" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_cuadro_estadistico_subtitulo" class="form-label">Subtítulo del Cuadro</label>
                        <input type="text" class="form-control" id="edit_cuadro_estadistico_subtitulo" name="cuadro_estadistico_subtitulo">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_tema_id_select" class="form-label">Tema <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_tema_id_select" name="tema_id" required>
                                    <option value="">Seleccionar tema...</option>
                                    @if(isset($temas))
                                        @foreach($temas as $tema)
                                            <option value="{{ $tema->tema_id }}">{{ $tema->tema_titulo }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_subtema_id" class="form-label">Subtema <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_subtema_id" name="subtema_id" required>
                                    <option value="">Seleccionar subtema...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mostrar archivos actuales -->
                    <div id="archivos_actuales" class="mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-save"></i> Actualizar Cuadro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript para funcionalidades -->
<script>
// Inicializar DataTables
$(document).ready(function() {
    @if(isset($cuadros) && count($cuadros) > 0)
    $('#tablaCuadros').DataTable({
        responsive: true,
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        columnDefs: [
            {
                targets: 0, // Columna ID
                width: "6%",
                className: "text-center"
            },
            {
                targets: 1, // Columna Código
                width: "10%",
                className: "text-center"
            },
            {
                targets: 2, // Columna Título
                width: "25%"
            },
            {
                targets: 3, // Columna Subtítulo
                width: "15%"
            },
            {
                targets: 4, // Columna Tema
                width: "12%"
            },
            {
                targets: 5, // Columna Subtema
                width: "12%"
            },
            {
                targets: 6, // Columna Archivos
                width: "8%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 7, // Columna Gráfica
                width: "8%",
                className: "text-center",
                type: "num"
            },
            {
                targets: 8, // Columna Acciones
                width: "120px",
                className: "text-center",
                orderable: false,
                searchable: false
            }
        ],
        order: [[2, 'asc']], // Ordenar por título por defecto
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function() {
            // Ya no necesitamos reinicializar tooltips de Bootstrap
        }
    });
    @endif
});

// Rutas para JavaScript
const routesCuadros = {
    update: '{{ route("sigem.admin.cuadros.actualizar", ":id") }}',
    delete: '{{ route("sigem.admin.cuadros.eliminar", ":id") }}',
    subtemasPorTema: '{{ url("/sigem/admin/cuadros/subtemas") }}'
};

// Datos de subtemas para filtrado
const subtemasData = @json($subtemas ?? []);

function editarCuadro(id) {
    // Buscar los datos del cuadro en la tabla
    const fila = event.target.closest('tr');
    const codigo_cuadro = fila.cells[1].querySelector('code').textContent;
    const titulo = fila.cells[2].querySelector('strong').textContent;
    const subtitulo_cell = fila.cells[3];
    const subtitulo = subtitulo_cell.classList.contains('text-muted') ? '' : subtitulo_cell.textContent;
    
    // Obtener tema y subtema
    const tema_badge = fila.cells[4].querySelector('.badge');
    const subtema_badge = fila.cells[5].querySelector('.badge');
    
    // Llenar el modal de edición
    document.getElementById('edit_cuadro_estadistico_id').value = id;
    document.getElementById('edit_codigo_cuadro').value = codigo_cuadro;
    document.getElementById('edit_cuadro_estadistico_titulo').value = titulo;
    document.getElementById('edit_cuadro_estadistico_subtitulo').value = subtitulo === '-' ? '' : subtitulo;
    
    // Seleccionar tema y subtema
    if (tema_badge && subtema_badge) {
        const temaTexto = tema_badge.textContent;
        const subtemaTexto = subtema_badge.textContent;
        
        // Seleccionar tema
        const temaSelect = document.getElementById('edit_tema_id_select');
        for (let option of temaSelect.options) {
            if (option.text === temaTexto) {
                option.selected = true;
                
                // Cargar subtemas del tema seleccionado
                filtrarSubtemas(temaSelect, document.getElementById('edit_subtema_id'));
                
                // Dar tiempo para que se carguen los subtemas y luego seleccionar
                setTimeout(() => {
                    const subtemaSelect = document.getElementById('edit_subtema_id');
                    for (let option of subtemaSelect.options) {
                        if (option.text === subtemaTexto) {
                            option.selected = true;
                            break;
                        }
                    }
                }, 100);
                break;
            }
        }
    }
    
    // Mostrar archivos actuales
    const archivosCell = fila.cells[6];
    const archivosActualesDiv = document.getElementById('archivos_actuales');
    
    let archivosHtml = '<div class="card"><div class="card-header"><h6 class="mb-0">Archivos Actuales</h6></div><div class="card-body"><div class="row">';
    
    const excelBadge = archivosCell.querySelector('.badge.bg-success');
    const pdfBadge = archivosCell.querySelector('.badge.bg-danger');
    const imgBadge = archivosCell.querySelector('.badge.bg-primary');
    
    if (excelBadge) {
        archivosHtml += '<div class="col-md-4"><span class="badge bg-success"><i class="bi bi-file-earmark-excel"></i> Excel</span></div>';
    }
    if (pdfBadge) {
        archivosHtml += '<div class="col-md-4"><span class="badge bg-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</span></div>';
    }
    if (imgBadge) {
        archivosHtml += '<div class="col-md-4"><span class="badge bg-primary"><i class="bi bi-image"></i> Imagen</span></div>';
    }
    
    if (!excelBadge && !pdfBadge && !imgBadge) {
        archivosHtml += '<div class="col-12"><span class="text-muted">Sin archivos actuales</span></div>';
    }
    
    archivosHtml += '</div></div></div>';
    archivosActualesDiv.innerHTML = archivosHtml;
    
    // Actualizar la acción del formulario
    const form = document.getElementById('formEditarCuadro');
    form.action = routesCuadros.update.replace(':id', id);
    
    // Mostrar modal
    new bootstrap.Modal(document.getElementById('modalEditarCuadro')).show();
}

function eliminarCuadro(id, titulo) {
    // Obtener información adicional de la fila para confirmación más específica
    const fila = event.target.closest('tr');
    const codigo = fila.cells[1].querySelector('code').textContent;
    const archivosCell = fila.cells[6];
    const tieneArchivos = archivosCell.querySelector('.badge') !== null;
    
    let mensaje = `¿Estás seguro de eliminar el cuadro estadístico "${titulo}" (${codigo})?`;
    
    if (tieneArchivos) {
        mensaje += `\n\n⚠️ ADVERTENCIA: Este cuadro tiene archivos asociados que también serán eliminados.`;
    }
    
    mensaje += `\n\n✗ Esta acción NO se puede deshacer.`;
    
    if (confirm(mensaje)) {
        // Crear formulario temporal para envío DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routesCuadros.delete.replace(':id', id);
        form.style.display = 'none';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Method spoofing para DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Enviar formulario
        document.body.appendChild(form);
        form.submit();
    }
}

// Filtrar subtemas por tema seleccionado
function filtrarSubtemas(temaSelect, subtemaSelect) {
    const temaId = temaSelect.value;
    subtemaSelect.innerHTML = '<option value="">Seleccionar subtema...</option>';
    
    if (temaId) {
        const subtemasDelTema = subtemasData.filter(subtema => 
            subtema.tema && subtema.tema.tema_id == temaId
        );
        
        subtemasDelTema.forEach(subtema => {
            const option = document.createElement('option');
            option.value = subtema.subtema_id;
            option.textContent = subtema.subtema_titulo;
            subtemaSelect.appendChild(option);
        });
    }
}

// Event listeners para filtrado de subtemas
document.getElementById('tema_id_select')?.addEventListener('change', function() {
    filtrarSubtemas(this, document.getElementById('subtema_id'));
});

document.getElementById('edit_tema_id_select')?.addEventListener('change', function() {
    filtrarSubtemas(this, document.getElementById('edit_subtema_id'));
});

// Control de campos de gráfica
document.getElementById('permite_grafica')?.addEventListener('change', function() {
    const tipoGrafica = document.getElementById('tipo_grafica_permitida');
    const ejeVertical = document.getElementById('eje_vertical_mchart');
    
    if (this.checked) {
        tipoGrafica.disabled = false;
        ejeVertical.disabled = false;
    } else {
        tipoGrafica.disabled = true;
        tipoGrafica.value = '';
        ejeVertical.disabled = true;
        ejeVertical.value = '';
    }
});

// Auto-generar código basado en tema y subtema
document.getElementById('subtema_id')?.addEventListener('change', function() {
    if (this.value) {
        const subtemaSeleccionado = subtemasData.find(subtema => subtema.subtema_id == this.value);
        if (subtemaSeleccionado && subtemaSeleccionado.tema) {
            const codigoBase = subtemaSeleccionado.tema.clave_tema || 'CUA';
            const sugerido = codigoBase + '_' + String(subtemaSeleccionado.subtema_id).padStart(3, '0');
            document.getElementById('codigo_cuadro').placeholder = `Sugerido: ${sugerido}`;
        }
    }
});

// Limpiar modales al cerrar
document.getElementById('modalAgregarCuadro')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('tipo_grafica_permitida').disabled = true;
    document.getElementById('eje_vertical_mchart').disabled = true;
    document.getElementById('subtema_id').innerHTML = '<option value="">Seleccionar subtema...</option>';
    document.getElementById('codigo_cuadro').placeholder = 'Ej: CUA001';
});

document.getElementById('modalEditarCuadro')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('archivos_actuales').innerHTML = '';
    document.getElementById('edit_subtema_id').innerHTML = '<option value="">Seleccionar subtema...</option>';
});
</script>