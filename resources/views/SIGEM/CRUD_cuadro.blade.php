<!-- Header del CRUD -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
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
                <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalAgregarCuadro">
                    <i class="bi bi-plus-circle"></i> Nuevo Cuadro
                </button>
                <small class="text-muted d-block mt-2">Total de registros: <strong>{{ count($cuadros ?? []) }}</strong></small>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de datos -->
<div class="row pb-5">
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
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Asignar Nuevo Cuadro Estadístico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarCuadro" method="POST" action="{{ route('sigem.admin.cuadros.crear') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

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

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="codigo_cuadro" class="form-label">Código del Cuadro <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('codigo_cuadro') is-invalid @enderror" 
                                       id="codigo_cuadro" name="codigo_cuadro" 
                                       placeholder="#NTema.CodigoSubtemaAsignado.#NCuadro, por ejemplo: 2.MA.5" value="{{ old('codigo_cuadro') }}" required>
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
                               placeholder="Subtitulo adicional del cuadro (opcional)"
                               value="{{ old('cuadro_estadistico_subtitulo') }}">
                        @error('cuadro_estadistico_subtitulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                                <div class="col-md-12">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" 
                                               id="permite_grafica" name="permite_grafica" value="1"
                                               {{ old('permite_grafica') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permite_grafica">
                                            Permite gráficas
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row" id="tipos_grafica_container" style="display: none;">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Tipos de Gráfica Permitidos <span class="text-danger">*</span></label>
                                        <div class="form-check">
                                            <input class="form-check-input tipo-grafica-check" type="checkbox" 
                                                   id="tipo_barras" name="tipo_grafica_permitida[]" value="Barras"
                                                   {{ is_array(old('tipo_grafica_permitida')) && in_array('Barras', old('tipo_grafica_permitida')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tipo_barras">
                                                Barras
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input tipo-grafica-check" type="checkbox" 
                                                   id="tipo_columnas" name="tipo_grafica_permitida[]" value="Columnas"
                                                   {{ is_array(old('tipo_grafica_permitida')) && in_array('Columnas', old('tipo_grafica_permitida')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tipo_columnas">
                                                Columnas
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input tipo-grafica-check" type="checkbox" 
                                                   id="tipo_pie" name="tipo_grafica_permitida[]" value="Pie"
                                                   {{ is_array(old('tipo_grafica_permitida')) && in_array('Pie', old('tipo_grafica_permitida')) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tipo_pie">
                                                Pastel (Pie)
                                            </label>
                                        </div>
                                        @error('tipo_grafica_permitida')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="pie_pagina" class="form-label">Pie de Página / Notas</label>
                                        <div id="pie_pagina_toolbar" class="border rounded-top p-2 bg-light">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('bold')">
                                                <i class="bi bi-type-bold"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('italic')">
                                                <i class="bi bi-type-italic"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatText('underline')">
                                                <i class="bi bi-type-underline"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertLineBreak()">
                                                <i class="bi bi-arrow-return-left"></i> Salto de línea
                                            </button>
                                        </div>
                                        <div class="form-control" id="pie_pagina" contenteditable="true" 
                                             style="min-height: 100px; max-height: 200px; overflow-y: auto;"
                                             placeholder="Escriba aquí las notas o fuente del cuadro. Puede usar formato HTML básico.">{{ old('pie_pagina') }}</div>
                                        <input type="hidden" id="pie_pagina_hidden" name="pie_pagina" value="{{ old('pie_pagina') }}">
                                        <small class="form-text text-muted">Use los botones de arriba para dar formato al texto.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
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

                    <!-- Archivos actuales y nuevos -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-files"></i> Gestión de Archivos</h6>
                        </div>
                        <div class="card-body">
                            
                            <!-- Archivos actuales -->
                            <div id="archivos_actuales_section" class="mb-4">
                                <h6 class="text-muted mb-3">Archivos Actuales</h6>
                                <div id="archivo_excel_actual" class="alert alert-info d-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-excel text-success"></i>
                                            <strong>Excel:</strong> <span id="nombre_excel_actual"></span>
                                            <br><small class="text-muted">Archivo guardado como: <span id="archivo_excel_sistema"></span></small>
                                        </div>
                                        <div>
                                            <input type="hidden" id="remove_excel_hidden" name="remove_excel" value="0">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarArchivoExcel()">
                                                <i class="bi bi-trash"></i> Eliminar Excel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="archivo_pdf_actual" class="alert alert-warning d-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            <strong>PDF:</strong> <span id="nombre_pdf_actual"></span>
                                            <br><small class="text-muted">Archivo guardado como: <span id="archivo_pdf_sistema"></span></small>
                                        </div>
                                        <div>
                                            <input type="hidden" id="remove_pdf_hidden" name="remove_pdf" value="0">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarArchivoPdf()">
                                                <i class="bi bi-trash"></i> Eliminar PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="sin_archivos_actuales" class="alert alert-light">
                                    <i class="bi bi-info-circle"></i> No hay archivos actuales asociados a este cuadro.
                                </div>
                            </div>
                            
                            <!-- Subir nuevos archivos -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_excel_file" class="form-label">
                                            <span id="label_excel_nuevo">Nuevo Archivo Excel</span>
                                            <span id="label_excel_reemplazar" class="d-none">Reemplazar Archivo Excel</span>
                                        </label>
                                        <input type="file" class="form-control" id="edit_excel_file" name="excel_file" accept=".xlsx,.xls">
                                        <small class="form-text text-muted">Formato: .xlsx o .xls (Max: 5MB)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_pdf_file" class="form-label">
                                            <span id="label_pdf_nuevo">Nuevo Archivo PDF</span>
                                            <span id="label_pdf_reemplazar" class="d-none">Reemplazar Archivo PDF</span>
                                        </label>
                                        <input type="file" class="form-control" id="edit_pdf_file" name="pdf_file" accept=".pdf">
                                        <small class="form-text text-muted">Formato: .pdf (Max: 5MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración de Gráficas para Editar -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-graph-up"></i> Configuración de Gráficas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" 
                                               id="edit_permite_grafica" name="permite_grafica" value="1">
                                        <label class="form-check-label" for="edit_permite_grafica">
                                            Permite gráficas
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row" id="edit_tipos_grafica_container" style="display: none;">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Tipos de Gráfica Permitidos <span class="text-danger">*</span></label>
                                        <div class="form-check">
                                            <input class="form-check-input edit-tipo-grafica-check" type="checkbox" 
                                                   id="edit_tipo_barras" name="tipo_grafica_permitida[]" value="Barras">
                                            <label class="form-check-label" for="edit_tipo_barras">
                                                Barras
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input edit-tipo-grafica-check" type="checkbox" 
                                                   id="edit_tipo_columnas" name="tipo_grafica_permitida[]" value="Columnas">
                                            <label class="form-check-label" for="edit_tipo_columnas">
                                                Columnas
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input edit-tipo-grafica-check" type="checkbox" 
                                                   id="edit_tipo_pie" name="tipo_grafica_permitida[]" value="Pie">
                                            <label class="form-check-label" for="edit_tipo_pie">
                                                Pastel (Pie)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="edit_pie_pagina" class="form-label">Pie de Página / Notas</label>
                                        <div id="edit_pie_pagina_toolbar" class="border rounded-top p-2 bg-light">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatEditText('bold')">
                                                <i class="bi bi-type-bold"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatEditText('italic')">
                                                <i class="bi bi-type-italic"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="formatEditText('underline')">
                                                <i class="bi bi-type-underline"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertEditLineBreak()">
                                                <i class="bi bi-arrow-return-left"></i> Salto de línea
                                            </button>
                                        </div>
                                        <div class="form-control" id="edit_pie_pagina" contenteditable="true" 
                                             style="min-height: 100px; max-height: 200px; overflow-y: auto;"
                                             placeholder="Escriba aquí las notas o fuente del cuadro. Puede usar formato HTML básico."></div>
                                        <input type="hidden" id="edit_pie_pagina_hidden" name="pie_pagina">
                                        <small class="form-text text-muted">Use los botones de arriba para dar formato al texto.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

<!-- JavaScript actualizado -->
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
    subtemasPorTema: '{{ url("/sigem/admin/cuadros/subtemas") }}',
    obtenerCuadro: '{{ route("sigem.admin.cuadros.obtener", ":id") }}'
};

// Datos de subtemas para filtrado
const subtemasData = @json($subtemas ?? []);

function editarCuadro(id) {
    // Hacer petición AJAX para obtener datos completos del cuadro
    fetch(routesCuadros.obtenerCuadro.replace(':id', id))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cuadro = data.cuadro;
                
                // Llenar campos básicos
                document.getElementById('edit_cuadro_estadistico_id').value = cuadro.cuadro_estadistico_id;
                document.getElementById('edit_codigo_cuadro').value = cuadro.codigo_cuadro;
                document.getElementById('edit_cuadro_estadistico_titulo').value = cuadro.cuadro_estadistico_titulo;
                document.getElementById('edit_cuadro_estadistico_subtitulo').value = cuadro.cuadro_estadistico_subtitulo || '';
                
                // Configurar tema y subtema
                if (cuadro.tema_id) {
                    document.getElementById('edit_tema_id_select').value = cuadro.tema_id;
                    filtrarSubtemas(document.getElementById('edit_tema_id_select'), document.getElementById('edit_subtema_id'));
                    
                    setTimeout(() => {
                        document.getElementById('edit_subtema_id').value = cuadro.subtema_id;
                    }, 100);
                }
                
                // Configurar gráficas
                const permiteGraficaCheck = document.getElementById('edit_permite_grafica');
                const tiposContainer = document.getElementById('edit_tipos_grafica_container');
                
                permiteGraficaCheck.checked = cuadro.permite_grafica;
                if (cuadro.permite_grafica) {
                    tiposContainer.style.display = 'block';
                    
                    // Limpiar checkboxes anteriores
                    document.querySelectorAll('.edit-tipo-grafica-check').forEach(check => {
                        check.checked = false;
                    });
                    
                    // Marcar tipos seleccionados
                    if (cuadro.tipo_grafica_permitida && Array.isArray(cuadro.tipo_grafica_permitida)) {
                        cuadro.tipo_grafica_permitida.forEach(tipo => {
                            const checkbox = document.querySelector(`#edit_tipo_${tipo.toLowerCase()}`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }
                } else {
                    tiposContainer.style.display = 'none';
                }
                
                // Configurar pie de página
                const editorPie = document.getElementById('edit_pie_pagina');
                const hiddenPie = document.getElementById('edit_pie_pagina_hidden');
                if (cuadro.pie_pagina) {
                    editorPie.innerHTML = cuadro.pie_pagina;
                    hiddenPie.value = cuadro.pie_pagina;
                } else {
                    editorPie.innerHTML = '';
                    hiddenPie.value = '';
                }
                
                // Gestionar archivos actuales
                mostrarArchivosActuales(cuadro);
                
                // Actualizar la acción del formulario
                const form = document.getElementById('formEditarCuadro');
                form.action = routesCuadros.update.replace(':id', id);
                
                // Mostrar modal
                new bootstrap.Modal(document.getElementById('modalEditarCuadro')).show();
            } else {
                alert('Error al cargar los datos del cuadro: ' + (data.error || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del cuadro');
        });
}

function mostrarArchivosActuales(cuadro) {
    const excelActual = document.getElementById('archivo_excel_actual');
    const pdfActual = document.getElementById('archivo_pdf_actual');
    const sinArchivos = document.getElementById('sin_archivos_actuales');
    
    let tieneArchivos = false;
    
    // Resetear estado
    excelActual.classList.add('d-none');
    pdfActual.classList.add('d-none');
    document.getElementById('remove_excel_hidden').value = '0';
    document.getElementById('remove_pdf_hidden').value = '0';
    
    // Mostrar Excel si existe
    if (cuadro.excel_file) {
        const nombreOriginal = extraerNombreOriginal(cuadro.excel_file, cuadro.codigo_cuadro, 'excel');
        document.getElementById('nombre_excel_actual').textContent = nombreOriginal;
        document.getElementById('archivo_excel_sistema').textContent = cuadro.excel_file;
        excelActual.classList.remove('d-none');
        document.getElementById('label_excel_nuevo').classList.add('d-none');
        document.getElementById('label_excel_reemplazar').classList.remove('d-none');
        tieneArchivos = true;
    } else {
        document.getElementById('label_excel_nuevo').classList.remove('d-none');
        document.getElementById('label_excel_reemplazar').classList.add('d-none');
    }
    
    // Mostrar PDF si existe
    if (cuadro.pdf_file) {
        const nombreOriginal = extraerNombreOriginal(cuadro.pdf_file, cuadro.codigo_cuadro, 'pdf');
        document.getElementById('nombre_pdf_actual').textContent = nombreOriginal;
        document.getElementById('archivo_pdf_sistema').textContent = cuadro.pdf_file;
        pdfActual.classList.remove('d-none');
        document.getElementById('label_pdf_nuevo').classList.add('d-none');
        document.getElementById('label_pdf_reemplazar').classList.remove('d-none');
        tieneArchivos = true;
    } else {
        document.getElementById('label_pdf_nuevo').classList.remove('d-none');
        document.getElementById('label_pdf_reemplazar').classList.add('d-none');
    }
    
    // Mostrar mensaje si no hay archivos
    if (tieneArchivos) {
        sinArchivos.style.display = 'none';
    } else {
        sinArchivos.style.display = 'block';
    }
}

function extraerNombreOriginal(nombreArchivo, codigoCuadro, tipo) {
    // Patrón: codigo_cuadro_nombreOriginal_timestamp.extension
    const extension = tipo === 'excel' ? '(xlsx|xls)' : 'pdf';
    const patron = new RegExp(`^${codigoCuadro.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}_(.+)_\\d+\\.(${extension})$`);
    
    const match = nombreArchivo.match(patron);
    if (match) {
        return match[1] + '.' + match[2];
    }
    
    return nombreArchivo; // Fallback
}

function eliminarArchivoExcel() {
    if (confirm('¿Está seguro de eliminar el archivo Excel? Esta acción no se puede deshacer.')) {
        document.getElementById('remove_excel_hidden').value = '1';
        document.getElementById('archivo_excel_actual').classList.add('d-none');
        document.getElementById('label_excel_nuevo').classList.remove('d-none');
        document.getElementById('label_excel_reemplazar').classList.add('d-none');
        
        // Verificar si quedan archivos
        verificarArchivosRestantes();
    }
}

function eliminarArchivoPdf() {
    if (confirm('¿Está seguro de eliminar el archivo PDF? Esta acción no se puede deshacer.')) {
        document.getElementById('remove_pdf_hidden').value = '1';
        document.getElementById('archivo_pdf_actual').classList.add('d-none');
        document.getElementById('label_pdf_nuevo').classList.remove('d-none');
        document.getElementById('label_pdf_reemplazar').classList.add('d-none');
        
        // Verificar si quedan archivos
        verificarArchivosRestantes();
    }
}

function verificarArchivosRestantes() {
    const excelVisible = !document.getElementById('archivo_excel_actual').classList.contains('d-none');
    const pdfVisible = !document.getElementById('archivo_pdf_actual').classList.contains('d-none');
    const sinArchivos = document.getElementById('sin_archivos_actuales');
    
    if (!excelVisible && !pdfVisible) {
        sinArchivos.style.display = 'block';
    } else {
        sinArchivos.style.display = 'none';
    }
}

// Función para filtrar subtemas por tema (FALTABA ESTA FUNCIÓN)
function filtrarSubtemas(selectTema, selectSubtema) {
    const temaId = selectTema.value;
    
    // Limpiar subtemas
    selectSubtema.innerHTML = '<option value="">Seleccionar subtema...</option>';
    
    if (temaId) {
        // Hacer petición AJAX para obtener subtemas
        fetch(`${routesCuadros.subtemasPorTema}/${temaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.subtemas) {
                    data.subtemas.forEach(subtema => {
                        const option = document.createElement('option');
                        option.value = subtema.subtema_id;
                        option.textContent = subtema.subtema_titulo;
                        selectSubtema.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error al cargar subtemas:', error);
            });
    }
}

// Event listeners para los selects de tema (FALTABA ESTO TAMBIÉN)
document.getElementById('tema_id_select')?.addEventListener('change', function() {
    filtrarSubtemas(this, document.getElementById('subtema_id'));
});

document.getElementById('edit_tema_id_select')?.addEventListener('change', function() {
    filtrarSubtemas(this, document.getElementById('edit_subtema_id'));
});

// Limpiar modal de edición al cerrar
document.getElementById('modalEditarCuadro')?.addEventListener('hidden.bs.modal', function() {
    this.querySelector('form').reset();
    document.getElementById('edit_tipos_grafica_container').style.display = 'none';
    document.getElementById('edit_subtema_id').innerHTML = '<option value="">Seleccionar subtema...</option>';
    document.getElementById('edit_pie_pagina').innerHTML = '';
    document.getElementById('edit_pie_pagina_hidden').value = '';
    
    // Resetear archivos
    document.getElementById('archivo_excel_actual').classList.add('d-none');
    document.getElementById('archivo_pdf_actual').classList.add('d-none');
    document.getElementById('sin_archivos_actuales').style.display = 'block';
    document.getElementById('remove_excel_hidden').value = '0';
    document.getElementById('remove_pdf_hidden').value = '0';
});
</script>